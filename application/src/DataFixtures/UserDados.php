<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserDados extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 11; $i++) {
            $user = new User;
            $user->setUsername('carlos'.$i)->setPassword(md5('123456'));
            $manager->persist($user);
            $manager->flush();
        }
        // docker exec -it symfony_php bin/console doctrine:fixtures:load
    }
}
