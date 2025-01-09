<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Tag;
use Faker\Factory;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;


class TagFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 20; $i++) {
            $tag = new Tag();
            $tag->setLabel($faker->word());

            $manager->persist($tag);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 6;
    }
}
