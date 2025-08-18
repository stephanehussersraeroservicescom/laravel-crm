<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Airline;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\CachedDataService;
#[Title('Airlines')]

class AirlinesTable extends Component
{
    use WithPagination;
    // Modal Properties
    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $selectedAirline = null;
    
    // Form Properties
    public $name = '';
    public $region = '';
    public $account_executive_id = '';
    
    // Search and Filter Properties
    public $search = '';
    public $filterRegion = '';
    public $filterAccountExecutive = '';
    public $showDeleted = false;
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public $availableRegions = [
        'North America',
        'South America', 
        'Europe',
        'Asia',
        'Africa',
        'Oceania',
        'Middle East'
    ];

    public function mount()
    {
        // Initialize default values if needed
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
        
        // Pre-select current user if they have sales role
        if (Auth::check() && Auth::user()->role === 'sales') {
            $this->account_executive_id = Auth::user()->id;
        }
    }
    
    public function openEditModal($airlineId)
    {
        $this->selectedAirline = Airline::withTrashed()->findOrFail($airlineId);
        $this->fillForm($this->selectedAirline);
        $this->modalMode = 'edit';
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->selectedAirline = null;
    }
    
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'region' => 'required|in:' . implode(',', $this->availableRegions),
            'account_executive_id' => 'nullable|exists:users,id',
        ]);

        if ($this->modalMode === 'create') {
            Airline::create($this->getFormData());
            session()->flash('message', 'Airline created successfully.');
        } else {
            $this->selectedAirline->update($this->getFormData());
            session()->flash('message', 'Airline updated successfully.');
        }

        $this->closeModal();
    }

    private function fillForm($airline)
    {
        $this->name = $airline->name;
        $this->region = $airline->region;
        $this->account_executive_id = $airline->account_executive_id;
    }
    
    private function getFormData()
    {
        return [
            'name' => $this->name,
            'region' => $this->region,
            'account_executive_id' => $this->account_executive_id ?: null,
        ];
    }

    public function delete($airlineId)
    {
        $airline = Airline::withTrashed()->findOrFail($airlineId);
        if ($airline->trashed()) {
            $airline->restore();
            session()->flash('message', 'Airline restored successfully.');
        } else {
            $airline->delete();
            session()->flash('message', 'Airline deleted successfully.');
        }
    }
    
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterRegion = '';
        $this->filterAccountExecutive = '';
        $this->showDeleted = false;
    }


    private function resetForm()
    {
        $this->name = '';
        $this->region = '';
        $this->account_executive_id = '';
    }
    
    public function updatedSearch()
    {
        // Reset to first page when searching
    }
    
    public function updatedFilterRegion()
    {
        // Reset to first page when filtering
    }
    
    public function updatedFilterAccountExecutive()
    {
        // Reset to first page when filtering
    }
    
    public function updatedShowDeleted()
    {
        // Reset to first page when toggling deleted
    }

    public function render()
    {
        $airlines = $this->getAirlines();
        
        return view('livewire.airlines-table', [
            'airlines' => $airlines,
            'availableRegions' => $this->availableRegions,
            'salesUsers' => CachedDataService::getSalesUsers()
        ]);
    }
    
    private function getAirlines()
    {
        $query = $this->showDeleted ? Airline::withTrashed() : Airline::query();
        
        // Apply search filter
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        
        // Apply region filter
        if ($this->filterRegion) {
            $query->where('region', $this->filterRegion);
        }
        
        // Apply account executive filter
        if ($this->filterAccountExecutive) {
            $query->where('account_executive_id', $this->filterAccountExecutive);
        }
        
        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);
        
        return $query->with('accountExecutive')->paginate($this->perPage);
    }
}
