<div class="max-w-6xl mx-auto px-0 py-6">
    <h1 class="text-2xl font-bold mb-6 text-center">User Management</h1>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full border-collapse text-sm">
            <thead>
            <tr class="bg-gray-200 text-gray-700">
                <th class="p-2 border text-center">ID</th>
                <th class="p-2 border text-left">Name</th>
                <th class="p-2 border text-left">Wallet</th>
                <th class="p-2 border text-left">Registered At</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr class="hover:bg-gray-100">
                    <td class="border p-2 text-center">{{ $user->id }}</td>
                    <td class="border p-2">{{ $user->name }}</td>
                    <td class="border p-2">{{ $user->wallet ?? '-' }}</td>
                    <td class="border p-2">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @include('components.custom-pagination', ['paginator' => $users])

</div>
