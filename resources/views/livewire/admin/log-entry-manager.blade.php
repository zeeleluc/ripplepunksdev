<div class="max-w-6xl mx-auto px-0 py-6">
    <h1 class="text-2xl font-bold mb-6 text-center">Log Entries</h1>

    <div class="bg-white border rounded p-5">
        @if (session()->has('message'))
            <div class="mb-4 p-2 bg-green-200 text-green-800 rounded">
                {{ session('message') }}
            </div>
        @endif

        <!-- Create or Edit Form -->
        <form wire:submit.prevent="{{ $editingLogId ? 'updateLog' : 'createLog' }}">
            <textarea wire:model.defer="text" placeholder="Write log text" class="w-full p-2 border rounded" rows="3"></textarea>
            @error('text') <span class="text-red-600">{{ $message }}</span> @enderror

            <input type="url" wire:model.defer="link" placeholder="Optional link" class="w-full p-2 border rounded mt-2" />
            @error('link') <span class="text-red-600">{{ $message }}</span> @enderror

            <label class="inline-flex items-center mt-2">
                <input type="checkbox" wire:model.defer="is_published" class="form-checkbox" />
                <span class="ml-2">Published</span>
            </label>

            <div class="mt-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    {{ $editingLogId ? 'Update Log' : 'Create Log' }}
                </button>

                @if ($editingLogId)
                    <button type="button" wire:click="cancel" class="ml-2 px-4 py-2 rounded border border-gray-300 hover:bg-gray-100">Cancel</button>
                @endif
            </div>
        </form>

        <hr class="my-6">

        <!-- Log Entries List -->
        <ul>
            @foreach ($logs as $log)
                <li class="mb-4 border-b pb-2">
                    <p><strong>Text:</strong> {{ $log->text }}</p>
                    @if ($log->link)
                        <p><strong>Link:</strong> <a href="{{ $log->link }}" target="_blank" class="text-blue-600 underline">{{ $log->link }}</a></p>
                    @endif
                    <p><strong>Published:</strong> {{ $log->is_published ? 'Yes' : 'No' }}</p>
                    <p><small>Created: {{ $log->created_at->format('Y-m-d H:i') }}</small></p>

                    <button wire:click="editLog({{ $log->id }})" class="mt-2 mr-2 px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">Edit</button>
                    <button wire:click="confirmDelete({{ $log->id }})" class="mt-2 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                </li>
            @endforeach
        </ul>

        <!-- Confirmation Modal -->
        @if ($confirmingDeletionId)
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white p-6 rounded shadow-lg max-w-sm w-full">
                    <p class="mb-4">Are you sure you want to delete this log entry?</p>
                    <button wire:click="deleteLog" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 mr-2">Yes, Delete</button>
                    <button wire:click="cancel" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-100">Cancel</button>
                </div>
            </div>
        @endif
    </div>

</div>
