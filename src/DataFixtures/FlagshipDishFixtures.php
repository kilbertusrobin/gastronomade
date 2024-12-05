<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Restaurant;
use App\Entity\FlagshipDish;
use Faker\Factory;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class FlagshipDishFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $restaurants = $manager->getRepository(Restaurant::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $flagshipDish = new FlagshipDish();            

            $restaurant = $restaurants[array_rand($restaurants)];

            $flagshipDish->setRestaurant($restaurant);
            $flagshipDish->setLabel($faker->sentence(3));
            $flagshipDish->setDescription($faker->sentence(10));
            $flagshipDish->setPhoto('mayokipik.jpg');

            $manager->persist($flagshipDish);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 4;
    }
}
