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
                                <p>Hello,</p>

                                <p>
                                    This is to inform you that the joining date change request for {{ $employee->full_name }}, {{ $employee->designation?->name }}, has been approved.
                                </p>

                                <p>
                                    The updated joining date is <strong>{{ $updatedJoiningDate }}</strong>.
                                </p>

                                <p>
                                    Please take note of this change for your records and planning purposes.
                                </p>

                                <p>
                                    Thanks,<br>
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
