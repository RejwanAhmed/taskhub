<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email Address</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f3f4f6;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #0d9488;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body p {
            color: #374151;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 20px 0;
        }
        .verify-button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #0d9488;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }
        .verify-button:hover {
            background-color: #0f766e;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .alternate-link {
            background-color: #f3f4f6;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
        }
        .alternate-link p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .alternate-link a {
            color: #0d9488;
            word-break: break-all;
            font-size: 12px;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer p {
            color: #6b7280;
            font-size: 14px;
            margin: 5px 0;
        }
        .icon {
            width: 64px;
            height: 64px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#ffffff" viewBox="0 0 16 16">
                    <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083l6-15Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
                </svg>
            </div>
            <h1>TaskHub</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>Hello!</p>
            
            <p>Welcome to <strong>TaskHub</strong>! We're excited to have you on board.</p>
            
            <p>Please click the button below to verify your email address and get started with your account:</p>

            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    Verify Email Address
                </a>
            </div>

            <p>This verification link will expire in {{ config('auth.verification.expire', 60) }} minutes.</p>

            <p>If you did not create an account with TaskHub, no further action is required.</p>

            <!-- Alternate Link Section -->
            <div class="alternate-link">
                <p><strong>Having trouble with the button?</strong></p>
                <p>Copy and paste this URL into your browser:</p>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} TaskHub. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>