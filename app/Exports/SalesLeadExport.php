<?php

namespace App\Exports;

use App\Helpers\AppHelper;
use App\Models\SalesLead;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesLeadExport implements FromCollection, WithHeadings
{
    public $user;
    public $headerNames = [
        'Sr. No',
        'Create Date',
        'Sales User',
        'Branch',
        'Progress',
        'Company name',
        'Model'
    ];
    public $months = [];
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct()
    {
        $currentMonth = \Carbon\Carbon::now();

        for ($i = 0; $i < 5; $i++) {
            $this->headerNames[] = $currentMonth->copy()->addMonthsNoOverflow($i)->format('F');
            $this->months[] = $currentMonth->copy()->addMonthsNoOverflow($i)->format('F');
        }

        $this->headerNames[] = 'Qty';
        $this->headerNames[] = 'Comment';
    }


    public function collection()
    {
        $this->user = Auth::user();
        return SalesLead::withCount(['followups'])->with(['company.salesUser.branch'])
            ->whereHas('company')
            ->where('status', 0)
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where('sales_user_id', $this->user->id)
                        ->orWhere('sales_user_id', 0);
                });
            })
            ->orderBy('followups_count', 'asc')
            ->get()->map(function ($data, $key) {



                $dataMonth = \Carbon\Carbon::create()
                    ->month($data->sales_month)
                    ->format('F');
                foreach ($this->months as $month) {
                    $monthValue[$month] = ($month == $dataMonth) ? $data->qty : '-';
                }

                $maindata = [
                    'sr_no' => ($key + 1),
                    'date' => \Carbon\Carbon::parse($data->created_at)->format('Y-m-d'),
                    'sales_user' => $data->company->salesUser->name ?? 'All',
                    'branch' => $data->company->salesUser->branch->name ?? '-',
                    'progress' => $data->followups_count > 0 ?  $data->followups_count . 0 : '0',
                    'comapny_name' => $data->company->company_name,
                    'model' => $data->model,
                ];

                $maindata = array_merge($maindata, $monthValue, [
                    'qty' => $data->qty,
                    'comment' => $data->followups->isEmpty() ? $data->comment : $data->followups->last()->comment,
                ]);
                return $maindata;
            });
    }

    public function headings(): array
    {
        return $this->headerNames;
    }
}
