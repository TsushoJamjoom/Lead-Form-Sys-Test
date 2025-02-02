<?php

namespace App\Helpers;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Laravel\Prompts\Progress;

class AppHelper
{
    # Roles
    const SUPER_ADMIN = 'super_admin';
    const DEPARTMENT = 'department';
    const STAFF = 'staff';

    // Department
    const SALES = 'sales';
    const SPARE_PARTS = 'spare_parts';
    const SERVICE = 'service';
    const AFTER_SALES = 'after_sales';
    const S3 = '3S';

    // Positions
    const ADMIN = 'admin';
    const DIRECTOR = 'director';
    const MANAGER = 'manager';
    const NORMAL_USER = 'normal_user';

    const RIYADH = 'Riyadh';
    const JEDDAH = 'Jeddah';
    const DAMMAM = 'Dammam';
    const KHAMIS_MUSHAIT = 'Khamis Mushait';
    const KSA = 'KSA';


    const NOT_FOUND = 'Record not found';


    const PROGRESS_STAGES = [
        '10' => '10%',
        '20' => '20%',
        '30' => '30%',
        '40' => '40%',
        '50' => '50%',
        '60' => '60%',
        '70' => '70%',
        '80' => '80%',
        '90' => '90%',
        '100' => '100%'
    ];

    /**
     * Get role list function
     *
     * @return array
     */
    public static function getRoles()
    {
        return [
            self::SUPER_ADMIN => 'Super Admin',
            self::DEPARTMENT => 'Department',
            self::STAFF => 'Staff',
        ];
    }

    /**
     * Get role name function
     *
     * @return string
     */
    public static function getRole($key)
    {
        $roles = self::getRoles();
        return $roles[$key] ?? '-';
    }

    /**
     * Check permission for super user function
     *
     * @return boolean
     */
    public static function isPermission()
    {
        return (auth()->user()->role == self::SUPER_ADMIN);
    }

    /**
     * Get position list function
     *
     * @return array
     */
    public static function getPositions()
    {
        return [
            self::DIRECTOR => 'Director',
            self::MANAGER => 'Manager',
            self::NORMAL_USER => 'Normal User',
        ];
    }

    /**
     * Get position name function
     *
     * @return string
     */
    public static function getPosition($key)
    {
        $positions = self::getPositions();
        return $positions[$key] ?? '-';
    }

    /**
     * Check is manager function
     *
     * @param slug $position
     * @return boolean
     */
    public static function isManager($position)
    {
        return (self::MANAGER == $position);
    }

    public static function isSalesUser($user)
    {
        $salesDeptId = Department::where('slug', AppHelper::SALES)->first()->id;
        return $user->role == self::DEPARTMENT && $user->department_id == $salesDeptId;
    }

    public static function getPreviousUrl($routeName)
    {
        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        $newUrl = route($routeName);
        return ($previousUrl != $currentUrl) ? $previousUrl : $newUrl;
    }

    public static function isSalesDeptUser($user)
    {
        return !empty($user->dept) && $user->dept->slug == self::SALES;
    }

    public static function isStaffUser($user)
    {
        return !empty($user->character) && $user->character->slug == self::STAFF;
    }

    public static function getPercentageValue($baseValue, $inoutValue)
    {
        if ($baseValue > 0) {
            $percentage = (($inoutValue / $baseValue) * 100);
            return round($percentage);
        }
        return 0;
    }

    public static function getSalesAnd3sUser()
    {
        $roleId = Role::where('slug', AppHelper::STAFF)->first()->id;
        $salesDeptId = Department::whereIn('slug', [AppHelper::SALES, AppHelper::S3])->pluck('id');
        return User::where('role_id', $roleId)
            ->whereIn('department_id', $salesDeptId)
            ->when(AppHelper::isSalesDeptUser(auth()->user()), function ($query) {
                return $query->where('role', '!=', 'TJT');
            })
            ->get();
    }

    public static function getSatisfactionScale($scale = '')
    {
        $value = '';
        if (!empty($scale)) {
            switch ($scale) {
                case 1:
                    $value = 'Very Poor';
                    break;
                case 2:
                    $value = 'Poor';
                    break;
                case 3:
                    $value = 'Normal';
                    break;
                case 4:
                    $value = 'Average';
                    break;
                case 5:
                    $value = 'Excellent';
                    break;
                case 6:
                    $value = 'Failed';
                    break;
            }
        }

        return $value;
    }

    public static function getAddress($latLong)
    {
        // Get the API key from the .env file
        $apiKey = env('ADDRESS_API_KEY');

        // Extract latitude and longitude from $latLong array
        $latitude = trim($latLong[0]);
        $longitude = trim($latLong[1]);

        // Build the dynamic URL with latitude, longitude, and API key
        $url = "https://apina.address.gov.sa/NationalAddress/v3.1/address/address-geocode?language=E&format=JSON&lat={$latitude}&long={$longitude}&api_key={$apiKey}&encode=utf8";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        if ($error) {
            return null;
        }
        // Decode the JSON response
        return json_decode($response, true);
    }
}
