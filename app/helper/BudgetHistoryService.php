<?php

namespace App\helper;
use App\helper\Helper;
use Carbon\Carbon;
use App\Models\Budjetdetails;
use App\Models\BudgetDetailHistory;

class BudgetHistoryService
{
	public static function updateHistory($budgetmasterID)
	{
		$budgetDeatils = Budjetdetails::where('budgetmasterID', $budgetmasterID)
                                      ->get()
                                      ->toArray();

        $budgetDeatilsHistory = BudgetDetailHistory::insert($budgetDeatils);

		return ['status' => true];
	}
}