<?php

namespace App\Exports;

use App\Helpers\AppHelper;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DashboardReportExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Company::with(['salesUser', 'event', 'salesLead.followups'])->withCount(['pendingTicketCount', 'salesLeadCount', 'archivedsalesLeadCount', 'lostsalesLeadCount'])->get()->map(function ($data, $key) {
            $latest = @$data->salesLead->where('status', 1)->first()->followups ? $data->salesLead->where('status', 1)->first()->followups->sortByDesc('created_at')->first() : null;
            $visitCount = @$data->event ? $data->event->where('customer_satisfaction', '!=', null)
                ->where('visit_date', '>=', \Carbon\Carbon::now()->subMonths(3)->startOfMonth())
                ->count() : '';
            // Determine visit category
            $visitValue = '';
            if (@$visitCount && $visitCount > 1) {
                $visitValue = 'High';
            } elseif (@$visitCount && $visitCount == 1) {
                $visitValue = 'Low';
            } elseif (@$visitCount && $visitCount == 0) {
                $visitValue = 'Unattended';
            }
            return [
                'sales_user_id' => $data->sales_user_id,
                'sales_user_name' => @$data->salesUser->name ?: "All",
                'customer_code' => $data->customer_code,
                'customer' => $data->company_name,
                'customer_type' => $visitValue,
                'number_of_visits_last_90_days' => $visitCount,
                'created_sales_lead' => $data->sales_lead_count_count,
                'achived_sales_lead' => $data->archivedsales_lead_count_count,
                'lost_sales_lead' => $data->lostsales_lead_count_count,
                'pending_ticket' => $data->pending_ticket_count_count,
                'total_num_of_hino_trucks' => $data->hino_total,
                'last_achieved_sales_lead_date' => $latest ? \Carbon\Carbon::parse($latest->created_at)->format('Y-m-d') : '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Sales User Id',
            'Sales User',
            'Customer code',
            'Customer',
            'Customer Type',
            'Number of visits last 90 days',
            'Created Sales Lead',
            'Achieved sales lead',
            'Lost sales lead',
            'Pending Tickets',
            'Total num of HINO trucks',
            'Last achieved sales lead date',
        ];
    }
}
