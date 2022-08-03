<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    //php bin/console doctrine:fixtures:load
    public function load(ObjectManager $manager): void
    {
        $friend = new Category;
        $friend->setTitle('ami');
        $manager->persist($friend);

        $family = new Category;
        $family->setTitle('famille');
        $manager->persist($family);

        $collegue = new Category;
        $collegue->setTitle('collègue');
        $manager->persist($collegue);

        $somebody = new Category;
        $somebody->setTitle('connaissance');
        $manager->persist($somebody);

        $vendor = new Category;
        $vendor->setTitle('fournisseur');
        $manager->persist($vendor);

        $customer = new Category;
        $customer->setTitle('client');
        $manager->persist($customer);

        $manager->flush();
    }
}
