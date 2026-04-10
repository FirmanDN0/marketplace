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
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #2563eb, #1d4ed8); padding:28px 32px 20px; text-align:center;">
                            <div style="display:inline-block; width:40px; height:40px; background-color:#ffffff; border-radius:10px; line-height:40px; margin-bottom:10px;">
                                <span style="color:#2563eb; font-size:20px; font-weight:bold;">S</span>
                            </div>
                            <h1 style="color:#ffffff; font-size:20px; margin:0;">{{ $title }}</h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="color:#374151; font-size:15px; line-height:1.6; margin:0 0 6px;">
                                Halo <strong>{{ $userName }}</strong>,
                            </p>
                            <p style="color:#6b7280; font-size:14px; line-height:1.6; margin:0 0 24px;">
                                {{ $message }}
                            </p>

                            @if ($actionUrl)
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $actionUrl }}" style="display:inline-block; background-color:#2563eb; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:10px; font-size:14px; font-weight:600;">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#f9fafb; padding:16px 32px; text-align:center; border-top:1px solid #e5e7eb;">
                            <p style="color:#9ca3af; font-size:12px; margin:0;">&copy; {{ date('Y') }} ServeMix. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
