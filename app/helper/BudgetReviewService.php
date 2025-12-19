<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\ProcumentOrder;
use App\Models\BudgetTransferForm;
use App\Models\DocumentApproved;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\PurchaseRequest;
use App\Models\BudgetReviewTransferAddition;

class BudgetReviewService
{
	public static function notfifyBudgetBlockRemoval($documentSystemID, $documentSystemCode)
	{
		switch ($documentSystemID) {
			case 46:
				return self::notfifyBudgetBlockRemovalByTransfer($documentSystemCode);
				break;
			
			default:
				return ['status' => false, 'message' => "Document not set for budget review"];
				break;
		}

		return ['status' => true];
	}

	public static function notfifyBudgetBlockRemovalByTransfer($documentSystemCode)
	{
		$budgetTransfer = BudgetTransferForm::find($documentSystemCode);

		if (!$budgetTransfer) {
			return ['status' => false, 'message' => "Budget Transfer Not found"];
		}

		$checkFromReview = BudgetReviewTransferAddition::where('budgetTransferAdditionID', $documentSystemCode)
													   ->where('budgetTransferType', 1)
													   ->get();


		$employees = [];
		$documents = "";
		if (count($checkFromReview) > 0) {
			foreach ($checkFromReview as $key => $value) {
				if (in_array($value->documentSystemID, [2,5,52])) {
					$purchaseOrder = ProcumentOrder::find($value->documentSystemCode);

					if ($purchaseOrder && !is_null($purchaseOrder->createdUserSystemID)) {
						$temp['employeeSystemID'] = $purchaseOrder->createdUserSystemID;
						$temp['documentSystemCode'] = $value->documentSystemCode;
						$temp['documentCode'] = $purchaseOrder->purchaseOrderCode;

						$employees[] = $temp;
					}

					if ($purchaseOrder && !is_null($purchaseOrder->poConfirmedByEmpSystemID)) {
						$temp['employeeSystemID'] = $purchaseOrder->poConfirmedByEmpSystemID;
						$temp['documentSystemCode'] = $value->documentSystemCode;
						$temp['documentCode'] = $purchaseOrder->purchaseOrderCode;

						$employees[] = $temp;
					}

					$documentApproved = DocumentApproved::where("documentSystemID", $purchaseOrder->documentSystemID)
						                                ->where("documentSystemCode", $value->documentSystemCode)
						                                ->get();

					foreach ($documentApproved as $key1 => $value1) {
						$approvalList = EmployeesDepartment::where('employeeGroupID', $value1->approvalGroupID)
                                            ->whereHas('employee', function($q) {
                                                $q->where('discharegedYN',0);
                                            })
                                            ->where('companySystemID', $value1->companySystemID)
                                            ->where('documentSystemID', $value1->documentSystemID)
                                            ->where('isActive', 1)
                                            ->where('removedYN', 0)
				                            ->with(['employee'])
				                            ->groupBy('employeeSystemID')
				                            ->get();

				         foreach ($approvalList as $key2 => $value2) {
				         	$temp['employeeSystemID'] = $value2->employeeSystemID;
							$temp['documentSystemCode'] = $value->documentSystemCode;
							$temp['documentCode'] = $purchaseOrder->purchaseOrderCode;

							$employees[] = $temp;
				         }
					}
				} else {
					$purchaseRequest = PurchaseRequest::find($value->documentSystemCode);

					if ($purchaseRequest && !is_null($purchaseRequest->createdUserSystemID)) {
						$temp['employeeSystemID'] = $purchaseRequest->createdUserSystemID;
						$temp['documentSystemCode'] = $value->documentSystemCode;
						$temp['documentCode'] = $purchaseRequest->purchaseRequestCode;

						$employees[] = $temp;
					}

					if ($purchaseRequest && !is_null($purchaseRequest->PRConfirmedBySystemID)) {
						$temp['employeeSystemID'] = $purchaseRequest->PRConfirmedBySystemID;
						$temp['documentSystemCode'] = $value->documentSystemCode;
						$temp['documentCode'] = $purchaseRequest->purchaseRequestCode;

						$employees[] = $temp;
					}

					$documentApproved = DocumentApproved::where("documentSystemID", $purchaseRequest->documentSystemID)
						                                ->where("documentSystemCode", $value->documentSystemCode)
						                                ->get();

					foreach ($documentApproved as $key1 => $value1) {
						$approvalList = EmployeesDepartment::where('employeeGroupID', $value1->approvalGroupID)
                                            ->whereHas('employee', function($q) {
                                                $q->where('discharegedYN',0);
                                            })
                                            ->where('companySystemID', $value1->companySystemID)
                                            ->where('documentSystemID', $value1->documentSystemID)
                                            ->where('isActive', 1)
                                            ->where('removedYN', 0)
				                            ->with(['employee'])
				                            ->groupBy('employeeSystemID')
				                            ->get();

				         foreach ($approvalList as $key2 => $value2) {
				         	$temp['employeeSystemID'] = $value2->employeeSystemID;
							$temp['documentSystemCode'] = $value->documentSystemCode;
							$temp['documentCode'] = $purchaseRequest->purchaseRequestCode;

							$employees[] = $temp;
				         }
					}
				}
			}

			$employeeWiseDocs = collect($employees)->groupBy('employeeSystemID');
			$finalEmployees = [];
			foreach ($employeeWiseDocs as $key => $value) {
				$employees = collect($value)->groupBy('documentCode');
				$docs = "";
				foreach ($employees as $key1 => $value1) {
					$docs .= ($docs == "") ? $key1 : ", ".$key1;
				}

				$temp1['employeeSystemID'] = $key;
				$temp1['docs'] = $docs;

				$finalEmployees[] = $temp1;
			}

			foreach ($finalEmployees as $key => $value) {
				$employee = Employee::where('employeeSystemID', $value['employeeSystemID'])
                    ->where('discharegedYN', 0)
                    ->where('ActivationFlag', -1)
                    ->where('empLoginActive', 1)
                    ->where('empActive', 1)->first();

				if ($employee && !is_null($employee->empEmail)) {
					$dataEmail['empEmail'] = $employee->empEmail;

	                $dataEmail['companySystemID'] = $budgetTransfer->companySystemID;

	                $temp = "<p>Dear " . $employee->empName . ',</p><p>Please be informed that sufficient budget amounts have been allocated for following documents which are created/confirmed by you or pending for your approval</p><p>'.$value['docs'].'</p>';
	                $dataEmail['alertMessage'] = "Budget Allocation Approved";
	                $dataEmail['emailAlertMessage'] = $temp;
	                $sendEmail = \Email::sendEmailErp($dataEmail);
				}
			}
		}

		return ['status' => true];
	}
}