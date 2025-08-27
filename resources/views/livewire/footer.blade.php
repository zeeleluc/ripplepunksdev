<footer class="bg-primary-900 text-white py-12 mt-12">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Top Section: Logo + Quick Links --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-8 md:gap-0 mb-8">

            {{-- Logo / Branding --}}
            <div class="flex flex-col items-center md:items-start text-center md:text-left">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-primary-50 mb-2">RipplePunks</h2>
                <p class="text-primary-200 text-sm sm:text-base">20,000 Punks on the XRPL</p>
            </div>

            {{-- Navigation Links --}}
            <div class="flex flex-col sm:flex-row gap-6 md:gap-12 justify-center">

                <div class="flex flex-col space-y-2">
                    <h3 class="font-semibold text-primary-100">Explore</h3>
                    <a href="/" class="hover:text-primary-50 transition">Home</a>
                    <a href="/rewards" class="hover:text-primary-50 transition">Rewards</a>
                    <a href="/shoutboard" class="hover:text-primary-50 transition">Shoutboard</a>
                    <a href="/badges" class="hover:text-primary-50 transition">Badges</a>
                    <a href="/holders" class="hover:text-primary-50 transition">Holders</a>
                    <a href="/punks" class="hover:text-primary-50 transition">Punks</a>
                </div>

                <div class="flex flex-col space-y-2">
                    <h3 class="font-semibold text-primary-100">Community</h3>
                    <a target="_blank" href="https://discord.gg/TmHWFSHdSn" class="hover:text-primary-50 transition">Discord</a>
                    <a target="_blank" href="https://twitter.com/RipplePunks" class="hover:text-primary-50 transition">Twitter</a>
                    <a target="_blank" href="https://xrp.cafe/user/rpLqwPLX9ZHhQvismadgHmWFfK2nWxxGTx" class="hover:text-primary-50 transition">Rewards Wallet</a>
                </div>

                <div class="flex flex-col space-y-2">
                    <h3 class="font-semibold text-primary-100">Buy</h3>
                    <a target="_blank" href="https://xrp.cafe/collection/ripplepunks" class="hover:text-primary-50 transition">xrp.cafe</a>
                    <a target="_blank" href="https://bidds.com/collection/ripplepunks" class="hover:text-primary-50 transition">bidds</a>
                </div>

            </div>

        </div>

        {{-- Divider --}}
        <div class="border-t border-primary-700 mb-6"></div>

        {{-- Bottom Section: Copyright + Social Icons --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">

            <p class="text-primary-200 text-sm text-center md:text-left">&copy; {{ date('Y') }} RipplePunks. All rights reserved.</p>

            {{-- Optional Social Icons --}}
            <div class="flex justify-center md:justify-end space-x-4">
                <a target="_blank" href="https://twitter.com/RipplePunks" class="text-primary-200 hover:text-primary-50 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                </a>
                <a target="_blank" href="https://discord.gg/TmHWFSHdSn" class="text-primary-200 hover:text-primary-50 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20 0H4C1.8 0 0 1.8 0 4v16c0 2.2 1.8 4 4 4h16c2.2 0 4-1.8 4-4V4c0-2.2-1.8-4-4-4zm-3.2 17.1c-.6.3-1.2.5-1.8.6-.4.1-.9.2-1.3.2-.5 0-.9-.1-1.4-.2-.6-.1-1.2-.3-1.8-.6-.2-.1-.3-.3-.3-.5s.1-.4.3-.5c.6-.3 1.2-.5 1.8-.6.4-.1.9-.2 1.4-.2s1 .1 1.4.2c.6.1 1.2.3 1.8.6.2.1.3.3.3.5s-.1.4-.3.5zm-5.6-3.1c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>
