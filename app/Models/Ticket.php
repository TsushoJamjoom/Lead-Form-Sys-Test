<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'dept_id',
        'user_id',
        'status',
        'note',
        'in_process_note',
        'completed_note',
        'related_dept_id',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function dept()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'id');
    }

    public static function getCountByDepartmentAndStatus()
    {
        return self::select('departments.name as department_name', 'departments.id', 'tickets.status', DB::raw('count(tickets.id) as total'))
            ->RightJoin('departments', 'tickets.dept_id', '=', 'departments.id')
            ->groupBy('tickets.status', 'departments.id')
            ->get();
    }
}
