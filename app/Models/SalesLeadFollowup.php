<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesLeadFollowup extends Model
{
    use HasFactory;
    protected $table = 'sales_lead_followup';
    protected $fillable = ['sales_lead_id', 'comment', 'value'];

    public function salesLead()
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }
}
