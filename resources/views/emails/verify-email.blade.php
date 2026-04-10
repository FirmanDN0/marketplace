<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:'Segoe UI',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="background: linear-gradient(135deg, #2563eb, #1d4ed8); padding:32px 32px 24px; text-align:center;">
                            <div style="width:48px; height:48px; background-color:#ffffff; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                                <span style="color:#2563eb; font-size:24px; font-weight:bold;">S</span>
                            </div>
                            <h1 style="color:#ffffff; font-size:22px; margin:0;">Verifikasi Email</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="color:#374151; font-size:15px; line-height:1.6; margin:0 0 8px;">
                                Halo <strong>{{ $user->name }}</strong>,
                            </p>
                            <p style="color:#6b7280; font-size:14px; line-height:1.6; margin:0 0 24px;">
                                Terima kasih telah mendaftar di ServeMix! Klik tombol di bawah untuk memverifikasi email kamu.
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $verifyUrl }}" style="display:inline-block; background-color:#2563eb; color:#ffffff; text-decoration:none; padding:14px 32px; border-radius:12px; font-size:15px; font-weight:600;">
                                            Verifikasi Email Saya
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="color:#9ca3af; font-size:13px; line-height:1.6; margin:24px 0 0; text-align:center;">
                                Link ini berlaku selama <strong>24 jam</strong>.
                            </p>
                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:24px 0;">
                            <p style="color:#9ca3af; font-size:12px; line-height:1.6; margin:0;">
                                Jika kamu tidak mendaftar di ServeMix, abaikan email ini.
                            </p>
                            <p style="color:#9ca3af; font-size:12px; line-height:1.6; margin:16px 0 0;">
                                Jika tombol tidak berfungsi, salin link berikut ke browser:<br>
                                <a href="{{ $verifyUrl }}" style="color:#2563eb; word-break:break-all;">{{ $verifyUrl }}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f9fafb; padding:20px 32px; text-align:center; border-top:1px solid #e5e7eb;">
                            <p style="color:#9ca3af; font-size:12px; margin:0;">&copy; {{ date('Y') }} ServeMix. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
