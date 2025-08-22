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
		$ids = BudgetControlInfo::where('companySystemID', $comapnySystemId)
                                 ->where('isChecked', 1)->pluck('id')->toArray();

        $existIds = BudgetControlLink::whereIn('controlId', $ids)->pluck('glAutoID')->toArray();
        return $existIds;
	}

    public static function checkIgnoreGL($id,$comapnySystemId,$name,$value)
	{
		return BudgetControlLink::where('companySystemID', $comapnySystemId)
                                 ->where('glAutoID', $id)->with('master')->whereHas('master', function($query) use($name, $value) {
                                        $query->where($name, $value)->where('isChecked', 1);
                                    })->exists();
   
	}


}