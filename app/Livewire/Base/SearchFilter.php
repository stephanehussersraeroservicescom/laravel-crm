<?php

namespace App\Livewire\Base;

use Livewire\Component;

class SearchFilter extends Component
{
    public $search = '';
    public $filters = [];
    public $availableFilters = [];
    public $showAdvanced = false;
    public $placeholder = 'Search...';

    public function mount($filters = [], $placeholder = 'Search...')
    {
        $this->availableFilters = $filters;
        $this->placeholder = $placeholder;
        $this->initializeFilters();
    }

    public function updatedSearch()
    {
        $this->dispatch('search-updated', $this->search);
    }

    public function updatedFilters()
    {
        $this->dispatch('filters-updated', $this->filters);
    }

    public function toggleAdvanced()
    {
        $this->showAdvanced = !$this->showAdvanced;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filters']);
        $this->initializeFilters();
        $this->dispatch('filters-cleared');
    }

    public function applyFilters()
    {
        $this->dispatch('filters-applied', [
            'search' => $this->search,
            'filters' => $this->filters,
        ]);
    }

    protected function initializeFilters()
    {
        foreach ($this->availableFilters as $filter) {
            if (!isset($this->filters[$filter['key']])) {
                $this->filters[$filter['key']] = $filter['default'] ?? null;
            }
        }
    }

    public function render()
    {
        return view('livewire.base.search-filter');
    }
}