<h1>You've been invited to join a team!</h1>

<p>{{ $invitation->message }}</p>

<p>
    Click the link below to accept the invitation:
</p>

<p>
    <a href="{{ url('/team/invitations/accept/'.$invitation->token) }}">
        Accept Invitation
    </a>
</p>

<p>If you didn't expect this email, you can safely ignore it.</p>
