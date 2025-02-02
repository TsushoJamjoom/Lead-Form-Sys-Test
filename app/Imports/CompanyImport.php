<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CompanyImport implements ToModel, WithHeadingRow, WithMapping, WithLimit, SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        if (array_key_exists('company_name', $row)) {
            // Check if the row is empty
            if (!array_filter($row)) {
                return null;
            }
            // Apply validation rules to the processed data
            $validator = Validator::make($row, $this->rules());
            // Check if validation fails
            if ($validator->fails()) {
                // Throw a validation exception with the error messages
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            // Only sales user assign for customer
            if (!empty($row['sales_user_id'])) {
                $salesUser = User::find($row['sales_user_id']);
                $isSalesUser = \App\Helpers\AppHelper::isSalesDeptUser($salesUser);
                $row['sales_user_id'] = ($isSalesUser) ? $salesUser->id : null;
            }

            // Specify the search criteria for finding or creating the record
            $searchCriteria = ['company_name' => $row['company_name'], 'customer_code' => $row['customer_code']];
            // Use updateOrCreate to find or create the record
            $company = Company::whereRaw('lower(company_name) = ?', [$row['company_name']])
                ->orWhereRaw('lower(customer_code) = ?', $row['customer_code'])
                ->first();
            if (!empty($company)) {
                $company->update($row);
            } else {
                $company = Company::create($row);
            }
            // $company = Company::updateOrCreate($searchCriteria, $row);
            // $company->histories()->create($row);
            return $company;
            // Create the model instance using the validated data
            return new Company($row);
        }
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:100',
            'customer_code' => 'required',
        ];
    }

    public function limit(): int
    {
        return 1000;
    }

    public function map($row): array
    {

        unset($row[1]);
        // Manually apply model casts
        if (array_key_exists('company_name', $row)) {
            foreach ($row as $key => $value) {
                if (array_key_exists($key, (new Company)->getCasts())) {
                    $row[$key] = !empty($value) ? 1 : 0;
                }
            }
            // Check if the row is empty
            if (!empty(array_filter($row))) {
                $row['mobile_no'] = $this->getPhone($row['mobile_no']);
                $row['l12m_parts_date'] = !empty($row['l12m_parts_date']) ? $this->getDate($row['l12m_parts_date']) : null;
                $row['l12m_service_date'] = !empty($row['l12m_service_date']) ? $this->getDate($row['l12m_service_date']) : null;
                $row['l12m_sales_date'] = !empty($row['l12m_sales_date']) ? $this->getDate($row['l12m_sales_date']) : null;
                $row['visit_date'] = !empty($row['visit_date']) ? $this->getDate($row['visit_date']) : null;
                $row['customer_voice'] = $row['cutomer_voice'];
                $row['created_by'] = auth()->id();
            }
        }
        return $row;
    }

    public function getDate($value, $format = 'Y-m-d')
    {
        if ($this->validateDate($value, $format)) {
            return $value;
        }
        return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $value)->format($format);
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function getPhone($value, $length = 10)
    {
        return substr(str_replace(' ', '', $value), 0, $length);
    }
}
