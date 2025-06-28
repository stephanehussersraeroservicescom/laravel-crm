<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subcontractors</h2>
    </x-slot>
    <div class="py-4">
        <table class="min-w-full border rounded shadow bg-white">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border">Name</th>
                    <th class="px-3 py-2 border">Parent</th>
                    <th class="px-3 py-2 border">Contacts</th>
                    <th class="px-3 py-2 border"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($subcontractors as $sub)
                <tr>
                    <td class="px-3 py-2 border">{{ $sub->name }}</td>
                    <td class="px-3 py-2 border">{{ $sub->parent?->name ?? '—' }}</td>
                    <td class="px-3 py-2 border">
                        <a href="{{ route('contacts.index', $sub) }}" class="text-blue-600 hover:underline">
                            {{ $sub->contacts->count() }} contact{{ $sub->contacts->count() === 1 ? '' : 's' }}
                        </a>
                    </td>
                    <td class="px-3 py-2 border">
                        <button wire:click="edit({{ $sub->id }})" class="text-sm text-green-600 hover:underline">Edit</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
