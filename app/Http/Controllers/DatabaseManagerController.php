<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ProductRoot;
use App\Models\ProductSeriesMapping;
use App\Models\PriceList;
use App\Models\ContractPrice;
use App\Models\StockedProduct;
use App\Models\Airline;
use Illuminate\Http\Request;

class DatabaseManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('database_manager');
    }

    public function index()
    {
        return view('database-manager.index');
    }

    public function customers()
    {
        return view('database-manager.customers');
    }

    public function productRoots()
    {
        return view('database-manager.product-roots');
    }

    public function productSeries()
    {
        return view('database-manager.product-series');
    }


    public function contractPrices()
    {
        return view('database-manager.contract-prices');
    }

    public function stockedProducts()
    {
        return view('database-manager.stocked-products');
    }

    public function airlines()
    {
        return view('database-manager.airlines');
    }
}