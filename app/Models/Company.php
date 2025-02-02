<?php

namespace App\Models;

use App\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'website',
        'contact_person',
        'crid',
        'customer_code',
        'position',
        'mobile_no',
        'email',
        'user_logo',
        'vat',
        'construction',
        'food',
        'rental',
        'logistics',
        'describe_other',
        'national_address',
        'hino_pick_up',
        'hino_light_duty_truck',
        'hino_medium_duty_truck',
        'hino_heavy_duty_truck',
        'hino_total',
        'hino_oldest',
        'hino_latest',
        'isuzu_pick_up',
        'isuzu_light_duty_truck',
        'isuzu_medium_duty_truck',
        'isuzu_heavy_duty_truck',
        'isuzu_total',
        'isuzu_oldest',
        'isuzu_latest',
        'fuso_pick_up',
        'fuso_light_duty_truck',
        'fuso_medium_duty_truck',
        'fuso_heavy_duty_truck',
        'fuso_total',
        'fuso_oldest',
        'fuso_latest',
        'sitrak_pick_up',
        'sitrak_light_duty_truck',
        'sitrak_medium_duty_truck',
        'sitrak_heavy_duty_truck',
        'sitrak_total',
        'sitrak_oldest',
        'sitrak_latest',
        'sany_pick_up',
        'sany_light_duty_truck',
        'sany_medium_duty_truck',
        'sany_heavy_duty_truck',
        'sany_total',
        'sany_oldest',
        'sany_latest',
        'shacman_pick_up',
        'shacman_light_duty_truck',
        'shacman_medium_duty_truck',
        'shacman_heavy_duty_truck',
        'shacman_total',
        'shacman_oldest',
        'shacman_latest',
        'faw_pick_up',
        'faw_light_duty_truck',
        'faw_medium_duty_truck',
        'faw_heavy_duty_truck',
        'faw_total',
        'faw_oldest',
        'faw_latest',
        'sinotruk_pick_up',
        'sinotruk_light_duty_truck',
        'sinotruk_medium_duty_truck',
        'sinotruk_heavy_duty_truck',
        'sinotruk_total',
        'sinotruk_oldest',
        'sinotruk_latest',
        'european_pick_up',
        'european_light_duty_truck',
        'european_medium_duty_truck',
        'european_heavy_duty_truck',
        'european_total',
        'european_oldest',
        'european_latest',
        'man_pick_up',
        'man_light_duty_truck',
        'man_medium_duty_truck',
        'man_heavy_duty_truck',
        'man_total',
        'man_oldest',
        'man_latest',
        'volvo_pick_up',
        'volvo_light_duty_truck',
        'volvo_medium_duty_truck',
        'volvo_heavy_duty_truck',
        'volvo_total',
        'volvo_oldest',
        'volvo_latest',
        'mercedes_pick_up',
        'mercedes_light_duty_truck',
        'mercedes_medium_duty_truck',
        'mercedes_heavy_duty_truck',
        'mercedes_total',
        'mercedes_oldest',
        'other_medium_duty_truck',
        'other_heavy_duty_truck',
        'other_total',
        'other_oldest',
        'other_latest',
        'jeddah',
        'madina',
        'riyadh',
        'dammam',
        'al_khobar',
        'abha',
        'hafr_batin',
        'makkah',
        'alyth',
        'yanbu',
        'buraidah',
        'hail',
        'al_baha',
        'alqassim',
        'najran',
        'jizan',
        'khamis',
        'tabuk',
        'taif',
        'neom',
        'jubail',
        'other_cities',
        'new_vehicle_inquiry',
        'vehicle_shelf_life',
        'coordinates',
        'payment_term_of_sales',
        'images',
        'custownws_no_of_ws',
        'custownws_no_of_tech',
        'custownws_tech_languages',
        'custownws_oil_used',
        'custownws_parts_genuine',
        'custownws_parts_non_genunine',
        'custownws_parts_mix',
        'custownws_parts_gray',
        'custownws_parts_source',
        'locws_noof_ws',
        'locws_name_of_ws',
        'locws_noof_tech',
        'locws_approx_price',
        'locws_parts_utilized',
        'hinod_city',
        'hinod_amc_lvl',
        'l12m_parts_1half',
        'l12m_parts_2half',
        'l12m_parts_date',
        'l12m_service_1half',
        'l12m_service_2half',
        'l12m_service_date',
        'l12m_sales_1half',
        'l12m_sales_2half',
        'l12m_sales_date',
        'dept_id',
        'customer_voice',
        'customer_satisfaction',
        'aftersales_contact_person',
        'title',
        'mobile',
        'visit_date',
        'visit_time',
        'sales_note',
        // 'sales_dept_person_id',
        'spare_note',
        // 'spare_dept_person_id',
        'service_note',
        // 'service_dept_person_id',
        'created_by',
        'updated_by',
        'sales_user_id',
        'branch_id',
        'selected_coordinates',
    ];

    protected $casts = [
        'construction' => 'boolean',
        'food' => 'boolean',
        'rental' => 'boolean',
        'logistics' => 'boolean',
        'jeddah' => 'boolean',
        'madina' => 'boolean',
        'riyadh' => 'boolean',
        'dammam' => 'boolean',
        'al_khobar' => 'boolean',
        'abha' => 'boolean',
        'hafr_batin' => 'boolean',
        'makkah' => 'boolean',
        'alyth' => 'boolean',
        'yanbu' => 'boolean',
        'buraidah' => 'boolean',
        'hail' => 'boolean',
        'al_baha' => 'boolean',
        'alqassim' => 'boolean',
        'najran' => 'boolean',
        'jizan' => 'boolean',
        'khamis' => 'boolean',
        'tabuk' => 'boolean',
        'taif' => 'boolean',
        'neom' => 'boolean',
        'jubail' => 'boolean',
        'custownws_parts_genuine' => 'boolean',
        'custownws_parts_non_genunine' => 'boolean',
        'custownws_parts_mix' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'company_id', 'id');
    }

    public function histories()
    {
        return $this->hasMany(History::class, 'company_id', 'id');
    }

    public function salesUser()
    {
        return $this->belongsTo(User::class, 'sales_user_id', 'id');
    }

    public function scopeCreatedMe(Builder $query)
    {
        return;
        $user = auth()->user();
        if ($user->role == AppHelper::SUPER_ADMIN) {
            return;
        } else {
            $query->where('created_by', $user->id);
        }
    }

    public function scopeCalendar(Builder $query)
    {
        $salesDeptId = Department::where('slug', AppHelper::SALES)->first()->id;
        $user = auth()->user();
        if ($user->role == AppHelper::DEPARTMENT && $user->department_id == $salesDeptId) {
            $query->whereHas('tickets', function ($q) use ($user) {
                return $q->where('dept_id', $user->department_id)
                    ->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->orWhere('user_id', 0);
                    });
            });
            // return $query->where('sales_dept_person_id', $user->id)
            //     ->orWhere('sales_dept_person_id', 0)
            //     ->orWhere('spare_dept_person_id', $user->id)
            //     ->orWhere('spare_dept_person_id', 0)
            //     ->orWhere('service_dept_person_id', $user->id)
            //     ->orWhere('service_dept_person_id', 0);
        } elseif ($user->role == AppHelper::STAFF) {
            $query->where('created_by', $user->id);
        }
        $query->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($user), function ($q) use ($user) {
            return $q->orWhere('sales_user_id', $user->id);
        });
        return $query->whereNotNull('visit_date');
    }

    public function scopeAssignedMe(Builder $query)
    {
        $user = auth()->user();
        if ($user->role == AppHelper::DEPARTMENT) {
            return $query->where('sales_dept_person_id', $user->id)
                ->orWhere('sales_dept_person_id', 0)
                ->orWhere('spare_dept_person_id', $user->id)
                ->orWhere('spare_dept_person_id', 0)
                ->orWhere('service_dept_person_id', $user->id)
                ->orWhere('service_dept_person_id', 0);
        } elseif ($user->role == AppHelper::STAFF) {
            $query->where('created_by', $user->id);
        } else {
            return $query->whereNotNull('sales_dept_person_id')
                ->orWhereNotNull('spare_dept_person_id', $user->id)
                ->orWhereNotNull('service_dept_person_id', $user->id);
        }
        // if($user->role == AppHelper::DEPARTMENT){
        //     $dept = $user->dept;
        //     if($dept->slug == AppHelper::SALES){
        //         return $query->where('sales_dept_person_id', $user->id);
        //     }elseif($dept->slug == AppHelper::SPARE_PARTS){
        //         return $query->where('spare_dept_person_id', $user->id);
        //     }elseif($dept->slug == AppHelper::SERVICE){
        //         return $query->where('service_dept_person_id', $user->id);
        //     }
        // }
    }

    /**
     * Get the sales lead for the company.
     */
    public function salesLead()
    {
        return $this->hasMany(SalesLead::class);
    }

    public static function getCompanyVisit()
    {
        return self::select('company_name', DB::raw('count(*) as total'))
            ->groupBy('id')
            ->where('visit_date', '!=', null)
            ->pluck('total', 'company_name');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function event()
    {
        return $this->hasMany(CustomerEvent::class, 'company_id', 'id');
    }

    public function pendingTicketCount()
    {
        return $this->hasMany(Ticket::class, 'company_id', 'id')->where('status', 0);
    }

    public function salesLeadCount()
    {
        return $this->hasMany(SalesLead::class);
    }

    public function archivedsalesLeadCount()
    {
        return $this->hasMany(SalesLead::class)->where('status', 1);
    }

    public function lostsalesLeadCount()
    {
        return $this->hasMany(SalesLead::class)->where('status', 2);
    }
}
