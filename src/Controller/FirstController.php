<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    #[Route('/first', name: 'first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig', [
            'name' => 'FirstController',
        ]);
    }

    #[Route('/first/{name}/{firstname}', name: 'second')]
    public function second(Request $request, $name, $firstname)
    {
        dd($request);
        return $this->render('first/index.html.twig', [
            'name' => $name . ' ' . $firstname,
        ]);
    }

    #[Route(
        'multi/{entier1}/{entier2}',
        name: 'multi',
        requirements: ['entier1' => '\d+', 'entier2' => '\d+']
    )]
    public function multiplication($entier1, $entier2)
    {
        $result = $entier1 * $entier2;
        return new Response("<h1>Le résultat de la multiplication est $result</h1>");
    }

    public function sayHello($name, $firstname){
        return $this->render('first/hello.html.twig', [
            'name' => $name,
            'firstname' => $firstname
        ]);
    }
}
