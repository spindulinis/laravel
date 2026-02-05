<?php

use App\Models\Attribute;
use App\Models\User;

describe('/attribute', function () {
    $data = [
        'title' => 'The Test Attribute',
    ];
    $expectedJsonStructure = ['id', 'title'];

    beforeEach(function () {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    });

    test('GET /', function () use ($expectedJsonStructure) {
        Attribute::factory()->count(3)->create();

        $response = $this->get('/attribute');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => $expectedJsonStructure
        ]);
        $response->assertJsonCount(3);
    });

    test('POST /', function () use ($expectedJsonStructure) {
        $request = [
            'title' => 'The Test Attribute',
        ];
        $response = $this->postJson('/attribute', $request);
        $response->assertStatus(201);
        $this->assertDatabaseHas('attributes', [
            'title' => $request['title'],
        ]);
        $response->assertJsonFragment([
            'title' => $request['title'],
        ]);
        $this->assertDatabaseCount('attributes', 1);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('GET /{id}', function () use ($data, $expectedJsonStructure) {
        $attribute = Attribute::factory()->create($data);

        $response = $this->getJson("/attribute/{$attribute->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $attribute->id,
            'title' => $attribute->title,
        ]);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('PUT /{id}', function () use ($data, $expectedJsonStructure) {
        $attribute = Attribute::factory()->create($data);

        $updatedData = [
            'title' => 'Updated Title',
        ];

        $response = $this->putJson("/attribute/{$attribute->id}", $updatedData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('attributes', [
            'id' => $attribute->id,
            'title' => $updatedData['title'],
        ]);
        $response->assertJson([
            'id' => $attribute->id,
            'title' => $updatedData['title'],
        ]);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('DELETE /{id}', function () use ($data) {
        $attribute = Attribute::factory()->create($data);

        $response = $this->deleteJson("/attribute/{$attribute->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('attributes', [
            'id' => $attribute->id,
        ]);
        $this->assertDatabaseCount('attributes', 0);
    });
});
