<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Airline;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
#[Title('Airlines')]

class AirlinesTable extends Component
{
    public $name = '';
    public $region = '';
    public $account_executive_id = '';
    public $editing = false;
    public $editId = null;
    public $showDeleted = false; // Add option to show deleted records
    public $filterRegion = '';
    public $filterAccountExecutive = '';

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
        // Pre-select current user if they have sales role
        try {
            if (Auth::check() && Auth::user() && Auth::user()->role === 'sales') {
                $this->account_executive_id = Auth::user()->id;
            }
        } catch (\Exception $e) {
            // If there's an issue with auth, just continue without setting default
        }
    }

    public function save()
    {
        try {
            $this->validate([
                'name' => 'required|string|max:255',
                'region' => 'required|in:' . implode(',', $this->availableRegions),
                'account_executive_id' => 'nullable|exists:users,id',
            ]);

            if ($this->editing && $this->editId) {
                $airline = Airline::withTrashed()->find($this->editId);
                if ($airline) {
                    $airline->update([
                        'name' => $this->name,
                        'region' => $this->region,
                        'account_executive_id' => $this->account_executive_id ?: null,
                    ]);
                    session()->flash('message', 'Airline updated successfully.');
                }
            } else {
                Airline::create([
                    'name' => $this->name,
                    'region' => $this->region,
                    'account_executive_id' => $this->account_executive_id ?: null,
                ]);
                session()->flash('message', 'Airline created successfully.');
            }

            $this->name = '';
            $this->region = '';
            $this->account_executive_id = '';
            $this->editing = false;
            $this->editId = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving airline: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $airline = Airline::withTrashed()->findOrFail($id);
            $this->name = $airline->name;
            $this->region = $airline->region;
            $this->account_executive_id = $airline->account_executive_id ?: '';
            $this->editId = $id;
            $this->editing = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading airline for edit: ' . $e->getMessage());
        }
    }

    public function openAddForm()
    {
        $this->name = '';
        $this->region = '';
        $this->account_executive_id = '';
        $this->editId = null;
        $this->editing = true;
    }

    public function cancelEdit()
    {
        $this->name = '';
        $this->region = '';
        $this->account_executive_id = '';
        $this->editing = false;
        $this->editId = null;
    }

    public function delete($id)
    {
        try {
            Airline::findOrFail($id)->delete();
            session()->flash('message', 'Airline deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting airline: ' . $e->getMessage());
        }
    }

    public function toggleShowDeleted()
    {
        $this->showDeleted = !$this->showDeleted;
    }

    public function restore($id)
    {
        try {
            $airline = Airline::withTrashed()->findOrFail($id);
            $airline->restore();
            session()->flash('message', 'Airline restored successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error restoring airline: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $airline = Airline::withTrashed()->findOrFail($id);
            $airline->forceDelete();
            session()->flash('message', 'Airline permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error permanently deleting airline: ' . $e->getMessage());
        }
    }

    private function resetFields()
    {
        $this->name = '';
        $this->region = '';
        $this->account_executive_id = '';
        $this->editing = false;
        $this->editId = null;
        
        // Re-select current user if they have sales role
        try {
            if (Auth::check() && Auth::user() && Auth::user()->role === 'sales') {
                $this->account_executive_id = Auth::user()->id;
            }
        } catch (\Exception $e) {
            // If there's an issue with auth, just continue without setting default
        }
    }

    public function clearFilters()
    {
        $this->filterRegion = '';
        $this->filterAccountExecutive = '';
        $this->showDeleted = false;
    }

    public function render()
    {
        $airlinesQuery = $this->showDeleted ? Airline::withTrashed() : Airline::query();
        
        // Apply region filter
        if ($this->filterRegion) {
            $airlinesQuery->where('region', $this->filterRegion);
        }
        
        // Apply account executive filter
        if ($this->filterAccountExecutive) {
            $airlinesQuery->where('account_executive_id', $this->filterAccountExecutive);
        }
        
        return view('livewire.airlines-table', [
            'airlines' => $airlinesQuery->with('accountExecutive')->orderBy('name')->get(),
            'availableRegions' => $this->availableRegions,
            'salesUsers' => User::where('role', 'sales')->orderBy('name')->get()
        ])->layout('layouts.app');
    }
}
