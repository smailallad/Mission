<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FactureControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/index');
    }

    public function testEdit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/edit');
    }

    public function testNew()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/new');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/delete');
    }

}
