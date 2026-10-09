<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categoriesData = [
            ['name' => 'Action'],
            ['name' => 'Adventure'],
            ['name' => 'RPG'],
            ['name' => 'Sport'],
            ['name' => 'Racing'],
            ['name' => 'Platform'],
        ];

        foreach($categoriesData as $data){
            $category = new Category;
            $category->setName($data['name']);
            $manager->persist($category);

            $this->addReference($data['name'], $category);
        }

        $manager->flush();
    }
}
