<div class="max-w-6xl mx-auto px-0 py-6">
    <h1 class="text-2xl font-bold mb-6 text-center">Shoutboard</h1>

    <p class="text-center my-3 text-lg">
        You need to hold the
        <a href="{{ route('badges') }}">
            <span class="bg-primary-600 text-white text-lg font-medium px-2 py-1 rounded-lg">Other Punk</span>
        </a>
        badge to shout.
    </p>

    <div class="bg-white border rounded p-5 mt-6">
        @if (session()->has('message'))
            <div class="mb-4 p-2 bg-green-200 text-green-800 rounded">
                {{ session('message') }}
            </div>
        @endif

        <!-- Create / Edit Form -->
        @if (Auth::check() && Auth::user()->holder?->walletHasSticker(Auth::user()->wallet, 'Other Punk'))
            <form wire:submit.prevent="{{ $editingId ? 'updateShout' : 'postShout' }}">
                <textarea wire:model.defer="{{ $editingId ? 'editingMessage' : 'message' }}"
                          placeholder="Say something..."
                          class="w-full p-2 border rounded"
                          rows="2"></textarea>
                @error($editingId ? 'editingMessage' : 'message')
                <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror

                <div class="mt-1">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ $editingId ? 'Update Shout' : 'Shout!' }}
                    </button>

                    @if ($editingId)
                        <button type="button"
                                wire:click="cancelEdit"
                                class="ml-2 px-4 py-2 rounded border border-gray-300 hover:bg-gray-100">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>

            <hr class="my-6">
        @endif

        <!-- Shout List -->
        <ul>
            @foreach ($shouts as $shout)
                <li class="mb-4 border-b pb-2">
                    <p class="text-xs text-gray-600 pb-1">{{ $shout->user->name }}</p>

                    @if ($shout->user->isAdmin())
                        <a href="{{ route('badges') }}">
                            <span class="bg-yellow-400 text-yellow-800 text-xs font-medium px-2 py-1 rounded-lg">
                                The Dev
                            </span>
                        </a>
                    @else
                        @php
                            $badges = $shout->holder?->badges ?? [];
                            $first = $badges[0] ?? null;
                            $extra = max(count($badges) - 1, 0);
                        @endphp

                        @if ($first)
                            <div class="flex gap-2 items-center">
                                <a href="{{ route('badges') }}">
                                    <span class="bg-primary-600 text-white text-xs font-medium px-2 py-1 rounded-lg">
                                        {{ $first }}
                                    </span>
                                </a>
                                @if ($extra > 0)
                                    <a href="{{ route('badges') }}">
                                        <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded-lg">
                                            +{{ $extra }}
                                        </span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endif

                    <p class="text-lg mt-2">{{ $shout->message }}</p>
                    <p class="text-xs text-gray-500">{{ $shout->created_at->diffForHumans() }}</p>

                    @if (Auth::check() && $shout->wallet === Auth::user()->wallet && Auth::user()->holder?->walletHasSticker(Auth::user()->wallet, 'Other Punk'))
                        @if ($shout->created_at->gt(now()->subHour()))
                            <div class="mt-2">
                                <button wire:click="editShout({{ $shout->id }})"
                                        class="mr-2 px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">
                                    Edit
                                </button>
                                <button wire:click="confirmDelete({{ $shout->id }})"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                    Delete
                                </button>
                            </div>
                        @endif
                    @endif
                </li>
            @endforeach
        </ul>

        <!-- Delete Confirmation -->
        @if (Auth::check() && $confirmingDeletionId)
            @php
                $shoutToDelete = $shouts->firstWhere('id', $confirmingDeletionId);
            @endphp

            @if ($shoutToDelete && $shoutToDelete->wallet === Auth::user()->wallet && Auth::user()->holder?->walletHasSticker(Auth::user()->wallet, 'Other Punk'))
                <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                    <div class="bg-white p-6 rounded shadow-lg max-w-sm w-full">
                        <p class="mb-4">Are you sure you want to delete this shout?</p>
                        <button wire:click="deleteShout({{ $confirmingDeletionId }})"
                                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 mr-2">
                            Yes, Delete
                        </button>
                        <button wire:click="cancel"
                                class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-100">
                            Cancel
                        </button>
                    </div>
                </div>
            @endif
        @endif
    </div>

    @include('components.custom-pagination', ['paginator' => $shouts])
</div>
