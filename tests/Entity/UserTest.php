<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;

class UserTest extends ApiTestCase
{
    private ?string $token = null;
    private function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();
        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$token]]);
    }
    /**
     * Use other credentials if needed.
     */
    private function getToken($body = []): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = static::createClient()->request('POST', '/auth', ['json' => $body ?: [
            'email' => 'admin@admin.com',
            'password' => 'ilovejam',
        ]]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->token = $data['token'];

        return $data['token'];
    }

    public function testAdminResource()
    {
        $response = $this->createClientWithCredentials()->request('GET', '/admin');
        $this->assertResponseStatusCodeSame('302');
    }

    public function testLoginAsUser()
    {
        $token = $this->getToken([
            'email' => 'pop.mickael@gmail.com',
            'password' => 'ilovejam',
        ]);
        
        $response = $this->createClientWithCredentials($token)->request('GET', '/admin');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testValidLogin(): void
    {
        $body = [];
        $response = static::createClient()->request('POST', '/auth', ['json' => $body ?: [
            'email' => 'admin@admin.com',
            'password' => 'ilovejam',
        ]]);

        $this->assertResponseIsSuccessful();
    }

    public function testinvalidLogin(): void
    {
        $body = [];
        $response = static::createClient()->request('POST', '/auth', ['json' => $body ?: [
            'email' => 'thereseleduc@proot.com',
            'password' => 'ilovejam',
        ]]);
        
        $this->assertResponseStatusCodeSame('401');
    }
}
