<?php

namespace App\Http\Controllers\Landing;

use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class indexController extends Controller
{
    public function index()
    {
        $bestSellingProducts = TransactionDetail::with('product')
            ->select('KdProduct', DB::raw('SUM(qty) as total_qty_sold'), DB::raw('SUM(subtotal) as total_sales_amount'))
            ->groupBy('KdProduct')
            ->orderBy('total_qty_sold', 'desc')
            ->take(4)
            ->get();
        return view('content.Landing.index', compact('bestSellingProducts'));
    }
}
