<?php

use App\Models\User;

describe('/user', function () {
    $data = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@doe.com',
        'password' => 's3cr3t',
    ];
    $expectedJsonStructure = ['id', 'firstName', 'lastName', 'email'];

    beforeEach(function () {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    });

    test('GET /', function () use ($expectedJsonStructure) {
        User::factory()->count(3)->create();

        $response = $this->get('/user?limit=10&offset=0');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'items' => [
                '*' => $expectedJsonStructure
            ],
            'total',
            'limit',
            'offset'
        ]);
        // Admin + 3 created users
        $response->assertJsonPath('total', 4);
        $response->assertJsonCount(4, 'items');
    });

    test('POST /', function () use ($data, $expectedJsonStructure) {
        $response = $this->postJson('/user', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
        ]);

        $user = User::where('email', $data['email'])->first();
        $this->assertTrue(Hash::check($data['password'], $user->password));

        $response->assertJsonFragment([
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
        ]);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('GET /{id}', function () use ($data, $expectedJsonStructure) {
        $user = User::factory()->create([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $response = $this->getJson("/user/{$user->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
        ]);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('PUT /{id}', function () use ($data, $expectedJsonStructure) {
        $user = User::factory()->create([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $updatedData = [
            'firstName' => 'John Updated',
            'lastName' => 'Doe Updated',
            'email' => 'john@updated.com',
        ];

        $response = $this->putJson("/user/{$user->id}", $updatedData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => $updatedData['firstName'],
            'last_name' => $updatedData['lastName'],
            'email' => $updatedData['email'],
        ]);
        $response->assertJson([
            'id' => $user->id,
            'firstName' => $updatedData['firstName'],
            'lastName' => $updatedData['lastName'],
            'email' => $updatedData['email'],
        ]);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('DELETE /{id}', function () use ($data) {
        $user = User::factory()->create([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $response = $this->deleteJson("/user/{$user->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    });
});
