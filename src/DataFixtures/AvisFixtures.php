<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Restaurant;
use App\Entity\Avis;
use Faker\Factory;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class AvisFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = $manager->getRepository(User::class)->findAll();
        $restaurants = $manager->getRepository(Restaurant::class)->findAll();

        if (empty($users)) {
            throw new \Exception('Aucun utilisateur trouvé dans la base de données.');
        }

        if (empty($restaurants)) {
            throw new \Exception('Aucun restaurant trouvé dans la base de données.');
        }

        for ($i = 0; $i < 10; $i++) {
            $avis = new Avis();

            $user = $users[array_rand($users)];
            $restaurant = $restaurants[array_rand($restaurants)];

            $avis->setUser($user);
            $avis->setRestaurant($restaurant);
            $avis->setStarNb(rand(0, 5));
            $avis->setContent($faker->sentence);

            $manager->persist($avis);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 5;
    }
}
