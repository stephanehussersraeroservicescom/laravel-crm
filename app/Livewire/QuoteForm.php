<?php

namespace App\Livewire;

use App\Models\Airline;
use App\Models\Quote;
use App\Models\Customer;
use App\Models\QuoteLine;
use Livewire\Component;

class QuoteForm extends Component
{
    public $airlines;
    public $contacts;
    
    // Quote fields
    public $company_name = '';
    public $contact_name = '';
    public $email = '';
    public $phone = '';
    public $airline_id = '';
    public $date_entry;
    public $date_valid;
    public $shipping_terms = 'Ex Works Dallas Texas';
    public $payment_terms = 'Pro Forma';
    public $comments = '';
    public $is_subcontractor = false;
    
    // Quote lines
    public $quote_lines = [];
    
    // Contact autocomplete
    public $contact_search = '';
    public $show_contact_dropdown = false;
    public $filtered_contacts = [];

    public function mount($airlines = null, $contacts = null)
    {
        $this->airlines = $airlines ?? collect();
        $this->contacts = $contacts ?? collect();
        $this->date_entry = now()->format('Y-m-d');
        $this->date_valid = now()->addDays(30)->format('Y-m-d');
        
        // Add initial quote line
        $this->addQuoteLine();
    }

    public function updatedContactSearch()
    {
        if (strlen($this->contact_search) > 1) {
            $this->filtered_contacts = $this->contacts->filter(function ($contact) {
                return stripos($contact->company_name, $this->contact_search) !== false ||
                       stripos($contact->contact_name, $this->contact_search) !== false;
            })->take(10);
            $this->show_contact_dropdown = true;
        } else {
            $this->show_contact_dropdown = false;
        }
    }

    public function selectContact($contactId)
    {
        $contact = $this->contacts->find($contactId);
        if ($contact) {
            $this->company_name = $contact->company_name;
            $this->contact_name = $contact->contact_name;
            $this->email = $contact->email ?? '';
            $this->phone = $contact->phone ?? '';
            $this->is_subcontractor = $contact->is_subcontractor;
            $this->contact_search = $contact->company_name . ' - ' . $contact->contact_name;
        }
        $this->show_contact_dropdown = false;
    }

    public function addQuoteLine()
    {
        $this->quote_lines[] = [
            'part_number' => '',
            'description' => '',
            'quantity' => 1,
            'unit' => 'LY',
            'standard_price' => 0,
            'final_price' => 0,
            'moq' => 1,
            'lead_time' => '',
            'notes' => ''
        ];
    }

    public function removeQuoteLine($index)
    {
        if (count($this->quote_lines) > 1) {
            unset($this->quote_lines[$index]);
            $this->quote_lines = array_values($this->quote_lines);
        }
    }

    public function save()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'date_entry' => 'required|date',
            'date_valid' => 'required|date|after:date_entry',
            'quote_lines.*.part_number' => 'required|string|max:255',
            'quote_lines.*.description' => 'required|string',
            'quote_lines.*.quantity' => 'required|integer|min:1',
            'quote_lines.*.unit' => 'required|in:LY,UNIT',
            'quote_lines.*.standard_price' => 'required|numeric|min:0',
            'quote_lines.*.final_price' => 'required|numeric|min:0',
            'quote_lines.*.moq' => 'required|integer|min:1',
        ]);

        try {
            // Create or find contact
            $contact = Customer::firstOrCreate(
                [
                    'company_name' => $this->company_name,
                    'contact_name' => $this->contact_name,
                ],
                [
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'is_subcontractor' => $this->is_subcontractor,
                ]
            );

            // Create quote
            $quote = Quote::create([
                'user_id' => auth()->id(),
                'customer_id' => $contact->id,
                'airline_id' => $this->airline_id ?: null,
                'date_entry' => $this->date_entry,
                'date_valid' => $this->date_valid,
                'shipping_terms' => $this->shipping_terms,
                'payment_terms' => $this->payment_terms,
                'comments' => $this->comments,
                'is_subcontractor' => $this->is_subcontractor,
            ]);

            // Create quote lines
            foreach ($this->quote_lines as $index => $line) {
                QuoteLine::create([
                    'quote_id' => $quote->id,
                    'part_number' => $line['part_number'],
                    'description' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit' => $line['unit'],
                    'standard_price' => round($line['final_price'] * 100), // Convert to cents
                    'final_price' => round($line['final_price'] * 100), // Convert to cents
                    'moq' => $line['moq'],
                    'lead_time' => $line['lead_time'],
                    'notes' => $line['notes'],
                    'sort_order' => $index,
                ]);
            }

            session()->flash('message', 'Quote created successfully.');
            return redirect()->route('quotes.show', $quote);

        } catch (\Exception $e) {
            session()->flash('error', 'Error creating quote: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.quote-form');
    }
}