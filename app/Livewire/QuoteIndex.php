<?php

namespace App\Livewire;

use App\Models\Quote;
use Livewire\Component;
use Livewire\WithPagination;

class QuoteIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'status'];

    public function updatingSearch()
    {
        $this->resetPage();
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

    public function deleteQuote($quoteId)
    {
        $quote = Quote::findOrFail($quoteId);
        $quote->delete();
        
        session()->flash('message', 'Quote deleted successfully.');
    }

    public function render()
    {
        $quotes = Quote::with(['customer', 'airline', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('company_name', 'like', '%' . $this->search . '%')
                      ->orWhere('contact_name', 'like', '%' . $this->search . '%');
                })->orWhere('quote_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.quote-index', compact('quotes'));
    }
}
