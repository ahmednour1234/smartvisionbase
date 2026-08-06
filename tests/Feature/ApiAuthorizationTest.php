<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles/permissions exist so middleware checks (Spatie) behave correctly.
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_staff_cannot_view_other_users_lead_when_hide_existence_enabled()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $otherStaff = User::factory()->create(['role' => 'staff']);

        $lead = Lead::factory()->create(['sales_rep_id' => $otherStaff->id]);

        $token = $this->postJson('/api/auth/login', [
            'email' => $staff->email,
            'password' => 'password',
        ])->assertOk()->json('token');

        $expected = config('crm.security.hide_existence') ? 404 : 403;

        $this->withToken($token)->getJson('/api/leads/' . $lead->id)
            ->assertStatus($expected);
    }

    public function test_staff_cannot_reassign_sales_rep()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $otherStaff = User::factory()->create(['role' => 'staff']);

        $lead = Lead::factory()->create(['sales_rep_id' => $staff->id]);

        $token = $this->postJson('/api/auth/login', [
            'email' => $staff->email,
            'password' => 'password',
        ])->assertOk()->json('token');

        $this->withToken($token)->putJson('/api/leads/' . $lead->id, [
            'company_name' => $lead->company_name,
            'sales_rep_id' => $otherStaff->id,
        ])->assertStatus(422);
    }

    public function test_staff_cannot_delete_lead_without_permission()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $lead = Lead::factory()->create(['sales_rep_id' => $staff->id]);

        $token = $this->postJson('/api/auth/login', [
            'email' => $staff->email,
            'password' => 'password',
        ])->assertOk()->json('token');

        $expected = config('crm.security.hide_existence') ? 404 : 403;

        $this->withToken($token)->deleteJson('/api/leads/' . $lead->id)
            ->assertStatus($expected);
    }
}
