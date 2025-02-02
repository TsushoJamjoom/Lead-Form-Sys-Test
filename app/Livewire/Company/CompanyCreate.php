<?php

namespace App\Livewire\Company;

use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;

class CompanyCreate extends Component
{
    public $company_name;
    public $customer_code;
    public $branch_id;
    public $sales_user_id;
    public $searchVal;
    #[Url]
    public $id;

    public function mount($id = null)
    {
        if ($id) {
            $data = Company::findOrFail($id);
            $this->fillData($data);
        }
    }

    public function getCompanyDropDownProperty()
    {
        return Company::createdMe()->orderBy('created_at', 'desc')->get();
    }

    public function getSalesUsersProperty()
    {
        return AppHelper::getSalesAnd3sUser();
    }

    public function create()
    {
        $data = $this->validate([
            'company_name' => 'required|max:100|unique:companies,company_name',
            'customer_code' => 'required|max:20|unique:companies,customer_code',
            'branch_id' => 'required|numeric',
            'sales_user_id' => 'required|numeric',
        ], [
            'branch_id.required' => 'The branch field is required.',
            'sales_user_id.required' => 'The sales user field is required.'
        ]);

        try {
            $data['created_by'] = auth()->id();
            $company = Company::create($data);
            // $company->histories()->create($data);
            session()->flash('success', 'Record successfully created.');
            return $this->redirectRoute('company-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function updatedSearchVal()
    {
        $this->clear();
        $data = Company::find($this->searchVal);
        if ($data) {
            $this->dispatch('alert', type: 'success', message: "The form has been filled out successfully.");
            $this->fillData($data);
        }
    }

    public function fillData($data)
    {
        $this->company_name = $data->company_name;
        $this->customer_code = $data->customer_code;
        $this->sales_user_id = $data->sales_user_id;
        $this->branch_id = $data->branch_id;
        $this->id = $data->id;
    }

    public function clear()
    {
        return $this->redirectRoute('company-create', navigate: true);
    }

    public function updateRecord()
    {
        $data = $this->validate([
            'company_name' => 'required|max:100|unique:companies,company_name,' . $this->id,
            'customer_code' => 'required|max:20|unique:companies,customer_code,' . $this->id,
            'branch_id' => 'required|numeric',
            'sales_user_id' => 'required|numeric',
        ], [
            'branch_id.required' => 'The branch field is required.',
            'sales_user_id.required' => 'The sales user field is required.'
        ]);

        try {
            $data['updated_by'] = auth()->id();
            $company = Company::where('id', $this->id)->first();
            $company->update($data);
            $data['created_by'] = $company->created_by;
            // $company->histories()->create($data);
            session()->flash('success', 'Record successfully updated.');
            return $this->redirectRoute('company-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function getBranchListProperty()
    {
        return Branch::all();
    }

    public function render()
    {
        return view('livewire.company.company-create');
    }
}
