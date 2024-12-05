<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\TypeUser;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;


class TypeUserFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $typeUser = new TypeUser();
        $typeUser->setLabel('user');
        $manager->persist($typeUser);

        $typeUser = new TypeUser();
        $typeUser->setLabel('admin');
        $manager->persist($typeUser);

        $typeUser = new TypeUser();
        $typeUser->setLabel('critique');
        $manager->persist($typeUser);

        $typeUser = new TypeUser();
        $typeUser->setLabel('restaurateur');
        $manager->persist($typeUser);


        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
