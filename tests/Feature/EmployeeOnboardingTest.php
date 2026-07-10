<?php

namespace Tests\Feature;

use App\Mail\SendDocumentsToAccountManagersMail;
use App\Models\Document;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmployeeOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function employee(): Employee
    {
        return Employee::factory()->create([
            'user_id' => User::factory()->create(['role' => 'candidate'])->id,
            'department_id' => 1,
            'password' => bcrypt('password'),
        ]);
    }

    public function test_onboard_emails_employee_documents_to_account_managers(): void
    {
        Mail::fake();
        Storage::fake('public');
        config(['app.accounting_manager_emails' => ['ac1@beo.in', 'ac2@beo.in']]);

        Sanctum::actingAs($this->admin());
        $employee = $this->employee();

        Storage::disk('public')->put("documents/{$employee->id}/pan-original.pdf", 'pan-contents');
        Storage::disk('public')->put("documents/{$employee->id}/aadhar-original.jpg", 'aadhar-contents');

        Document::factory()->create([
            'employee_id' => $employee->id,
            'type' => 'pan',
            'file_path' => "/storage/documents/{$employee->id}/pan-original.pdf",
        ]);
        Document::factory()->create([
            'employee_id' => $employee->id,
            'type' => 'aadhar',
            'file_path' => "/storage/documents/{$employee->id}/aadhar-original.jpg",
        ]);

        $this->postJson("/api/employees/{$employee->id}/onboard")->assertNoContent();

        Mail::assertSent(SendDocumentsToAccountManagersMail::class, function ($mail) use ($employee) {
            $mail->assertHasSubject("New employee onboarded — {$employee->full_name}");
            $mail->assertHasAttachment(
                Attachment::fromStorageDisk('public', "documents/{$employee->id}/pan-original.pdf")->as('pan.pdf')
            );
            $mail->assertHasAttachment(
                Attachment::fromStorageDisk('public', "documents/{$employee->id}/aadhar-original.jpg")->as('aadhar.jpg')
            );

            return $mail->hasTo('ac1@beo.in') && $mail->hasTo('ac2@beo.in');
        });
    }

    public function test_onboard_does_not_email_when_employee_has_no_documents(): void
    {
        Mail::fake();
        Storage::fake('public');
        config(['app.accounting_manager_emails' => ['ac1@beo.in']]);

        Sanctum::actingAs($this->admin());
        $employee = $this->employee();

        $this->postJson("/api/employees/{$employee->id}/onboard")->assertNoContent();

        Mail::assertNotSent(SendDocumentsToAccountManagersMail::class);
    }

    public function test_onboard_does_not_email_when_no_account_managers_configured(): void
    {
        Mail::fake();
        Storage::fake('public');
        config(['app.accounting_manager_emails' => []]);

        Sanctum::actingAs($this->admin());
        $employee = $this->employee();

        Storage::disk('public')->put("documents/{$employee->id}/pan-original.pdf", 'pan-contents');
        Document::factory()->create([
            'employee_id' => $employee->id,
            'type' => 'pan',
            'file_path' => "/storage/documents/{$employee->id}/pan-original.pdf",
        ]);

        $this->postJson("/api/employees/{$employee->id}/onboard")->assertNoContent();

        Mail::assertNotSent(SendDocumentsToAccountManagersMail::class);
    }
}
