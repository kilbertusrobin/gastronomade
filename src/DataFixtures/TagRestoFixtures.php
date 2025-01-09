<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Tag;
use App\Entity\Restaurant;
use App\Entity\TagResto;
use Faker\Factory;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class TagRestoFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $restaurants = $manager->getRepository(Restaurant::class)->findAll();
        $tags = $manager->getRepository(Tag::class)->findAll();

        if (empty($tags)) {
            throw new \Exception('Aucun tag trouvé dans la base de données.');
        }

        if (empty($restaurants)) {
            throw new \Exception('Aucun restaurant trouvé dans la base de données.');
        }

        for ($i = 0; $i < 30; $i++) {
            $tagResto = new TagResto();

            $randomTag = $tags[array_rand($tags)];
            $randomRestaurant = $restaurants[array_rand($restaurants)];

            $tagResto->setTag($randomTag);
            $tagResto->setRestaurant($randomRestaurant);

            $manager->persist($tagResto);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 7;
    }
}
