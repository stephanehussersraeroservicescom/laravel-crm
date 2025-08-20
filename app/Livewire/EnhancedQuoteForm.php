<?php

namespace App\Livewire;

use App\Models\Airline;
use App\Models\Quote;
use App\Models\Subcontractor;
use App\Models\ExternalCustomer;
use App\Models\Product;
use App\Models\QuoteLine;
use App\Models\ProductClass;
// ProductSeriesMapping removed - series are now handled differently
use App\Models\ContractPrice;
use App\Services\SimplifiedImportService;
use App\Services\ProductParserService;
use Livewire\Component;

class EnhancedQuoteForm extends Component
{
    public $airlines;
    public $subcontractors;
    public $externalCustomers;
    public $productRoots;
    
    // Quote fields
    public $company_name = '';
    public $contact_name = '';
    public $email = '';
    public $phone = '';
    public $airline_id = '';
    public $date_entry;
    public $date_valid;
    
    // Customer selection
    public $customer_type = null;
    public $customer_id = null;
    public $customer_name = '';
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
    
    // Product autocomplete for each line
    public $product_searches = [];
    public $show_product_dropdowns = [];
    public $filtered_products = [];
    
    // Dirty field tracking per line
    public $dirty_fields = [];
    
    // Selected entities per line
    public $selected_products = []; // product_id per line
    public $selected_product_classes = []; // product_class_id per line
    
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

    public function mount($airlines = null, $subcontractors = null, $externalCustomers = null)
    {
        $this->airlines = $airlines ?? Airline::orderBy('name')->get();
        $this->subcontractors = $subcontractors ?? Subcontractor::orderBy('name')->get();
        $this->externalCustomers = $externalCustomers ?? ExternalCustomer::orderBy('name')->get();
        $this->productRoots = ProductClass::orderBy('root_code')->get();
        $this->date_entry = now()->format('Y-m-d');
        $this->date_valid = now()->addMonth()->format('Y-m-d'); // Default to 1 month validity
        
        // Add initial quote line
        $this->addQuoteLine();
    }

    public function updatedContactSearch()
    {
        if (strlen($this->contact_search) > 1) {
            $contacts = [];
            
            // Search airlines
            $airlines = Airline::where('name', 'like', '%' . $this->contact_search . '%')
                ->take(5)->get();
            foreach ($airlines as $airline) {
                $contacts[] = [
                    'id' => $airline->id,
                    'display_type' => 'Airline',
                    'display_name' => $airline->name,
                    'model_type' => 'App\\Models\\Airline',
                    'contact_name' => $airline->contact_name ?? '',
                    'email' => $airline->email ?? '',
                    'phone' => $airline->phone ?? '',
                    'payment_terms' => $airline->payment_terms ?? 'Pro Forma'
                ];
            }
            
            // Search subcontractors
            $subcontractors = Subcontractor::where('name', 'like', '%' . $this->contact_search . '%')
                ->take(5)->get();
            foreach ($subcontractors as $subcontractor) {
                $contacts[] = [
                    'id' => $subcontractor->id,
                    'display_type' => 'Subcontractor',
                    'display_name' => $subcontractor->name,
                    'model_type' => 'App\\Models\\Subcontractor',
                    'contact_name' => $subcontractor->contact_name ?? '',
                    'email' => $subcontractor->email ?? '',
                    'phone' => $subcontractor->phone ?? '',
                    'payment_terms' => $subcontractor->payment_terms ?? 'Pro Forma'
                ];
            }
            
            // Search external customers
            $externals = ExternalCustomer::search($this->contact_search)->take(5)->get();
            foreach ($externals as $external) {
                $contacts[] = [
                    'id' => $external->id,
                    'display_type' => 'External',
                    'display_name' => $external->name,
                    'model_type' => 'App\\Models\\ExternalCustomer',
                    'contact_name' => $external->contact_name ?? '',
                    'email' => $external->email ?? '',
                    'phone' => $external->phone ?? '',
                    'payment_terms' => $external->payment_terms ?? 'Pro Forma'
                ];
            }
            
            $this->filtered_contacts = $contacts;
            $this->show_contact_dropdown = count($contacts) > 0;
        } else {
            $this->show_contact_dropdown = false;
            $this->filtered_contacts = [];
        }
    }

    public function selectContact($contactData)
    {
        $this->customer_type = $contactData['model_type'];
        $this->customer_id = $contactData['id'];
        $this->customer_name = $contactData['display_name'];
        $this->company_name = $contactData['display_name'];
        $this->contact_name = $contactData['contact_name'];
        $this->email = $contactData['email'];
        $this->phone = $contactData['phone'];
        $this->payment_terms = $contactData['payment_terms'];
        $this->contact_search = $contactData['display_name'];
        
        if ($contactData['model_type'] === 'App\\Models\\Subcontractor') {
            $this->is_subcontractor = true;
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

    public function selectRoot($index, $rootCode, $hasInkResist = false, $isBio = false)
    {
        $this->productClassSelected($index, $rootCode, $hasInkResist, $isBio);
        $this->show_root_dropdowns[$index] = false;
    }
    
    private function getRootPricing(ProductClass $root, $partNumber = null)
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

    // Product autocomplete search
    public function updatedProductSearches($value, $index)
    {
        if (strlen($value) > 1) {
            // Search existing products
            $this->filtered_products[$index] = Product::search($value)
                ->with('productClass')
                ->take(10)
                ->get();
            $this->show_product_dropdowns[$index] = true;
        } else {
            $this->show_product_dropdowns[$index] = false;
            $this->filtered_products[$index] = collect();
        }
    }

    // Event: Product selected from autocomplete
    public function selectProduct($index, $productId)
    {
        $this->productSelected($index, $productId);
        $this->show_product_dropdowns[$index] = false;
    }
    
    
    // Event handlers
    public function productSelected($index, $productId)
    {
        $this->selected_products[$index] = $productId;
        $this->recomputeLine($index);
    }
    
    public function productCleared($index)
    {
        $this->selected_products[$index] = null;
        $this->product_searches[$index] = '';
        $this->recomputeLine($index);
    }
    
    public function productClassSelected($index, $rootCode, $hasInkResist = false, $isBio = false)
    {
        // Store the specific variant identifier
        $variantId = $rootCode . ($hasInkResist ? '_ink' : '') . ($isBio ? '_bio' : '');
        $this->selected_product_classes[$index] = $variantId;
        
        // If product is selected, validate it belongs to this class
        if ($this->selected_products[$index]) {
            $product = Product::find($this->selected_products[$index]);
            if ($product && $product->root_code !== $rootCode) {
                // Product doesn't belong to this class, clear it
                $this->productCleared($index);
            }
        }
        
        $this->recomputeLine($index);
    }
    
    public function productClassCleared($index)
    {
        $this->selected_product_classes[$index] = null;
        $this->root_searches[$index] = '';
        $this->recomputeLine($index);
    }

    // Check MOQ and handle waiver
    public function checkMOQ($index)
    {
        if (!isset($this->quote_lines[$index])) {
            return;
        }
        
        $line = $this->quote_lines[$index];
        if (isset($line['quantity']) && isset($line['moq']) && $line['quantity'] < $line['moq'] && !($line['moq_waived'] ?? false)) {
            // This will trigger a confirmation dialog in the view
            $this->dispatch('confirm-moq-waiver', [
                'index' => $index,
                'moq' => $line['moq'],
                'quantity' => $line['quantity']
            ]);
        }
    }

    // Confirm MOQ waiver
    public function confirmMOQWaiver($index)
    {
        if (isset($this->quote_lines[$index])) {
            $this->quote_lines[$index]['moq_waived'] = true;
            $moq = $this->quote_lines[$index]['moq'] ?? '1';
            $unit = $this->quote_lines[$index]['unit'] ?? 'LY';
            $this->quote_lines[$index]['notes'] .= "\nMaterial provided below MOQ of " . $moq . " " . $unit;
        }
    }

    // Core recompute logic - single source of truth
    private function recomputeLine($index)
    {
        if (!isset($this->quote_lines[$index])) {
            return;
        }

        $productId = $this->selected_products[$index] ?? null;
        $productClassId = $this->selected_product_classes[$index] ?? null;
        
        $product = null;
        $productClass = null;
        
        // Load entities
        if ($productId) {
            $product = Product::with('productClass')->find($productId);
            if ($product) {
                $productClass = $product->productClass;
                // Ensure product class is also selected
                $this->selected_product_classes[$index] = $product->root_code;
            }
        } elseif ($productClassId) {
            // Parse variant identifier (e.g., "ULFRB9_ink_bio")
            $parts = explode('_', $productClassId);
            $rootCode = $parts[0];
            $hasInkResist = in_array('ink', $parts);
            $isBio = in_array('bio', $parts);
            
            $productClass = ProductClass::where('root_code', $rootCode)
                ->where('has_ink_resist', $hasInkResist)
                ->where('is_bio', $isBio)
                ->first();
        }
        
        // Apply merge logic with new precedence rules:
        // Product for: part_number, color_name, color_code
        // ProductClass for: description, price, moq, unit, lead_time
        $this->mergeField($index, 'part_number', $product?->part_number, null);
        $this->mergeField($index, 'color_name', $product?->color_name, null);
        $this->mergeField($index, 'color_code', $product?->color_code, null);
        
        // Use ProductClass for operational data (description, pricing, MOQ, etc.)
        $this->mergeField($index, 'description', null, $productClass?->description);
        $this->mergeField($index, 'moq', null, $productClass?->moq_ly);
        $this->mergeField($index, 'unit', null, $productClass?->uom);
        $this->mergeField($index, 'lead_time', null, $productClass?->lead_time_weeks);
        
        // Set quantity to MOQ if it's at the default value (for new quote creation)
        if ($productClass && !$this->isFieldDirty($index, 'quantity')) {
            $currentQty = $this->quote_lines[$index]['quantity'] ?? null;
            // Set to MOQ if quantity is empty or still at default value of 1
            if (empty($currentQty) || $currentQty == 1) {
                $this->quote_lines[$index]['quantity'] = $productClass->moq_ly;
            }
        }
        
        // For pricing, use ProductClass pricing logic with fallbacks
        if ($productClass && !$this->isFieldDirty($index, 'standard_price')) {
            $pricingResult = $this->getRootPricing($productClass, $product?->part_number);
            
            
            // If no price found from pricing logic, try ProductClass price or Product price as fallbacks
            if ($pricingResult['price'] == 0) {
                if ($productClass->price ?? 0 > 0) {
                    $pricingResult = ['price' => $productClass->price, 'source' => 'standard'];
                } elseif ($product && ($product->price ?? 0) > 0) {
                    $pricingResult = ['price' => $product->price, 'source' => 'standard'];
                }
            }
            
            $this->quote_lines[$index]['standard_price'] = $pricingResult['price'];
            if (!$this->isFieldDirty($index, 'final_price')) {
                $this->quote_lines[$index]['final_price'] = $pricingResult['price'];
                $this->quote_lines[$index]['pricing_source'] = $pricingResult['source'];
            }
        }
        
        
        // Update UI fields
        if ($productClass) {
            $this->quote_lines[$index]['root_code'] = $productClass->root_code;
            $this->root_searches[$index] = $productClass->root_code . ' - ' . $productClass->root_name;
        }
        
        if ($product) {
            $this->product_searches[$index] = $product->part_number . ' - ' . $product->color_name;
        }
        
        // Apply contract pricing if applicable
        $this->updateLinePricing($index);
    }
    
    // Merge field with precedence: productValue > classValue > currentValue (if not dirty)
    private function mergeField($index, $field, $productValue, $classValue)
    {
        if ($this->isFieldDirty($index, $field)) {
            return; // Don't overwrite user-edited fields
        }
        
        $newValue = $productValue ?? $classValue;
        
        if ($newValue !== null) {
            $this->quote_lines[$index][$field] = $newValue;
        }
    }
    
    // Check if field has been manually edited by user
    private function isFieldDirty($index, $field)
    {
        return isset($this->dirty_fields[$index][$field]) && $this->dirty_fields[$index][$field];
    }
    
    // Mark field as manually edited
    private function markFieldDirty($index, $field)
    {
        if (!isset($this->dirty_fields[$index])) {
            $this->dirty_fields[$index] = [];
        }
        $this->dirty_fields[$index][$field] = true;
    }
    
    // Auto-detect product class from part number prefix
    private function autoDetectProductClass($index, $partNumber)
    {
        // Try to find a product class that matches the beginning of the part number
        $productClasses = ProductClass::orderBy('root_code', 'desc')->get(); // Longer codes first
        
        foreach ($productClasses as $class) {
            $prefix = $class->part_number_prefix ?? $class->root_code;
            if (str_starts_with($partNumber, $prefix)) {
                $this->productClassSelected($index, $class->root_code);
                break; // Stop at first match
            }
        }
    }
    
    // Validate part number against product class
    private function validatePartNumberPrefix($index)
    {
        $line = $this->quote_lines[$index];
        if (!empty($line['root_code']) && !empty($line['part_number'])) {
            $productClass = ProductClass::where('root_code', $line['root_code'])->first();
            if ($productClass) {
                $prefix = $productClass->part_number_prefix ?? $productClass->root_code;
                if (!str_starts_with($line['part_number'], $prefix)) {
                    session()->flash('warning', "Part number should start with '{$prefix}' for product class {$productClass->root_code}");
                }
            }
        }
    }

    // Update line pricing based on contract prices
    private function updateLinePricing($index)
    {
        $line = $this->quote_lines[$index] ?? [];
        $customerIdentifier = $this->customer_name ?: $this->company_name;
        
        if (($customerIdentifier || $this->customer_id) && !empty($line['root_code'])) {
            $contractPrice = ContractPrice::findBestPrice(
                $customerIdentifier,
                $line['root_code'] ?? null,
                $line['part_number'] ?? null,
                $this->customer_type === 'App\\Models\\Airline' ? $this->customer_id : null
            );
            
            if ($contractPrice && !$this->isFieldDirty($index, 'final_price')) {
                $this->quote_lines[$index]['final_price'] = $contractPrice->contract_price / 100;
                $this->quote_lines[$index]['pricing_source'] = 'contract';
                
                // Add contract note if not already present
                $existingNotes = $this->quote_lines[$index]['notes'] ?? '';
                $contractNote = 'Contract pricing applied (' . $contractPrice->contract_type . ')';
                if ($existingNotes && !str_contains($existingNotes, 'Contract')) {
                    $this->quote_lines[$index]['notes'] = $existingNotes . ' - ' . $contractNote;
                } elseif (!$existingNotes) {
                    $this->quote_lines[$index]['notes'] = $contractNote;
                }
            }
        }
    }

    public function updatedQuoteLines($value, $property)
    {
        // Extract index and field from property (e.g., "0.part_number")
        if (preg_match('/(\d+)\.(\w+)/', $property, $matches)) {
            $index = (int)$matches[1];
            $field = $matches[2];
            
            // Mark field as dirty when user manually edits it
            $this->markFieldDirty($index, $field);
            
            // Handle part number changes
            if ($field === 'part_number') {
                $partNumber = $value;
                
                // Auto-detect from existing product if possible
                $existingProduct = Product::with('productClass')->where('part_number', $partNumber)->first();
                if ($existingProduct) {
                    // Use event system for product selection
                    $this->productSelected($index, $existingProduct->id);
                } else {
                    // Try to auto-detect product class from part number prefix
                    $this->autoDetectProductClass($index, $partNumber);
                }
                
                // Apply contract pricing
                $this->updateLinePricing($index);
            }
            
            // Handle quantity changes for MOQ checking - DISABLED
            // if ($field === 'quantity') {
            //     // Only check MOQ if quantity is not empty and is a valid number
            //     if (!empty($value) && is_numeric($value) && $value > 0) {
            //         $this->checkMOQ($index);
            //     }
            // }
        }
    }

    public function addQuoteLine()
    {
        $this->quote_lines[] = [
            'root_code' => '',
            'part_number' => '',
            'color_name' => '',
            'color_code' => '',
            'description' => '',
            'quantity' => 1,
            'unit' => 'LY',
            'standard_price' => 0,
            'final_price' => 0,
            'pricing_source' => 'manual',
            'moq' => 1,
            'lead_time' => '',
            'notes' => '',
            'moq_waived' => false
        ];
        
        $index = count($this->quote_lines) - 1;
        $this->root_searches[$index] = '';
        $this->show_root_dropdowns[$index] = false;
        $this->filtered_roots[$index] = collect();
        $this->available_series[$index] = collect();
        $this->selected_series[$index] = null;
        $this->product_searches[$index] = '';
        $this->show_product_dropdowns[$index] = false;
        $this->filtered_products[$index] = collect();
        
        // Initialize tracking arrays
        $this->dirty_fields[$index] = [];
        $this->selected_products[$index] = null;
        $this->selected_product_classes[$index] = null;
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
            unset($this->product_searches[$index]);
            unset($this->show_product_dropdowns[$index]);
            unset($this->filtered_products[$index]);
            unset($this->dirty_fields[$index]);
            unset($this->selected_products[$index]);
            unset($this->selected_product_classes[$index]);
            
            // Re-index arrays
            $this->quote_lines = array_values($this->quote_lines);
            $this->root_searches = array_values($this->root_searches);
            $this->show_root_dropdowns = array_values($this->show_root_dropdowns);
            $this->filtered_roots = array_values($this->filtered_roots);
            $this->available_series = array_values($this->available_series);
            $this->selected_series = array_values($this->selected_series);
            $this->product_searches = array_values($this->product_searches);
            $this->show_product_dropdowns = array_values($this->show_product_dropdowns);
            $this->filtered_products = array_values($this->filtered_products);
            $this->dirty_fields = array_values($this->dirty_fields);
            $this->selected_products = array_values($this->selected_products);
            $this->selected_product_classes = array_values($this->selected_product_classes);
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
                $root = ProductClass::find($line['root_code']);
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
            // Use the selected customer or create external customer if none selected
            if (!$this->customer_type || !$this->customer_id) {
                // Create external customer
                $externalCustomer = ExternalCustomer::firstOrCreate(
                    [
                        'name' => $this->company_name,
                        'contact_name' => $this->contact_name,
                    ],
                    [
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'payment_terms' => $this->payment_terms,
                    ]
                );
                $this->customer_type = 'App\\Models\\ExternalCustomer';
                $this->customer_id = $externalCustomer->id;
                $this->customer_name = $externalCustomer->name;
            }

            // Create quote
            $quote = Quote::create([
                'user_id' => auth()->id(),
                'customer_type' => $this->customer_type,
                'customer_id' => $this->customer_id,
                'customer_name' => $this->customer_name,
                'date_entry' => $this->date_entry,
                'date_valid' => $this->date_valid,
                'shipping_terms' => $this->shipping_terms,
                'payment_terms' => $this->payment_terms,
                'lead_time_weeks' => $this->getOverallLeadTime(),
                'comments' => $this->comments,
                'salesperson_code' => auth()->user()->salesperson_code ?? null,
            ]);

            // Create quote lines and auto-create products if they don't exist
            foreach ($this->quote_lines as $index => $line) {
                $parsed = $this->getParserService()->parsePartNumber($line['part_number']);
                
                // Use the root_code from the form (which is the database root)
                // not the parsed root (which might be different)
                $databaseRootCode = $line['root_code'];
                
                // Auto-create product if it doesn't exist
                if (!empty($line['part_number']) && !empty($databaseRootCode)) {
                    Product::firstOrCreate(
                        [
                            'part_number' => $line['part_number'],
                        ],
                        [
                            'root_code' => $databaseRootCode,
                            'color_name' => $line['color_name'] ?: 'Unknown',
                            'color_code' => $line['color_code'] ?: $parsed['color_code'],
                            'description' => $line['description'],
                            'price' => $line['standard_price'],
                            'moq' => $line['moq'],
                            'uom' => $line['unit'],
                            'lead_time_weeks' => $line['lead_time'],
                            'is_active' => true,
                        ]
                    );
                }
                
                QuoteLine::create([
                    'quote_id' => $quote->id,
                    'part_number' => $line['part_number'],
                    'root_code' => $databaseRootCode, // Use the selected root, not parsed
                    'series_code' => $parsed['series_code'],
                    'color_code' => $line['color_code'] ?: $parsed['color_code'],
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
                    'moq_waived' => $line['moq_waived'] ?? false,
                    'lead_time' => $line['lead_time'],
                    'notes' => $line['notes'],
                    'sort_order' => $index,
                ]);
            }

            session()->flash('message', 'Quote created successfully.');
            return $this->redirect(route('quotes.show', $quote));

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