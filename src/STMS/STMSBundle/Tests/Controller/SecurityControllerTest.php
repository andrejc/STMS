<?php

namespace STMS\STMSBundle\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class SecurityControllerTest extends WebTestCase {
    protected function setUp()
    {
        $client = static::createClient();

        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $application->run(new StringInput('doctrine:database:drop --force'));
        $application->run(new StringInput('doctrine:database:create'));
        $application->run(new StringInput('doctrine:schema:update --force'));
    }

    public function testUserRegistration() {
        $client = static::createClient();

        /**
         * Create new user account
         */
        $client->request('POST', '/register', array(
            'email' => 'test@test.com',
            'fullname' => 'John Doe',
            'password' => 'admin'));

        $response = $client->getResponse();

        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertTrue($response->isSuccessful());

        $this->assertContains('success', $response->getContent());

        /*
         * Make sure that the new account has been created
         *
         */
        $em = $client->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('STMSBundle:User')->findOneByEmail('test@test.com');

        $this->assertTrue($user->getFullname() == "John Doe");
    }
}