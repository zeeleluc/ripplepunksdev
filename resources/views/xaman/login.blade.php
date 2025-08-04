@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto p-6 bg-white rounded shadow text-center">

        @auth
            <h1>Welcome, {{ Auth::user()->name }}</h1>
            <p>You are already logged in.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Logout
                </button>
            </form>
        @else
            <div>
                <h1>Login with xaman</h1>
                <p>Scan the QR code below to log in:</p>
                <img src="{{ $qr }}" alt="xaman Login QR Code">
            </div>

            <script>
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                async function pollLoginStatus() {
                    try {
                        const res = await fetch('/xaman/login-check');
                        const data = await res.json();

                        if (data.success) {
                            // Finalize login on server so Laravel sets session cookie
                            fetch('/xaman/login-finalize', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } })
                                .then(res => res.json())
                                .then(final => {
                                    if (final.success) {
                                        window.location.href = '/'; // Redirect after successful login/session set
                                    }
                                });
                        } else {
                            // Not logged in yet, try again in 2 seconds
                            setTimeout(pollLoginStatus, 2000);
                        }
                    } catch (err) {
                        console.error('Error checking login status:', err);
                        setTimeout(pollLoginStatus, 5000); // Retry later on error
                    }
                }

                // Start polling when page loads
                pollLoginStatus();
            </script>
        @endauth

    </div>
@endsection
