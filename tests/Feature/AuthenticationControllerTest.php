<?php

use App\Models\User;

describe('/authentication', function () {
    $data = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@doe.com',
        'password' => 's3cr3t',
    ];
    $expectedJsonStructure = [
        'user' => ['id', 'firstName', 'lastName', 'email'],
        'accessToken'
    ];

    test('POST /sign-up', function () use ($data, $expectedJsonStructure) {
        $response = $this->postJson('/authentication/sign-up', $data);

        $response->assertStatus(201)
            ->assertJsonStructure($expectedJsonStructure)
            ->assertJsonPath('user.email', $data['email']);

        $this->assertDatabaseHas('users', [
            'email' => $data['email']
        ]);
    });

    test('POST /sign-in', function () use ($data, $expectedJsonStructure) {
        User::factory()->create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $response = $this->postJson('/authentication/sign-in', [
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure($expectedJsonStructure);
    });

    test('POST /sign-in should throw error', function () {
        User::factory()->create([
            'email' => 'wrong@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->postJson('/authentication/sign-in', [
            'email' => 'wrong@example.com',
            'password' => 'incorrect-password',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    });
});
