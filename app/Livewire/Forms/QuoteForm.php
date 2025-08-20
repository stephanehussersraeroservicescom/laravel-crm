<?php

namespace App\Livewire\Forms;

use App\Models\Quote;
use App\Models\Airline;
use App\Models\Subcontractor;
use App\Models\ExternalCustomer;
use Livewire\Form;
use Livewire\Attributes\Validate;

class QuoteForm extends Form
{
    #[Validate('required|string|max:255')]
    public $company_name = '';

    #[Validate('required|string|max:255')]
    public $contact_name = '';

    #[Validate('required|email|max:255')]
    public $email = '';

    #[Validate('nullable|string|max:20')]
    public $phone = '';

    #[Validate('nullable|exists:airlines,id')]
    public $airline_id = '';

    #[Validate('required|date')]
    public $date_entry;

    #[Validate('required|date|after:date_entry')]
    public $date_valid;

    #[Validate('required|in:airline,subcontractor,external')]
    public $customer_type = null;

    #[Validate('nullable|integer')]
    public $customer_id = null;

    #[Validate('required|string|max:255')]
    public $customer_name = '';

    #[Validate('required|string|max:255')]
    public $shipping_terms = 'Ex Works Dallas Texas';

    #[Validate('required|string|max:255')]
    public $payment_terms = 'Pro Forma';

    #[Validate('nullable|string')]
    public $comments = '';

    public function setQuote(Quote $quote)
    {
        $this->company_name = $quote->company_name;
        $this->contact_name = $quote->contact_name;
        $this->email = $quote->email;
        $this->phone = $quote->phone;
        $this->airline_id = $quote->airline_id;
        $this->date_entry = $quote->date_entry->format('Y-m-d');
        $this->date_valid = $quote->date_valid->format('Y-m-d');
        $this->customer_type = $quote->customer_type;
        $this->customer_id = $quote->customer_id;
        $this->customer_name = $quote->customer_name;
        $this->shipping_terms = $quote->shipping_terms;
        $this->payment_terms = $quote->payment_terms;
        $this->comments = $quote->comments;
    }

    public function store()
    {
        $this->validate();
        
        $quote = Quote::create($this->getFormData());
        
        return $quote;
    }

    public function update(Quote $quote)
    {
        $this->validate();
        
        $quote->update($this->getFormData());
        
        return $quote;
    }

    public function setCustomer($customerType, $customerId, $customerData = [])
    {
        $this->customer_type = $customerType;
        $this->customer_id = $customerId;
        
        // Set customer name based on type
        switch ($customerType) {
            case 'airline':
                $airline = Airline::find($customerId);
                $this->customer_name = $airline ? $airline->name : '';
                break;
                
            case 'subcontractor':
                $subcontractor = Subcontractor::find($customerId);
                $this->customer_name = $subcontractor ? $subcontractor->name : '';
                break;
                
            case 'external':
                $external = ExternalCustomer::find($customerId);
                $this->customer_name = $external ? $external->name : '';
                break;
        }
        
        // Set contact information if provided
        if (!empty($customerData)) {
            $this->company_name = $customerData['company_name'] ?? $this->customer_name;
            $this->contact_name = $customerData['contact_name'] ?? '';
            $this->email = $customerData['email'] ?? '';
            $this->phone = $customerData['phone'] ?? '';
            $this->payment_terms = $customerData['payment_terms'] ?? 'Pro Forma';
        }
    }

    private function getFormData(): array
    {
        return [
            'company_name' => $this->company_name,
            'contact_name' => $this->contact_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'airline_id' => $this->airline_id ?: null,
            'date_entry' => $this->date_entry,
            'date_valid' => $this->date_valid,
            'customer_type' => $this->customer_type,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer_name,
            'shipping_terms' => $this->shipping_terms,
            'payment_terms' => $this->payment_terms,
            'comments' => $this->comments,
        ];
    }
}