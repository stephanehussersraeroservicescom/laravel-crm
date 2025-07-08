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

class AircraftSeatConfiguration extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public $search = '';
    public $filterAirline = '';
    public $filterAircraftType = '';
    public $filterCabinClass = '';
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
    
    #[Validate('required|string')]
    public $cabin_class = '';
    
    #[Validate('required|integer|min:0')]
    public $total_seats = 0;
    
    #[Validate('nullable|string|max:100')]
    public $data_source = 'manual';
    
    #[Validate('required|numeric|min:0|max:1')]
    public $confidence_score = 1.0;
    
    // AI Lookup Properties
    public $showAiLookup = false;
    public $aiAirlineId = '';
    public $aiAircraftTypeId = '';
    public $aiLookupInProgress = false;
    public $aiLookupResult = '';

    public function mount()
    {
        // Initialize with any default values if needed
    }

    public function render()
    {
        $configurations = $this->getConfigurations();
        $airlines = Airline::orderBy('name')->get();
        $aircraftTypes = AircraftType::orderBy('name')->get();

        return view('livewire.aircraft-seat-configuration', [
            'configurations' => $configurations,
            'airlines' => $airlines,
            'aircraftTypes' => $aircraftTypes,
            'cabinClasses' => CabinClass::cases(),
        ])->layout('layouts.app');
    }

    public function getConfigurations()
    {
        $query = SeatConfig::with(['airline', 'aircraftType']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('airline', function ($aq) {
                    $aq->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('aircraftType', function ($atq) {
                    $atq->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('cabin_class', 'like', '%' . $this->search . '%');
            });
        }

        // Filters
        if ($this->filterAirline) {
            $query->where('airline_id', $this->filterAirline);
        }

        if ($this->filterAircraftType) {
            $query->where('aircraft_type_id', $this->filterAircraftType);
        }

        if ($this->filterCabinClass) {
            $query->where('cabin_class', $this->filterCabinClass);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
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
        $this->search = '';
        $this->filterAirline = '';
        $this->filterAircraftType = '';
        $this->filterCabinClass = '';
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

        // Additional enum validation
        $cabinValues = array_column(CabinClass::cases(), 'value');
        
        if (!in_array($this->cabin_class, $cabinValues)) {
            $this->addError('cabin_class', 'Invalid cabin class.');
            return;
        }

        try {
            $data = [
                'airline_id' => $this->airline_id,
                'aircraft_type_id' => $this->aircraft_type_id,
                'cabin_class' => $this->cabin_class,
                'total_seats' => $this->total_seats,
                'data_source' => $this->data_source,
                'confidence_score' => $this->confidence_score,
                'last_verified_at' => now(),
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
        $this->cabin_class = '';
        $this->total_seats = 0;
        $this->data_source = 'manual';
        $this->confidence_score = 1.0;
    }

    private function fillForm($configuration)
    {
        $this->airline_id = $configuration->airline_id;
        $this->aircraft_type_id = $configuration->aircraft_type_id;
        $this->cabin_class = $configuration->cabin_class;
        $this->total_seats = $configuration->total_seats;
        $this->data_source = $configuration->data_source;
        $this->confidence_score = $configuration->confidence_score;
    }

    // AI Lookup Methods
    public function openAiLookup()
    {
        $this->showAiLookup = true;
        $this->aiAirlineId = '';
        $this->aiAircraftTypeId = '';
        $this->aiLookupResult = '';
    }

    public function closeAiLookup()
    {
        $this->showAiLookup = false;
        $this->aiAirlineId = '';
        $this->aiAircraftTypeId = '';
        $this->aiLookupResult = '';
        $this->aiLookupInProgress = false;
    }

    public function performAiLookup()
    {
        if (!$this->aiAirlineId || !$this->aiAircraftTypeId) {
            $this->aiLookupResult = 'Please select both airline and aircraft type.';
            return;
        }

        $this->aiLookupInProgress = true;
        $this->aiLookupResult = 'Looking up seat configurations...';

        try {
            $airline = Airline::find($this->aiAirlineId);
            $aircraft = AircraftType::find($this->aiAircraftTypeId);

            if (!$airline || !$aircraft) {
                $this->aiLookupResult = 'Invalid airline or aircraft selection.';
                $this->aiLookupInProgress = false;
                return;
            }

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
                $this->aiLookupResult = "Successfully populated seat configurations for {$airline->name} {$aircraft->name}!";
                $this->dispatch('$refresh'); // Refresh the component to show new data
                
                // Close AI lookup modal after success
                $this->closeAiLookup();
                session()->flash('message', "AI successfully populated seat configurations for {$airline->name} {$aircraft->name}");
            } else {
                // Show the actual error message from the command
                $errorMessage = "Failed to populate seat configurations.";
                if (strpos($output, 'not found') !== false) {
                    $errorMessage .= " The airline or aircraft was not found. Try using partial names.";
                }
                $this->aiLookupResult = $errorMessage;
            }
        } catch (\Exception $e) {
            $this->aiLookupResult = "Error: " . $e->getMessage();
        }

        $this->aiLookupInProgress = false;
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