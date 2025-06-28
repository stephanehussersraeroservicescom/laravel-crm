<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Contacts for {{ $subcontractor->name }}
        </h2>
    </x-slot>
    <div class="py-4">
        <table class="min-w-full border rounded shadow bg-white">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border">Name</th>
                    <th class="px-3 py-2 border">Email</th>
                    <th class="px-3 py-2 border">Role</th>
                    <th class="px-3 py-2 border">Phone</th>
                    <th class="px-3 py-2 border"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($contacts as $contact)
                <tr>
                    <td class="px-3 py-2 border">{{ $contact->name }}</td>
                    <td class="px-3 py-2 border">{{ $contact->email }}</td>
                    <td class="px-3 py-2 border">{{ $contact->role }}</td>
                    <td class="px-3 py-2 border">{{ $contact->phone }}</td>
                    <td class="px-3 py-2 border">
                        <button wire:click="edit({{ $contact->id }})" class="text-sm text-green-600 hover:underline">Edit</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
