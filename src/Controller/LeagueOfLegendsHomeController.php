<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LeagueOfLegendsHomeController extends AbstractController
{
    #[Route('/league/of/legends/home', name: 'app_league_of_legends_home')]
    public function index(): Response
    {
        return $this->render('league_of_legends_home/index.html.twig', [
            'controller_name' => 'LeagueOfLegendsHomeController',
        ]);
    }
}
