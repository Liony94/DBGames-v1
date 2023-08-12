<?php

namespace App\Controller;

use App\Service\PokemonService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PokedexController extends AbstractController
{
    #[Route('/pokedex', name: 'app_pokedex')]
    public function index(PokemonService $pokemonService, PaginatorInterface $paginator, Request $request): Response
    {
        $pokemons = $pokemonService->getPokemonList();

        return $this->render('pokedex/main.html.twig', [
            'pokemons' => $pokemons,
        ]);
    }
}
