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
                'email' => 'diego.dlvv@example.com',
                'subscription_to_newsletter' => true,
                'password' => '$2y$13$a4zkCorIzT8nrJo15svQFuudgr16oe0cb/EmJiZO0Ae7cnd1PLcfe'

            ],
            [
                'email' => 'jane.doe@example.com',
                'subscription_to_newsletter' => false,
                'password' => '$2y$13$a4zkCorIzT8nrJo15svQFuudgr16oe0cb/EmJiZO0Ae7cnd1PLcfe'
            ],
            [
                'email' => 'john.doe@example.com',
                'subscription_to_newsletter' => true,
                'password' => '$2y$13$a4zkCorIzT8nrJo15svQFuudgr16oe0cb/EmJiZO0Ae7cnd1PLcfe'
            ]

        ];

        foreach($users as $data){
            $user = new User();
            $user->setEmail($data['email'])
                 ->setSubscriptionToNewsletter($data['subscription_to_newsletter'])
                 ->setPassword($data['password']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
