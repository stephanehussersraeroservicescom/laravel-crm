<?php

namespace App\Livewire\Components;

use App\Livewire\DataTable;
use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Builder;

class OpportunityTable extends DataTable
{
    protected string $model = Opportunity::class;
    
    protected array $sortableColumns = ['name', 'status', 'probability', 'potential_value', 'created_at'];
    
    protected array $searchableColumns = ['name', 'description', 'comments'];

    public function query(): Builder
    {
        return Opportunity::query()
            ->with(['project.airline', 'assignedUser', 'certificationStatus'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    foreach ($this->searchableColumns as $column) {
                        $q->orWhere($column, 'like', '%' . $this->search . '%');
                    }
                });
            })
            ->when($this->sortColumn, function ($query) {
                if (in_array($this->sortColumn, $this->sortableColumns)) {
                    $query->orderBy($this->sortColumn, $this->sortDirection);
                }
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            });
    }

    public function getRowsProperty()
    {
        return $this->applyPagination($this->query());
    }

    public function render()
    {
        return view('livewire.components.opportunity-table');
    }
}