<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');

        for($i = 1; $i <= 6; $i++)
        {
            $category = new Category();

           
            $category->setName($faker->word);
            $category->setDescription($faker->text());

    
            $manager->persist($category);
            
        }



        $manager->flush();
    }
}
