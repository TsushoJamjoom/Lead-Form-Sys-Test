<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesPerformanceReport implements FromCollection, WithHeadings
{
    private $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->report->map(function($item, $key){
            return [
                'sr_no' => ($key + 1),
                'executive_name' => $item['name'],
                'assigned_customers' => $item['company_count'],
                'total_visits' => $item['events_count'],
                'high_frequency' => $item['highFrequency'],
                'low_frequency' => $item['lowFrequency'],
                'unattended' => $item['unattended'],
                'sales_lead' => $item['salesleads_count'],
                'pending_tickets' => $item['ticketCount'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Sr. No',
            'Executive Name',
            'Assigned Customers',
            'Total Visits',
            'High Frequency',
            'Low Frequency',
            'Unattended',
            'Sales Lead',
            'Pending Tickets',
        ];
    }
}
