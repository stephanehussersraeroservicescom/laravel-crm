<?php

namespace App\Livewire\Traits;

trait HasValueCalculations
{
    public $price_per_linear_yard = '';
    public $linear_yards_per_seat = '';
    public $seats_in_opportunity = '';
    public $potential_value = 0;

    public function calculatePotentialValue(): float
    {
        $price = (float) $this->price_per_linear_yard;
        $yardsPerSeat = (float) $this->linear_yards_per_seat;
        $seats = (float) $this->seats_in_opportunity;

        if ($price > 0 && $yardsPerSeat > 0 && $seats > 0) {
            return round($price * $yardsPerSeat * $seats, 2);
        }

        return 0.0;
    }

    public function calculatePerAircraftValue(): float
    {
        $potentialValue = $this->calculatePotentialValue();
        $seats = (float) $this->seats_in_opportunity;

        if ($potentialValue > 0 && $seats > 0) {
            return round($potentialValue / $seats, 2);
        }

        return 0.0;
    }

    public function updatedPricePerLinearYard()
    {
        $this->updatePotentialValue();
    }

    public function updatedLinearYardsPerSeat()
    {
        $this->updatePotentialValue();
    }

    public function updatedSeatsInOpportunity()
    {
        $this->updatePotentialValue();
    }

    protected function updatePotentialValue()
    {
        $this->potential_value = $this->calculatePotentialValue();
    }

    protected function resetValueCalculations()
    {
        $this->price_per_linear_yard = '';
        $this->linear_yards_per_seat = '';
        $this->seats_in_opportunity = '';
        $this->potential_value = 0;
    }
}