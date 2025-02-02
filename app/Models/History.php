<?php

namespace App\Models;

use App\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class History extends Model
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
        'sales_note',
        'sales_dept_person_id',
        'spare_note',
        'spare_dept_person_id',
        'service_note',
        'service_dept_person_id',
        'event_id',
        'created_by',
        'updated_by',
        'sales_user_id',
        'branch_id',
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


    public function event()
    {
        return $this->belongsTo(CustomerEvent::class, 'event_id', 'id');
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

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'id');
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

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
