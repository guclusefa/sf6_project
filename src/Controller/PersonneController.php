<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonneEvent;
use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\MailService;
use App\Service\PdfService;
use App\Service\UploaderService;
use App\Service\UploadImage;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/personne'), IsGranted('ROLE_USER')]
class PersonneController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private Helpers $helpers,
        private EventDispatcherInterface $eventDispatcher,
    )
    {}

    #[Route('/', name: 'personne')]
    public function indexPersonne(ManagerRegistry $doctrine, MailerInterface $mailer): Response
    {
        // get repository for Personne
        $repository = $doctrine->getRepository(Personne::class);
        // get all Personne
        $personnes = $repository->findAll();

        $listAllPersonneEvent = new ListAllPersonneEvent(count($personnes));
        $this->eventDispatcher->dispatch($listAllPersonneEvent, ListAllPersonneEvent::LIST_ALL_PERSONNE_EVENT);
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    #[Route('/pdf/{id}', name: 'personne_pdf')]
    public function generatePdfPersonne(Personne $personne = null, PdfService $pdf){
        $html = $this->render('personne/show.html.twig', [
            'personne' => $personne,
        ]);
        $pdf = $pdf->generatePdf($html);
    }

    #[Route('/all/{page?1}/{nb?12}', name: 'personne_all'), IsGranted('ROLE_USER')]
    public function allPersonne(ManagerRegistry $doctrine, $page, $nb): Response
    {
        echo ($this->helpers->sayCoucou());
        // get repository for Personne
        $repository = $doctrine->getRepository(Personne::class);
        // get prenom par name, tries asc, limit nb de pages, commencent a la nb element, page -1*nb
        $nbPersonne = $repository->count([]);
        $nbPage = ceil($nbPersonne / $nb);
        $personnes = $repository->findBy([], [], $nb, ($page - 1) * $nb);

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbPage' => $nbPage,
            'page' => $page,
            'nb' => $nb,
        ]);
    }

    #[Route('/age/{ageMin}/{ageMax}', name: 'personne_age')]
    public function allPersonneAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonneByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne_age_stats')]
    public function statsPersonneAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonneByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0],
            'ageMin' => $ageMin,
            'ageMax' => $ageMax,
        ]);
    }

    #[Route('/{id}', name: 'personne_show', requirements: ['id' => '\d+'])]
    public function showPersonne(Personne $personne = null): Response
    {
        /*         // get repository for Personne   
        $repository = $doctrine->getRepository(Personne::class);
        // get Personne by id
        $personne = $repository->find($id);
        // if Personne not found */
        if (!$personne) {
            $this->addFlash('error', 'Personne pas trouvée');
            return $this->redirectToRoute('personne');
        }

        // pdf


        return $this->render('personne/show.html.twig', [
            'personne' => $personne,
        ]);
    }

    #[Route('/edit/{id?0}', name: 'personne_edit')]
    public function editPersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
        Request $request,
        UploaderService $uploaderService,
        MailService $mailer,
        MailerInterface $mailerInterface,
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $new = false;
        if (!$personne) {
            $new = true;
            $personne = new Personne();
        }
        $form = $this->createForm(PersonneType::class, $personne);
        // remove
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->remove('deletedAt');

        // handle request
        $form->handleRequest($request);
        // is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $targetDirectory = $this->getParameter('personne_directory');
                $personne->setImage($uploaderService->uploadFile($photo, $targetDirectory));
            }
            // msg
            if ($new) {
                $message = 'ajoutée avec succès';
                $personne->setCreatedBy($this->getUser());
            } else {
                $message = 'modifiée avec succès';
            }

            // manager
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();

            if ($new) {
                // on a cree notre evenement
                $addPersonneEvent = new AddPersonneEvent($personne);
                // on dispatche notre evenement
                $this->eventDispatcher->dispatch($addPersonneEvent, AddPersonneEvent::ADD_PERSONNE_EVENT);
            }

            $this->addFlash('success', $message);
            return $this->redirectToRoute('personne_show', ['id' => $personne->getId()]);
        } else {
            // render
            return $this->render('personne/add.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/update/{id}/{name}/{firstname}/{age}', name: 'personne_update')]
    public function updatePersonne(ManagerRegistry $doctrine, Personne $personne = null, $name, $firstname, $age): Response
    {
        if ($personne) {
            // update personne
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);
            // manager
            $entityManger = $doctrine->getManager();
            // update personne
            $entityManger->persist($personne);
            // commit transaction
            $entityManger->flush();

            $this->addFlash('success', 'Personne mise à jour');
            // redirect to show
            return $this->redirectToRoute('personne_show', ['id' => $personne->getId()]);
        } else {
            $this->addFlash('error', 'Personne pas trouvée');
            return $this->redirectToRoute('personne_all');
        }
    }

    #[Route('/delete/{id}', name: 'personne_delete'), IsGranted('ROLE_ADMIN')]
    public function deletePersonne(ManagerRegistry $doctrine, Personne $personne = null): RedirectResponse
    {
        if (!$personne) {
            $this->addFlash('error', 'Personne pas trouvée');
        } else {
            $entityManger = $doctrine->getManager();
            $entityManger->remove($personne);
            $entityManger->flush();
            $this->addFlash('success', 'Personne supprimée');
        }
        return $this->redirectToRoute('personne_all');
    }
}
