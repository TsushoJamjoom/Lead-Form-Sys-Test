<?php

namespace App\Livewire\Map;

use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CustomerEvent;
use App\Models\SalesLead;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\On;
use Livewire\Component;

class Map extends Component
{
    public $title = "Map";
    public $user;

    public $companyid;
    public $visit_date;
    public $visit_time;

    # Filter
    public $isCollapse;
    public $salesUserId;


    public $addStyle = false;

    public function mount()
    {
        $this->user = auth()->user();
        $this->salesUserId = request('salesUserId', $this->salesUserId);
    }

    public function getListDataProperty()
    {

        $locations = Company::select('id', 'company_name', 'customer_code', 'coordinates', 'user_logo', 'sales_user_id')
            ->with('salesUser:id,name,profile_photo')
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->orWhere('sales_user_id', $this->user->id)
                    ->orWhere('sales_user_id', 0);
            })
            ->when(!empty($this->salesUserId), function ($query) {
                return $query->where('sales_user_id', $this->salesUserId);
            })
            ->get();
        $companyLocations = [];
        if (!empty($locations)) {
            foreach ($locations as $key => $value) {
                $visitCount = CustomerEvent::where('company_id', $value->id)
                    ->where('customer_satisfaction', '!=', null)
                    ->where('visit_date', '>=', Carbon::now()->subMonths(3)->startOfMonth())
                    ->count();
                // Determine visit category
                $visitHigh = false;
                $visitLow = false;
                $visitUnattended = false;
                if ($visitCount > 1) {
                    $visitHigh = true;
                } elseif ($visitCount == 1) {
                    $visitLow = true;
                } else {
                    $visitUnattended = true;
                }

                $salesLeadCountByCompany = SalesLead::where([['company_id', $value->id], ['status', 0]])->count();
                $activeSymbol = '';
                $visitSymbol = '';
                if ($salesLeadCountByCompany > 0) {
                    $activeSymbol = '<b style="color:yellow;">&nbsp; &#10026;</b>';
                }

                if ($visitHigh) {
                    $visitSymbol = '(<i class="fa fa-forward" aria-hidden="true"></i>)';
                } elseif ($visitLow) {
                    $visitSymbol = '(<i class="fa fa-play" aria-hidden="true"></i>)';
                } elseif ($visitUnattended) {
                    $visitSymbol = '(<i class="fa fa-pause" aria-hidden="true"></i>)';
                }
                $coordinatesArray = json_decode($value->coordinates, true);
                if (!empty($coordinatesArray)) {
                    foreach ($coordinatesArray as $cordi) {
                        $seprateValue = explode(',', $cordi);
                        $companyLocations[] = [
                            'lat' => floatval($seprateValue[0]),
                            'lng' => floatval($seprateValue[1]),
                            'title' => $value->company_name,
                            'description' => $value->customer_code,
                            'link' => route('company-edit', ['id' => $value->id]),
                            'company_id' => $value->id,
                            'logo' => @$value->user_logo ? asset('storage/user-logo/' . $value->user_logo) : '',
                            'sales_user_profile_photo' => @$value->salesUser->profile_photo ? asset('storage/user-profile/' . $value->salesUser->profile_photo) : "",
                            'sales_user_name' => @$value->salesUser->name ? $value->salesUser->name : "",
                            'visit_symbol' => $visitSymbol,
                            'active_symbol' => $activeSymbol
                        ];
                    }
                }
            }
        }
        // dd($locations->toArray());
        return $companyLocations;
    }

    public function getCompanyDropDownProperty()
    {
        return Company::createdMe()
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->orWhere('sales_user_id', $this->user->id)
                    ->orWhere('sales_user_id', 0);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[On('triggerCreateEvent')]
    public function triggerCreateEvent($companyId)
    {
        $this->companyid = $companyId;
        $this->visit_date = '';
        $this->visit_time = '';
        $this->dispatch('showCreateEventModal', ['companyId' => $this->companyid]);
    }

    public function createAppointment()
    {
        if ($this->companyid == null || $this->visit_date == null) {
            $this->dispatch('alert', type: 'error', message: 'The company and vsist date field is required.');
            return;
        }
        try {
            $companyExists = CustomerEvent::where([['company_id', $this->companyid], ['visit_date', '=', $this->visit_date]])->count();
            if (!empty($companyExists)) {
                $this->dispatch('alert', type: 'error', message: 'Appointment already added.');
                return;
            } else {

                $addEvent =  CustomerEvent::create([
                    'company_id' => $this->companyid,
                    'visit_date' => $this->visit_date,
                    'visit_time' => $this->visit_time,
                    'created_by' => $this->user->id
                ]);
                session()->flash('success', 'Appointment successfully created.');
                return $this->redirectRoute('map', navigate: true);
            }
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function getSalesUsersProperty()
    {
        return AppHelper::getSalesAnd3sUser();
    }

    public function submitFilter()
    {
        $url = URL::route('map', [
            'salesUserId' => $this->salesUserId,
        ]);
        return redirect($url);
    }

    public function render()
    {
        return view('livewire.map.map')
            ->title($this->title);
    }
}
