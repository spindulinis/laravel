<?php

use App\Models\Product;

describe('/public-product', function () {
    $data = [
        'title' => 'The Test Product',
        'description' => 'A wonderful new product for testing purposes.',
        'number' => 'ABC123',
    ];
    $expectedJsonStructure = ['id', 'title', 'number', 'description'];

    test('GET /', function () use ($expectedJsonStructure) {
        Product::factory()->count(3)->create();

        $response = $this->get('/public-product?limit=10&offset=0');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'items' => [
                '*' => $expectedJsonStructure
            ],
            'total',
            'limit',
            'offset'
        ]);
        $response->assertJsonPath('total', 3);
        $response->assertJsonCount(3, 'items');
    });

    test('GET /{id}', function () use ($data, $expectedJsonStructure) {
        $product = Product::factory()->create($data);

        $response = $this->get("/public-product/{$product->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $product->id,
            'title' => $product->title,
            'number' => $product->number,
            'description' => $product->description,
        ]);
        $response->assertJsonStructure($expectedJsonStructure);
    });
});
