<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password — {{ $appName }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }
        table {
            border-collapse: collapse;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            padding: 36px 32px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 8px 0 0 0;
            color: #d1fae5;
            font-size: 14px;
        }
        .content {
            padding: 40px 36px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .text {
            font-size: 15px;
            line-height: 1.65;
            color: #475569;
            margin: 0 0 24px 0;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            background: #059669;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            padding: 14px 34px;
            border-radius: 10px;
            box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.35);
        }
        .token-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 16px 20px;
            margin: 24px 0;
            text-align: center;
        }
        .token-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .token-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
            font-weight: 700;
            color: #059669;
            word-break: break-all;
        }
        .callout {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 14px 16px;
            border-radius: 6px;
            margin: 24px 0;
        }
        .callout-text {
            font-size: 13px;
            color: #166534;
            margin: 0;
            line-height: 1.5;
        }
        .fallback-link {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
            word-break: break-all;
        }
        .fallback-link a {
            color: #059669;
            text-decoration: underline;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 36px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            font-size: 12px;
            color: #94a3b8;
            margin: 4px 0;
            line-height: 1.5;
        }
    </style>
</head>
<body style="margin: 0; padding: 30px 15px; background-color: #f1f5f9;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <div class="container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);">
                    <!-- Header -->
                    <div class="header" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 36px 32px; text-align: center;">
                        <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">🛒 {{ $appName }}</h1>
                        <p style="margin: 8px 0 0 0; color: #d1fae5; font-size: 14px;">Password Reset Request</p>
                    </div>

                    <!-- Content -->
                    <div class="content" style="padding: 40px 36px;">
                        <div class="greeting" style="font-size: 18px; font-weight: 600; color: #0f172a; margin-bottom: 16px;">
                            Hello {{ $user->name ?? 'Valued Customer' }},
                        </div>
                        <p class="text" style="font-size: 15px; line-height: 1.65; color: #475569; margin: 0 0 20px 0;">
                            We received a request to reset the password for your <strong>{{ $appName }}</strong> account associated with <span style="color: #0f172a; font-weight: 600;">{{ $user->email }}</span>.
                        </p>
                        <p class="text" style="font-size: 15px; line-height: 1.65; color: #475569; margin: 0 0 24px 0;">
                            Click the button below to set a new password. For security reasons, this link is valid for <strong>{{ $expireMinutes }} minutes</strong>.
                        </p>

                        <!-- CTA Button -->
                        <div class="btn-wrapper" style="text-align: center; margin: 32px 0;">
                            <a href="{{ $resetUrl }}" class="btn" style="display: inline-block; background-color: #059669; color: #ffffff !important; font-size: 15px; font-weight: 600; text-decoration: none; padding: 14px 34px; border-radius: 10px; box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.35);">
                                Reset Password
                            </a>
                        </div>

                        <!-- Token Box -->
                        <div class="token-box" style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 16px 20px; margin: 24px 0; text-align: center;">
                            <div class="token-label" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 6px; font-weight: 600;">Reset Token</div>
                            <div class="token-code" style="font-family: 'Courier New', Courier, monospace; font-size: 14px; font-weight: 700; color: #059669; word-break: break-all;">{{ $token }}</div>
                        </div>

                        <!-- Security Notice -->
                        <div class="callout" style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 14px 16px; border-radius: 6px; margin: 24px 0;">
                            <p class="callout-text" style="font-size: 13px; color: #166534; margin: 0; line-height: 1.5;">
                                🛡️ <strong>Security Tip:</strong> If you didn't request a password reset, you can safely ignore this email. Your password will not change until you click the link and set a new one.
                            </p>
                        </div>

                        <!-- Fallback URL -->
                        <div class="fallback-link" style="font-size: 13px; color: #64748b; line-height: 1.6; margin-top: 28px; padding-top: 24px; border-top: 1px solid #e2e8f0; word-break: break-all;">
                            If you are having trouble clicking the "Reset Password" button, copy and paste the following URL into your web browser:
                            <br>
                            <a href="{{ $resetUrl }}" style="color: #059669; text-decoration: underline;">{{ $resetUrl }}</a>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer" style="background-color: #f8fafc; padding: 24px 36px; text-align: center; border-top: 1px solid #e2e8f0;">
                        <p style="font-size: 12px; color: #64748b; margin: 0 0 6px 0; font-weight: 500;">
                            {{ $appName }} &bull; Fresh Groceries Delivered to Your Doorstep
                        </p>
                        <p style="font-size: 11px; color: #94a3b8; margin: 0;">
                            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                        </p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
