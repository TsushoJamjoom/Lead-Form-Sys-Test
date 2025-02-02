<?php

namespace App\Livewire\Company;

use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Livewire\Component;

class CompanyView extends Component
{
    public $title = 'Company Details';
    public $form;
    public $userLogoName;
    public $fileNames = [];
    public $salesUserId;
    public $sparePartsUserId;
    public $serviceUserId;

    public $salesUsers;
    public $sparePartsUsers;
    public $serviceUsers;
    public $branchList = [];
    public $searchVal;

    public function mount($id)
    {
        $data = Company::with(['tickets'])->findOrFail($id);
        $this->form = $data->toArray();
        $this->userLogoName = $this->form['user_logo'];
        $this->fileNames = !empty($this->form['images']) ? json_decode($this->form['images'], true) : [];

        // Get All Dept Users
        // Sales
        $this->salesUsers = User::whereHas('dept', function ($query) {
            return $query->where('slug', AppHelper::SALES);
        })->get();
        // Spare Parts
        $this->sparePartsUsers = User::whereHas('dept', function ($query) {
            return $query->where('slug', AppHelper::SPARE_PARTS);
        })->get();
        // Service
        $this->serviceUsers = User::whereHas('dept', function ($query) {
            return $query->where('slug', AppHelper::SERVICE);
        })->get();

        // Get All Dept and Users
        $salesDeptId = Department::where('slug', AppHelper::SALES)->first()->id;
        $sparePartsDeptId = Department::where('slug', AppHelper::SPARE_PARTS)->first()->id;
        $serviceDeptId = Department::where('slug', AppHelper::SERVICE)->first()->id;

        if (!$data->tickets->isEmpty()) {
            foreach ($data->tickets as $ticket) {
                if ($ticket->dept_id == $salesDeptId) {
                    $this->salesUserId = $ticket->user_id;
                }
                if ($ticket->dept_id == $sparePartsDeptId) {
                    $this->sparePartsUserId = $ticket->user_id;
                }
                if ($ticket->dept_id == $serviceDeptId) {
                    $this->serviceUserId = $ticket->user_id;
                }
            }
        }

        $this->branchList = Branch::all();
    }

    public function getDepartmentsProperty()
    {
        return Department::all();
    }

    public function render()
    {
        return view('livewire.company.company-view')
        ->title($this->title);
    }
}
