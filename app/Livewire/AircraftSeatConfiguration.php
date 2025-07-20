<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AircraftSeatConfiguration as SeatConfig;
use App\Models\Airline;
use App\Models\AircraftType;
use App\Enums\CabinClass;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\Validate;
use App\Services\CachedDataService;

class AircraftSeatConfiguration extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public $filterAirline = '';
    public $filterAircraftType = '';
    public $filterVersion = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal Properties
    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $selectedConfiguration = null;

    // Form Properties
    #[Validate('required|exists:airlines,id')]
    public $airline_id = '';
    
    #[Validate('required|exists:aircraft_types,id')]
    public $aircraft_type_id = '';
    
    #[Validate('required|string|max:50')]
    public $version = 'Standard';
    
    #[Validate('required|integer|min:0')]
    public $first_class_seats = 0;
    
    #[Validate('required|integer|min:0')]
    public $business_class_seats = 0;
    
    #[Validate('required|integer|min:0')]
    public $premium_economy_seats = 0;
    
    #[Validate('required|integer|min:0')]
    public $economy_seats = 0;
    
    #[Validate('nullable|string|max:100')]
    public $data_source = 'manual';
    
    #[Validate('required|numeric|min:0|max:1')]
    public $confidence_score = 1.0;
    

    public function mount()
    {
        // Initialize with any default values if needed
    }

    public function render()
    {
        $configurations = $this->getConfigurations();
        $airlines = CachedDataService::getAirlines();
        $aircraftTypes = CachedDataService::getAircraftTypes();
        $versions = $this->getAvailableVersions();

        return view('livewire.aircraft-seat-configuration', [
            'configurations' => $configurations,
            'airlines' => $airlines,
            'aircraftTypes' => $aircraftTypes,
            'versions' => $versions,
        ])->layout('layouts.app');
    }

    public function getConfigurations()
    {
        $query = SeatConfig::with(['airline', 'aircraftType', 'updatedBy']);

        // Filters
        if ($this->filterAirline) {
            $query->where('airline_id', $this->filterAirline);
        }

        if ($this->filterAircraftType) {
            $query->where('aircraft_type_id', $this->filterAircraftType);
        }

        if ($this->filterVersion) {
            $query->where('version', $this->filterVersion);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function getAvailableVersions()
    {
        return SeatConfig::select('version')
            ->distinct()
            ->orderBy('version')
            ->pluck('version');
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->filterAirline = '';
        $this->filterAircraftType = '';
        $this->filterVersion = '';
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEditModal($configurationId)
    {
        $this->selectedConfiguration = SeatConfig::findOrFail($configurationId);
        $this->fillForm($this->selectedConfiguration);
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->selectedConfiguration = null;
    }

    public function save()
    {
        $this->validate();

        try {
            $totalSeats = $this->first_class_seats + $this->business_class_seats + 
                         $this->premium_economy_seats + $this->economy_seats;
            
            $data = [
                'airline_id' => $this->airline_id,
                'aircraft_type_id' => $this->aircraft_type_id,
                'version' => $this->version,
                'first_class_seats' => $this->first_class_seats,
                'business_class_seats' => $this->business_class_seats,
                'premium_economy_seats' => $this->premium_economy_seats,
                'economy_seats' => $this->economy_seats,
                'total_seats' => $totalSeats,
                'data_source' => $this->data_source,
                'confidence_score' => $this->confidence_score,
                'last_verified_at' => now(),
                'updated_by' => auth()->id(),
            ];

            if ($this->modalMode === 'create') {
                SeatConfig::create($data);
                session()->flash('message', 'Seat configuration created successfully.');
            } else {
                $this->selectedConfiguration->update($data);
                session()->flash('message', 'Seat configuration updated successfully.');
            }
            
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving seat configuration: ' . $e->getMessage());
        }
    }

    public function delete($configurationId)
    {
        try {
            $configuration = SeatConfig::findOrFail($configurationId);
            $configuration->delete();
            session()->flash('message', 'Seat configuration deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting seat configuration: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->airline_id = '';
        $this->aircraft_type_id = '';
        $this->version = 'Standard';
        $this->first_class_seats = 0;
        $this->business_class_seats = 0;
        $this->premium_economy_seats = 0;
        $this->economy_seats = 0;
        $this->data_source = 'manual';
        $this->confidence_score = 1.0;
    }

    private function fillForm($configuration)
    {
        $this->airline_id = $configuration->airline_id;
        $this->aircraft_type_id = $configuration->aircraft_type_id;
        $this->version = $configuration->version;
        $this->first_class_seats = $configuration->first_class_seats;
        $this->business_class_seats = $configuration->business_class_seats;
        $this->premium_economy_seats = $configuration->premium_economy_seats;
        $this->economy_seats = $configuration->economy_seats;
        $this->data_source = $configuration->data_source;
        $this->confidence_score = $configuration->confidence_score;
    }


    public function performAiLookup($configurationId)
    {
        try {
            $configuration = SeatConfig::with(['airline', 'aircraftType'])->findOrFail($configurationId);
            
            $airline = $configuration->airline;
            $aircraft = $configuration->aircraftType;

            // Run the AI population command
            // Use partial names that will match with LIKE queries
            $airlineName = strtolower(explode(' ', $airline->name)[0]); // First word only
            $aircraftName = strtolower($aircraft->name); // Keep original format

            // Capture output to show any errors
            $exitCode = Artisan::call('aircraft:populate-seats', [
                '--airline' => $airlineName,
                '--aircraft' => $aircraftName,
            ]);
            
            $output = Artisan::output();

            if ($exitCode === 0) {
                // Refresh the specific configuration from database
                $configuration->refresh();
                session()->flash('message', "AI successfully updated seat configurations for {$airline->name} {$aircraft->name}!");
            } else {
                // Show the actual error message from the command
                $errorMessage = "Failed to populate seat configurations.";
                if (strpos($output, 'not found') !== false) {
                    $errorMessage .= " The airline or aircraft was not found. Try using partial names.";
                }
                session()->flash('error', $errorMessage);
            }
        } catch (\Exception $e) {
            session()->flash('error', "Error: " . $e->getMessage());
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterAirline()
    {
        $this->resetPage();
    }

    public function updatedFilterAircraftType()
    {
        $this->resetPage();
    }

    public function updatedFilterCabinClass()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}