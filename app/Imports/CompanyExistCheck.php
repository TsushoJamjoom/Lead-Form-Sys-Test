<?php

namespace App\Imports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompanyExistCheck implements ToModel, WithHeadingRow, WithMapping, WithLimit, SkipsEmptyRows
{
    protected $existingCustomers = [];
    public function model(array $row)
    {
        // Check if the row is empty
        if (array_key_exists('company_name', $row)) {
            if (!array_filter($row)) {
                return null;
            }

            // Use updateOrCreate to find or create the record
            $company = Company::whereRaw('lower(company_name) = ?', [$row['company_name']])
                ->orWhereRaw('lower(customer_code) = ?', $row['customer_code'])
                ->first();

            if (!empty($company)) {
                $this->existingCustomers[] = $company->toArray();
            }
            return $company;
        }
    }

    public function map($row): array
    {
        unset($row[1]);
        return $row;
    }

    public function limit(): int
    {
        return 1000;
    }

    public function getExistingCustomers()
    {
        return $this->existingCustomers;
    }
}
