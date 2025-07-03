<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Airline;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AirlineCreate extends Component
{
    public $name, $region, $account_executive;
    public $success = false;

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
            $this->account_executive = Auth::user()->name;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:airlines,name',
            'region' => 'required|in:' . implode(',', $this->availableRegions),
            'account_executive' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Airline name is required.',
            'name.unique' => 'An airline with this name already exists.',
            'name.max' => 'Airline name cannot be longer than 255 characters.',
            'region.required' => 'Please select a region.',
            'region.in' => 'Please select a valid region.',
            'account_executive.max' => 'Account executive name cannot be longer than 255 characters.',
        ]);

        Airline::create([
            'name' => $this->name,
            'region' => $this->region,
            'account_executive' => $this->account_executive,
        ]);

        $this->reset(['name', 'region', 'account_executive']);
        $this->success = true;
        
        // Re-select current user if they have sales role
        if (Auth::check() && Auth::user()->role === 'sales') {
            $this->account_executive = Auth::user()->name;
        }
    }

    public function render()
    {
        return view('livewire.airline-create', [
            'availableRegions' => $this->availableRegions,
            'salesUsers' => User::where('role', 'sales')->orderBy('name')->get()
        ])->layout('layouts.app');
    }
}

