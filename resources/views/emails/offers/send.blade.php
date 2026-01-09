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
                                <p>Hi,</p>

                                <p>
                                    We would like to inform you that the offer letter for
                                    the candidate, {{ $employeeName }} has been successfully generated.
                                </p>

                                <p>
                                    All relevant details regarding the offer — including compensation, joining date,
                                    and terms of employment — have been included in the attached document.
                                </p>

                                <p>
                                    Kindly review the attachment at your convenience.<br>
                                    If you need any clarification or further changes, please feel free to reach out.
                                </p>

                                <p>
                                    Thanks,<br>
                                    {{ config('app.name') }}
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