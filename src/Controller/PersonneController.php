<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'personne')]
    public function index(ManagerRegistry $doctrine): Response
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
    public function all(ManagerRegistry $doctrine, $page, $nb): Response
    {
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

    #[Route('/{id}', name: 'personne_show')]
    public function show(Personne $personne = null): Response
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
        // create personne
        $personne2 = new Personne();
        $personne2->setFirstname('Jane');
        $personne2->setName('Doe');
        $personne2->setAge(39);
        // insertion dans transaction
        $entityManger->persist($personne);
        $entityManger->persist($personne2);
        // commit transaction
        $entityManger->flush();

        return $this->render('personne/index.html.twig', [
            'personne' => $personne,
        ]);
    }
}
