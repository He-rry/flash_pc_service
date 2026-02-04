<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Route as RouteModel;

class RbacApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // run migrations
        $this->artisan('migrate', ['--force' => true]);
        // seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolePermissionSeeder']);
    }

    public function test_editor_cannot_create_or_delete_routes()
    {
        $editor = User::factory()->create(["email" => "editor@example.com"]);
        $editor->assignRole('editor');

        $response = $this->actingAs($editor)->postJson('/api/v1/routes', [
            'route_name' => 'Test Route',
            'waypoints' => [],
        ]);

        $response->assertStatus(403);

        // create a route as super-admin to attempt delete
        $super = User::factory()->create(["email" => "super@example.com"]);
        $super->assignRole('super-admin');

        $created = RouteModel::create(['route_name' => 'ToDelete', 'waypoints' => []]);

        $delResponse = $this->actingAs($editor)->deleteJson('/api/v1/routes/' . $created->id);
        $delResponse->assertStatus(403);
    }

    public function test_manager_and_super_can_create_and_delete_routes()
    {
        $manager = User::factory()->create(["email" => "manager@example.com"]);
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->postJson('/api/v1/routes', [
            'route_name' => 'Manager Route',
            'waypoints' => json_encode([]),
        ]);

        $response->assertStatus(201);

        $routeId = $response->json('data.id');

        $delResponse = $this->actingAs($manager)->deleteJson('/api/v1/routes/' . $routeId);
        $delResponse->assertStatus(200);

        $super = User::factory()->create(["email" => "super2@example.com"]);
        $super->assignRole('super-admin');

        $response2 = $this->actingAs($super)->postJson('/api/v1/routes', [
            'route_name' => 'Super Route',
            'waypoints' => json_encode([]),
        ]);
        $response2->assertStatus(201);
    }
}
