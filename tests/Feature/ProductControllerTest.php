<?php

use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to login when viewing products', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view products', function () {
    $user = User::factory()->create();
    Product::factory()->count(15)->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('products.data', 10) // default page size
        );
});

test('products are paginated', function () {
    $user = User::factory()->create();
    Product::factory()->count(25)->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('products.data', 10)
            ->has('products.links')
        );
});

test('products can be paginated with custom page size', function () {
    $user = User::factory()->create();
    Product::factory()->count(25)->create();

    $response = $this->actingAs($user)->get(route('dashboard', ['perPage' => 20]));

    $response->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('products.data', 20)
        );
});

