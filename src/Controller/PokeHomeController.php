<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PokeHomeController extends AbstractController
{
    #[Route('/poke/home', name: 'app_poke_home')]
    public function index(): Response
    {
        return $this->render('poke_home/main.html.twig', [
            'controller_name' => 'PokeHomeController',
        ]);
    }
}
