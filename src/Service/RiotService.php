<?php

namespace App\Service;

use GuzzleHttp\Client;

class RiotService
{
    private $client;
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->client = new Client([
            'base_uri' => 'https://<RIOT_API_BASE_URL>',
        ]);
        $this->apiKey = $apiKey;
    }

    public function getChampion(): array
    {
        $response = $this->client->request('GET', 'http://ddragon.leagueoflegends.com/cdn/13.15.1/data/fr_FR/champion.json', [
            'headers' => [
                'X-Riot-Token' => $this->apiKey,
            ],
        ]);

        $champions = json_decode($response->getBody()->getContents(), true);

        return $champions;
    }
}