<?php

namespace App\Livewire\History;

use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Department;
use App\Models\History;
use App\Models\User;
use Livewire\Component;

class HistoryView extends Component
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
    public $customerStf;

    public function mount($id)
    {
        $data = History::with('event')->findOrFail($id);
        if (!empty($data->event_id)) {
            $this->customerStf = $data->event->customer_satisfaction;
            $data['visit_date'] = $data->event->visit_date;
            $data['visit_time'] = $data->event->visit_time;
        }
        $this->form = $data->toArray();
        $this->userLogoName = $this->form['user_logo'];
        $this->fileNames = !empty($this->form['images']) ? json_decode($this->form['images'], true) : [];
        $this->salesUserId = $this->form['sales_dept_person_id'];
        $this->sparePartsUserId = $this->form['spare_dept_person_id'];
        $this->serviceUserId = $this->form['service_dept_person_id'];

        // Get All Dept Users
        // Sales
        $this->salesUsers = User::whereHas('dept', function ($query) {
            return $query->whereIn('slug', [AppHelper::SALES, AppHelper::S3]);
        })->get();
        // Spare Parts
        $this->sparePartsUsers = User::whereHas('dept', function ($query) {
            return $query->where('slug', AppHelper::SPARE_PARTS);
        })->get();
        // Service
        $this->serviceUsers = User::whereHas('dept', function ($query) {
            return $query->where('slug', AppHelper::SERVICE);
        })->get();

        $this->branchList = Branch::all();
    }

    public function getDepartmentsProperty()
    {
        return Department::all();
    }

    public function render()
    {
        return view('livewire.history.history-view');
    }
}
