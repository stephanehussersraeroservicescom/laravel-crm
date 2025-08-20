<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Airline;
use App\Models\Subcontractor;
use App\Models\ExternalCustomer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    public function index()
    {
        return view('quotes.index');
    }

    public function create()
    {
        $airlines = Airline::orderBy('name')->get();
        $subcontractors = Subcontractor::orderBy('name')->get();
        $externalCustomers = ExternalCustomer::orderBy('name')->get();
        
        return view('quotes.create', compact('airlines', 'subcontractors', 'externalCustomers'));
    }

    public function show(Quote $quote)
    {
        $quote->load(['quoteLines', 'user']);
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $airlines = Airline::orderBy('name')->get();
        $subcontractors = Subcontractor::orderBy('name')->get();
        $externalCustomers = ExternalCustomer::orderBy('name')->get();
        $quote->load(['quoteLines']);
        
        return view('quotes.edit', compact('quote', 'airlines', 'subcontractors', 'externalCustomers'));
    }

    public function preview(Quote $quote)
    {
        $quote->load(['quoteLines', 'user']);
        
        $pdf = Pdf::loadView('quotes.pdf', compact('quote'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('quotation-' . $quote->id . '.pdf');
    }
    
    public function download(Quote $quote)
    {
        $quote->load(['quoteLines', 'user']);
        
        $pdf = Pdf::loadView('quotes.pdf', compact('quote'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('quotation-' . $quote->id . '.pdf');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Quote deleted successfully.');
    }
}