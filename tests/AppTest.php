<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RouteTest extends WebTestCase
{
    public function test()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame("200" );
        $this->assertPageTitleSame("Accueil");
        // $this->assertResponseIsSuccessful();
    }
}