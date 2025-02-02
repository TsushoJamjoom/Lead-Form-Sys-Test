<?php

namespace App\Livewire\Company;

use App\Events\TicketAssigned;
use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CustomerEvent;
use App\Models\Department;
use App\Models\SalesLead;
use App\Models\SalesLeadModel;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;

class CompanyAdd extends Component
{
    use WithFileUploads;

    public $title = "Customer Profile";
    #[Validate('image|max:1024')]
    public $userLogo;
    public $userLogoName;
    public $form = [];
    public $event = [];
    #[Validate(['images.*' => 'image|max:1024'])]
    public $images = [];
    public $fileNames = [];
    public $removedUserLogo = [];
    public $removedImages = [];
    public $salesUserId;
    public $companySalesUserId;
    public $companySalesUserDepId;
    public $sparePartsUserId;
    public $serviceUserId;

    // Search By Customer Code Or Name
    public $searchVal;
    #[Url]
    public $id;
    public $eventid;
    public $salesDeptId;
    public $s3DeptId;
    public $sparePartsDeptId;
    public $serviceDeptId;
    public $afterSalesDeptId;
    public $salesUsers;
    public $sparePartsUsers;
    public $serviceUsers;
    public $departmentData;

    // Location Coordinates
    public $coordinates = [];
    // Customer Satisfaction
    public $customerStf;
    public $oldCustomerStf;
    public $user;
    public $fields = [];
    public $branchList = [];

    public $companyid;
    public $visit_date;
    public $visit_time;

    public $salesLeadModels;
    public $currentMonth;
    public $enabledMonths = [];

    public $selectedCoordinate;

    public function mount(Request $request)
    {
        $this->user = auth()->user();
        // Get All Dept and Users
        $this->salesDeptId = Department::where('slug', AppHelper::SALES)->first()->id;
        $this->s3DeptId = Department::where('slug', AppHelper::S3)->first()->id;
        $this->sparePartsDeptId = Department::where('slug', AppHelper::SPARE_PARTS)->first()->id;
        $this->serviceDeptId = Department::where('slug', AppHelper::SERVICE)->first()->id;
        $this->afterSalesDeptId = Department::where('slug', AppHelper::AFTER_SALES)->first()->id;
        $this->fields = [['model' => '', 'qty' => '', 'sales_month' => Date::now()->format('n'), 'comment' => '']];

        if (!empty($request->id)) {
            $this->id = $request->id;
            $data = Company::with(['salesUser', 'tickets', 'salesLead' => function ($query) {
                $query->where('status', 0);
            }, 'salesLead.followups'])->findOrFail($this->id);
            $this->fillData($data);
            if ($data->salesLead->count() > 0) {
                $this->fields = $data->salesLead->toArray();
            }
        }
        if (!empty($request->eventid)) {
            $this->eventid = $request->eventid;
            $eventdata = CustomerEvent::findOrFail($this->eventid);
            $this->event = $eventdata->toArray();
            $this->customerStf = $this->event['customer_satisfaction'];
            $this->oldCustomerStf = $this->customerStf;
            // dd($this->event['visit_date']);
        }
        // Sales
        $this->salesUsers = User::whereIn('department_id', [$this->salesDeptId, $this->s3DeptId])
            ->where('id', '!=', $this->user->id)
            ->get();
        // Spare Parts
        $this->sparePartsUsers = User::whereIn('department_id', [$this->sparePartsDeptId, $this->afterSalesDeptId])
            ->where('id', '!=', $this->user->id)
            ->get();
        // Service
        $this->serviceUsers = User::whereIn('department_id', [$this->serviceDeptId, $this->afterSalesDeptId])
            ->where('id', '!=', $this->user->id)
            ->get();

        $this->departmentData = Department::all();

        $this->branchList = Branch::all();

        $this->salesLeadModels = SalesLeadModel::active()->get();

        $currentDate = Carbon::now();
        for ($i = 0; $i < 5; $i++) {
            $this->enabledMonths[] = $currentDate->format('n'); // Get the month number
            $currentDate = $currentDate->addMonthsNoOverflow(1); // Move to the next month
        }

        if (empty($this->form['national_address']) && !empty($this->selectedCoordinate)) {
            $this->setAddress();
        } elseif (empty($this->selectedCoordinate) && !empty($this->coordinates)) {
            $lastLocation = end($this->coordinates);
            $this->selectedCoordinate = $lastLocation;
            $this->setAddress();
        }
    }

    public function addSalesLeadField()
    {
        $this->fields[] = ['model' => '', 'qty' => '', 'sales_month' => Date::now()->format('n'), 'comment' => ''];
    }

    public function removeSalesLeadField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function saveInitiateSalesLead()
    {
        if (empty($this->id)) {
            $this->dispatch('alert', type: 'error', message: 'To assign the ticket, you need to first create a new record or select an existing one.');
            return;
        } elseif (empty($this->fields)) {
            $this->dispatch('alert', type: 'error', message: 'Please add at least one initiate sales lead.');
            return;
        }

        $validator = Validator::make($this->fields, [
            '*.model' => 'required|max:120',
            '*.qty' => 'required|numeric',
            '*.sales_month' => 'digits_between:1,2',
            '*.comment' => 'nullable|max:255',
        ]);
        if ($validator->fails()) {
            $this->dispatch('alert', type: 'error', message: 'Please fill all required fields and ensure they are valid.');
            return;
        }

        try {
            $companyId = $this->id;
            $unsavedDataWithCompanyId = array_map(function ($item) use ($companyId) {
                if (!isset($item['id'])) {
                    $item['company_id'] = $companyId;
                    $item['created_at'] = Carbon::now();
                    $item['updated_at'] = Carbon::now();
                    $item['created_by'] = auth()->id();
                    return $item;
                }
            }, $this->fields);
            $unsavedDataWithCompanyId = array_filter($unsavedDataWithCompanyId);
            SalesLead::insert($unsavedDataWithCompanyId);
            $this->fields = SalesLead::where('company_id', $this->id)
                ->where('status', 0)
                ->get()->toArray();
            $this->dispatch('alert', type: 'success', message: 'Initiate sales lead save successfully.');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function getCompanyDropDownProperty()
    {
        $data = Company::createdMe()
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->orWhere('sales_user_id', $this->user->id)
                    ->orWhere('sales_user_id', 0);
            })
            ->orderBy('created_at', 'desc');

        return $data->get();
    }

    public function updatedSearchVal()
    {
        if (!empty($this->searchVal)) {
            $data = Company::with(['tickets'])->where(function ($query) {
                return $query->where('company_name', $this->searchVal)
                    ->orWhere('customer_code', $this->searchVal)
                    ->orWhere('id', $this->searchVal);
            })->first();
            if ($data) {
                $this->dispatch('alert', type: 'success', message: "The form has been filled out successfully.");
                $this->fillData($data);
            } else {
                $this->dispatch('alert', type: 'error', message: "Record not found!");
                $this->form = [];
                $this->userLogoName = '';
                $this->fileNames = [];
                $this->id = '';
            }
            $this->dispatch('table-input');
        } else {
            return $this->redirectRoute('company-add', navigate: true);
        }
    }

    public function fillData($data)
    {
        $this->form = $data->toArray();
        $this->form['dept_id'] = @$this->form['dept_id'] ?: $this->user->department_id;
        $this->companySalesUserId = $this->form['sales_user_id'];
        $this->companySalesUserDepId = $this->form['sales_user']['department_id'] ?? null;
        $this->id = $this->form['id'];
        $this->userLogoName = $this->form['user_logo'];
        $this->fileNames = !empty($this->form['images']) ? json_decode($this->form['images'], true) : [];
        $this->selectedCoordinate = $this->form['selected_coordinates'];

        // Coordinates
        if (!empty($this->form['coordinates'])) {
            $this->coordinates = json_decode($this->form['coordinates'], true);
        }

        if (!$data->tickets->isEmpty()) {
            foreach ($data->tickets as $ticket) {
                if ($ticket->dept_id == $this->salesDeptId || ($ticket->dept_id == $this->s3DeptId && $ticket->related_dept_id == $this->salesDeptId)) {
                    $this->salesUserId = $ticket->user_id;
                }
                if ($ticket->dept_id == $this->sparePartsDeptId || ($ticket->dept_id == $this->afterSalesDeptId && $ticket->related_dept_id == $this->sparePartsDeptId)) {
                    $this->sparePartsUserId = $ticket->user_id;
                }
                if ($ticket->dept_id == $this->serviceDeptId || ($ticket->dept_id == $this->afterSalesDeptId && $ticket->related_dept_id == $this->serviceDeptId)) {
                    $this->serviceUserId = $ticket->user_id;
                }
            }
        }
    }

    public function clear()
    {
        return $this->redirectRoute('company-add', navigate: true);
    }

    public function getDepartmentsProperty()
    {
        return Department::all();
    }

    public function getUsersProperty()
    {
        return User::where('role', AppHelper::STAFF)->get();
    }

    #[On('update-location')]
    public function updateLocation($location)
    {
        $this->coordinates[] = $location;
        if (count($this->coordinates) > 4) {
            $this->dispatch('alert', type: 'warning', message: "The coordinate limit has been exceeded.");
        } else {
            $this->dispatch('alert', type: 'success', message: "Location updated successfully.");
        }
        $this->updateAddress();
    }
    public function removeLocation($index)
    {
        unset($this->coordinates[$index]);
        $this->dispatch('alert', type: 'success', message: "The selected coordinate has been removed.");
        $this->updateAddress();
    }

    public function updateAddress()
    {
        if (!empty($this->coordinates)) {
            $lastLocation = end($this->coordinates);
            $this->selectedCoordinate = $lastLocation;
            $this->setAddress();
        }
    }

    public function setAddress()
    {
        if (empty($this->selectedCoordinate)) {
            return;
        }
        $letLong = explode(',', $this->selectedCoordinate);
        if (empty($letLong)) {
            return;
        }
        $letLong = ['24.7136', '46.6753'];
        $decodedResponse = AppHelper::getAddress($letLong);
        // dd($decodedResponse);
        if (!empty($decodedResponse['Addresses'])) {
            $address = $decodedResponse['Addresses'][0] ?? null;
            if (!empty($address)) {
                $newAddress = $address['BuildingNumber'] . ', ' . $address['Street'] . ', ';
                $newAddress .= $address['AdditionalNumber'] . ', ' . $address['District'] . ', ';
                $newAddress .= $address['PostCode'] . ', ' . $address['City'];
                $this->form['national_address'] = $newAddress;
            }
        } else if (!empty($decodedResponse['PostCode'])) {
            $address = $decodedResponse['PostCode'][0] ?? null;
            if (!empty($address)) {
                $newAddress = $address['districtName'] . ', ' . $address['postCode'] . ', ';
                $newAddress .= $address['cityName'] . ', ' . $address['regionName'];
                $this->form['national_address'] = $newAddress;
            }
        }
    }

    #[On('update-address')]
    public function selectCoordinate()
    {
        $this->setAddress();
    }

    #[On('update-cust-stf')]
    public function updateCusStf($customerSft)
    {
        $this->customerStf = $customerSft;
    }

    public function updatedUserLogo($value)
    {
        $name = rand(0001, 9999) . '-' . time();
        $extension = pathinfo($value->getFilename(), PATHINFO_EXTENSION);
        $fullName = $name . '.' . $extension;
        $value->storeAs(path: 'public/user-logo', name: $fullName);
        $this->userLogoName = $fullName;
    }

    public function removeUserLogo($name)
    {
        $fullPath = 'public/user-logo/' . $name;
        $this->userLogoName = '';
        if (empty($this->id)) {
            Storage::delete($fullPath);
        } else {
            $this->removedUserLogo[] = $name;
        }
    }

    public function updatedImages($value)
    {
        foreach ($value as $image) {
            $name = rand(0001, 9999) . '-' . time();
            $extension = pathinfo($image->getFilename(), PATHINFO_EXTENSION);
            $fullName = $name . '.' . $extension;
            $image->storeAs(path: 'public/images', name: $fullName);
            $this->fileNames[] = $fullName;
        }
    }

    public function removeImage($key)
    {
        $name = $this->fileNames[$key] ?? '';
        if (!empty($name)) {
            $fullPath = 'public/images/' . $name;
            unset($this->fileNames[$key]);
            if (empty($this->id)) {
                Storage::delete($fullPath);
            } else {
                $this->removedImages[] = $name;
            }
        }
    }

    #[On('qrScanned')]
    public function qrScanned($data, $image)
    {
        $order = array("\r\n", "\n", "\r", "\t", "\t\n", "\t\r", "\r\t", "\n\t");
        $data = str_replace($order, "", $data);
        // dd($data, $image);
    }

    public function updated()
    {
        // HINO
        $hino_pick_up = $this->checkFleetData('hino_pick_up');
        $hino_light_duty_truck = $this->checkFleetData('hino_light_duty_truck');
        $hino_medium_duty_truck = $this->checkFleetData('hino_medium_duty_truck');
        $hino_heavy_duty_truck = $this->checkFleetData('hino_heavy_duty_truck');
        $this->form['hino_total'] = ($hino_pick_up + $hino_light_duty_truck + $hino_medium_duty_truck + $hino_heavy_duty_truck);

        // ISUZU
        $isuzu_pick_up = $this->checkFleetData('isuzu_pick_up');
        $isuzu_light_duty_truck = $this->checkFleetData('isuzu_light_duty_truck');
        $isuzu_medium_duty_truck = $this->checkFleetData('isuzu_medium_duty_truck');
        $isuzu_heavy_duty_truck = $this->checkFleetData('isuzu_heavy_duty_truck');
        $this->form['isuzu_total'] = ($isuzu_pick_up + $isuzu_light_duty_truck + $isuzu_medium_duty_truck + $isuzu_heavy_duty_truck);

        // FUSO
        $fuso_pick_up = $this->checkFleetData('fuso_pick_up');
        $fuso_light_duty_truck = $this->checkFleetData('fuso_light_duty_truck');
        $fuso_medium_duty_truck = $this->checkFleetData('fuso_medium_duty_truck');
        $fuso_heavy_duty_truck = $this->checkFleetData('fuso_heavy_duty_truck');
        $this->form['fuso_total'] = ($fuso_pick_up + $fuso_light_duty_truck + $fuso_medium_duty_truck + $fuso_heavy_duty_truck);

        // SITRAK
        $sitrak_pick_up = $this->checkFleetData('sitrak_pick_up');
        $sitrak_light_duty_truck = $this->checkFleetData('sitrak_light_duty_truck');
        $sitrak_medium_duty_truck = $this->checkFleetData('sitrak_medium_duty_truck');
        $sitrak_heavy_duty_truck = $this->checkFleetData('sitrak_heavy_duty_truck');
        $this->form['sitrak_total'] = ($sitrak_pick_up + $sitrak_light_duty_truck + $sitrak_medium_duty_truck + $sitrak_heavy_duty_truck);

        // SANY
        $sany_pick_up = $this->checkFleetData('sany_pick_up');
        $sany_light_duty_truck = $this->checkFleetData('sany_light_duty_truck');
        $sany_medium_duty_truck = $this->checkFleetData('sany_medium_duty_truck');
        $sany_heavy_duty_truck = $this->checkFleetData('sany_heavy_duty_truck');
        $this->form['sany_total'] = ($sany_pick_up + $sany_light_duty_truck + $sany_medium_duty_truck + $sany_heavy_duty_truck);

        // SHACMAN
        $shacman_pick_up = $this->checkFleetData('shacman_pick_up');
        $shacman_light_duty_truck = $this->checkFleetData('shacman_light_duty_truck');
        $shacman_medium_duty_truck = $this->checkFleetData('shacman_medium_duty_truck');
        $shacman_heavy_duty_truck = $this->checkFleetData('shacman_heavy_duty_truck');
        $this->form['shacman_total'] = ($shacman_pick_up + $shacman_light_duty_truck + $shacman_medium_duty_truck + $shacman_heavy_duty_truck);

        // FAW
        $faw_pick_up = $this->checkFleetData('faw_pick_up');
        $faw_light_duty_truck = $this->checkFleetData('faw_light_duty_truck');
        $faw_medium_duty_truck = $this->checkFleetData('faw_medium_duty_truck');
        $faw_heavy_duty_truck = $this->checkFleetData('faw_heavy_duty_truck');
        $this->form['faw_total'] = ($faw_pick_up + $faw_light_duty_truck + $faw_medium_duty_truck + $faw_heavy_duty_truck);

        // SINOTRUK
        $sinotruk_pick_up = $this->checkFleetData('sinotruk_pick_up');
        $sinotruk_light_duty_truck = $this->checkFleetData('sinotruk_light_duty_truck');
        $sinotruk_medium_duty_truck = $this->checkFleetData('sinotruk_medium_duty_truck');
        $sinotruk_heavy_duty_truck = $this->checkFleetData('sinotruk_heavy_duty_truck');
        $this->form['sinotruk_total'] = ($sinotruk_pick_up + $sinotruk_light_duty_truck + $sinotruk_medium_duty_truck + $sinotruk_heavy_duty_truck);

        // EUROPEAN
        $european_pick_up = $this->checkFleetData('european_pick_up');
        $european_light_duty_truck = $this->checkFleetData('european_light_duty_truck');
        $european_medium_duty_truck = $this->checkFleetData('european_medium_duty_truck');
        $european_heavy_duty_truck = $this->checkFleetData('european_heavy_duty_truck');
        $this->form['european_total'] = ($european_pick_up + $european_light_duty_truck + $european_medium_duty_truck + $european_heavy_duty_truck);

        // MAN
        $man_pick_up = $this->checkFleetData('man_pick_up');
        $man_light_duty_truck = $this->checkFleetData('man_light_duty_truck');
        $man_medium_duty_truck = $this->checkFleetData('man_medium_duty_truck');
        $man_heavy_duty_truck = $this->checkFleetData('man_heavy_duty_truck');
        $this->form['man_total'] = ($man_pick_up + $man_light_duty_truck + $man_medium_duty_truck + $man_heavy_duty_truck);

        // VOLVO
        $volvo_pick_up = $this->checkFleetData('volvo_pick_up');
        $volvo_light_duty_truck = $this->checkFleetData('volvo_light_duty_truck');
        $volvo_medium_duty_truck = $this->checkFleetData('volvo_medium_duty_truck');
        $volvo_heavy_duty_truck = $this->checkFleetData('volvo_heavy_duty_truck');
        $this->form['volvo_total'] = ($volvo_pick_up + $volvo_light_duty_truck + $volvo_medium_duty_truck + $volvo_heavy_duty_truck);

        // MERCEDES
        $mercedes_pick_up = $this->checkFleetData('mercedes_pick_up');
        $mercedes_light_duty_truck = $this->checkFleetData('mercedes_light_duty_truck');
        $mercedes_medium_duty_truck = $this->checkFleetData('mercedes_medium_duty_truck');
        $mercedes_heavy_duty_truck = $this->checkFleetData('mercedes_heavy_duty_truck');
        $this->form['mercedes_total'] = ($mercedes_pick_up + $mercedes_light_duty_truck + $mercedes_medium_duty_truck + $mercedes_heavy_duty_truck);

        // UD
        $ud_pick_up = $this->checkFleetData('ud_pick_up');
        $ud_light_duty_truck = $this->checkFleetData('ud_light_duty_truck');
        $ud_medium_duty_truck = $this->checkFleetData('ud_medium_duty_truck');
        $ud_heavy_duty_truck = $this->checkFleetData('ud_heavy_duty_truck');
        $this->form['ud_total'] = ($ud_pick_up + $ud_light_duty_truck + $ud_medium_duty_truck + $ud_heavy_duty_truck);
        // OTHER
        $other_pick_up = $this->checkFleetData('other_pick_up');
        $other_light_duty_truck = $this->checkFleetData('other_light_duty_truck');
        $other_medium_duty_truck = $this->checkFleetData('other_medium_duty_truck');
        $other_heavy_duty_truck = $this->checkFleetData('other_heavy_duty_truck');
        $this->form['other_total'] = ($other_pick_up + $other_light_duty_truck + $other_medium_duty_truck + $other_heavy_duty_truck);
        $this->dispatch('table-input');
    }

    public function checkFleetData($key)
    {
        $result = $this->form[$key] !== '' && $this->form[$key] !== null ? (int) data_get($this->form, $key, 0) : null;
        $this->form[$key] = $result;
        return $result;
    }

    #[On('delete')]
    public function delete($id)
    {
        try {
            Company::find($id)->delete();
            session()->flash('success', 'Record successfully deleted.');
            return $this->redirectRoute('company-add', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function store()
    {
        $this->validate([
            'form.company_name' => 'required|max:100|unique:companies,company_name',
            // 'form.contact_person' => 'required|max:100',
            'form.customer_code' => 'required|max:20|unique:companies,customer_code',
            // 'form.mobile_no' => 'required|numeric|digits:10|unique:companies,mobile_no',
            // 'form.email' => 'required|email:rfx,dns|max:30|unique:companies,email',
            // 'form.crid' => 'sometimes|unique:companies,crid',
        ], [
            'form.company_name.required' => 'The company name field is required.',
            'form.company_name.max' => 'The company name field must not be greater than 100.',
            'form.company_name.unique' => 'The company name has already been taken.',
            'form.contact_person.required' => 'The contact person field is required.',
            'form.contact_person.max' => 'The contact person field must not be greater than 100.',
            'form.customer_code.required' => 'The customer code field is required.',
            'form.customer_code.max' => 'The customer code field must not be greater than 20.',
            'form.customer_code.unique' => 'The customer code has already been taken.',
            'form.mobile_no.required' => 'The mobile number field is required.',
            'form.mobile_no.numeric' => 'The mobile number field must be a number.',
            'form.mobile_no.digits' => 'The mobile number field must not be greater than 10.',
            'form.mobile_no.unique' => 'The mobile no has already been taken.',
            'form.email.required' => 'The email field is required.',
            'form.email.email' => 'The email field must be a valid email address.',
            'form.email.max' => 'The email field must not be greater than 30 characters.',
            'form.email.unique' => 'The email has already been taken.',
            'form.crid.unique' => 'The CR/ID has already been taken.',
        ]);

        try {
            $data = $this->form;
            // Coordinates
            if (!empty($this->coordinates)) {
                $data['coordinates'] = json_encode($this->coordinates);
            }
            // User Logo Store
            $data['user_logo'] = $this->userLogoName;
            // Customer satisfaction
            $data['customer_satisfaction'] = $this->customerStf;
            // Store Uploaded Images
            $data['images'] = !empty($this->fileNames) ? json_encode($this->fileNames) : null;
            $data['created_by'] = auth()->id();
            $company = Company::create($data);
            // $company->histories()->create($data);
            session()->flash('success', 'Record successfully created.');
            return $this->redirectRoute('company-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function updateRecord()
    {
        $salesCount = Ticket::where('company_id', $this->id)->whereIn('dept_id', [$this->salesDeptId, $this->s3DeptId])->count();
        $sparePartsCount = Ticket::where('company_id', $this->id)->whereIn('dept_id', [$this->sparePartsDeptId, $this->afterSalesDeptId])->count();
        $serviceCount = Ticket::where('company_id', $this->id)->whereIn('dept_id', [$this->serviceDeptId, $this->afterSalesDeptId])->count();
        if (!empty($this->form['sales_note']) && $salesCount < 1) {
            $this->dispatch('alert-update');
        } elseif (!empty($this->form['spare_note']) && $sparePartsCount < 1) {
            $this->dispatch('alert-update');
        } elseif (!empty($this->form['service_note']) && $serviceCount < 1) {
            $this->dispatch('alert-update');
        } else {
            $this->dispatch('update');
        }
    }

    #[On('update')]
    public function update()
    {

        $this->validate([
            'form.company_name' => 'required|max:100|unique:companies,company_name,' . $this->id,
            // 'form.contact_person' => 'required|max:100',
            'form.customer_code' => 'required|max:20|unique:companies,customer_code,' . $this->id,
            // 'form.mobile_no' => 'required|numeric|digits:10|unique:companies,mobile_no,' . $this->id,
            // 'form.email' => 'required|email:rfx,dns|max:30|unique:companies,email,' . $this->id,
            // 'form.crid' => 'sometimes|unique:companies,crid,' . $this->id,
        ], [
            'form.company_name.required' => 'The company name field is required.',
            'form.company_name.max' => 'The company name field must not be greater than 100.',
            'form.company_name.unique' => 'The company name has already been taken.',
            'form.contact_person.required' => 'The contact person field is required.',
            'form.contact_person.max' => 'The contact person field must not be greater than 100.',
            'form.customer_code.required' => 'The customer code field is required.',
            'form.customer_code.max' => 'The customer code field must not be greater than 20.',
            'form.customer_code.unique' => 'The customer code has already been taken.',
            'form.mobile_no.required' => 'The mobile number field is required.',
            'form.mobile_no.numeric' => 'The mobile number field must be a number.',
            'form.mobile_no.digits' => 'The mobile number field must not be greater than 10.',
            'form.mobile_no.unique' => 'The mobile no has already been taken.',
            'form.email.email' => 'The email field must be a valid email address.',
            'form.email.max' => 'The email field must not be greater than 30 characters.',
            'form.email.unique' => 'The email has already been taken.',
            'form.crid.unique' => 'The CR/ID has already been taken.',
        ]);

        try {
            $data = $this->form;
            // Coordinates
            if (!empty($this->coordinates)) {
                $data['coordinates'] = json_encode($this->coordinates);
                // dd($data['coordinates']);
            } else {
                $data['coordinates'] = null;
            }
            $data['selected_coordinates'] = $this->selectedCoordinate;
            // Customer satisfaction
            $data['customer_satisfaction'] = $this->customerStf;
            // User Logo Store
            $data['user_logo'] = $this->userLogoName;
            // Store Uploaded Images
            $data['images'] = !empty($this->fileNames) ? json_encode($this->fileNames) : null;
            $data['updated_by'] = auth()->id();
            unset($data['id']);
            unset($data['created_at']);
            unset($data['updated_at']);
            if (!empty($this->coordinates)) {
                $data['coordinates'] = json_encode($this->coordinates);
            }
            $this->event['customer_satisfaction'] = $this->customerStf;
            $eventUpdateData = $this->event;
            $isFeedback = false;
            if (!empty($this->eventid)) {
                $eventUpdateData['updated_by'] = auth()->id();
                $data['event_id'] = $this->eventid;
                $eventUpdate = CustomerEvent::where('id', $this->eventid)->first();
                $isFeedback = $eventUpdate->update($eventUpdateData);
            } elseif (!empty($this->event['visit_date']) && !empty($this->customerStf)) {
                $isFeedback = CustomerEvent::where('company_id', $this->id)
                    ->where('visit_date', $this->event['visit_date'])
                    ->update(['customer_satisfaction' => $this->customerStf, 'updated_by' => auth()->id()]);
            }
            $company = Company::where('id', $this->id)->first();
            $company->update($data);
            if ($isFeedback && !empty($this->customerStf) && ($this->oldCustomerStf != $this->customerStf)) {
                if ($data['sales_note'] != null) {
                    $data['sales_dept_person_id'] = $this->salesUserId;
                }
                if ($data['service_note'] != null) {
                    $data['service_dept_person_id'] = $this->serviceUserId;
                }
                if ($data['spare_note'] != null) {
                    $data['spare_dept_person_id'] = $this->sparePartsUserId;
                }
                $company->histories()->create($data);
            }
            // // Remove old user logo
            // if (!empty($this->removedUserLogo)) {
            //     foreach ($this->removedUserLogo as $name) {
            //         $fullPath = 'public/user-logo/' . $name;
            //         Storage::delete($fullPath);
            //     }
            // }
            // // Remove old images
            // if (!empty($this->removedImages)) {
            //     foreach ($this->removedImages as $name) {
            //         $fullPath = 'public/images/' . $name;
            //         Storage::delete($fullPath);
            //     }
            // }

            session()->flash('success', 'Record successfully updated.');
            return $this->redirectRoute('company-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function assignSales()
    {
        try {
            if (empty($this->id)) {
                $this->dispatch('alert', type: 'error', message: 'To assign the ticket, you need to first create a new record or select an existing one.');
                return;
            } elseif ($this->salesUserId == '' || $this->salesUserId == null) {
                $this->dispatch('alert', type: 'error', message: 'To assign the ticket, you need to select sales person.');
                return;
            }
            $company = Company::where('id', $this->id)->first();
            $company->update(['sales_note' => $this->form['sales_note']]);

            $user = User::find($this->salesUserId);
            $deptId = $user->department_id;
            // Check if the user already has a ticket for the current visit date and department
            $currentDate = Carbon::now()->format('Y-m-d');
            $createdBy = auth()->id();
            $existingTicket = Ticket::where('company_id', $this->id)
                ->where('dept_id', $deptId)
                ->where('user_id', $this->salesUserId)
                ->where('created_by', $createdBy)
                ->where(DB::raw('date(created_at)'), $currentDate)
                ->first();
            // If no existing ticket is found, create a new one
            if (!$existingTicket) {
                $ticket = Ticket::create([
                    'company_id' => $this->id,
                    'dept_id' => $deptId,
                    'user_id' => $this->salesUserId,
                    'note' => $this->form['sales_note'],
                    'created_by' => $createdBy,
                    'related_dept_id' => $this->salesDeptId,
                ]);
                $this->sendNotification($this->salesUserId,  $this->salesUsers, $ticket->id);
                $this->dispatch('alert', type: 'success', message: 'The ticket has been assigned successfully.');
            } else {
                $this->dispatch('alert', type: 'error', message: 'This user already has a ticket assigned.');
            }
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function assignSpareParts()
    {
        try {
            if (empty($this->id)) {
                $this->dispatch('alert', type: 'error', message: 'To assign the ticket, you need to first create a new record or select an existing one.');
                return;
            } elseif ($this->sparePartsUserId == '' || $this->sparePartsUserId == null) {
                $this->dispatch('alert', type: 'error', message: 'To assign the ticket, you need to select spare parts person.');
                return;
            }
            $company = Company::where('id', $this->id)->first();
            $company->update(['spare_note' => $this->form['spare_note']]);

            $user = User::find($this->sparePartsUserId);
            $deptId = $user->department_id;

            // Check if the user already has a ticket for the current visit date and department
            $currentDate = Carbon::now()->format('Y-m-d');
            $createdBy = auth()->id();
            $existingTicket = Ticket::where('company_id', $this->id)
                ->where('dept_id', $deptId)
                ->where('user_id', $this->sparePartsUserId)
                ->where('created_by', $createdBy)
                ->where(DB::raw('date(created_at)'), $currentDate)
                ->first();
            // If no existing ticket is found, create a new one
            if (!$existingTicket) {
                $ticket = Ticket::create([
                    'company_id' => $this->id,
                    'dept_id' => $deptId,
                    'user_id' => $this->sparePartsUserId,
                    'note' => $this->form['spare_note'],
                    'created_by' => $createdBy,
                    'related_dept_id' => $this->sparePartsDeptId,
                ]);
                $this->sendNotification($this->sparePartsUserId,  $this->sparePartsUsers, $ticket->id);
                $this->dispatch('alert', type: 'success', message: 'The ticket has been assigned successfully.');
            } else {
                $this->dispatch('alert', type: 'error', message: 'This user already has a ticket assigned.');
            }
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function assignService()
    {
        try {
            if (empty($this->id)) {
                $this->dispatch('alert', type: 'error', message: 'To assign the ticket, you need to first create a new record or select an existing one.');
                return;
            } elseif ($this->serviceUserId == '' || $this->serviceUserId == null) {
                $this->dispatch('alert', type: 'error', message: 'To assign the ticket, you need to select service person.');
                return;
            }
            $company = Company::where('id', $this->id)->first();
            $company->update(['service_note' => $this->form['service_note']]);

            $user = User::find($this->serviceUserId);
            $deptId = $user->department_id;

            // Check if the user already has a ticket for the current visit date and department
            $currentDate = Carbon::now()->format('Y-m-d');
            $createdBy = auth()->id();
            $existingTicket = Ticket::where('company_id', $this->id)
                ->where('dept_id', $deptId)
                ->where('user_id', $this->serviceUserId)
                ->where('created_by', $createdBy)
                ->where(DB::raw('date(created_at)'), $currentDate)
                ->first();
            // If no existing ticket is found, create a new one
            if (!$existingTicket) {
                $ticket = Ticket::create([
                    'company_id' => $this->id,
                    'dept_id' => $deptId,
                    'user_id' => $this->serviceUserId,
                    'note' => $this->form['service_note'],
                    'created_by' => $createdBy,
                    'related_dept_id' => $this->serviceDeptId,
                ]);
                $this->sendNotification($this->serviceUserId,  $this->serviceUsers, $ticket->id);
                $this->dispatch('alert', type: 'success', message: 'The ticket has been assigned successfully.');
            } else {
                $this->dispatch('alert', type: 'error', message: 'This user already has a ticket assigned.');
            }
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    private function sendNotification($id, $recipientEmails, $ticketId)
    {
        $recipientEmails = ($id == 0) ? $recipientEmails : $recipientEmails->where('id', $id);
        $recipientEmails = $recipientEmails->pluck('email')->toArray();
        if (count($recipientEmails) > 0) {
            event(new TicketAssigned($recipientEmails, $ticketId));
        }
    }

    #[On('triggerBookEvent')]
    public function triggerBookEvent($companyId)
    {
        $this->companyid = $companyId;
        $this->visit_date = '';
        $this->visit_time = '';
        $this->dispatch('showBookEventModal', ['companyId' => $this->companyid]);
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
                return $this->redirectRoute('company-edit', ['id' => $this->id], navigate: true);
            }
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.company.company-add')
            ->title($this->title);
    }
}
