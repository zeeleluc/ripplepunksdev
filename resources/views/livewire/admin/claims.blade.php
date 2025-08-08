<div class="max-w-6xl mx-auto px-0 py-6">
    <h1 class="text-2xl font-bold mb-6 text-center">Claim Management</h1>

    @if (session()->has('message'))
        <div class="bg-green-200 p-3 mb-4 rounded text-green-800 text-center">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-200 p-3 mb-4 rounded text-red-800 text-center">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="{{ $isEditing ? 'updateClaim' : 'createClaim' }}" class="space-y-4 bg-white p-6 border rounded shadow-sm">
        <div>
            <input wire:model="title" type="text" placeholder="Title" class="w-full border p-2 rounded">
            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <textarea wire:model="description" placeholder="Description" class="w-full border p-2 rounded"></textarea>
            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <input wire:model="prize" type="text" placeholder="Prize" class="w-full border p-2 rounded">
            @error('prize') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <input wire:model="total" type="number" placeholder="Total" class="w-full border p-2 rounded">
            @error('total') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <h3 class="font-semibold mb-3">Required Badges</h3>
            @error('selectedBadges')
            <span class="text-red-500 text-sm block mb-2">{{ $message }}</span>
            @enderror

            <div class="space-y-4">
                @foreach($badges as $tier => $tierBadges)
                    <div class="flex flex-col md:flex-row md:items-start md:space-x-6 border-b pb-3">
                        <div class="w-full md:w-1/5 mb-2 md:mb-0 shrink-0">
                            <span class="font-medium text-gray-700">{{ $tier }}+ Punks</span>
                        </div>
                        <div class="w-full md:flex-1 flex flex-wrap gap-3">
                            @foreach($tierBadges as $badge)
                                <label class="flex items-center space-x-2 border rounded px-3 py-2 cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" wire:model="selectedBadges" value="{{ $badge }}">
                                    <span>{{ $badge }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <label class="flex items-center gap-2">
            <input wire:model="is_open" type="checkbox"> Open claim
        </label>

        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 transition">
                {{ $isEditing ? 'Update Claim' : 'Create Claim' }}
            </button>

            @if($isEditing)
                <button type="button" wire:click="cancelEdit" class="bg-gray-400 text-white px-5 py-2 rounded hover:bg-gray-500 transition">
                    Cancel
                </button>
            @endif
        </div>
    </form>

    <hr class="my-8 border-gray-300">

    <div class="space-y-6">
        @foreach ($claims as $claim)
            <div class="bg-white rounded shadow border-l-4
                        {{ $claim->is_open ? 'border-green-500' : 'border-red-500' }}">
                <div class="flex items-center p-4 border-b justify-between">
                    <div class="flex items-center gap-2">
                        <button
                            wire:click="toggleClaim({{ $claim->id }})"
                            class="px-3 py-1 rounded transition
                                   {{ $claim->is_open ? 'bg-gray-300 hover:bg-gray-400 text-black' : 'text-white bg-green-500 hover:bg-green-600' }}">
                            {{ $claim->is_open ? 'Close' : 'Open' }}
                        </button>

                        <button
                            wire:click="editClaim({{ $claim->id }})"
                            class="px-3 py-1 rounded bg-blue-600 hover:bg-blue-700 text-white transition"
                            title="Edit Claim">
                            Edit
                        </button>
                    </div>

                    <h2 class="font-semibold text-lg ml-3 flex-1">{{ $claim->title }}</h2>
                </div>

                <div class="px-4 py-3 space-y-2 border-b">
                    <p class="text-gray-700"><strong>Description:</strong> {{ $claim->description }}</p>
                    <p><strong>Prize:</strong> <span class="text-primary-600 font-semibold">{{ $claim->prize }}</span></p>
                    <p><strong>Total:</strong> {{ $claim->total }}</p>

                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $claim->required_badges) as $badge)
                            <span class="inline-block bg-primary-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                {{ trim($badge) }}
                            </span>
                        @endforeach
                    </div>
                </div>

                @if ($claim->submissions->count())
                    <div class="bg-gray-50 rounded-b">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                            <tr class="bg-gray-200 text-gray-700">
                                <th class="p-2 border text-center">User Wallet</th>
                                <th class="p-2 border text-center">Claimed At</th>
                                <th class="p-2 border text-center">Distributed At</th>
                                <th class="p-2 border text-center">Prize Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($claim->submissions as $submission)
                                <tr class="hover:bg-gray-100">
                                    <td class="border p-2 text-center">
                                        {{ $submission->user->wallet ?? 'Unknown' }}
                                    </td>
                                    <td class="border p-2 text-center">{{ $submission->claimed_at }}</td>
                                    <td class="border p-2 text-center">{{ $submission->received_at ?? '-' }}</td>
                                    <td class="border p-2 text-center">
                                        <button
                                            wire:click="togglePrize({{ $submission->id }})"
                                            class="px-3 py-1 rounded text-sm transition
                                                       {{ $submission->received_at ? 'bg-gray-300 hover:bg-gray-400 text-black' : 'text-white bg-green-500 hover:bg-green-600' }}">
                                            {{ $submission->received_at ? 'Cancel' : 'Distributed' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-gray-500 italic text-sm">No submissions yet.</div>
                @endif
            </div>
        @endforeach
    </div>
</div>
