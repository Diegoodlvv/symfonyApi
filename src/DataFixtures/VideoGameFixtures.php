<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Editor;
use App\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VideoGameFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $videoGamesData = [
            [
                'title' => 'The Legend of Zelda: Breath of the Wild',
                'releaseDate' => new \DateTime('2017-03-03 00:00:00'),
                'description' => 'Aventure en monde ouvert dans le royaume d’Hyrule.',
                'editor' => 'Nintendo',
                'categories' => ['Adventure', 'Action'],
            ],
            [
                'title' => 'Super Mario Odyssey',
                'releaseDate' => new \DateTime('2017-10-27 00:00:00'),
                'description' => 'Jeu de plateforme mettant en scène Mario dans différents mondes.',
                'editor' => 'Nintendo',
                'categories' => ['Platform', 'Adventure'],
            ],
            [
                'title' => 'Marvel’s Spider-Man',
                'releaseDate' => new \DateTime('2018-09-07 00:00:00'),
                'description' => 'Jeu d’action et d’aventure dans la peau de Spider-Man.',
                'editor' => 'Sony Interactive Entertainment',
                'categories' => ['Action', 'Adventure'],
            ],
            [
                'title' => 'God of War',
                'releaseDate' => new \DateTime('2018-04-20 00:00:00'),
                'description' => 'Kratos part à l’aventure dans les terres nordiques avec son fils.',
                'editor' => 'Sony Interactive Entertainment',
                'categories' => ['Action', 'Adventure', 'RPG'],
            ],
            [
                'title' => 'Forza Horizon 5',
                'releaseDate' => new \DateTime('2021-11-09 00:00:00'),
                'description' => 'Jeu de course en monde ouvert situé au Mexique.',
                'editor' => 'Xbox Game Studios',
                'categories' => ['Racing', 'Sport'],
            ],
            [
                'title' => 'Assassin’s Creed Mirage',
                'releaseDate' => new \DateTime('2023-10-05 00:00:00'),
                'description' => 'Jeu d’action-aventure se déroulant dans le Bagdad du IXe siècle.',
                'editor' => 'Ubisoft',
                'categories' => ['Action', 'Adventure'],
            ],
            [
                'title' => 'EA Sports FC 24',
                'releaseDate' => new \DateTime('2023-09-29 00:00:00'),
                'description' => 'Simulation de football avec de nombreuses équipes.',
                'editor' => 'Electronic Arts',
                'categories' => ['Sport'],
            ],
            [
                'title' => 'Hogwarts Legacy',
                'releaseDate' => new \DateTime('+1 day'),
                'description' => 'Jeu d’action-RPG se déroulant dans l’univers de Harry Potter.',
                'editor' => 'Warner Bros',
                'categories' => ['Action', 'RPG'],
            ],
            [
                'title' => 'GTA VI',
                'releaseDate' => new \DateTime('+3 days'),
                'description' => 'Le prochain opus de la célèbre série Grand Theft Auto, se déroulant dans une ville fictive inspirée de Miami.',
                'editor' => 'Rockstar Games',
                'categories' => ['Action', 'Adventure'],
            ],
            [
                'title' => 'Zelda: Ocarina of Time Remake',
                'releaseDate' => new \DateTime('+5 days'),
                'description' => 'Le remake tant attendue du classique de Nintendo, avec de nouvelles aventures et des graphismes améliorés.',
                'editor' => 'Nintendo',
                'categories' => ['Adventure', 'Action'],
            ],
            [
                'title' => 'Cyberpunk 2077: Phantom Liberty',
                'releaseDate' => new \DateTime('+7 days'),
                'description' => 'La nouvelle extension de Cyberpunk 2077, offrant de nouvelles missions et un scénario captivant.',
                'editor' => 'CD Projekt',
                'categories' => ['RPG', 'Action'],
            ]
        ];

        foreach($videoGamesData as $data){
            $videoGame = new VideoGame();
            $videoGame->setTitle($data['title'])
                    ->setReleaseDate($data['releaseDate'])
                    ->setDescription($data['description'])
                    ->setEditor($this->getReference($data['editor'], Editor::class))
                    ->setCoverImage($data['coverImage']);
            foreach($data['categories'] as $dataCategories){
                $videoGame->addCategory($this->getReference($dataCategories, Category::class));
            }

            $manager->persist($videoGame);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EditorFixtures::class,
            CategoryFixtures::class
        ];
    }
}
