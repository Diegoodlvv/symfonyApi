<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'username' => 'diego.dlvv',
                'password' => '$2y$13$a4zkCorIzT8nrJo15svQFuudgr16oe0cb/EmJiZO0Ae7cnd1PLcfe'
            ]
        ];

        foreach($users as $data){
            $user = new User();
            $user->setUsername($data['username'])
                 ->setPassword($data['password']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
