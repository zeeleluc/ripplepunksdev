<div class="max-w-6xl mx-auto px-0 py-6">
    <h1 class="text-2xl font-bold mb-6 text-center">Shoutboard</h1>

    <p class="text-center my-3 text-lg">
        You need to hold badges
        <a href="{{ route('badges') }}">
            <span class="bg-primary-600 text-white text-lg font-medium px-2 py-1 rounded-lg">Colony Climber</span>
        </a>
        and
        <a href="{{ route('badges') }}">
            <span class="bg-primary-600 text-white text-lg font-medium px-2 py-1 rounded-lg">OG Initiate</span> to shout.
        </a>
    </p>

    <div class="bg-white border rounded p-5 mt-6">
        @if (session()->has('message'))
            <div class="mb-4 p-2 bg-green-200 text-green-800 rounded">
                {{ session('message') }}
            </div>
        @endif

        <!-- Create or Edit Form -->
        @if (\Illuminate\Support\Facades\Auth::check())
            @if (Auth::user()->hasSticker('Colony Climber') && Auth::user()->hasSticker('OG Initiate'))
                <form wire:submit.prevent="{{ $editingId ? 'updateShout' : 'postShout' }}">
                    <textarea wire:model.defer="{{ $editingId ? 'editingMessage' : 'message' }}" placeholder="Say something..." class="w-full p-2 border rounded" rows="2"></textarea>
                    @error($editingId ? 'editingMessage' : 'message') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

                    <div class="mt-3">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ $editingId ? 'Update Shout' : 'Post Shout' }}
                        </button>

                        @if ($editingId)
                            <button type="button" wire:click="cancelEdit" class="ml-2 px-4 py-2 rounded border border-gray-300 hover:bg-gray-100">
                                Cancel
                            </button>
                        @endif
                    </div>
                </form>

                <hr class="my-6">
            @endif
        @endif

        <!-- Shout List -->
        <ul>
            @foreach ($shouts as $shout)
                <li class="mb-4 border-b pb-2">
                    <p class="text-base">{{ $shout->message }}</p>
                    <p class="text-xs text-gray-500">{{ $shout->created_at->diffForHumans() }}</p>

                    @if (\Illuminate\Support\Facades\Auth::check() && $shout->wallet === Auth::user()->wallet)
                        @if (Auth::user()->hasSticker('Colony Climber') && Auth::user()->hasSticker('OG Initiate'))
                            <div class="mt-2">
                                <button wire:click="editShout({{ $shout->id }})" class="mr-2 px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">Edit</button>
                                <button wire:click="confirmDelete({{ $shout->id }})" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                            </div>
                        @endif
                    @endif
                </li>
            @endforeach
        </ul>

        @if (\Illuminate\Support\Facades\Auth::check() && $shout->wallet === Auth::user()->wallet)
            @if (Auth::user()->hasSticker('Colony Climber') && Auth::user()->hasSticker('OG Initiate'))
                <!-- Confirmation Modal -->
                @if ($confirmingDeletionId)
                    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                        <div class="bg-white p-6 rounded shadow-lg max-w-sm w-full">
                            <p class="mb-4">Are you sure you want to delete this shout?</p>
                            <button wire:click="deleteShout({{ $confirmingDeletionId }})" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 mr-2">
                                Yes, Delete
                            </button>
                            <button wire:click="cancel" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-100">
                                Cancel
                            </button>
                        </div>
                    </div>
                @endif
            @endif
        @endif
    </div>
</div>
