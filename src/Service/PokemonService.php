<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PokemonService {
    private $client;
    private const BASE_URL = 'https://pokebuildapi.fr/api/v1/pokemon';

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
    }

    public function getPokemonList(int $limit = 50, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;
        $response = $this->client->request('GET', self::BASE_URL . "?limit=$limit&offset=$offset");
        return $response->toArray();
    }

    public function getPokemonDetails(string $url): array
    {
        $response = $this->client->request('GET', $url);
        $pokemonDetails = $response->toArray();

        return $pokemonDetails;
    }
}

