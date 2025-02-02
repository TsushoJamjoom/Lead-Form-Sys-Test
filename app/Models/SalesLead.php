<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SalesLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['company_id', 'model', 'qty', 'sales_month', 'comment', 'reason', 'competitor_info', 'status', 'created_by', 'created_at', 'deleted_at'];

    /**
     * Get the company that owns the example.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function followups()
    {
        return $this->hasMany(SalesLeadFollowup::class, 'sales_lead_id');
    }

    public static function getSalesLead()
    {
        return self::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    public function createdby()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
