<?php

namespace App\Exports;

use App\Helpers\AppHelper;
use App\Models\SalesLead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesLeadHistroyExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public $user;
    public $status;
    public $searchId;
    public $startDateFilter;
    public $endDateFilter;
    public $progressStage;

    public function __construct($status, $searchId, $startDateFilter, $endDateFilter, $progressStage)
    {
        $this->status = $status;
        $this->searchId = $searchId;
        $this->startDateFilter = $startDateFilter;
        $this->endDateFilter = $endDateFilter;
        $this->progressStage = $progressStage;
    }

    public function collection()
    {
        $this->user = Auth::user();
        return SalesLead::withCount(['followups'])->with(['company.salesUser.branch'])
            ->where('status', '!=', 0)
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where('sales_user_id', $this->user->id)
                        ->orWhere('sales_user_id', 0);
                });
            })
            ->when(!empty($this->status), function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->searchId, function ($query) {
                $query->where('company_id', $this->searchId);
            })
            ->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
                $query->whereBetween(DB::raw('date(created_at)'), [$this->startDateFilter, $this->endDateFilter]);
            })
            ->when(!empty($this->progressStage), function ($query) {
                $query->whereHas('followups', function ($q) {
                    return $q->where('value', ((int)$this->progressStage));
                });
            })
            ->orderBy('updated_at', 'desc')
            ->get()->map(function ($data, $key) {
                return [
                    'sr_no' => ($key + 1),
                    'date' => \Carbon\Carbon::parse($data->created_at)->format('Y-m-d'),
                    'progress' => $percent = $data->followups_count . 0,
                    'comapny_name' => $data->company->company_name,
                    'model' => $data->model,
                    'month' => \Carbon\Carbon::create()->month($data->sales_month)->format('F'),
                    'sales_user' => $data->company->salesUser->name ?? '-',
                    'sales_user_branch' => $data->company->salesUser->branch->name ?? '-',
                    'reason' => @$data->reason ?: "-",
                    'competitor_info' => @$data->competitor_info ?: "-",
                    'qty' => $data->qty,
                    'status' => (@$data->status && ($data->status == 1)) ? 'Archived' : 'Lost',
                    'comment' => $data->followups->isEmpty() ? $data->comment : $data->followups->last()->comment,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Sr. No',
            'Create Date',
            'Progress',
            'Company name',
            'Model',
            'Month',
            'Sales User',
            'Sales User Branch',
            'Reason',
            'Competitor Info',
            'Qty',
            'Status',
            'Comment'
        ];
    }
}
