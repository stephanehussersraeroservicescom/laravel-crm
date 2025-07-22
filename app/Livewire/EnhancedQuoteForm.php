<?php

namespace App\Livewire;

use App\Models\Airline;
use App\Models\Quote;
use App\Models\Customer;
use App\Models\QuoteLine;
use App\Models\ProductRoot;
// ProductSeriesMapping removed - series are now handled differently
use App\Models\StockedProduct;
use App\Models\ContractPrice;
use App\Services\SimplifiedImportService;
use App\Services\ProductParserService;
use Livewire\Component;

class EnhancedQuoteForm extends Component
{
    public $airlines;
    public $productRoots;
    
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
    
    // Product search for each line
    public $root_searches = [];
    public $show_root_dropdowns = [];
    public $filtered_roots = [];
    
    // Series selection for each line
    public $available_series = [];
    public $selected_series = [];

    protected $importService;
    protected $parserService;
    
    protected function getImportService()
    {
        if (!$this->importService) {
            $this->importService = new SimplifiedImportService();
        }
        return $this->importService;
    }
    
    protected function getParserService()
    {
        if (!$this->parserService) {
            $this->parserService = new ProductParserService();
        }
        return $this->parserService;
    }

    public function mount($airlines = null)
    {
        $this->airlines = $airlines ?? Airline::orderBy('name')->get();
        $this->productRoots = ProductRoot::orderBy('root_code')->get();
        $this->date_entry = now()->format('Y-m-d');
        $this->date_valid = now()->addDays(30)->format('Y-m-d');
        
        // Add initial quote line
        $this->addQuoteLine();
    }

    public function updatedContactSearch()
    {
        if (strlen($this->contact_search) > 1) {
            $this->filtered_contacts = Customer::search($this->contact_search)->take(10)->get();
            $this->show_contact_dropdown = true;
        } else {
            $this->show_contact_dropdown = false;
        }
    }

    public function selectContact($contactId)
    {
        $contact = Customer::find($contactId);
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

    public function updatedRootSearches($value, $index)
    {
        if (strlen($value) > 0) {
            $this->filtered_roots[$index] = $this->productRoots->filter(function ($root) use ($value) {
                return stripos($root->root_code, $value) !== false ||
                       stripos($root->root_name, $value) !== false;
            })->take(10);
            $this->show_root_dropdowns[$index] = true;
        } else {
            $this->show_root_dropdowns[$index] = false;
        }
    }

    public function selectRoot($index, $rootCode)
    {
        $root = ProductRoot::find($rootCode);
        if ($root) {
            // Auto-fill all default information
            $this->quote_lines[$index]['root_code'] = $root->root_code;
            $this->quote_lines[$index]['part_number'] = ''; // Don't pre-fill part number
            $this->quote_lines[$index]['description'] = $root->description; // This stays fixed
            $this->quote_lines[$index]['moq'] = $root->moq_ly;
            $this->quote_lines[$index]['lead_time'] = $root->lead_time_weeks;
            
            // Get pricing information directly from root without parsing
            $pricingResult = $this->getRootPricing($root);
            $this->quote_lines[$index]['standard_price'] = $pricingResult['price'];
            $this->quote_lines[$index]['final_price'] = $pricingResult['price'];
            $this->quote_lines[$index]['pricing_source'] = $pricingResult['source'];
            
            $this->root_searches[$index] = $root->root_code . ' - ' . $root->root_name;
            
            // Series are no longer stored separately - they're part of the part number
            // Clear any previous series selection
            $this->available_series[$index] = collect();
            $this->selected_series[$index] = null;
        }
        $this->show_root_dropdowns[$index] = false;
    }
    
    private function getRootPricing(ProductRoot $root, $partNumber = null)
    {
        // First, check for contract pricing
        $customerIdentifier = $this->company_name ?: $this->contact_name;
        if ($customerIdentifier || $this->airline_id) {
            $contractPrice = ContractPrice::findBestPrice(
                $customerIdentifier, 
                $root->root_code,
                $partNumber,
                $this->airline_id
            );
            if ($contractPrice) {
                return [
                    'price' => $contractPrice->contract_price / 100, // Convert from cents to dollars
                    'source' => 'contract'
                ];
            }
        }
        
        // If no contract price, use standard pricing from price lists
        $priceList = $root->activePriceList();
        if ($priceList) {
            return ['price' => $priceList->price_ly, 'source' => 'standard'];
        }
        
        return ['price' => 0, 'source' => 'manual'];
    }
    
    // Series selection is no longer needed - series are part of the part number
    // This method is kept for backward compatibility but does nothing
    public function selectSeries($index, $seriesCode)
    {
        // Series are now handled as part of the part number directly
        // No separate series selection is needed
    }

    public function updatedQuoteLines($value, $property)
    {
        // Extract index and field from property (e.g., "0.part_number")
        if (preg_match('/(\d+)\.part_number/', $property, $matches)) {
            $index = $matches[1];
            $partNumber = $value;
            
            // Check if this is a stocked product
            $stockedProduct = StockedProduct::where('full_part_number', $partNumber)->first();
            if ($stockedProduct) {
                // Update MOQ and lead time for stocked items - all stocked items have 5 LY MOQ
                $this->quote_lines[$index]['moq'] = 5;
                $this->quote_lines[$index]['lead_time'] = 'Stocked';
                $this->quote_lines[$index]['notes'] = 'Stocked item - MOQ: 5 LY';
            }
            
            // Check for contract pricing for specific part numbers
            $customerIdentifier = $this->company_name ?: $this->contact_name;
            if ($customerIdentifier || $this->airline_id) {
                // Use the enhanced contract pricing logic
                $rootCode = $this->quote_lines[$index]['root_code'] ?? null;
                $contractPrice = ContractPrice::findBestPrice(
                    $customerIdentifier,
                    $rootCode,
                    $partNumber,
                    $this->airline_id
                );
                    
                if ($contractPrice) {
                    $this->quote_lines[$index]['final_price'] = $contractPrice->contract_price / 100;
                    $this->quote_lines[$index]['pricing_source'] = 'contract';
                    $existingNotes = $this->quote_lines[$index]['notes'] ?? '';
                    $contractNote = 'Contract pricing applied (' . $contractPrice->contract_type . ')';
                    if ($existingNotes && !str_contains($existingNotes, 'Contract')) {
                        $this->quote_lines[$index]['notes'] = $existingNotes . ' - ' . $contractNote;
                    } elseif (!$existingNotes) {
                        $this->quote_lines[$index]['notes'] = $contractNote;
                    }
                }
            }
            
            // DO NOT update description when part number changes
            // Description should only come from root product or manual user edit
        }
    }

    public function addQuoteLine()
    {
        $this->quote_lines[] = [
            'root_code' => '',
            'part_number' => '',
            'description' => '',
            'quantity' => 1,
            'unit' => 'LY',
            'standard_price' => 0,
            'final_price' => 0,
            'pricing_source' => 'manual',
            'moq' => 1,
            'lead_time' => '',
            'notes' => ''
        ];
        
        $index = count($this->quote_lines) - 1;
        $this->root_searches[$index] = '';
        $this->show_root_dropdowns[$index] = false;
        $this->filtered_roots[$index] = collect();
        $this->available_series[$index] = collect();
        $this->selected_series[$index] = null;
    }

    public function removeQuoteLine($index)
    {
        if (count($this->quote_lines) > 1) {
            unset($this->quote_lines[$index]);
            unset($this->root_searches[$index]);
            unset($this->show_root_dropdowns[$index]);
            unset($this->filtered_roots[$index]);
            unset($this->available_series[$index]);
            unset($this->selected_series[$index]);
            
            // Re-index arrays
            $this->quote_lines = array_values($this->quote_lines);
            $this->root_searches = array_values($this->root_searches);
            $this->show_root_dropdowns = array_values($this->show_root_dropdowns);
            $this->filtered_roots = array_values($this->filtered_roots);
            $this->available_series = array_values($this->available_series);
            $this->selected_series = array_values($this->selected_series);
        }
    }

    // Update pricing when airline changes
    public function updatedAirlineId($value)
    {
        $this->refreshAllPricing();
    }

    // Update pricing when customer details change
    public function updatedCompanyName($value)
    {
        $this->refreshAllPricing();
    }

    public function updatedContactName($value)
    {
        $this->refreshAllPricing();
    }

    private function refreshAllPricing()
    {
        foreach ($this->quote_lines as $index => $line) {
            if (!empty($line['root_code'])) {
                $root = ProductRoot::find($line['root_code']);
                if ($root) {
                    $pricingResult = $this->getRootPricing($root, $line['part_number']);
                    $this->quote_lines[$index]['standard_price'] = $pricingResult['price'];
                    $this->quote_lines[$index]['final_price'] = $pricingResult['price'];
                    $this->quote_lines[$index]['pricing_source'] = $pricingResult['source'];
                    
                    // Update notes if contract pricing was applied
                    if ($pricingResult['source'] === 'contract') {
                        $existingNotes = $line['notes'] ?? '';
                        $contractNote = 'Contract pricing applied';
                        if ($existingNotes && !str_contains($existingNotes, 'Contract')) {
                            $this->quote_lines[$index]['notes'] = $existingNotes . ' - ' . $contractNote;
                        } elseif (!$existingNotes) {
                            $this->quote_lines[$index]['notes'] = $contractNote;
                        }
                    }
                }
            }
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
            'quote_lines.*.final_price' => 'required|numeric|min:0',
            'quote_lines.*.moq' => 'required|integer|min:1',
        ]);

        try {
            // Create or find customer
            $customer = Customer::firstOrCreate(
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
                'customer_id' => $customer->id,
                'airline_id' => $this->airline_id ?: null,
                'date_entry' => $this->date_entry,
                'date_valid' => $this->date_valid,
                'shipping_terms' => $this->shipping_terms,
                'payment_terms' => $this->payment_terms,
                'lead_time_weeks' => $this->getOverallLeadTime(),
                'comments' => $this->comments,
                'is_subcontractor' => $this->is_subcontractor,
            ]);

            // Create quote lines
            foreach ($this->quote_lines as $index => $line) {
                $parsed = $this->getParserService()->parsePartNumber($line['part_number']);
                
                // Use the root_code from the form (which is the database root)
                // not the parsed root (which might be different)
                $databaseRootCode = $line['root_code'];
                
                QuoteLine::create([
                    'quote_id' => $quote->id,
                    'part_number' => $line['part_number'],
                    'root_code' => $databaseRootCode, // Use the selected root, not parsed
                    'series_code' => $parsed['series_code'],
                    'color_code' => $parsed['color_code'],
                    'treatment_suffix' => $parsed['treatment_suffix'],
                    'is_exotic' => $parsed['is_exotic'],
                    'base_part_number' => $parsed['base_part_number'],
                    'description' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit' => $line['unit'],
                    'standard_price' => round($line['standard_price'] * 100), // Convert to cents
                    'final_price' => round($line['final_price'] * 100), // Convert to cents
                    'pricing_source' => $line['pricing_source'],
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

    private function getOverallLeadTime()
    {
        $maxWeeks = 0;
        foreach ($this->quote_lines as $line) {
            if ($line['lead_time'] === 'In Stock') continue;
            
            if (preg_match('/(\d+)-(\d+)/', $line['lead_time'], $matches)) {
                $maxWeeks = max($maxWeeks, (int)$matches[2]);
            }
        }
        
        return $maxWeeks > 0 ? "Up to {$maxWeeks} weeks" : "In Stock";
    }

    public function render()
    {
        return view('livewire.enhanced-quote-form');
    }
}