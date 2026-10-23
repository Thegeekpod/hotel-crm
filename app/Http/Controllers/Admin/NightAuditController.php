<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NightAudit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NightAuditController extends Controller
{
    /**
     * Display list of all historical night audits in the Admin panel
     */
    public function index(Request $request)
    {
        $query = NightAudit::with('auditor')->orderBy('audit_date', 'desc');

        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        $search = $request->query('search');

        if ($request->filled('from_date')) {
            $query->whereDate('audit_date', '>=', $fromDate);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('audit_date', '<=', $toDate);
        }

        if ($request->filled('search')) {
            $s = trim($search);
            $query->where(function ($q) use ($s) {
                $q->where('auditor_name', 'like', "%{$s}%")
                  ->orWhere('remarks', 'like', "%{$s}%")
                  ->orWhereDate('audit_date', $s);
            });
        }

        $audits = $query->paginate(20)->withQueryString();

        // Overall Historical Aggregate Metrics
        $totalAuditsCount = NightAudit::count();
        $totalAuditedRevenue = (float)NightAudit::sum('total_revenue');
        $totalAuditedPayments = (float)NightAudit::sum('total_payments');
        $avgOccupancy = (float)NightAudit::avg('occupancy_rate');

        return view('admin.nightaudit.index', compact(
            'audits',
            'totalAuditsCount',
            'totalAuditedRevenue',
            'totalAuditedPayments',
            'avgOccupancy',
            'fromDate',
            'toDate',
            'search'
        ));
    }

    /**
     * Get detailed JSON data for an audit record to view or print
     */
    public function show($id)
    {
        $audit = NightAudit::with('auditor')->findOrFail($id);

        return response()->json([
            'success' => true,
            'audit' => $audit
        ]);
    }
}
