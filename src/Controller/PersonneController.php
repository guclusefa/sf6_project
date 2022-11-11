<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'personne')]
    public function indexPersonne(ManagerRegistry $doctrine): Response
    {
        // get repository for Personne
        $repository = $doctrine->getRepository(Personne::class);
        // get all Personne
        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    #[Route('/all/{page?1}/{nb?12}', name: 'personne_all')]
    public function allPersonne(ManagerRegistry $doctrine, $page, $nb): Response
    {
        // get repository for Personne
        $repository = $doctrine->getRepository(Personne::class);
        // get prenom par name, tries asc, limit nb de pages, commencent a la nb element, page -1*nb
        /** @var \App\Entity\Personne; $repository **/
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
        /** @var \App\Entity\Personne; $repository **/
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonneByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne_age_stats')]
    public function statsPersonneAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        /** @var \App\Entity\Personne; $repository **/
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
        return $this->render('personne/show.html.twig', [
            'personne' => $personne,
        ]);
    }

    #[Route('/add', name: 'personne_add')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        // manager
        $entityManger = $doctrine->getManager();
        // create personne
        $personne = new Personne();
        $personne->setFirstname('John');
        $personne->setName('Doe');
        $personne->setAge(42);
        // insertion dans transaction
        $entityManger->persist($personne);
        // commit transaction
        $entityManger->flush();

        return $this->render('personne/show.html.twig', [
            'personne' => $personne,
        ]);
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

    #[Route('/delete/{id}', name: 'personne_delete')]
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