<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\CompanyDocumentAttachment;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\EnvelopType;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\SRMTenderCalendarLog;
use App\Models\SRMTenderPaymentProof;
use App\Models\TenderBoqItems;
use App\Models\TenderMaster;
use App\Models\TenderMasterSupplier;
use App\Models\TenderType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
/**
 * Class TenderMasterRepository
 * @package App\Repositories
 * @version March 10, 2022, 1:54 pm +04
 *
 * @method TenderMaster findWithoutFail($id, $columns = ['*'])
 * @method TenderMaster find($id, $columns = ['*'])
 * @method TenderMaster first($columns = ['*'])
 */
class TenderMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'title_sec_lang',
        'description',
        'description_sec_lang',
        'tender_type_id',
        'currency_id',
        'envelop_type_id',
        'procument_cat_id',
        'procument_sub_cat_id',
        'evaluation_type_id',
        'estimated_value',
        'allocated_budget',
        'budget_document',
        'tender_document_fee',
        'bank_id',
        'bank_account_id',
        'document_sales_start_date',
        'document_sales_end_date',
        'pre_bid_clarification_start_date',
        'pre_bid_clarification_end_date',
        'pre_bid_clarification_method',
        'site_visit_date',
        'bid_submission_opening_date',
        'bid_submission_closing_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id',
        'bid_opening_date',
        'bid_opening_end_date',
        'technical_bid_opening_date',
        'technical_bid_closing_date',
        'commerical_bid_opening_date',
        'commerical_bid_closing_date',
        'is_negotiation_started',
        'negotiation_published'

    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderMaster::class;
    }

    public function getTenderFilterData(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];

        $currency = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $selection = TenderType::select('id', 'name')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });

        $envelope = EnvelopType::select('id','name')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });

        $published =  array(
            array('value'=> 0 , 'label'=> 'Not Published'),
            array('value'=> 1 , 'label'=> 'Published'),
        );

        $tenderNegotiationStatus =  array(
            array('value'=> 1 , 'label'=> 'Negotiation Not Started'),
            array('value'=> 2 , 'label'=> 'Negotiation Started'),
            array('value'=> 3 , 'label'=> 'Negotiation Completed'),
        );

        $status =array(
            array('value'=> 1 , 'label'=> 'Not Confirmed'),
            array('value'=> 2 , 'label'=> 'Pending Approval'),
            array('value'=> 3 , 'label'=> 'Fully Approved'),
            array('value'=> 4 , 'label'=> 'Referred Back'),
            array('value'=> 5 , 'label'=> 'Rejected'),
        );

        $rfxTypes = array(
            array('value'=> 1 , 'label'=> 'RFQ'),
            array('value'=> 2 , 'label'=> 'RFI'),
            array('value'=> 3 , 'label'=> 'RFP'),
        );

        $gonogo = array(
            array('value'=> 1 , 'label'=> 'Not Completed'),
            array('value'=> 2 , 'label'=> 'Completed'),
        );

        $technical = array(
            array('value'=> 0 , 'label'=> 'Not Completed'),
            array('value'=> 1 , 'label'=> 'Completed'),
        );

        $stage = array(
            array('value'=> 1 , 'label'=> 'Single Stage'),
            array('value'=> 2 , 'label'=> 'Two Stage'),
        );

        $commercial = array(
            array('value'=> 0 , 'label'=> 'Not Completed'),
            array('value'=> 1 , 'label'=> 'Completed'),
        );

        $data = array(
            'currency' => $currency,
            'selection' => $selection,
            'envelope' => $envelope,
            'published' => $published,
            'status' => $status,
            'rfxTypes' => $rfxTypes,
            'technical' => $technical,
            'gonogo' => $gonogo,
            'stage' => $stage,
            'commercial' => $commercial,
            'tenderNegotiationStatus' => $tenderNegotiationStatus
        );

        return $data;
    }

    public function getTenderPr(Request $request){
        $input = $request->all();
        $tenderId = $input['tenderId'];
        $companyId = $input['companyId'];

        $data = PurchaseRequest::select('purchaseRequestID','companyID','purchaseRequestCode')
            ->with(['tender_purchase_request' => function ($query) use ($tenderId) {
                $query->where('tender_id', $tenderId);
            }])
            ->where('companySystemID',$companyId)
            ->whereHas('tender_purchase_request', function ($query) use ($tenderId) {
                $query->where('tender_id', $tenderId);
            })
            ->get();

        return $data;

    }

    public function getPurchaseRequestDetails(Request $request)
    {
        $purchaseRequestID = $request->input('purchaseRequestID');
        $tender_id = $request->input('tenderId');
        $main_work_id = $request->input('main_work_id');

        $purchaseRequestIDToCheck = $purchaseRequestID;

        $result = TenderBoqItems::where('tender_id', $tender_id)
            ->whereRaw("FIND_IN_SET('$purchaseRequestIDToCheck', purchase_request_id) > 0")
            ->where('main_work_id', $main_work_id)
            ->first();

        if ($result) {
            return [
                'success' => false,
                'message' => 'Line items are already added',
                'data' => ''
            ];
        }

        $pr = PurchaseRequestDetails::where('purchaseRequestID', $purchaseRequestID);

        $pr = $pr->with(['uom' , 'purchase_request'])->get()
            ->transform(function ($data) {
                return $this->prDetailFormat($data);
            });

        $result['prDetail'] = $pr;
        return [
            'success' => true,
            'message' => 'PR Details Retrieved',
            'data' => $result
        ];
    }

    public function prDetailFormat($data)
    {
        return [
            'purchaseRequestID' => $data['purchaseRequestID'],
            'purchaseRequestCode' => $data['purchase_request']['purchaseRequestCode'],
            'purchaseRequestDetailsID' => $data['purchaseRequestDetailsID'],
            'itemPrimaryCode' => $data['itemPrimaryCode'],
            'itemDescription' => $data['itemDescription'],
            'noQty' => $data['quantityRequested'],
            'unitID' => $data['uom']['UnitID'],
            'unitShortCode' => $data['uom']['UnitShortCode'],
            'item_id' => $data['itemCode']
        ];
    }

    public static function getTenderDidOpeningDates($tenderId, $companyId)
    {
        $current_date = Carbon::now();
        $tender = TenderMaster::getTenderDidOpeningDates($tenderId, $companyId);

        if (!$tender) {
            return [
                'error' => 'Tender not found.',
            ];
        }

        $opening_date_comp = $tender->stage === 1 ? $tender->bid_opening_date : $tender->technical_bid_opening_date;
        $opening_date_comp_end = $tender->stage === 1 ? $tender->bid_opening_end_date : $tender->technical_bid_closing_date;
        return $current_date->gt($opening_date_comp) && ($opening_date_comp_end === null || $opening_date_comp_end->gt($current_date));
    }


    public static function getTenderPOData($tenderId, $companyId)
    {
        return TenderMaster::getTenderPOData($tenderId, $companyId);
    }
    public function getPaymentProofDocumentApproval($request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empId = \Helper::getEmployeeSystemID();
        $tenderPaymentProof =  SRMTenderPaymentProof::getTenderPaymentReview($companyId,$empId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $tenderPaymentProof = $tenderPaymentProof->where(function($query) use ($search) {
                $query->where('tm.title', 'LIKE', "%{$search}%")
                    ->orWhere('tm.tender_code', 'LIKE', "%{$search}%");
            });
        }
        return \DataTables::of($tenderPaymentProof)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getSupplierWiseProofNotApproved($request)
    {
        $input = $request->all();
        $empId = \Helper::getEmployeeSystemID();
        $companyId = $input['companyId'];
        $tenderUuid = $input['uuid'];
        $tenderData = TenderMaster::getTenderByUuid($tenderUuid);


        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $supplierPaymentProof = SRMTenderPaymentProof::getSupplierWiseDataNotApproved($companyId,$empId,$tenderData['id']);
        return \DataTables::of($supplierPaymentProof)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function approveSupplierWiseTender($request)
    {
        $input = $request->all();
        $data = $this->prepareDocumentData($input);

        unset($data['approvedComments']);
        $data['approvedComments'] = ($input['approvedComments']) ?? null;

        $approve = \Helper::approveDocument($data);

        if ($approve['data'] && $approve['data']['numberOfLevels'] == $approve['data']['currentLevel']) {
            $this->purchaseTender($request);
        }

        return ['success' => $approve["success"], 'message' => $approve["message"], 'data'=> $approve];
    }

    public function purchaseTender($request)
    {
        $input = $request->all();
        $getPaymentProofDocument = SRMTenderPaymentProof::getPaymentProofDataByUuid($input['uuid']);
        $result = DB::transaction(function () use ($getPaymentProofDocument) {
            $data = [
                'tender_master_id' => $getPaymentProofDocument['tender_id'],
                'purchased_date' =>  Carbon::parse(now())->format('Y-m-d H:i:s'),
                'purchased_by' => $getPaymentProofDocument['srm_supplier_id'],
                'created_by' => $getPaymentProofDocument['srm_supplier_id']
            ];
            TenderMasterSupplier::create($data);
        });
    }

    public function rejectSupplierWiseTender($request)
    {
        $input = $request->all();
        $data = DocumentApproved::getDocumentApprovedData($input['documentApCode']);
        $data = $this->prepareDocumentData($input);
        unset($data['rejectedComments']);
        $data['rejectedComments'] = ($input['rejectedComments']) ?? null;

        $approve = \Helper::rejectDocument($data);
        return ['success' => $approve["success"], 'message' => $approve["message"], 'data'=> $approve];
    }

    protected function prepareDocumentData($input)
    {
        $documentData = DocumentApproved::getDocumentApprovedData($input['documentApCode']);
        $paymentProofData = SRMTenderPaymentProof::getPaymentProofDataByUuid($input['uuid']);
        $tenderData = TenderMaster::getTenderDidOpeningDates(
            $paymentProofData['tender_id'] ?? null,
            $paymentProofData['company_id'] ?? null
        );

        $documentData->tenderCode = $tenderData->tender_code ?? null;
        $documentData->tenderTitle = $tenderData->title ?? null;
        $documentData->supplierName = $input['supplierName'] ?? null;

        return $documentData;
    }

    public function getSupplierWiseProofApproved($request)
    {
        $input = $request->all();
        $empId = \Helper::getEmployeeSystemID();
        $companyId = $input['companyId'];
        $tenderUuid = $input['uuid'];
        $tenderData = TenderMaster::getTenderByUuid($tenderUuid);


        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $supplierPaymentProof = SRMTenderPaymentProof::getSupplierWiseDataApproved($companyId,$empId,$tenderData['id']);
        return \DataTables::of($supplierPaymentProof)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function updateTenderCalendarDays($request)
    {
        try
        {
            $input = $request->all();
            $tenderData = TenderMaster::getTenderByUuid($input['tenderCode']);


            if(empty($tenderData)){
                return ['success' => false, 'message' => 'Tender data not found'];
            }

            $formattedDatesAndTime = $this->getFormattedDatesAndTime($input,$tenderData);
            $validationResult = $this->validateTenderDates($formattedDatesAndTime,$tenderData);
            if (!$validationResult['success']) {
                return $validationResult;
            }

            $updatedData = $this->processTenderUpdate($formattedDatesAndTime, $tenderData,$input);

            return [
                'success' => true,
                'message' => 'Tender calendar days updated successfully.',
                'data' => $updatedData
            ];

        }
        catch(\Exception $e)
        {
            return ['success' => false, 'message' => $e->getMessage(),];
        }
    }

    private function validateTenderDates($data,$tenderData)
    {
        $submissionClosingDate = $data['submissionClosingDate'];
        $submissionOpeningDate = $data['submissionOpeningDate'];
        $bidOpeningStartDate = $data['bidOpeningStartDate'];
        $bidOpeningEndDate = $data['bidOpeningEndDate'];

        $technicalStartDate = $data['technicalStartDate'];
        $technicalEndDate = $data['technicalEndDate'];
        $commercialStartDate = $data['commercialStartDate'];
        $commercialEndDate = $data['commercialEndDate'];
        $employee = \Helper::getEmployeeInfo();


        $bidClosingDate = $tenderData['bid_submission_closing_date'];
        $currentDate = (Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()));

        if ($employee->isSuperAdmin != -1) {
            return ['success' => false, 'message' => 'You do not have permission to edit this record'];
        }

        if ($submissionOpeningDate > $submissionClosingDate) {
            return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Bid Submission'];
        }

        if($tenderData['stage'] == 1)
        {
            if ($submissionClosingDate >= $bidOpeningStartDate) {
                return ['success' => false, 'message' => 'Bid Opening from date and time should greater than bid submission to date and time'];
            }

            if ($bidOpeningStartDate > $bidOpeningEndDate) {
                return ['success' => false, 'message' => 'Bid Opening to date and time should greater than bid opening from date and time'];
            }
        }


        if($tenderData['stage'] == 2)
        {

            if (!is_null($commercialEndDate)) {
                if ($commercialStartDate > $commercialEndDate) {
                    return ['success' => false, 'message' => 'Commercial Bid Opening to date and time should greater than commercial bid opening from date and time'];
                }
            }

            if ($submissionClosingDate > $technicalStartDate) {
                return ['success' => false, 'message' => 'Technical Bid Opening from date and time should greater than bid submission from date and time'];
            }


            if (is_null($technicalEndDate)) {
                if ($technicalStartDate > $commercialStartDate) {
                    return ['success' => false, 'message' => 'Commercial Bid Opening from date and time should be greater than technical bid from date and time'];
                }
            } else {
                if (($technicalEndDate >= $commercialStartDate)) {
                    return ['success' => false, 'message' => 'Commercial Bid Opening from date and time should be greater than technical bid to date and time'];
                }
            }


            if (!empty($technicalStartDate) && !empty($technicalEndDate)) {
                if ($technicalStartDate > $technicalEndDate) {
                    return ['success' => false, 'message' => 'Technical Bid Opening to date and time should greater than Technical Bid Opening from date and time'];
                }
            }

        }

        return ['success' => true];
    }
    private function getFormattedDatesAndTime($input, $tenderData)
    {
        return [
            'submissionClosingDate' => $this->parseDateTime($input, 'submissionClosingDate', 'bidSubmissionClosingTime'),
            'submissionOpeningDate' => Carbon::parse($tenderData['bid_submission_opening_date']),
            'bidOpeningStartDate' => $this->parseDateTime($input, 'bidOpeningStartDate', 'bidOpeningStarDateTime'),
            'bidOpeningEndDate' => $this->parseDateTime($input, 'bidOpeningEndDate', 'bidOpeningEndDateTime'),
            'technicalStartDate' => $this->parseDateTime($input, 'technicalBidOpeningStartDate', 'technicalBidOpeningStarDateTime'),
            'technicalEndDate' => $this->parseDateTime($input, 'technicalBidOpeningEndDate', 'technicalBidOpeningEndDateTime'),
            'commercialStartDate' => $this->parseDateTime($input, 'commercialBidOpeningStartDate', 'commercialBidOpeningStarDateTime'),
            'commercialEndDate' => $this->parseDateTime($input, 'commercialBidOpeningEndDate', 'commercialBidOpeningEndDateTime')
        ];
    }
    private function parseDateTime($input, $dateKey, $timeKey)
    {
        if (!empty($input[$dateKey]) && !empty($input[$timeKey])) {
            $dateTime = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                Carbon::parse($input[$dateKey])->format('Y-m-d') . ' ' . Carbon::parse($input[$timeKey])->format('H:i:s')
            );
            return $dateTime;
        }
        return null;
    }
    private function processTenderUpdate($formattedDatesAndTime, $tenderData,$input)
    {
        try {
            DB::transaction(function () use ($formattedDatesAndTime, $tenderData, $input) {
                $tenderMaster = TenderMaster::find($tenderData['id']);
                $calendarLog = $this->insertCalendarLog($formattedDatesAndTime, $tenderData, $input);
                $data = [
                    'bid_submission_closing_date' => $formattedDatesAndTime['submissionClosingDate'] ?? null,
                    'bid_opening_date' => $formattedDatesAndTime['bidOpeningStartDate'] ?? null,
                    'bid_opening_end_date' => $formattedDatesAndTime['bidOpeningEndDate'] ?? null,
                    'technical_bid_opening_date' => $formattedDatesAndTime['technicalStartDate'] ?? null,
                    'technical_bid_closing_date' => $formattedDatesAndTime['technicalEndDate'] ?? null,
                    'commerical_bid_opening_date' => $formattedDatesAndTime['commercialStartDate'] ?? null,
                    'commerical_bid_closing_date' => $formattedDatesAndTime['commercialEndDate'] ?? null,
                ];

                $tenderMaster->update($data);
            });

            return [
                'success' => true,
                'message' => 'Tender calendar days updated successfully.',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTenderCalendarValidation($request)
    {
        $input = $request->all();
        $tenderData = TenderMaster::getTenderByUuid($input['tenderCode']);
        $tenderBidEmployeeDetails = SrmTenderBidEmployeeDetails::getTendetBidEmployeeDetails($tenderData['id']);
        $currentDate = date('Y-m-d H:i:s');
        $currentDateFormatted = Carbon::createFromFormat('Y-m-d H:i:s', $currentDate);
        $formatedDates = $this->getFormattedDatesAndTime($input, $tenderData);
        $isCommDateDisable = false;
        $isDateDisable = false;

        if ($tenderData['stage'] == 1) {
            $bidOpeningStartDate = $formatedDates['bidOpeningStartDate'];
            $bidOpeningEndDate = $formatedDates['bidOpeningEndDate'];
        } else if ($tenderData['stage'] == 2) {
            $bidOpeningStartDate = $formatedDates['technicalStartDate'];
            $bidOpeningEndDate = $formatedDates['technicalEndDate'];

            $commercialStartDate = $formatedDates['commercialStartDate'];
            $commercialEndDate = $formatedDates['commercialEndDate'];

            $result1 = $currentDateFormatted->gt($commercialStartDate);
            if ($commercialEndDate == null) {
                $result2 = true;
            } else {
                $result2 = $commercialEndDate->gt($currentDateFormatted);
            }

            if ($result1 && $result2) {
                $isCommDateDisable = true;
            }

        }

        $technicalBidOpened = $currentDateFormatted->gt($bidOpeningStartDate);

        if ($bidOpeningEndDate == null) {
            $result4 = true;
        } else {
            $result4 = $bidOpeningEndDate->gt($currentDateFormatted);
        }

        if ($technicalBidOpened && $result4) {
            $isDateDisable = true;
        }

        $isEmpApprovalActive = $currentDateFormatted->gte($formatedDates['submissionClosingDate']);
        $minApprovalBidOpening = $tenderData['min_approval_bid_opening'];
        $employeeApprovalCount = $tenderBidEmployeeDetails->count();

        $technicalBidValidation = (($technicalBidOpened || $employeeApprovalCount >= $minApprovalBidOpening) || $isEmpApprovalActive);

        $disable = $technicalBidValidation ? false : true;
        $disableCommercial = $technicalBidValidation ? false : true;
        $commercialBidValidation = (!$isCommDateDisable || $minApprovalBidOpening > $employeeApprovalCount);

        $comActive = (!$commercialBidValidation && !$disable) ? true : false;


        return [
            'technicalBidValidation'=> $disable,
            'commercialBidValidation' => $comActive,
            'isTecDisable' => $technicalBidOpened
        ] ;
    }

    public function insertCalendarLog($formattedDatesAndTime, $tenderData, $input)
    {
        return DB::transaction(function () use ($formattedDatesAndTime, $tenderData, $input) {
            $logData = [];
            $baseData = [
                'tender_id' => $tenderData['id'],
                'company_id' => $tenderData['company_id'],
                'created_by' => Helper::getEmployeeSystemID(),
                'created_at' => Helper::currentDateTime(),
                'narration' => $input['comment'],
            ];

            $fields = $this->getFieldMappings();

            foreach ($fields as $newField => $info) {
                $oldValue = isset($tenderData[$info['oldField']]) ? $tenderData[$info['oldField']] : null;
                $newValue = isset($formattedDatesAndTime[$newField]) ? $formattedDatesAndTime[$newField] : null;

                $oldDateTime = $oldValue ? Carbon::parse($oldValue) : null;
                $newDateTime = $newValue ? Carbon::parse($newValue) : null;

                $oldDate = $oldDateTime ? $oldDateTime->format('Y-m-d') : null;
                $newDate = $newDateTime ? $newDateTime->format('Y-m-d') : null;

                if ($oldDate !== $newDate) {
                    $logData[] = array_merge($baseData, [
                        'filed_description' => $info['descriptionDate'],
                        'old_value' => $oldDate,
                        'new_value' => $newDate,
                    ]);
                }

                $oldTime = $oldDateTime ? $oldDateTime->format('H:i:s') : null;
                $newTime = $newDateTime ? $newDateTime->format('H:i:s') : null;

                if ($oldTime !== $newTime) {
                    $logData[] = array_merge($baseData, [
                        'filed_description' => $info['descriptionTime'],
                        'old_value' => $oldTime,
                        'new_value' => $newTime,
                    ]);
                }
            }

            if (!empty($logData)) {
                SRMTenderCalendarLog::insert($logData);
            }
        });
    }

    private function getFieldMappings(): array
    {
        return [
            'submissionClosingDate' => [
                'oldField' => 'bid_submission_closing_date',
                'descriptionDate' => 'Bid Submission to date',
                'descriptionTime' => 'Bid Submission to time',
            ],
            'bidOpeningStartDate' => [
                'oldField' => 'bid_opening_date',
                'descriptionDate' => 'Bid Opening from date',
                'descriptionTime' => 'Bid Opening from time',
            ],

            'bidOpeningEndDate' => [
                'oldField' => 'bid_opening_end_date',
                'descriptionDate' => 'Bid Opening to date',
                'descriptionTime' => 'Bid Opening to time',
            ],

            'technicalStartDate' => [
                'oldField' => 'technical_bid_opening_date',
                'descriptionDate' => 'Technical bid opening from date',
                'descriptionTime' => 'Technical bid opening from time',
            ],

            'technicalEndDate' => [
                'oldField' => 'technical_bid_closing_date',
                'descriptionDate' => 'Technical bid opening to date',
                'descriptionTime' => 'Technical bid opening  to time',
            ],

            'commercialStartDate' => [
                'oldField' => 'commerical_bid_opening_date',
                'descriptionDate' => 'Commercial bid opening from date',
                'descriptionTime' => 'Commercial bid opening from time',
            ],

            'commercialEndDate' => [
                'oldField' => 'commerical_bid_closing_date',
                'descriptionDate' => 'Commercial bid opening to date',
                'descriptionTime' => 'Commercial bid opening to time',
            ],
        ];
    }
}
