<?php

use App\Models\Category;
use App\Models\User;

describe('/category', function () {
    $data = [
        'parent_id' => null,
        'order' => 0,
        'title' => 'The Test Category',
        'description' => 'A wonderful new category for testing purposes.',
    ];
    $expectedJsonStructure = ['id', 'title', 'order', 'description', 'parentId'];

    beforeEach(function () {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    });

    test('GET /', function () use ($expectedJsonStructure) {
        Category::factory()->count(3)->create();

        $response = $this->get('/category');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => $expectedJsonStructure
        ]);
        $response->assertJsonCount(3);
    });

    test('GET /csv returns raw CSV file', function () use ($data) {
        $category = Category::factory()->create($data);

        $response = $this->get('/category/csv');

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=categories.csv');
        
        $content = $response->streamedContent();

        expect($content)->toContain('ID,"Parent ID",Order,Title,Description');
        expect($content)->toContain($category->title);
    });

    test('POST /', function () use ($expectedJsonStructure) {
        $request = [
            'order' => 0,
            'title' => 'The Test Category',
            'description' => 'A wonderful new category for testing purposes.',
            'parentId' => null,
        ];
        $response = $this->postJson('/category', $request);
        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', [
            'title' => $request['title'],
            'parent_id' => $request['parentId'],
            'order' => $request['order'],
            'description' => $request['description'],
        ]);
        $response->assertJsonFragment([
            'title' => $request['title'],
            'order' => $request['order'],
            'description' => $request['description'],
            'parentId' => $request['parentId'],
        ]);
        $this->assertDatabaseCount('categories', 1);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('GET /{id}', function () use ($data, $expectedJsonStructure) {
        $category = Category::factory()->create($data);

        $response = $this->getJson("/category/{$category->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $category->id,
            'parentId' => $category->parent_id,
            'title' => $category->title,
            'order' => $category->order,
            'description' => $category->description,
        ]);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('PUT /{id}', function () use ($data, $expectedJsonStructure) {
        $category = Category::factory()->create($data);

        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'order' => 2,
            'parentId' => null,
        ];

        $response = $this->putJson("/category/{$category->id}", $updatedData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => $updatedData['parentId'],
            'title' => $updatedData['title'],
            'order' => $updatedData['order'],
            'description' => $updatedData['description'],
        ]);
        $response->assertJson([
            'id' => $category->id,
            'parentId' => $updatedData['parentId'],
            'title' => $updatedData['title'],
            'order' => $updatedData['order'],
            'description' => $updatedData['description'],
        ]);
        $response->assertJsonStructure($expectedJsonStructure);
    });

    test('DELETE /{id}', function () use ($data) {
        $category = Category::factory()->create($data);

        $response = $this->deleteJson("/category/{$category->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
        $this->assertDatabaseCount('categories', 0);
    });
});
