<?php

use App\Models\Product;
use App\Models\User;

describe('/product', function () {
    $data = [
        'title' => 'The Test Product',
        'description' => 'A wonderful new product for testing purposes.',
        'number' => 'ABC123',
    ];
    $expectedJsonStructure = ['id', 'title', 'number', 'description'];

    beforeEach(function () {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    });

    test('GET /', function () use ($expectedJsonStructure) {
        Product::factory()->count(3)->create();

        $response = $this->get('/product?limit=10&offset=0');
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

    test('POST /', function () use ($data, $expectedJsonStructure) {
        $response = $this->postJson('/product', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'title' => $data['title'],
            'number' => $data['number'],
            'description' => $data['description'],
        ]);
        $response->assertJsonFragment([
            'title' => $data['title'],
        ]);
        $this->assertDatabaseCount('products', 1);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('GET /{id}', function () use ($data, $expectedJsonStructure) {
        $product = Product::factory()->create($data);

        $response = $this->getJson("/product/{$product->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $product->id,
            'title' => $product->title,
            'number' => $product->number,
            'description' => $product->description,
        ]);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('PUT /{id}', function () use ($data, $expectedJsonStructure) {
        $product = Product::factory()->create($data);

        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'number' => 'UPD789',
        ];

        $response = $this->putJson("/product/{$product->id}", $updatedData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => $updatedData['title'],
            'number' => $updatedData['number'],
            'description' => $updatedData['description'],
        ]);
        $response->assertJson([
            'id' => $product->id,
            'title' => $updatedData['title'],
            'number' => $updatedData['number'],
            'description' => $updatedData['description'],
        ]);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('DELETE /{id}', function () use ($data) {
        $product = Product::factory()->create($data);

        $response = $this->deleteJson("/product/{$product->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
        $this->assertDatabaseCount('products', 0);
    });
});
