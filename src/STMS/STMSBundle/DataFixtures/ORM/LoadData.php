<?php

namespace STMS\STMSBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use STMS\STMSBundle\Entity\Task;
use STMS\STMSBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadData implements FixtureInterface, ContainerAwareInterface {

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var User $user */
        $user = new User();
        $user->setEmail("admin@test.com");
        $user->setFullname("John Smith");

        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword('admin', $user->getSalt()));

        $manager->persist($user);

        /** @var Task $task */
        $task = new Task();
        $task->setName("Test task 1");
        $task->setDate(new DateTime());
        $task->setMinutes(60);
        $task->setUser($user);

        $manager->persist($task);

        $task = new Task();
        $task->setName("Test task 2");
        $task->setDate(new DateTime());
        $task->setMinutes(30);
        $task->setUser($user);
        $task->setNotes("Lorem ipsum");

        $manager->persist($task);

        $task = new Task();
        $task->setName("Test task 3");
        $task->setDate(new DateTime());
        $task->setMinutes(120);
        $task->setUser($user);

        $manager->persist($task);

        $manager->flush();
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}