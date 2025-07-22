<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Customer;
use App\Models\Airline;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        return view('quotes.index');
    }

    public function create()
    {
        $airlines = Airline::orderBy('name')->get();
        $contacts = Customer::orderBy('company_name')->orderBy('contact_name')->get();
        
        return view('quotes.create', compact('airlines', 'contacts'));
    }

    public function show(Quote $quote)
    {
        $quote->load(['quoteLines', 'customer', 'airline', 'user']);
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $airlines = Airline::orderBy('name')->get();
        $contacts = Customer::orderBy('company_name')->orderBy('contact_name')->get();
        $quote->load('quoteLines');
        
        return view('quotes.edit', compact('quote', 'airlines', 'contacts'));
    }

    public function preview(Quote $quote)
    {
        $quote->load(['quoteLines', 'customer', 'airline', 'user']);
        return view('quotes.pdf', compact('quote'));
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Quote deleted successfully.');
    }
}