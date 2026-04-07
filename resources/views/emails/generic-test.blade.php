<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subjectLine }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { border: 1px solid #e5e7eb; padding: 30px; border-top: none; border-radius: 0 0 8px 8px; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dineflo SMTP Test</h1>
    </div>
    <div class="content">
        <p>Halo,</p>
        <p>{{ $messageBody }}</p>
        <p>Pesan ini dikirim secara otomatis untuk memastikan konfigurasi SMTP restoran Anda berfungsi dengan baik.</p>
        <p>Terima kasih,<br>Tim Dineflo</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Dineflo. All rights reserved.
    </div>
</body>
</html>
