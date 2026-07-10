<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeBankDetail;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmployeeBankDetailTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function employeeFor(User $user): Employee
    {
        return Employee::factory()->create([
            'user_id' => $user->id,
            'department_id' => 1,
            'password' => bcrypt('password'),
        ]);
    }

    public function test_admin_can_store_bank_detail_with_proof_document(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->admin());
        $employee = $this->employeeFor(User::factory()->create(['role' => 'candidate']));

        $response = $this->postJson("/api/employees/{$employee->id}/bank-detail", [
            'bank_name' => 'HDFC Bank',
            'account_holder_name' => 'Jane Doe',
            'account_number' => '12345678901234',
            'branch_name' => 'MG Road',
            'ifsc_code' => 'HDFC0001234',
            'proof_document' => UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('bank_name', 'HDFC Bank')
            ->assertJsonPath('ifsc_code', 'HDFC0001234');

        $this->assertDatabaseHas('employee_bank_details', [
            'employee_id' => $employee->id,
            'account_number' => '12345678901234',
        ]);

        $path = str_replace('/storage/', '', $response->json('proof_document_path'));
        Storage::disk('public')->assertExists($path);
    }

    public function test_store_fails_validation_with_invalid_ifsc_and_missing_proof(): void
    {
        Sanctum::actingAs($this->admin());
        $employee = $this->employeeFor(User::factory()->create(['role' => 'candidate']));

        $response = $this->postJson("/api/employees/{$employee->id}/bank-detail", [
            'bank_name' => 'HDFC Bank',
            'account_holder_name' => 'Jane Doe',
            'account_number' => '12345678901234',
            'branch_name' => 'MG Road',
            'ifsc_code' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ifsc_code', 'proof_document']);
    }

    public function test_store_is_rejected_when_bank_detail_already_exists(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->admin());
        $employee = $this->employeeFor(User::factory()->create(['role' => 'candidate']));
        EmployeeBankDetail::factory()->create(['employee_id' => $employee->id]);

        $response = $this->postJson("/api/employees/{$employee->id}/bank-detail", [
            'bank_name' => 'HDFC Bank',
            'account_holder_name' => 'Jane Doe',
            'account_number' => '12345678901234',
            'branch_name' => 'MG Road',
            'ifsc_code' => 'HDFC0001234',
            'proof_document' => UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf'),
        ]);

        $response->assertStatus(409);
    }

    public function test_admin_can_show_bank_detail(): void
    {
        Sanctum::actingAs($this->admin());
        $employee = $this->employeeFor(User::factory()->create(['role' => 'candidate']));
        $bankDetail = EmployeeBankDetail::factory()->create(['employee_id' => $employee->id]);

        $this->getJson("/api/employees/{$employee->id}/bank-detail")
            ->assertOk()
            ->assertJsonPath('id', $bankDetail->id);
    }

    public function test_show_returns_404_when_no_bank_detail(): void
    {
        Sanctum::actingAs($this->admin());
        $employee = $this->employeeFor(User::factory()->create(['role' => 'candidate']));

        $this->getJson("/api/employees/{$employee->id}/bank-detail")
            ->assertStatus(404);
    }

    public function test_admin_can_update_bank_detail(): void
    {
        Sanctum::actingAs($this->admin());
        $employee = $this->employeeFor(User::factory()->create(['role' => 'candidate']));
        EmployeeBankDetail::factory()->create(['employee_id' => $employee->id]);

        $this->putJson("/api/employees/{$employee->id}/bank-detail", [
            'bank_name' => 'ICICI Bank',
        ])->assertOk()->assertJsonPath('bank_name', 'ICICI Bank');

        $this->assertDatabaseHas('employee_bank_details', [
            'employee_id' => $employee->id,
            'bank_name' => 'ICICI Bank',
        ]);
    }

    public function test_admin_can_delete_bank_detail(): void
    {
        Sanctum::actingAs($this->admin());
        $employee = $this->employeeFor(User::factory()->create(['role' => 'candidate']));
        $bankDetail = EmployeeBankDetail::factory()->create(['employee_id' => $employee->id]);

        $this->deleteJson("/api/employees/{$employee->id}/bank-detail")
            ->assertNoContent();

        $this->assertDatabaseMissing('employee_bank_details', ['id' => $bankDetail->id]);
    }

    public function test_candidate_cannot_access_another_employees_bank_detail(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);
        $ownEmployee = $this->employeeFor($candidate);
        Offer::factory()->create([
            'user_id' => $candidate->id,
            'employee_id' => $ownEmployee->id,
            'department_id' => 1,
        ]);

        $otherEmployee = $this->employeeFor(User::factory()->create(['role' => 'candidate']));
        EmployeeBankDetail::factory()->create(['employee_id' => $otherEmployee->id]);

        Sanctum::actingAs($candidate);

        $this->getJson("/api/employees/{$otherEmployee->id}/bank-detail")
            ->assertForbidden();
    }
}
