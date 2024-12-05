<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\TypeUser;
use Faker\Factory;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;


class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $typeUsers = $manager->getRepository(TypeUser::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword("azerty");
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setUsername($faker->userName);

            $typeUser = $typeUsers[array_rand($typeUsers)];
            $user->setTypeUser($typeUser);

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 3;
    }
}
