<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        protected UserPasswordHasherInterface $hasher,
        #[Autowire(param: 'env(ADMIN_PLAIN_PWD)')]
        protected string $plainPawd,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->setEmail('admin@sensiolabs.com')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ;
        $user->setPassword($this->hasher->hashPassword($user, 'admin1234'));

        $manager->persist($user);
        $manager->flush();
    }
}
