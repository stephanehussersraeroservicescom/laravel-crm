<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;

class ProjectsTable extends Component
{
    use WithPagination;

    public function render()
    {
        $projects = Project::with(['airline', 'aircraftType', 'designStatus', 'commercialStatus'])
            ->paginate(10);

        return view('livewire.projects-table', [
            'projects' => $projects,
        ]);
    }
}

// Note: Ensure you have the necessary models and relationships defined in your Project model.