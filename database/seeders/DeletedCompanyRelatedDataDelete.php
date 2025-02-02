<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CustomerEvent;
use App\Models\History;
use App\Models\SalesLead;
use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeletedCompanyRelatedDataDelete extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = Company::onlyTrashed()->get();
        if(!$data->isEmpty()){
            foreach($data as $company){
                $id = $company->id;
                Ticket::where('company_id', $id)->delete();
                SalesLead::where('company_id', $id)->delete();
                History::where('company_id', $id)->delete();
                CustomerEvent::where('company_id', $id)->delete();
            }
        }
    }
}
