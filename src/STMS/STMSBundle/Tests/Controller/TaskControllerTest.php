<?php

namespace STMS\STMSBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    private $client = null;
    private $em = null;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();

        $application = new Application($this->client->getKernel());
        $application->setAutoExit(false);

        $application->run(new StringInput('doctrine:database:drop --force'));
        $application->run(new StringInput('doctrine:database:create'));
        $application->run(new StringInput('doctrine:schema:update --force'));
        $application->run(new StringInput('doctrine:fixtures:load --append'));

        $this->logIn();
    }

    public function testListTasks() {
        $this->client->request('GET', '/task/list');

        $response = $this->client->getResponse();

        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertTrue($response->isSuccessful());

        $tasks = json_decode($response->getContent());

        $this->assertTrue($tasks[0]->id == 1);
        $this->assertTrue($tasks[0]->name == "Test task 1");
        $this->assertTrue($tasks[0]->date == "2015-04-12");
        $this->assertTrue($tasks[0]->minutes == 60);
        $this->assertTrue($tasks[0]->notes == null);

        $this->assertTrue($tasks[1]->id == 2);
        $this->assertTrue($tasks[1]->name == "Test task 2");
        $this->assertTrue($tasks[1]->date == "2015-04-12");
        $this->assertTrue($tasks[1]->minutes == 30);
        $this->assertTrue($tasks[1]->notes == "Lorem ipsum");

        $this->assertTrue($tasks[2]->id == 3);
        $this->assertTrue($tasks[2]->name == "Test task 3");
        $this->assertTrue($tasks[2]->date == "2015-04-12");
        $this->assertTrue($tasks[2]->minutes == 120);
        $this->assertTrue($tasks[2]->notes == null);
    }

    public function testAddTask() {
        $this->client->request('POST', '/task/add', array(
            'name' => 'New test task',
            'date' => '2015-01-01',
            'minutes' => '20',
            'notes' => 'Some notes'));

        $response = $this->client->getResponse();

        /*
         * Check for successful result
         */
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertTrue($response->isSuccessful());
        $taskData = json_decode($response->getContent());
        $this->assertTrue($taskData->result == 'success');

        /*
         * Make sure that task was actually created
         */
        $taskId = $taskData->taskId;
        $taskEntity = $this->em->getRepository('STMSBundle:Task')->findOneById($taskId);

        $this->assertTrue($taskEntity->getName() == "New test task");
        $this->assertTrue($taskEntity->getDate()->format('Y-m-d') == '2015-01-01');
        $this->assertTrue($taskEntity->getMinutes() == 20);
        $this->assertTrue($taskEntity->getNotes() == "Some notes");
    }

    public function testEditTask() {
        $this->client->request('PUT', '/task/edit/1', array(
            'name' => 'Edited test task',
            'date' => '2015-02-01',
            'minutes' => '30',
            'notes' => 'Edited notes'));

        $response = $this->client->getResponse();

        /*
         * Check for successful result
         */
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertTrue($response->isSuccessful());
        $taskData = json_decode($response->getContent());
        $this->assertTrue($taskData->result == 'success');

        /*
         * Make sure that task was actually edited
         */
        $taskEntity = $this->em->getRepository('STMSBundle:Task')->findOneById(1);

        $this->assertTrue($taskEntity->getName() == "Edited test task");
        $this->assertTrue($taskEntity->getDate()->format('Y-m-d') == '2015-02-01');
        $this->assertTrue($taskEntity->getMinutes() == 30);
        $this->assertTrue($taskEntity->getNotes() == "Edited notes");
    }


    public function testDeleteTask() {
        $this->client->request('DELETE', '/task/delete/1');

        $response = $this->client->getResponse();

        /*
         * Check for successful result
         */
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertTrue($response->isSuccessful());
        $taskData = json_decode($response->getContent());
        $this->assertTrue($taskData->result == 'success');

        /*
         * Make sure that task was actually deleted
         */
        $taskEntity = $this->em->getRepository('STMSBundle:Task')->findOneById(1);
        $this->assertTrue($taskEntity == null);
    }

    /*
     * Create new session so that we can test actions that require authentication
     */
    private function logIn()
    {
        $user = $this->em->getRepository('STMSBundle:User')->findOneByEmail('admin@test.com');

        $session = $this->client->getContainer()->get('session');

        $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());

        $session->set('_security_secured_area', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
