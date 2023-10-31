<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ProductTest extends ApiTestCase
{
    private $client;
    
    public static function setUpBeforeClass(): void
    {
        exec('make db-test-reset');
    }

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

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

    public function testGetProductsByCategory(): void
    {
        $response = $this->client->request('GET','/api/products?categories=1');

        $this->assertResponseIsSuccessful();

        $responseArray = $response->toArray();
        $errors = 0;
        foreach ($responseArray['hydra:member'] as $product) {
            if (!in_array("/api/categories/1", $product['categories'])) {
                $errors++;
            }
        }
        $this->assertJsonContains(['hydra:view' => ['@id' => '/api/products?categories=1']]);
        $this->assertEquals(0,$errors);
        $this->assertMatchesResourceCollectionJsonSchema(Product::class);
        
    }

    public function testGetProductsSearchByName(): void
    {
        $response = $this->client->request('GET','/api/products?name=fra');

        $this->assertResponseIsSuccessful();

        $responseArray = $response->toArray();
        $errors = 0;
        foreach ($responseArray['hydra:member'] as $product) {
            if (!str_contains(strtolower($product['name']),'fra')) {
                $errors++;
            }
        }
        
        $this->assertJsonContains(['hydra:view' => ['@id' => '/api/products?name=fra']]);
        $this->assertEquals(0,$errors);
        $this->assertMatchesResourceCollectionJsonSchema(Product::class);

    }

    public function testGetProductsOrderedByPriceAsc(): void
    {
        $response = $this->client->request('GET','/api/products?order[price]=asc');

        $this->assertResponseIsSuccessful();

        $responseArray = $response->toArray();
        $errors = 0;
        foreach ($responseArray['hydra:member'] as $key => $product) {
            if ($key > 0 && $responseArray['hydra:member'][$key-1]["price"] > $product['price']) {
                $errors++;
            }
        }

        $this->assertJsonContains(['hydra:view' => ['@id' => '/api/products?order%5Bprice%5D=asc']]);
        $this->assertEquals(0,$errors);
        $this->assertMatchesResourceCollectionJsonSchema(Product::class);

    }

    public function testGetProductsOrderedByPriceDesc(): void
    {
        $response = $this->client->request('GET','/api/products?order[price]=desc');

        $this->assertResponseIsSuccessful();

        $responseArray = $response->toArray();
        $errors = 0;
        foreach ($responseArray['hydra:member'] as $key => $product) {
            if ($key > 0 && $responseArray['hydra:member'][$key-1]["price"] < $product['price']) {
                $errors++;
            }
        }

        $this->assertJsonContains(['hydra:view' => ['@id' => '/api/products?order%5Bprice%5D=desc']]);
        $this->assertEquals(0,$errors);
        $this->assertMatchesResourceCollectionJsonSchema(Product::class);

    }

    public function testGetProductsByPriceRange(): void
    {
        $response = $this->client->request('GET','/api/products?price[between]=400..500');

        $this->assertResponseIsSuccessful();

        $responseArray = $response->toArray();
        $errors = 0;
        foreach ($responseArray['hydra:member'] as $product) {
            if ($product['price'] < 400 || $product['price'] > 500) {
                $errors++;
            }
        }

        $this->assertJsonContains(['hydra:view' => ['@id' => '/api/products?price%5Bbetween%5D=400..500']]);
        $this->assertEquals(0,$errors);
        $this->assertMatchesResourceCollectionJsonSchema(Product::class);

    }
}