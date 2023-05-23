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

        foreach (array_chunk($budgetDeatils,1000) as $chunkData)  
		{
        	$budgetDeatilsHistory = BudgetDetailHistory::insert($chunkData);
		}

		return ['status' => true];
	}
}