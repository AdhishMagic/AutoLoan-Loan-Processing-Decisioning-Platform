<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $appName }} — Application Submitted</title>
    <style>
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .px { padding-left: 16px !important; padding-right: 16px !important; }
            .stack { display: block !important; width: 100% !important; }
            .cta { width: 100% !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background:#f5f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f5f7fb;">
    <tr>
        <td align="center" style="padding:24px 0;">
            <table role="presentation" cellpadding="0" cellspacing="0" width="600" class="container" style="width:600px; max-width:600px; background:#ffffff; border:1px solid #e6e9f2; border-radius:12px; overflow:hidden;">
                <tr>
                    <td class="px" style="padding:20px 24px; background:#0b1220;">
                        <div style="color:#ffffff; font-weight:700; font-size:16px; letter-spacing:0.2px;">{{ $appName }}</div>
                        <div style="color:#aab3c5; font-size:12px; margin-top:4px;">FinTech Loan Platform</div>
                    </td>
                </tr>

                <tr>
                    <td class="px" style="padding:24px;">
                        <div style="font-size:18px; font-weight:700; color:#0f172a;">Application submitted</div>
                        <div style="margin-top:8px; font-size:14px; color:#334155; line-height:1.6;">
                            Hi {{ $applicantName }}, we’ve received your loan application. Our team will review it and keep you updated.
                        </div>

                        <div style="margin-top:16px;">
                            <span style="display:inline-block; padding:6px 10px; font-size:12px; font-weight:700; border-radius:999px; background:#dbeafe; color:#1d4ed8;">
                                SUBMITTED
                            </span>
                        </div>

                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-top:18px; border:1px solid #eef2ff; border-radius:10px;">
                            <tr>
                                <td class="px" style="padding:14px 16px; background:#f8fafc; border-bottom:1px solid #eef2ff;">
                                    <div style="font-size:12px; color:#64748b; font-weight:700; letter-spacing:0.6px; text-transform:uppercase;">Loan Summary</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px" style="padding:14px 16px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td class="stack" style="width:33.33%; padding:6px 0;">
                                                <div style="font-size:12px; color:#64748b;">Application No</div>
                                                <div style="font-size:14px; color:#0f172a; font-weight:700;">{{ $applicationNumber }}</div>
                                            </td>
                                            <td class="stack" style="width:33.33%; padding:6px 0;">
                                                <div style="font-size:12px; color:#64748b;">Amount</div>
                                                <div style="font-size:14px; color:#0f172a; font-weight:700;">₹{{ is_numeric($amount) ? number_format((float) $amount, 2) : $amount }}</div>
                                            </td>
                                            <td class="stack" style="width:33.33%; padding:6px 0;">
                                                <div style="font-size:12px; color:#64748b;">Tenure</div>
                                                <div style="font-size:14px; color:#0f172a; font-weight:700;">{{ $tenureMonths ?? '—' }} months</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-top:20px;">
                            <tr>
                                <td align="left">
                                    <a href="{{ $loanShowUrl }}" class="cta" style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; font-weight:700; font-size:14px; padding:12px 16px; border-radius:10px;">
                                        View Application
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <div style="margin-top:18px; font-size:12px; color:#64748b; line-height:1.6;">
                            If you have questions, reply to this email or contact support.
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="px" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e6e9f2;">
                        <div style="font-size:11px; color:#64748b; line-height:1.6;">
                            © {{ date('Y') }} {{ $appName }} • This is an automated message.
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
