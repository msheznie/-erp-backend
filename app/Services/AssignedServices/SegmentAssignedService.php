<?php

namespace App\Services\AssignedServices;
use Carbon\Carbon;
use App\Models\CompanyPolicyMaster;
use App\Models\SegmentAssigned;
use App\Models\Company;
use App\Models\SegmentMaster;

class SegmentAssignedService
{
	public static function assignSegment($serviceLineSystemID, $companySystemID)
	{
    	$assignedData = [
	        'serviceLineSystemID' => $serviceLineSystemID,
	        'companySystemID'=> $companySystemID,
	        'isActive' => 1,
	        'isAssigned' => 1
	    ];

    	$res = SegmentAssigned::create($assignedData);
		
		return ['status' => true];
	}
}