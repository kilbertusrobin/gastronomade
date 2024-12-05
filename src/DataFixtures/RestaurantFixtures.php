<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Restaurant;
use Faker\Factory;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;


class RestaurantFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $restaurant = new Restaurant();
            $restaurant->setName($faker->company);
            $restaurant->setPostalCode($faker->postcode);
            $restaurant->setCity($faker->city);
            $restaurant->setAdress($faker->address);
            $restaurant->setPhone($faker->phoneNumber);
            $restaurant->setLat($faker->latitude);
            $restaurant->setLongitude($faker->longitude);

            $manager->persist($restaurant);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
