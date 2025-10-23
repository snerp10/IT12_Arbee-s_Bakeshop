<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');
        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('table_name')) $query->where('table_name', $request->table_name);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s){
                $q->where('description','like',"%$s%")
                  ->orWhere('table_name','like',"%$s%")
                  ->orWhere('action','like',"%$s%");
            });
        }
        if ($request->filled('date_from')) $query->whereDate('created_at','>=',$request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at','<=',$request->date_to);

        $logs = $query->orderByDesc('created_at')->paginate(20);
        $users = User::orderBy('username')->get(['user_id','username']);
        $actions = ['create','update','delete','login','logout','import','export','backup','restore'];
        return view('admin.audit-logs.index', compact('logs','users','actions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AuditLog $audit_log)
    {
        $audit_log->load('user');
        return view('admin.audit-logs.show', compact('audit_log'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function export()
    {
        $fileName = 'audit_logs_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date','User','Action','Table','Record ID','Description']);
            AuditLog::with('user')->orderByDesc('created_at')->chunk(500, function($rows) use ($handle) {
                foreach ($rows as $log) {
                    fputcsv($handle, [
                        $log->created_at,
                        optional($log->user)->username,
                        $log->action,
                        $log->table_name,
                        $log->record_id,
                        $log->description,
                    ]);
                }
            });
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = AuditLog::with('user');
        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('table_name')) $query->where('table_name', $request->table_name);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s){
                $q->where('description','like',"%$s%")
                  ->orWhere('table_name','like',"%$s%")
                  ->orWhere('action','like',"%$s%");
            });
        }
        if ($request->filled('date_from')) $query->whereDate('created_at','>=',$request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at','<=',$request->date_to);

        $logs = $query->orderByDesc('created_at')->get();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $pdf = Pdf::loadView('admin.audit-logs.pdf', compact('logs','dateFrom','dateTo'))
            ->setPaper('a4','portrait');
        return $pdf->download('audit_logs_'.now()->format('Ymd_His').'.pdf');
    }
}
