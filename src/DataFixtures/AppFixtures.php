<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
        
    }
    //php bin/console doctrine:fixtures:load
    public function load(ObjectManager $manager): void

    {
        $user = new User;
        $user->setEmail('test@test.test');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('$2y$13$aaq.3p271ERT7qNsKZ2sbur9eJOEbt4Qs0BCygehBvg8DJFFoVGJy');

        $manager->persist($user);
        $manager->flush();

        $categories = $this->categoryRepository->findAll();

        //Create categories
        $friend = new Category;
        $friend->setTitle('ami');
        $manager->persist($friend);

        $family = new Category;
        $family->setTitle('famille');
        $manager->persist($family);

        $collegue = new Category;
        $collegue->setTitle('travail');
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

        $health = new Category;
        $health->setTitle('santé');
        $manager->persist($health);

        //Create Contacts
        $faker = Faker\Factory::create('fr_FR');
        $contacts = [];
        for ($i = 0; $i <10; $i++) {
            $contacts[$i] = new Contact;
            $contacts[$i]->setFirstname($faker->firstName);
            $contacts[$i]->setLastname($faker->lastName);
            $contacts[$i]->setEmail($faker->email);
            $contacts[$i]->setPhoneNumber($faker->mobileNumber);
            $contacts[$i]->setAddress($faker->address);
            $contacts[$i]->setCategory($faker->randomElement($categories));
            $contacts[$i]->setUser($user);

            $manager->persist($contacts[$i]);
        }
        
        $manager->flush();
    }
}
