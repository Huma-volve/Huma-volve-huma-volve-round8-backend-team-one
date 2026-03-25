<!DOCTYPE html>
<html>

<head>
    <title>Welcome to Huma-volve</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #2563eb;">Welcome, Dr. {{ $user->name }}!</h2>
        <p>Your doctor account has been successfully created on Huma-volve.</p>
        <p>You can now log in to manage your appointments and availability.</p>

        <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0; font-weight: bold;">Your Login Credentials:</p>
            <p style="margin: 5px 0 0 0;">Email: {{ $user->email }}</p>
            <p style="margin: 5px 0 0 0;">Password: {{ $password }}</p>
        </div>

        <p>Please log in and change your password as soon as possible for security reasons.</p>

        <p>Best regards,<br>The Huma-volve Team</p>
    </div>
</body>

</html>