<?php

namespace App\Services\Inventory;

use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\MaterielRequest;
use PhpParser\Node\Expr\Array_;
use function foo\func;

class MaterialIssueService
{

    public static  function validateRequestWithQty($input):Array {
        $materielRequest = MaterielRequest::where('RequestID',$input['reqDocID'])->first();
        $totalQuantityRequested = $materielRequest->details->sum('quantityRequested');
        $materielIssue = ItemIssueMaster::with(['details'])->where('reqDocID',$input['reqDocID'])->get();
        $totalIssuedQty = 0;
        foreach ($materielIssue as $mi) {
            $totalIssuedQty += $mi->details->sum('qtyIssued');
        }

        if($totalQuantityRequested != 0 && ($totalQuantityRequested == $totalIssuedQty)) {
            return ['message' => 'Item/s fully issued for this request'];
        }
        return [];
    }

    public static  function getMaterialRequest($subCompanies,$request,$input,$confirmYn):Array {

        $search = $input['search'];

        $materielRequests = MaterielRequest::whereIn('companySystemID', $subCompanies)
            ->where("approved", -1)
            ->where("cancelledYN", 0)
            ->where("serviceLineSystemID", $request['serviceLineSystemID']);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $materielRequests = $materielRequests->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $materielRequests = $materielRequests->get(['RequestID', 'RequestCode']);
        $data = array();
        foreach ($materielRequests as $mr) {
            $totalQuantityRequested = $mr->details->sum('quantityRequested');
            $materielIssue = ItemIssueMaster::with(['details'])->where('reqDocID',$mr->RequestID)->get();
            $totalIssuedQty = 0;
            foreach ($materielIssue as $mi) {
                $totalIssuedQty += $mi->details->sum('qtyIssued');
            }

            if($confirmYn == 1) {
                array_push($data,$mr->only(['RequestCode','RequestID']));
            }else {
                if($totalQuantityRequested != 0 && ($totalQuantityRequested != $totalIssuedQty)) {
                    array_push($data,$mr->only(['RequestCode','RequestID']));
                }
            }


        }

        return $data;
    }



    public static function getItemDetailsForMaterialIssue($input):Array {
        $materielRequest = MaterielRequest::select(['RequestID'])->where('RequestID', $input['reqDocID'])->first();
        if(isset($materielRequest)) {
            $issuedQty = 0;
            $materielIssue = ItemIssueMaster::with(['details'])->where('reqDocID',$materielRequest->RequestID)->get();
            if($input['issueType'] == 2) {
                foreach($materielIssue as $mi) {
                    $item = $mi->details()->where('itemCodeSystem',$input['itemCodeSystem'])->first();
                    $issuedQty += isset($item->qtyIssued) ? (int) $item->qtyIssued : 0;
                }

                $input['issuedQty'] = $issuedQty;
                $input['qtyAvailableToIssue'] = (int) ($issuedQty == 0) ? $input['qtyRequested']: ($input['qtyRequested'] - $issuedQty);
                $input['qtyIssued'] = $input['qtyAvailableToIssue'];
                $input['qtyIssuedDefaultMeasure'] = $input['qtyAvailableToIssue'];
                return $input;

            }
        }
        return $input;
    }

    public static function getItemDetailsForMaterialIssueUpdate($input):Array {
        $materielIssueParent = ItemIssueMaster::where('itemIssueAutoID',$input['itemIssueAutoID'])->first();
        if(isset($materielIssueParent) && $materielIssueParent->issueType == 2) {
            $materielRequest = MaterielRequest::select(['RequestID'])->where('RequestID', $materielIssueParent->reqDocID)->first();
            if(isset($materielRequest)) {
                $materielAllIssues = ItemIssueMaster::with(['details'])->where('reqDocID',$materielRequest->RequestID)->get();
                $issuedQty = 0;
                if(count($materielAllIssues) == 1 ) {
                    $issuedQty = $input['qtyIssued'] ;
                }else {
                    $materielIssue = ItemIssueMaster::with(['details'])->where('reqDocID',$materielRequest->RequestID)->whereNotIn('itemIssueAutoID',[$input['itemIssueAutoID']])->get();
                    foreach($materielIssue as $mi) {
                        $item = $mi->details()->where('itemCodeSystem',$input['itemCodeSystem'])->first();
                        $issuedQty += isset($item->qtyIssued) ? (int) $item->qtyIssued : 0;
                    }
                }
                $input['qtyAvailableToIssue'] = (int) ($issuedQty == 0) ? $input['qtyRequested']: ($input['qtyRequested'] - ($issuedQty + $input['qtyIssued']));
                return $input;
            }
        }

        return $input;
    }

}
