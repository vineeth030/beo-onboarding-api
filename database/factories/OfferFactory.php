<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $htmlContent = $this->generateOfferLetterHtml();

        return [
            'email_attachment_content_for_client' => $htmlContent,
            'email_content_for_employee' => $htmlContent,
            'client_emails' => ['c1@cc.com', 'c2@cc.com', 'c3@cc.com'],
            'beo_emails' => ['b1@cc.com', 'b2@cc.com', 'b3@cc.com'],
            'user_id' => User::factory(),
            'employee_id' => Employee::factory(),
            'department_id' => rand(1, 9),
        ];
    }

    /**
     * Generate a sample HTML offer letter
     */
    private function generateOfferLetterHtml(): string
    {
        $company = $this->faker->company;
        $position = $this->faker->jobTitle;
        $salary = $this->faker->numberBetween(50000, 150000);
        $startDate = $this->faker->dateTimeBetween('+1 week', '+1 month')->format('F j, Y');
        $candidateName = $this->faker->name;

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Job Offer Letter</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-logo { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .offer-title { font-size: 20px; margin: 20px 0; color: #34495e; }
        .content { margin: 20px 0; }
        .highlight { background-color: #f39c12; color: white; padding: 2px 8px; border-radius: 3px; }
        .terms { background-color: #ecf0f1; padding: 15px; border-left: 4px solid #3498db; margin: 20px 0; }
        .signature { margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">{$company}</div>
        <h2 class="offer-title">Job Offer Letter</h2>
    </div>

    <div class="content">
        <p>Dear <strong>{$candidateName}</strong>,</p>

        <p>We are pleased to extend an offer of employment for the position of <span class="highlight">{$position}</span> at {$company}. We believe your skills and experience will be a valuable addition to our team.</p>

        <div class="terms">
            <h3>Terms of Employment:</h3>
            <ul>
                <li><strong>Position:</strong> {$position}</li>
                <li><strong>Start Date:</strong> {$startDate}</li>
                <li><strong>Annual Salary:</strong> \${$salary}</li>
                <li><strong>Employment Type:</strong> Full-time</li>
                <li><strong>Benefits:</strong> Health insurance, dental coverage, 401(k) matching</li>
                <li><strong>Vacation:</strong> 15 days paid vacation per year</li>
            </ul>
        </div>

        <p>This offer is contingent upon successful completion of background verification and reference checks. Please confirm your acceptance of this offer by signing and returning this letter within <strong>7 business days</strong>.</p>

        <p>We look forward to welcoming you to our team and are excited about the contributions you will make to {$company}.</p>

        <div class="signature">
            <p>Sincerely,</p>
            <br>
            <p><strong>HR Department</strong><br>
            {$company}</p>
        </div>

        <hr style="margin: 30px 0;">

        <p><strong>Candidate Acceptance:</strong></p>
        <p>I, {$candidateName}, accept the terms of employment as outlined in this offer letter.</p>

        <p>Signature: _________________________ Date: _____________</p>
    </div>
</body>
</html>
HTML;
    }
}