<?php

namespace App\Services\Validation;

use App\Models\Company;

class CommonValidationService
{

    public function validateCompany($input)
    {
        if(!isset($input['companySystemID']))
            throw  new \Exception("Parameter Company ID not found");

        $company = Company::find($input['companySystemID']);

        if(empty($company))
            throw new \Exception("Company Details not found");

        if(!$company->isActive)
            throw new \Exception("Company is not active");

    }

    public function validateFinanicalYear($input)
    {
        if(!isset($input['companyFinanceYearID']))
            throw new \Exception("Parameter companyFinanceYearID is missing from the input");

        if(!isset($input['companySystemID']))
            throw new \Exception("Parameter companySystemID is missing from the input");

        try {
           return \Helper::companyFinancePeriodCheck($input);
        }catch (\Exception $exception)
        {
            return $exception;
        }


    }

    public function validateFinancialPeriod($input)
    {
        if(!isset($input['companyFinancePeriodID']))
            throw new \Exception("Parameter companyFinanceYearID is missing from the input");

        if(!isset($input['departmentSystemID']))
            throw new \Exception("Parameter departmentSystemID is missing from the input");

        if(!isset($input['companySystemID']))
            throw new \Exception("Parameter companySystemID is missing from the input");

        try {
            $data =  \Helper::companyFinancePeriodCheck($input);

            if(!$data['success'])
                throw new \Exception($data['message']);

            $input = collect($input)->merge([
                'FYBiggin' => $data['message']->dateFrom ?? 0,
                'FYEnd' => $data['message']->dateTo ?? 0,
            ])->toArray();

            return $input;

        }catch (\Exception $exception)
        {
            return $exception;
        }
    }



}
