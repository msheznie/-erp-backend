<?php

namespace App\helper;
use App\helper\Helper;
use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use App\Models\BudgetControlInfo;
use App\Models\BudgetControlLink;

class BudgetControlService
{
	public static function checkControl($comapnySystemId)
	{
		if(!isset($comapnySystemId))
        {
            return [];
        }

        $ids = BudgetControlInfo::where('companySystemID', $comapnySystemId)
                                ->where('isChecked', 1)->pluck('id')->toArray();

        if (empty($ids)) {
            return [];
        }

        $existIds = BudgetControlLink::whereIn('controlId', $ids)->pluck('glAutoID')->toArray();
        return $existIds;
	}

    public static function checkIgnoreGL($id,$comapnySystemId,$name,$value)
	{
        if(!isset($comapnySystemId))
        {
            return false;
        }

		return BudgetControlInfo::where('companySystemID', $comapnySystemId)
                                        ->where($name, $value)
                                        ->where('isChecked', 1)
                                        ->where('controlType', $id)
                                        ->exists();
   
	}


}