<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_public_user_cannot_access_adding_travel(): void
    {
        $response = $this->postJson('/api/admin/travel');

        $response->assertStatus(401);
    }
    public function test_non_admin_cannot_access_adding_travel(): void
    {
        
        $this->seed(RoleSeeder::class);
        $user=User::factory()->create();
        $user->roles()->attach(Role::where('name','editor')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/admin/travel');
        $response->assertStatus(403);
    }
    public function test_saves_travel_successfully(): void
    {
        $this->seed(RoleSeeder::class);
        $user=User::factory()->create();
        $user->roles()->attach(Role::where('name','admin')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/admin/travel',[
            'name'=>'travel name',
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson('/api/admin/travel',[
            'name'=>'travel name',
            'is_public'=> 1,
            'description'=>'some description',
            'number_of_days'=>5
        ]);
        $response->assertStatus(201);
        $response=$this->getJson('/api/travel');
        $response->assertJsonFragment(['name'=>'travel name']);  

    }
    public function test_update_travel_successfully_with_valid_data(): void
    {
        
        $this->seed(RoleSeeder::class);
        $user=User::factory()->create();
        $user->roles()->attach(Role::where('name','admin')->value('id'));
        $travel = Travel::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/admin/travel/'.$travel->id,[
            'name'=>'travel name',
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson('/api/admin/travel/'.$travel->id,[
            'name'=>'travel name update',
            'is_public'=> 1,
            'description'=>'some description update',
            'number_of_days'=>5
        ]);
        $response->assertStatus(200);
        $response=$this->getJson('/api/travel');
        $response->assertJsonFragment(['name'=>'travel name update']);  

    }
}
