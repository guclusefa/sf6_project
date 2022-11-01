<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    #[Route('/todo', name: 'todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        // cree des todo si il n'y en a pas
        if (!$session->has('todos')) {
            $todos = [
                'achat' => 'Acheter du pain',
                'travail' => 'Faire le ménage',
                'sport' => 'Faire du sport',
            ];
            $session->set('todos', $todos);
            $this -> addFlash('success', "La liste des tâches a été initialisée");
        } 
        return $this->render('todo/index.html.twig');
    }

    #[Route('/todo/add/{name}/{content}', name: 'todo_add')]
    public function addTodo(Request $request, $name, $content)
    {
        $session = $request->getSession();
        // si jai des todos, sinon redirection avec msg derreur
        if ($session->has('todos')) {
            $todos = $session->get('todos');
            // si type de todo existe deja
            if (isset($todos[$name])) {
                $this -> addFlash('error', "La tâche $name existe déjà");
            } else {
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this -> addFlash('success', "La tâche $name a été ajoutée");
            }
        } else {
            $this -> addFlash('error', "La liste des tâches n'a pas été initialisée");
        }
        return $this->redirectToRoute('todo');
    }
}
