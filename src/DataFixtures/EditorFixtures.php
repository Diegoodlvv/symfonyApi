<?php

namespace App\DataFixtures;

use App\Entity\Editor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EditorFixtures extends Fixture
{
    
    public function load(ObjectManager $manager): void
    {
        $editorsData = [
            [
                'name' => 'Nintendo',
                'country' => 'Japan',
            ],
            [
                'name' => 'Sony Interactive Entertainment',
                'country' => 'Japan',
            ],
            [
                'name' => 'Xbox Game Studios',
                'country' => 'United States',
            ],
            [
                'name' => 'Ubisoft',
                'country' => 'France',
            ],
            [
                'name' => 'Electronic Arts',
                'country' => 'United States',
            ],
            [
                'name' => 'Rockstar Games',
                'country' => 'United States',
            ],
            [
                'name' => 'Warner Bros',
                'country' => 'United States',
            ],
            [
                'name' => 'CD Projekt',
                'country' => 'Poland'
            ]
        ];

        foreach($editorsData as $data){
            $editor = new Editor();
            $editor->setName($data['name'])
                   ->setCountry($data['country']);
            $manager->persist($editor);

            $this->addReference($data['name'], $editor);
        }
        $manager->flush();
    }
}
