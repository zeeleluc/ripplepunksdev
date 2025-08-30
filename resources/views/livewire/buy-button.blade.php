<div>
    <!-- Buy button -->
    <button wire:click="toggleModal"
            class="w-full bg-gray-900 hover:bg-gray-800 text-white text-lg font-semibold py-2 sm:py-3 px-4 sm:px-6 mb-2 rounded shadow text-center block">
        Buy RipplePunks
    </button>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white px-4 py-3 rounded shadow-lg max-w-lg w-full relative text-center">

                {{-- Close X button --}}
                <button wire:click="toggleModal"
                        class="absolute top-3 right-5 text-gray-500 hover:text-gray-800 text-xl font-bold">
                    &times;
                </button>

                <h3 class="font-semibold mb-4 text-2xl">Buy RipplePunks</h3>

                <p class="text-gray-800 text-base sm:text-lg leading-relaxed px-4 sm:px-0">
                    Newly minted <em>The Other Punks</em> are RipplePunks #10000 through #19999. Offers are welcome starting at <strong>2 XRP</strong>. Listings are set higher to support a rising floor and benefit holders, but 2 XRP offers are still gladly accepted into the project wallet.
                </p>

                <div class="flex justify-center gap-3 my-4">
                    <a href="https://xrp.cafe/usercollection/r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS/r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS/604/0/rarity%20high/false/[]" target="_blank" class="px-5 py-3 bg-primary-700 hover:bg-primary-600 text-white rounded border font-semibold text-lg">
                        xrp.cafe
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 sm:w-4 sm:h-4 inline-block ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14M5 5v14h14v-5" />
                        </svg>
                    </a>
                    <a href="https://bidds.com/collection/ripplepunks/" target="_blank" class="px-5 py-3 bg-primary-700 hover:bg-primary-600 text-white rounded border font-semibold text-lg">
                        bidds
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 sm:w-4 sm:h-4 inline-block ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14M5 5v14h14v-5" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
