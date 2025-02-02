<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CustomerEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'company_id',
        'customer_satisfaction',
        'visit_date',
        'visit_time',
        'note',
        'created_by',
        'updated_by'
    ];

    public function histories()
    {
        return $this->hasMany(History::class, 'event_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public static function getCountByFeedback($userId = '', $departmentId = '')
    {
        $feedbackData = self::select('customer_satisfaction', DB::raw('count(*) as total'))
            ->groupBy('customer_satisfaction') // Note: changed from 'company_id, customer_satisfaction' to just 'customer_satisfaction' for grouping correctly
            ->where('customer_satisfaction', '!=', null)
            ->when(!empty($userId), function ($query) use ($userId) {
                $query->where('created_by', $userId);
            })->when(!empty($departmentId), function ($query) use ($departmentId) {
                $query->whereHas('createdBy', function ($q) use ($departmentId) {
                    return $q->where('department_id', 2);
                });
            })
            ->pluck('total', 'customer_satisfaction');

        return $feedbackData->sortDesc();
    }
}
