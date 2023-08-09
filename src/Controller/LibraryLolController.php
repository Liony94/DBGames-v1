<?php

namespace App\Controller;

use App\Service\RiotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LibraryLolController extends AbstractController
{
    private $riotService;

    public function __construct(RiotService $riotService)
    {
        $this->riotService = $riotService;
    }

    #[Route('/libraryLol', name: 'app_library_lol')]
    public function allChampions(): Response
    {
        $champions = $this->riotService->getChampion();
        $allChampions = $champions['data'];
        $randomChampion = array_rand($allChampions, 154);
        $selectedChampions = [];

        foreach ($randomChampion as $champion) {
            $selectedChampions[] = $allChampions[$champion];
        }

        return $this->render('libraryLol/allChampions.html.twig', [
            'champions' => $selectedChampions,
        ]);
    }

//    #[Route('/libraryLol/{id}', name: 'app_champion')]
//    public function champion($id): Response
//    {
//        $champions = $this->riotService->getChampion();
//        $allChampions = $champions['data'];
//        $selectedChampion = $allChampions[$id];
//
//        return $this->render('libraryLol/champion.html.twig', [
//            'champion' => $selectedChampion,
//        ]);
//    }
}
