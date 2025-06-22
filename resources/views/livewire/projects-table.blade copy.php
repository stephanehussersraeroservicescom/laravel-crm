<div>
    <h1 class="font-bold text-2xl mb-4">Project List</h1>
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">Name</th>
                <th class="border px-2 py-1">Airline</th>
                <th class="border px-2 py-1">Aircraft Type</th>
                <th class="border px-2 py-1">Number of Aircraft</th>
                <th class="border px-2 py-1">Design Status</th>
                <th class="border px-2 py-1">Commercial Status</th>
                <th class="border px-2 py-1">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($projects as $project)
            <tr>
                <td class="border px-2 py-1">{{ $project->name }}</td>
                <td class="border px-2 py-1">{{ $project->airline->name ?? '' }}</td>
                <td class="border px-2 py-1">{{ $project->aircraftType->name ?? '' }}</td>
                <td class="border px-2 py-1">{{ $project->number_of_aircraft }}</td>
                <td class="border px-2 py-1">{{ $project->designStatus->status ?? '' }}</td>
                <td class="border px-2 py-1">{{ $project->commercialStatus->status ?? '' }}</td>
                <td class="border px-2 py-1">
                    <a href="#" class="text-blue-500">Show</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-2">
        {{ $projects->links() }}
    </div>
</div>
