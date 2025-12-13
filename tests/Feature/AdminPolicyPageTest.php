<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Policy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPolicyPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_policies_page()
    {
        $admin = User::factory()->create(); 
        
        $response = $this->actingAs($admin)->get('/admin/policies');

        $response->assertStatus(200);
        
        $response->assertSee('No policies found');
    }

    public function test_policies_are_displayed_correctly()
    {
        
        $admin = User::factory()->create();
        $policy = Policy::create([
            'slug' => 'privacy-policy',
            'title' => ['en' => 'Privacy Policy Test'],
            'content' => ['en' => 'This is test content.'],
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/policies');

        $response->assertDontSee('No policies found');
        $response->assertSee('Privacy Policy Test');
        $response->assertSee($policy->slug);
    }
}