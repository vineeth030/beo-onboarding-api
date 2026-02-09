<div>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <title>{{ config('app.name') }}</title>
    </head>

    <body style="margin:0;padding:0;background-color:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" style="padding:30px 0;">
                    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:6px;overflow:hidden;">

                        <!-- Content -->
                        <tr>
                            <td style="padding:30px;color:#333333;">
                                <p>Dear {{ $employee->fullname }},</p>

                                <p>
                                    This is a friendly reminder that your background verification form for the position of <strong>{{ $employee->designation?->name }}</strong> is still pending submission.
                                </p>

                                <p>
                                    Completing your background verification is an important step in your onboarding process. We kindly request you to submit the form at your earliest convenience.
                                </p>

                                <p>
                                    If you have any questions or need assistance with the background verification process, please don't hesitate to reach out to us.
                                </p>

                                <p>
                                    We look forward to receiving your submission soon!
                                </p>

                                <p>
                                    Best regards,<br>
                                    BEO HR Team
                                </p>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style="background:#f1f5f9;padding:15px;text-align:center;
                                    font-size:12px;color:#6b7280;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
    </body>

    </html>
</div>
