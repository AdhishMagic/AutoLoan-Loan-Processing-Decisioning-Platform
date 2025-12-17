<?php

namespace Tests\Feature;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoanWizardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create user role
        DB::table('roles')->insert([
            'id' => 1,
            'name' => 'user',
            'description' => 'User',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->user = User::factory()->create(['role_id' => 1]);
    }

    public function test_create_route_redirects_to_step_1()
    {
        $response = $this->actingAs($this->user)->get(route('loans.create'));
        
        $response->assertStatus(302);
        // Assert redirected to a step 1 url
        // We can't know the ID easily without querying, but we can verify it's a redirect to step.show
        $loan = LoanApplication::latest()->first();
        $this->assertNotNull($loan);
        $response->assertRedirect(route('loans.step.show', ['loan' => $loan->id, 'step' => 1]));
    }

    public function test_submit_step_1_saves_and_redirects_to_step_2()
    {
        // 1. Create Loan
        $loan = LoanApplication::create([
            'user_id' => $this->user->id,
            'status' => 'DRAFT',
            'application_number' => 'TEST-001',
            'stage_order' => 1,
        ]);

        // 2. Post Data to Step 1
        $response = $this->actingAs($this->user)->post(route('loans.step.store', ['loan' => $loan->id, 'step' => 1]), [
            'loan_product_type' => 'HOME_LOAN',
            'loan_purpose' => 'Buying a house',
            'requested_amount' => 5000000,
            'requested_tenure_months' => 240,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('loans.step.show', ['loan' => $loan->id, 'step' => 2]));

        // 3. Verify Data Persisted
        $this->assertDatabaseHas('loan_applications', [
            'id' => $loan->id,
            'loan_product_type' => 'HOME_LOAN',
            'requested_amount' => 5000000,
        ]);
    }
}
