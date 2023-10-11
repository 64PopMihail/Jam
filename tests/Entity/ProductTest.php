<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ProductTest extends ApiTestCase
{
    public function testGetCollection(): void
    {

       $client = static::createClient();

       $response = $client->request('GET', '/api/products');
       $this->assertResponseIsSuccessful();

       $errors = $response->toArray(false);
       if (!empty($errors['hydra:description'])) {
           $this->fail('Error in response: ' . $errors['hydra:description']);
       }
       $this->assertJsonContains(['@id' => '/api/products']);
       $this->assertMatchesResourceCollectionJsonSchema(Product::class);
    }

    public function testGetItem(): void
    {
        $client = static::createClient();
        
        $response = $client->request('GET', '/api/products/1');
        
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@type' => 'Product']);
        $this->assertMatchesResourceItemJsonSchema(Product::class);
    }

    public function testGetNonExistingItem(): void
    {
        $client = static::createClient();

        $response = $client->request('GET', '/api/products/1234');
        
        $this->assertResponseStatusCodeSame('404');
    }
}