<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Illuminate\Http\Request;

class salesHistoryController extends Controller
{
    public function index()
    {
        $transactions = Transactions::with('user', 'details.product')->get();
        return view('content.Dashboard.Report.SalesHistory.index', compact('transactions'));
    }
}
