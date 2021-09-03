<?php

namespace App\helper;

use App\Models\Company;

class CompanyService
{
    public static function hrIntegrated_company_count( $company_list ){
        return Company::selectRaw('COUNT(companySystemID), ')
            ->whereIn('companySystemID', $company_list)
            ->where('isHrmsIntergrated', 1)
            ->count();
    }
}
