<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $eventsList = [
            'User Access Logs' => [
                'login'          => 'Logged In',
                'logout'         => 'Logged Out',
                'failed_login'   => 'Failed Login',
                'password_reset' => 'Password Reset',
            ],
            'Production Report' => [
                'report_create'   => 'Created Report',
                'report_edit'     => 'Edited Report',
                'report_validate' => 'Validated Report',
                'report_pdf'      => 'Export to Daily Report',
            ],
            'Role' => [
                'role_add'    => 'Created Role',
                'role_update' => 'Updated Role',
            ],
            'Employee' => [
                'employee_add'    => 'Created Employee',
                'employee_update' => 'Updated Employee',
            ],
            'Standard' => [
                'standard_create' => 'Created Standard',
                'standard_edit'   => 'Updated Standard',
                'standard_delete' => 'Deleted Standard',
            ],
            'Defect' => [
                'defect_add'    => 'Created Defect',
                'defect_update' => 'Update Defect',
                'defect_delete' => 'Deleted Defect',
            ],
            'Maintenance' => [
                'maintenance_store'   => 'Created Maintenance',
                'maintenance_update'  => 'Updated Maintenance',
                'maintenance_destroy' => 'Deleted Maintenance',
            ],
            'Line' => [
                'line_store'   => 'Created Line',
                'line_update'  => 'Updated Line',
                'line_destroy' => 'Deleted Line',
            ],
            'Analytics' => [
                'material_summary_export' => 'Material Summary Export',
                'material_annual_export'  => 'Material Annual Export',
                'material_monthly_export' => 'Material Monthly Export',
                'line_summary_export'     => 'Line Summary Export',
                'line_annual_export'      => 'Line Annual Export',
                'line_monthly_export'     => 'Line Monthly Export',
            ],
        ];


        $logs = AuditLog::with('user')
            ->ofEvent($request->get('event'))
            ->dateRange($request->get('date_from'), $request->get('date_to'))
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->search($request->get('q'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $users = User::all(['id','first_name','last_name']);

        return view('admin.audit-logs.index', [
            'logs'       => $logs,
            'users'      => $users,
            'eventsList' => $eventsList,
            'filters'    => $request->only(['q','event','user_id','date_from','date_to']),
        ]);
    }
}