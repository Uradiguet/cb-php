<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(Session $session): Response
    {
        return $this->render('index/index.html.twig', [
            'menu'=>[
                ['caption'=>'Utilisateurs','route'=>'app_user']
            ]
        ]);
    }
}
