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
        if (Auth::check() && Auth::user()->role === 'sales') {
            $this->account_executive_id = Auth::user()->id;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'region' => 'required|in:' . implode(',', $this->availableRegions),
            'account_executive_id' => 'nullable|exists:users,id',
        ]);

        if ($this->editing && $this->editId) {
            $airline = Airline::find($this->editId);
            if ($airline) {
                $airline->update([
                    'name' => $this->name,
                    'region' => $this->region,
                    'account_executive_id' => $this->account_executive_id,
                ]);
            }
        } else {
            Airline::create([
                'name' => $this->name,
                'region' => $this->region,
                'account_executive_id' => $this->account_executive_id,
            ]);
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $airline = Airline::findOrFail($id);
        $this->name = $airline->name;
        $this->region = $airline->region;
        $this->account_executive_id = $airline->account_executive_id;
        $this->editId = $id;
        $this->editing = true;
    }

    public function cancelEdit()
    {
        $this->resetFields();
    }

    public function delete($id)
    {
        Airline::findOrFail($id)->delete();
        $this->resetFields();
    }

    public function toggleShowDeleted()
    {
        $this->showDeleted = !$this->showDeleted;
        $this->resetFields();
    }

    public function restore($id)
    {
        $airline = Airline::withTrashed()->findOrFail($id);
        $airline->restore();
        $this->resetFields();
    }

    public function forceDelete($id)
    {
        $airline = Airline::withTrashed()->findOrFail($id);
        $airline->forceDelete();
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->name = '';
        $this->region = '';
        $this->account_executive_id = '';
        $this->editing = false;
        $this->editId = null;
        
        // Re-select current user if they have sales role
        if (Auth::check() && Auth::user()->role === 'sales') {
            $this->account_executive_id = Auth::user()->id;
        }
    }

    public function render()
    {
        $airlinesQuery = $this->showDeleted ? Airline::withTrashed() : Airline::query();
        
        return view('livewire.airlines-table', [
            'airlines' => $airlinesQuery->with('accountExecutive')->orderBy('name')->get(),
            'availableRegions' => $this->availableRegions,
            'salesUsers' => User::where('role', 'sales')->orderBy('name')->get()
        ])->layout('layouts.app');
    }
}
