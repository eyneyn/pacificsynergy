<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',       // ✅ add this
        'role_id',           // ✅ if you want role-based notifications
        'defect_id',
        'standard_id',
        'type',
        'production_report_id',
        'message',
        'is_read',
        'required_permission',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Relationship → linked production report
     */
    public function report()
    {
        return $this->belongsTo(ProductionReport::class, 'production_report_id');
    }

    /**
     * Relationship → assigned user (nullable if permission-based)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }
    /**
     * ✅ Scope: only notifications the logged-in user can view
     */
    public function scopeVisibleTo($query, $user = null)
    {
        $user = $user ?: Auth::user();

        if (! $user) {
            // No logged-in user → return empty
            return $query->whereRaw('1=0');
        }

        return $query->where(function ($q) use ($user) {
            // Case 1: user-specific notifications
            $q->where('user_id', $user->id);

            // Case 2: permission-based notifications
            $q->orWhere(function ($sub) use ($user) {
                $sub->whereNull('user_id')
                    ->whereNotNull('required_permission')
                    ->whereIn('required_permission', $user->getAllPermissions()->pluck('name'));
            });
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    public function defect()
    {
        return $this->belongsTo(\App\Models\Defect::class, 'defect_id');
    }

    public function standard()
    {
        return $this->belongsTo(\App\Models\Standard::class, 'standard_id');
    }

    public function getUrlAttribute()
    {
        if (!empty($this->attributes['url'])) {
            return $this->attributes['url'];
        }

        switch ($this->type) {
            case 'analytics_warning':
            case 'analytics_access':
                $date = optional($this->report)->production_date;
                return route('analytics.material.monthly_report', [
                    'month' => $date ? \Carbon\Carbon::parse($date)->month : now()->month,
                    'date'  => $date ? \Carbon\Carbon::parse($date)->year : now()->year,
                    'line'  => $this->report->line ?? null,
                    'tab'   => 'production',
                ]);

            case 'report_create':
            case 'report_edit':
            case 'report_submitted':
            case 'report_validate':
                return $this->report ? route('report.view', $this->report->id) : '#';

            case 'config':
                return route('configuration.index');

            case 'role':
                return $this->role_id 
                    ? route('roles.edit', $this->role_id)  
                    : '#';

            case 'employee':
                return $this->employee_id 
                    ? route('employees.view', $this->employee_id) 
                    : '#';

            case 'line':
                return route('configuration.line.index');

            case 'maintenance':
                return route('configuration.maintenance.index');

            case 'defect':
                return $this->defect_id 
                    ? route('configuration.defect.view', $this->defect_id) 
                    : '#';

            case 'standard':   // 👈 NEW
                return $this->standard_id 
                    ? route('configuration.standard.view', $this->standard_id) 
                    : '#';

            case 'setting':
                return route('setting.index');

            default:
                return '#';
        }
    }

    /**
     * ✅ Helper to create standard-related notifications
     */
    public static function standardEvent($action, $standard, $userId = null)
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();

        $userName = $user
            ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
            : 'System';

        return self::create([
            'user_id'             => null, // broadcast
            'standard_id'         => $standard->id, // 👈 reference standard
            'type'                => 'standard',
            'message'             => "Standard <span style=\"color:#23527c;font-weight:bold;\">{$standard->description}</span> was {$action} by " .
                                    "<span style=\"color:#23527c;font-weight:bold;\">{$userName}</span>",
            'required_permission' => 'configuration.index',
            'is_read'             => false,
        ]);
    }


    /**
     * ✅ Helper to create defect-related notifications
     */
    public static function defectEvent($action, $defect, $userId = null)
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();

        $userName = $user
            ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
            : 'System';

        return self::create([
            'user_id'             => null, // broadcast
            'defect_id'           => $defect->id, // 👈 reference defect
            'type'                => 'defect',
            'message'             => "Defect <span style=\"color:#23527c;font-weight:bold;\">{$defect->defect_name}</span> was {$action} by " .
                                    "<span style=\"color:#23527c;font-weight:bold;\">{$userName}</span>",
            'required_permission' => 'configuration.index',
            'is_read'             => false,
        ]);
    }

    public static function maintenanceEvent($action, $maintenanceName, $userId = null)
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();

        $userName = $user
            ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
            : 'System';

        return self::create([
            'user_id'             => null, // broadcast to all with permission
            'type'                => 'maintenance',
            'message'             => "Maintenance <span style=\"color:#23527c;font-weight:bold;\">{$maintenanceName}</span> was {$action} by " .
                                    "<span style=\"color:#23527c;font-weight:bold;\">{$userName}</span>",
            'required_permission' => 'configuration.index', // 👈 permission gate
            'is_read'             => false,
        ]);
    }
    /**
     * ✅ Helper to create line-related notifications
     */
    public static function lineEvent($action, $lineNumber, $userId = null)
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();

        $userName = $user
            ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
            : 'System';

        return self::create([
            'user_id'             => null, // broadcast
            'type'                => 'line',
            'message'             => "Line <span style=\"color:#23527c;font-weight:bold;\">{$lineNumber}</span> was {$action} by " .
                                    "<span style=\"color:#23527c;font-weight:bold;\">{$userName}</span>",
            'required_permission' => 'configuration.index',
            'is_read'             => false,
        ]);
    }
    /**
     * ✅ Helper to create role-related notifications
     */
    public static function roleEvent($action, $roleName, $roleId = null, $userId = null)
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();

        $userName = $user
            ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
            : 'System';

        return self::create([
            'user_id'            => null, // broadcast
            'role_id'            => $roleId, // ✅ now saved properly
            'type'               => 'role',
            'message'            => "Role <span style=\"color:#23527c;font-weight:bold;\">{$roleName}</span> was {$action} by " .
                                    "<span style=\"color:#23527c;font-weight:bold;\">{$userName}</span>",
            'required_permission'=> 'roles.permission',
            'is_read'            => false,
        ]);
    }

    /**
     * ✅ Helper to create employee-related notifications
     */
    public static function employeeEvent($action, $employee, $id = null)
    {
        $actor = $id ? \App\Models\User::find($id) : Auth::user();

        $actorName = $actor
            ? trim(($actor->first_name ?? '') . ' ' . ($actor->last_name ?? ''))
            : 'System';

        $employeeName = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''));

        return self::create([
            'user_id'             => null,            // 👈 broadcast
            'type'                => 'employee',
            'employee_id'         => $employee->id,   // 👈 reference employee
            'message'             => "Employee <span style=\"color:#23527c;font-weight:bold;\">{$employeeName}</span> was {$action} by " .
                                    "<span style=\"color:#23527c;font-weight:bold;\">{$actorName}</span>",
            'required_permission' => 'employees.index',
            'is_read'             => false,
        ]);
    }
    public static function settingEvent($action, $setting, $userId = null)
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();

        $userName = $user
            ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
            : 'System';

        $companyName = $setting->company_name ?? 'Company';

        return self::create([
            'user_id'             => null, // broadcast to all with permission
            'type'                => 'setting',
            'message'             => "Settings for <span style=\"color:#23527c;font-weight:bold;\">{$companyName}</span> were {$action} by " .
                                    "<span style=\"color:#23527c;font-weight:bold;\">{$userName}</span>",
            'required_permission' => 'user.dashboard', // 👈 corrected to match your settings route
            'is_read'             => false,
        ]);
    }
}
