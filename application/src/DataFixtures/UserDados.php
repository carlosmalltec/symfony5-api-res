<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserDados extends Fixture
{
    public function load(ObjectManager $manager)
    {
            $user = new User;
            $user->setUsername('carlos')->setPassword('$argon2id$v=19$m=65536,t=4,p=1$FAMHkGyXg1WPt0AgvY/vNg$dyakRQ96Xts0x/10dmNiJOrHiAlQYbr7U6A+atn6G8M');
            $manager->persist($user);
            $manager->flush();
    }
}
