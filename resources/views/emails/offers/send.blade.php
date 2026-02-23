<div>
    @if($isClient)
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
                        
                        <!-- Header -->
                        <!-- <tr>
                            <td style="background:#2563eb;color:#ffffff;padding:20px 30px;">
                                <h2 style="margin:0;font-size:20px;">{{ config('app.name') }}</h2>
                            </td>
                        </tr> -->

                        <!-- Content -->
                        <tr>
                            <td style="padding:30px;color:#333333;">
                                <p>Hello,</p>

                                <p>
                                    We would like to inform you that the offer letter for the candidate, <strong>{{ $employee->full_name }}</strong> for the position of {{ $employee->designation->name }} has been sent.
                                </p>

                                <p>
                                    All relevant details regarding compensation and employment terms are included in the attached document.
                                </p>

                                @if (str_contains($employee->joining_date, 'month'))
                                    <p>
                                        The candidate is expected to join <strong>{{ $employee->joining_date }}</strong> from today. However, you will be notified of the exact date once the candidate accepts the offer.
                                    </p>    
                                    @else
                                    <p>
                                        The candidate is expected to join <strong>{{ $employee->joining_date }}</strong>.
                                    </p>
                                @endif

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

    @else
        {!! $content !!}
    @endif
</div>