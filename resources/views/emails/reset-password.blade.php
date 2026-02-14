@extends('emails.layout')

@section('icon')
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#ffffff" viewBox="0 0 16 16">
        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
    </svg>
@endsection

@section('content')
    <p>Hello!</p>
    
    <p>You are receiving this email because we received a password reset request for your account.</p>
    
    <p>Click the button below to reset your password:</p>

    <div class="button-container">
        <a href="{{ $resetUrl }}" class="primary-button">
            Reset Password
        </a>
    </div>

    <p>This password reset link will expire in <strong>{{ $count }} minutes</strong>.</p>

    <!-- Warning Box -->
    <div class="warning-box">
        <p><strong>⚠️ Security Notice:</strong> If you did not request a password reset, no further action is required. Your account is safe.</p>
    </div>

    <!-- Alternate Link Section -->
    <div class="alternate-link">
        <p><strong>Having trouble with the button?</strong></p>
        <p>Copy and paste this URL into your browser:</p>
        <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
    </div>
@endsection