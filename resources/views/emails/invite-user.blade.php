@extends('emails.layout')

@section('icon')
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#ffffff" viewBox="0 0 16 16">
        <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083l6-15Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
    </svg>
@endsection

@section('content')
    <p>Hello!</p>
    
    <p><strong>{{ $inviterName }}</strong> has invited you to join <strong>{{ $organizationName }}</strong> as a <strong>{{ $role }}</strong>.</p>
    
    <p>Please click the button below to accept invitation:</p>

    <div class="button-container">
        <a href="{{ $acceptUrl }}" class="primary-button">
            Accept Invitation
        </a>
    </div>

    <p>This verification link will expire in 7 days</p>

    <p>If you did not expect this invitation, you can safely ignore this email.</p>

    <!-- Alternate Link Section -->
    <div class="alternate-link">
        <p><strong>Having trouble with the button?</strong></p>
        <p>Copy and paste this URL into your browser:</p>
        <a href="{{ $acceptUrl }}">{{ $acceptUrl }}</a>
    </div>
@endsection