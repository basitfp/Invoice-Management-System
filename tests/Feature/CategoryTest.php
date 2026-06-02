<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'status' => true,
    ]);

    $this->agent = User::create([
        'name' => 'Agent User',
        'email' => 'agent@example.com',
        'password' => bcrypt('password'),
        'role' => 'agent',
        'status' => true,
    ]);
});

test('guest cannot access category index', function () {
    $response = $this->get(route('admin.categories.index'));
    $response->assertRedirect(route('login'));
});

test('agent cannot access category index', function () {
    $response = $this->actingAs($this->agent)->get(route('admin.categories.index'));
    $response->assertRedirect(route('agent.dashboard'));
});

test('admin can access category index and see listing', function () {
    Category::create(['name' => 'Electronics', 'status' => true]);
    Category::create(['name' => 'Home Decor', 'status' => false]);

    $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));
    $response->assertStatus(200);
    $response->assertSee('Electronics');
    $response->assertSee('Home Decor');
});

test('admin can store category via AJAX', function () {
    $payload = ['name' => '  Mobile   Phones  ']; // spaces will be collapsed

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.categories.store'), $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => [
                'name' => 'Mobile Phones',
                'status' => true
            ]
        ]);

    $this->assertDatabaseHas('categories', [
        'name' => 'Mobile Phones',
        'status' => true
    ]);
});

test('admin cannot store category with invalid name', function () {
    // Required rule check
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.categories.store'), ['name' => '']);
    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed.'
        ]);

    // Min:2 rule check
    $response2 = $this->actingAs($this->admin)
        ->postJson(route('admin.categories.store'), ['name' => ' A ']); // trims to "A" (1 char)
    $response2->assertStatus(422);

    // Max:100 rule check
    $response3 = $this->actingAs($this->admin)
        ->postJson(route('admin.categories.store'), ['name' => str_repeat('A', 101)]);
    $response3->assertStatus(422);
});

test('admin cannot store duplicate category name', function () {
    Category::create(['name' => 'Appliances']);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.categories.store'), ['name' => 'Appliances']); // Case-insensitive or direct match

    $response->assertStatus(422);
});

test('admin can view category details via AJAX', function () {
    $category = Category::create(['name' => 'Clothing', 'status' => true]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.categories.show', $category));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => 'Clothing'
            ]
        ]);
});

test('admin can update category via AJAX', function () {
    $category = Category::create(['name' => 'Books', 'status' => true]);

    $response = $this->actingAs($this->admin)
        ->putJson(route('admin.categories.update', $category), ['name' => '  Novel   Books  ']); // space collapse

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => [
                'id' => $category->id,
                'name' => 'Novel Books'
            ]
        ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Novel Books'
    ]);
});

test('admin can disable category via AJAX', function () {
    $category = Category::create(['name' => 'Fitness', 'status' => true]);

    $response = $this->actingAs($this->admin)
        ->deleteJson(route('admin.categories.destroy', $category));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Category disabled successfully.',
            'data' => [
                'id' => $category->id,
                'status' => false
            ]
        ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'status' => false
    ]);
});

test('admin can enable category via AJAX', function () {
    $category = Category::create(['name' => 'Toys', 'status' => false]);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.categories.enable', $category));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Category enabled successfully.',
            'data' => [
                'id' => $category->id,
                'status' => true
            ]
        ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'status' => true
    ]);
});
