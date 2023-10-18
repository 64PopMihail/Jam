<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Exception\EmailAlreadyExistsException;
use App\Entity\User;

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
            'email' => 'thereseleducblabla@proot.com',
            'password' => 'ilovejam',
        ]]);
        
        $this->assertResponseStatusCodeSame('401');
    }

    public function testValidRegistation(): void
    {
        $body = [];
        $response = static::createClient()->request('POST', '/api/register', ['json' => $body ?: [
            'email' => 'thereseleduc@proot.com',
            'plainPassword' => 'ilovejAm!82',
            'agreedTerms' => true
        ]]);
        
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testRegistationWithAlreadyExistingMail(): void
    {
        $body = [];
        $response = static::createClient()->request('POST', '/api/register', ['json' => $body ?: [
            'email' => 'admin@admin.com',
            'plainPassword' => 'ilovejAm!82',
            'agreedTerms' => true
        ]]);

        $this->assertResponseStatusCodeSame(500);
        $this->assertJsonContains(["hydra:description" => "Un compte utilise déjà cette adresse email."]);
    }

    public function testRegistationWithInvalidEmail(): void
    {
        $body = [];
        $response = static::createClient()->request('POST', '/api/register', ['json' => $body ?: [
            'email' => 'admin@admin',
            'plainPassword' => 'ilovejAm!82',
            'agreedTerms' => true
        ]]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRegistationWithBlankEmail(): void
    {
        $body = [];
        $response = static::createClient()->request('POST', '/api/register', ['json' => $body ?: [
            'email' => '',
            'plainPassword' => 'ilovejAm!82',
            'agreedTerms' => true
        ]]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRegistationWithoutAgreeingTerms(): void
    {
        $body = [];
        $response = static::createClient()->request('POST', '/api/register', ['json' => $body ?: [
            'email' => 'admin@admin.com',
            'plainPassword' => 'ilovejAm!82',
            'agreedTerms' => false
        ]]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRegistationWithAnInvalidPassword(): void
    {
        $body = [];
        $response = static::createClient()->request('POST', '/api/register', ['json' => $body ?: [
            'email' => 'admin@admin.com',
            'plainPassword' => 'ilovejam',
            'agreedTerms' => true
        ]]);

        $this->assertResponseStatusCodeSame(422);
    }
}
