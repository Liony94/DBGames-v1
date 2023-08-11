<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Games;

class GamesFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $game1 = new Games();
        $game1->setName('League of Legends');
        $manager->persist($game1);

        $game2 = new Games();
        $game2->setName('Dota 2');

        $manager->persist($game2);
        $manager->flush();
    }
}

