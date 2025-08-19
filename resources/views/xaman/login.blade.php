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
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ wallet: data.wallet }),
                });
                const finalizeData = await finalizeRes.json();

                if (finalizeData.success) {
                    // Redirect to /profile/{wallet} after login
                    window.location.href = `/profile/${data.wallet}`;
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
