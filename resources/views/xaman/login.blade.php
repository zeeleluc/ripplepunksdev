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
                <h1 class="text-2xl font-bold mb-2">Login with Xaman</h1>
                <img src="{{ $qr }}" class="w-full" alt="xaman Login QR Code">
                <a href="{{ $url }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 sm:hidden">
                    Open Xaman
                </a>
            </div>

            <script>
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                async function pollLoginStatus() {
                    try {
                        const res = await fetch('/xaman/login-check');
                        const data = await res.json();

                        if (data.success) {
                            // Finalize login by sending wallet explicitly
                            const finalizeRes = await fetch('/xaman/login-finalize', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ wallet: data.wallet }),
                            });
                            const finalizeData = await finalizeRes.json();

                            if (finalizeData.success) {
                                window.location.href = '/'; // Redirect after login
                            } else {
                                console.error('Login finalize failed');
                            }
                        } else {
                            setTimeout(pollLoginStatus, 2000);
                        }
                    } catch (err) {
                        console.error('Error checking login status:', err);
                        setTimeout(pollLoginStatus, 5000);
                    }
                }

                // Start polling when page loads
                pollLoginStatus();
            </script>
        @endauth

    </div>
@endsection
