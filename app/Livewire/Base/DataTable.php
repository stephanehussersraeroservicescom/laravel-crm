<?php

namespace App\Livewire\Base;

use Livewire\Component;
use Livewire\WithPagination;

abstract class DataTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $selectedItems = [];
    public $selectAll = false;
    public $showFilters = false;
    public $bulkActions = ['delete'];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->getQuery()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'sortField', 'sortDirection', 'selectedItems', 'selectAll']);
        $this->resetPage();
    }

    public function bulkDelete()
    {
        if (empty($this->selectedItems)) {
            return;
        }

        $this->authorize('delete', $this->getModelClass());

        $this->getModelClass()::whereIn('id', $this->selectedItems)->delete();

        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('bulk-deleted', count($this->selectedItems));
    }

    // Abstract methods that must be implemented by child classes
    abstract protected function getQuery();
    abstract protected function getModelClass();
    abstract protected function getColumns();
    abstract protected function getSearchableColumns();

    protected function getTableData()
    {
        return $this->getQuery()
            ->when($this->search, function ($query) {
                $searchableColumns = $this->getSearchableColumns();
                $query->where(function ($q) use ($searchableColumns) {
                    foreach ($searchableColumns as $column) {
                        $q->orWhere($column, 'like', '%' . $this->search . '%');
                    }
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    protected function getSortIcon($field)
    {
        if ($this->sortField !== $field) {
            return 'heroicon-m-chevron-up-down';
        }

        return $this->sortDirection === 'asc' 
            ? 'heroicon-m-chevron-up' 
            : 'heroicon-m-chevron-down';
    }

    public function render()
    {
        return view('livewire.base.data-table', [
            'items' => $this->getTableData(),
            'columns' => $this->getColumns(),
        ]);
    }
}