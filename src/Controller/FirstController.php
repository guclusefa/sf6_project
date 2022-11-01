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
}
