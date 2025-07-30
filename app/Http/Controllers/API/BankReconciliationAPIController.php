<?php
/**
 * =============================================
 * -- File Name : BankReconciliationAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Bank Reconciliation
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - September 2018
 * -- Description : This file contains the all CRUD for Bank Reconciliation
 * -- REVISION HISTORY
 * -- Date: 18-September 2018 By: Fayas Description: Added new functions named as getAllBankReconciliationByBankAccount()
 * -- Date: 26-September 2018 By: Fayas Description: Added new functions named as getBankReconciliationFormData()
 * -- Date: 27-September 2018 By: Fayas Description: Added new functions named as getBankReconciliationApprovalByUser(),getBankReconciliationApprovedByUser()
 * -- Date: 28-September 2018 By: Fayas Description: Added new functions named as bankReconciliationAudit()
 * -- Date: 05 - October 2018 By: Fayas Description: Added new functions named as getTreasuryManagementFilterData(),validateTMReport(),
 *                                                      generateTMReport(),exportTMReport()
 * -- Date: 23-November 2018 By: Fayas Description: Added new functions named as bankRecReopen()
 * -- Date: 11-December 2018 By: Fayas Description: Added new functions named as bankReconciliationReferBack()
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankReconciliationAPIRequest;
use App\Http\Requests\API\UpdateBankReconciliationAPIRequest;
use App\Jobs\UploadBankStatement;
use App\Models\BankAccount;
use App\Models\BankLedger;
use App\Models\BankMaster;
use App\Models\BankReconciliation;
use App\Models\BankReconciliationDocuments;
use App\Models\BankReconciliationRefferedBack;
use App\Models\BankReconciliationTemplateMapping;
use App\Models\BankStatementMaster;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\PaymentBankTransferDetailRefferedBack;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Repositories\BankLedgerRepository;
use App\Repositories\BankReconciliationDocumentsRepository;
use App\Repositories\BankReconciliationRepository;
use App\Services\CustomerReceivePaymentService;
use App\Services\PaymentVoucherServices;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use PHPExcel_IOFactory;

/**
 * Class BankReconciliationController
 * @package App\Http\Controllers\API
 */
class BankReconciliationAPIController extends AppBaseController
{
    /** @var  BankReconciliationRepository */
    private $bankReconciliationRepository;
    private $bankLedgerRepository;
    private $bankReconciliationDocument;
    private $bankStatementMaster;

    public function __construct(BankReconciliationRepository $bankReconciliationRepo, BankLedgerRepository $bankLedgerRepo, BankReconciliationDocumentsRepository $bankReconciliationDocumentsRepo, BankStatementMaster $bankStatementMasterRepo)
    {
        $this->bankReconciliationRepository = $bankReconciliationRepo;
        $this->bankLedgerRepository = $bankLedgerRepo;
        $this->bankReconciliationDocument = $bankReconciliationDocumentsRepo;
        $this->bankStatementMaster = $bankStatementMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankReconciliations",
     *      summary="Get a listing of the BankReconciliations.",
     *      tags={"BankReconciliation"},
     *      description="Get all BankReconciliations",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/BankReconciliation")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->bankReconciliationRepository->pushCriteria(new RequestCriteria($request));
        $this->bankReconciliationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankReconciliations = $this->bankReconciliationRepository->all();

        return $this->sendResponse($bankReconciliations->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_reconciliation')]));
    }

    /**
     * @param CreateBankReconciliationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bankReconciliations",
     *      summary="Store a newly created BankReconciliation in storage",
     *      tags={"BankReconciliation"},
     *      description="Store BankReconciliation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankReconciliation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankReconciliation")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/BankReconciliation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankReconciliationAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $validator = \Validator::make($input, [
            'description' => 'required',
            'bankRecAsOf' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $input['bankRecAsOf'] = new Carbon($input['bankRecAsOf']);


        $end = (new Carbon())->endOfMonth();
        if ($input['bankRecAsOf'] > $end) {
            // return $this->sendError('You cannot select a date greater than the current month last day', 500);
        }

        $input['documentSystemID'] = 62;
        $input['documentID'] = 'BRC';

        $bankAccount = BankAccount::find($input['bankAccountAutoID']);

        if (!empty($bankAccount)) {
            $input['bankGLAutoID'] = $bankAccount->chartOfAccountSystemID;
            $input['companySystemID'] = $bankAccount->companySystemID;
            $input['bankMasterID'] = $bankAccount->bankmasterAutoID;
        } else {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_accounts')]), 500);
        }


        $checkPending = BankReconciliation::where('bankAccountAutoID', $input['bankAccountAutoID'])
            ->where('approvedYN', 0)
            ->first();


        if (!empty($checkPending)) {
            return $this->sendError(trans('custom.there_is_a_bank_reconciliation') .' '. $checkPending->bankRecPrimaryCode .' '. trans('custom.pending_for_approval_for_the_bank_reconciliation_you_are_trying_to_add_please_check_again'), 500);
        }

        $maxAsOfDate = BankReconciliation::where('bankAccountAutoID', $input['bankAccountAutoID'])
            ->max('bankRecAsOf');

        if ($maxAsOfDate >= $input['bankRecAsOf']) {
            return $this->sendError(trans('custom.pending_for_approval_for_the_bank_reconciliation_you_are_trying_to_add_please_check_again') .' '. (new Carbon($maxAsOfDate))->format('d/m/Y'), 500);
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $lastSerial = BankReconciliation::where('companySystemID', $input['companySystemID'])
            ->orderBy('bankRecAutoID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }
        $input['serialNo'] = $lastSerialNumber;

        $dateArray = explode('-', $input['bankRecAsOf']);
        $input['month'] = $dateArray[1];
        $input['year'] = $dateArray[0];

        $code = ($input['companyID'] . '\\' . $input['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $input['bankRecPrimaryCode'] = $code;


        $openingBalance = BankLedger::selectRaw('companySystemID,bankAccountID,trsClearedYN,bankClearedYN,ABS(SUM(if(bankClearedAmount < 0,bankClearedAmount,0))) - SUM(if(bankClearedAmount > 0,bankClearedAmount,0)) as opening')
            ->where('companySystemID', $input['companySystemID'])
            ->where("bankAccountID", $input['bankAccountAutoID'])
            ->where("trsClearedYN", -1)
            ->where("bankClearedYN", -1)
            ->groupBy('companySystemID', 'bankAccountID')
            ->first();

        if (!empty($openingBalance)) {
            $input['openingBalance'] = $openingBalance->opening;
            $input['closingBalance'] = $openingBalance->opening;
        } else {
            $input['openingBalance'] = 0;
            $input['closingBalance'] = 0;
        }

        $bankReconciliations = $this->bankReconciliationRepository->create($input);

        return $this->sendResponse($bankReconciliations->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_reconciliation')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankReconciliations/{id}",
     *      summary="Display the specified BankReconciliation",
     *      tags={"BankReconciliation"},
     *      description="Get BankReconciliation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankReconciliation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/BankReconciliation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var BankReconciliation $bankReconciliation */
        $bankReconciliation = $this->bankReconciliationRepository->with(['bank_account.currency', 'confirmed_by'])->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation')]));
        }

        if (!empty($bankReconciliation)) {
            $confirmed = $bankReconciliation->confirmedYN;
        }

        $totalReceiptAmount = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '<', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->whereDate("postedDate", '<=', $bankReconciliation->bankRecAsOf)
            ->where(function ($q) use ($bankReconciliation, $confirmed) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                })->when($confirmed == 0, function ($q2) {
                    $q2->orWhere("bankClearedYN", 0);
                });
            })->sum('payAmountBank');

        $totalPaymentAmount = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->whereDate("postedDate", '<=', $bankReconciliation->bankRecAsOf)
            ->where(function ($q) use ($bankReconciliation, $confirmed) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                })->when($confirmed == 0, function ($q2) {
                    $q2->orWhere("bankClearedYN", 0);
                });
            })->sum('payAmountBank');

        $totalReceiptClearedAmount = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '<', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->whereDate("postedDate", '<=', $bankReconciliation->bankRecAsOf)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                });
            })->sum('bankClearedAmount');

        $totalPaymentClearedAmount = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->whereDate("postedDate", '<=', $bankReconciliation->bankRecAsOf)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                });
            })->sum('bankClearedAmount');

        $bankReconciliation->totalReceiptAmount = $totalReceiptAmount * -1;
        $bankReconciliation->totalReceiptClearedAmount = $totalReceiptClearedAmount * -1;
        $bankReconciliation->totalPaymentAmount = $totalPaymentAmount;
        $bankReconciliation->totalPaymentClearedAmount = $totalPaymentClearedAmount;

        return $this->sendResponse($bankReconciliation->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_reconciliation')]));
    }

    /**
     * @param int $id
     * @param UpdateBankReconciliationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bankReconciliations/{id}",
     *      summary="Update the specified BankReconciliation in storage",
     *      tags={"BankReconciliation"},
     *      description="Update BankReconciliation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankReconciliation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankReconciliation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankReconciliation")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/BankReconciliation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankReconciliationAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'confirmedByEmpID', 'confirmedDate',
            'confirmed_by', 'confirmedByEmpSystemID']);
        /** @var BankReconciliation $bankReconciliation */
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation')]));
        }
        if ($bankReconciliation->confirmedYN == 1) {
            return $this->sendError(trans('custom.this_document_already_confirmed'), 500);
        }

        if ($bankReconciliation->confirmedYN == 0 && $input['confirmedYN'] == 1) {
            $validateAdditionalEntryApproved = $this->bankReconciliationDocument->validateConfirmation($id, $bankReconciliation->companySystemID);
            if(!$validateAdditionalEntryApproved->isEmpty()) {
                return $this->sendError('There are some manually created documents pending approval', 500);
            }

            $checkItems = BankLedger::where('bankRecAutoID', $id)
                ->count();
            if ($checkItems == 0) {
                //return $this->sendError('Every bank reconciliation should have at least one cleared item', 500);
            }

            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $bankReconciliation->companySystemID,
                'document' => $bankReconciliation->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => 0
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        //$bankReconciliation = $this->bankReconciliationRepository->update($input, $id);

        return $this->sendReponseWithDetails($bankReconciliation->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_reconciliation')]),1,$confirm['data'] ?? null);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bankReconciliations/{id}",
     *      summary="Remove the specified BankReconciliation from storage",
     *      tags={"BankReconciliation"},
     *      description="Delete BankReconciliation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankReconciliation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var BankReconciliation $bankReconciliation */
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation')]));
        }

        $validateAdditionalEntryApproved = $this->bankReconciliationDocument->validateConfirmation($id, $bankReconciliation->companySystemID);
        if(!$validateAdditionalEntryApproved->isEmpty()) {
            return $this->sendError('There are some pending additional entries to approve', 500);
        }

        $bankLedgerData = BankLedger::where('bankAccountID', $bankReconciliation->bankAccountAutoID)
            ->where('companySystemID', $bankReconciliation->companySystemID)
            ->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
            ->where('bankClearedYN', -1)
            ->get();

        foreach ($bankLedgerData as $data) {
            $updateArray = ['bankClearedYN' => 0, 'bankClearedAmount' => 0, 'bankClearedByEmpName' => null,
                'bankClearedByEmpID' => null, 'bankClearedByEmpSystemID' => null, 'bankClearedDate' => null, 'bankRecAutoID' => null];

            $bankLedger = $this->bankLedgerRepository->update($updateArray, $data['bankLedgerAutoID']);
        }

        $bankReconciliation->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_reconciliation')]));
    }

    public function getAllBankReconciliationByBankAccount(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $bankReconciliation = BankReconciliation::whereIn('companySystemID', $subCompanies)
            ->where("bankAccountAutoID", $input['bankAccountAutoID'])
            ->with(['month', 'created_by', 'bank_account']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankReconciliation = $bankReconciliation->where(function ($query) use ($search) {
                $query->where('bankRecPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankReconciliation)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankRecAsOf', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAllBankReconciliationList(Request $request)
    {
        $input = $request->all();
        // $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $bankmasterAutoID = $request['bankmasterAutoID'];
        $bankmasterAutoID = (array)$bankmasterAutoID;
        $bankmasterAutoID = collect($bankmasterAutoID)->pluck('id');

        $search = $request->input('search.value');

        $bankReconciliation = $this->bankReconciliationRepository->bankReconciliationListQuery($request, $input, $search, $bankmasterAutoID);

        return \DataTables::eloquent($bankReconciliation)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankRecAsOf', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getCheckBeforeCreate(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'bankAccountAutoID' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $bankAccountAutoID = $input['bankAccountAutoID'];
        $bankAccount = BankAccount::find($bankAccountAutoID);
        if (empty($bankAccount)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_accounts')]));
        }

        $matchingInProgress = BankStatementMaster::where('bankAccountAutoId', $bankAccountAutoID)
                                                    ->where('documentStatus', 1)
                                                    ->first();
        if(!empty($matchingInProgress))
        {
            return $this->sendError('Auto bank reconciliation is in progress. Manual reconciliation is not allowed.', 500);
        }

        $checkPending = BankReconciliation::where('bankAccountAutoID', $bankAccountAutoID)
            ->where('approvedYN', 0)
            ->first();

        if (!empty($checkPending)) {
            return $this->sendError(trans('custom.there_is_a_bank_reconciliation') .' '. $checkPending->bankRecPrimaryCode .' '. trans('custom.pending_for_approval_for_the_bank_reconciliation_you_are_trying_to_add_please_check_again'), 500);
        }

        return $this->sendResponse($bankAccount->toArray(), trans('custom.successfully'));
    }

    public function getBankReconciliationFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();
        $bankMasters = BankMaster::all();

        $submitBankTransfer = CompanyPolicyMaster::where('companyPolicyCategoryID', 102)
            ->where('companySystemID', $companyId)
            ->first();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'bankMasters' => $bankMasters,
            'bankTransferSubmitPolicy' => $submitBankTransfer->isYesNO ?? 0
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function getBankReconciliationApprovalByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $bankReconciliation = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_bankrecmaster.*',
                'employees.empName As created_emp',
                'erp_bankaccount.AccountNo As AccountNo',
                'months.monthDes As monthDes',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [62])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_bankrecmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'bankRecAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_bankrecmaster.companySystemID', $companyId)
                    ->where('erp_bankrecmaster.approvedYN', 0)
                    ->where('erp_bankrecmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('months', 'month', 'months.monthID')
            ->leftJoin('erp_bankaccount', 'erp_bankrecmaster.bankAccountAutoID', 'erp_bankaccount.bankAccountAutoID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [62])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankReconciliation = $bankReconciliation->where(function ($query) use ($search) {
                $query->where('bankRecPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $bankReconciliation = [];
        }

        return \DataTables::of($bankReconciliation)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankRecAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBankReconciliationApprovedByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $bankReconciliation = DB::table('erp_documentapproved')
            ->select(
                'erp_bankrecmaster.*',
                'employees.empName As created_emp',
                'erp_bankaccount.AccountNo As AccountNo',
                'months.monthDes As monthDes',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_bankrecmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'bankRecAutoID')
                    ->where('erp_bankrecmaster.companySystemID', $companyId)
                    ->where('erp_bankrecmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('months', 'month', 'months.monthID')
            ->leftJoin('erp_bankaccount', 'erp_bankrecmaster.bankAccountAutoID', 'erp_bankaccount.bankAccountAutoID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [62])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankReconciliation = $bankReconciliation->where(function ($query) use ($search) {
                $query->where('bankRecPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($bankReconciliation)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankRecAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function bankReconciliationAudit(Request $request)
    {

        $id = $request->id;
        /** @var BankReconciliation $bankReconciliation */
        $bankReconciliation = $this->bankReconciliationRepository->getAudit($id);

        if (empty($bankReconciliation)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation')]));
        }

        $bankReconciliation = $this->getUnClearReceiptPayment($bankReconciliation);

        return $this->sendResponse($bankReconciliation->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_reconciliation')]));
    }

    public function printBankReconciliation(Request $request)
    {
        $id = $request->get('id');
        $bankReconciliation = $this->bankReconciliationRepository->getAudit($id);

        if (empty($bankReconciliation)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation')]));
        }

        $bankReconciliation->docRefNo = \Helper::getCompanyDocRefNo($bankReconciliation->companySystemID, $bankReconciliation->documentSystemID);
        $bankReconciliation = $this->getUnClearReceiptPayment($bankReconciliation);

        $decimalPlaces = 2;

        if ($bankReconciliation->bank_account) {
            if ($bankReconciliation->bank_account->currency) {
                $decimalPlaces = $bankReconciliation->bank_account->currency->DecimalPlaces;
            }
        }

        $array = array('entity' => $bankReconciliation,
                       'decimalPlaces' => $decimalPlaces,
                       'date' => Carbon::now());
        $time = strtotime("now");
        $fileName = 'bank_reconciliation' . $id . '_' . $time . '.pdf';
        $html = view('print.bank_reconciliation', $array);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);
    }


    function getUnClearReceiptPayment($bankReconciliation)
    {

        $unClearedReceipt = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('bankAccountID', $bankReconciliation->bankAccountAutoID)
            ->whereDate('postedDate', '<=', $bankReconciliation->bankRecAsOf)
            ->where('payAmountBank', '<', 0)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where('bankClearedYN', 0)
                    ->orWhere(function ($q2) use ($bankReconciliation) {
                        if($bankReconciliation->confirmedYN == 1) {
                            $q2->where('bankRecAutoID', '!=', $bankReconciliation->bankRecAutoID)
                                ->where('bankClearedYN', -1)
                                ->where('bankReconciliationDate', '>', $bankReconciliation->bankRecAsOf);
                        }
                    });
            })
            ->get();

        $unClearedPayment = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('bankAccountID', $bankReconciliation->bankAccountAutoID)
            ->whereDate('postedDate', '<=', $bankReconciliation->bankRecAsOf)
            ->where('payAmountBank', '>', 0)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where('bankClearedYN', 0)
                    ->orWhere(function ($q2) use ($bankReconciliation) {
                        if($bankReconciliation->confirmedYN == 1){
                            $q2->where('bankRecAutoID', '!=', $bankReconciliation->bankRecAutoID)
                                ->where('bankClearedYN', -1)
                                ->where('bankReconciliationDate', '>', $bankReconciliation->bankRecAsOf);
                        }
                    });
            })
            ->get();

        $totalUnClearedReceipt = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('bankAccountID', $bankReconciliation->bankAccountAutoID)
            ->whereDate('postedDate', '<=', $bankReconciliation->bankRecAsOf)
            ->where('payAmountBank', '<', 0)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where('bankClearedYN', 0)
                    ->orWhere(function ($q2) use ($bankReconciliation) {
                        if($bankReconciliation->confirmedYN == 1) {
                            $q2->where('bankRecAutoID', '!=', $bankReconciliation->bankRecAutoID)
                                ->where('bankClearedYN', -1)
                                ->where('bankReconciliationDate', '>', $bankReconciliation->bankRecAsOf);
                        }
                    });
            })
            ->sum('payAmountBank');

        $totalUnClearedPayment = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('bankAccountID', $bankReconciliation->bankAccountAutoID)
            ->whereDate('postedDate', '<=', $bankReconciliation->bankRecAsOf)
            ->where('payAmountBank', '>', 0)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where('bankClearedYN', 0)
                    ->orWhere(function ($q2) use ($bankReconciliation) {
                        if($bankReconciliation->confirmedYN == 1) {
                            $q2->where('bankRecAutoID', '!=', $bankReconciliation->bankRecAutoID)
                                ->where('bankClearedYN', -1)
                                ->where('bankReconciliationDate', '>', $bankReconciliation->bankRecAsOf);
                        }
                    });
            })
            ->sum('payAmountBank');

        $bankReconciliation->unClearedReceipt = $unClearedReceipt;
        $bankReconciliation->unClearedPayment = $unClearedPayment;
        $bankReconciliation->totalUnClearedReceipt = $totalUnClearedReceipt;
        $bankReconciliation->totalUnClearedPayment = $totalUnClearedPayment;
        $bankReconciliation->bookBalance = $bankReconciliation->closingBalance + ($totalUnClearedReceipt * -1) - $totalUnClearedPayment;

        return $bankReconciliation;

    }

    public function getTreasuryManagementFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $subCompaniesByGroup = [];
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $subCompaniesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompaniesByGroup = (array)$selectedCompanyId;
        }

        $banks = BankMaster::all();
        $bankIds = $banks->pluck('bankmasterAutoID');
        $accounts = BankAccount::whereIn('companySystemID', $subCompaniesByGroup)->whereIN('bankmasterAutoID', $bankIds)->where('isAccountActive', 1)->get();
        $output = array(
            'banks' => $banks,
            'accounts' => $accounts
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    /*validate each report*/
    public function validateTMReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'BRC':
            case 'TCS':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }

                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required|date',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'bankAccountID' => 'required',
                    'bankID' => 'required',
                    'reportTypeID' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
        }
    }

    public function generateTMReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'BRC': // Bank Reconciliation
            case 'TCS': // Bank Reconciliation
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('bankID', 'bankAccountID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getBankReconciliationReportQry($request);
                return array('reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                );
                break;
            default:
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
        }
    }


    public function exportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'BRC': //// Bank Reconciliation
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('bankID', 'bankAccountID'));
                $data = array();
                $output = $this->getBankReconciliationReportQry($request);

                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {

                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                        $data[$x]['Narration'] = $val->documentNarration;
                        $data[$x]['Payee Name'] = $val->payeeName;
                        $decimal = 3;
                        if ($val['bank_account']) {
                            if ($val['bank_account']['currency']) {
                                $data[$x]['Bank Currency'] = $val['bank_account']['currency']['CurrencyCode'];
                                $decimal = $val['bank_account']['currency']['DecimalPlaces'];
                            } else {
                                $data[$x]['Bank Currency'] = '';
                            }
                        } else {
                            $data[$x]['Bank Currency'] = '';
                        }
                        $data[$x]['Bank Amount'] = number_format($val->payAmountBank, $decimal);
                        $data[$x]['Reconciliation Date'] = \Helper::dateFormat($val->bankReconciliationDate);
                        $data[$x]['Bank Cleared By'] = $val->bankClearedByEmpName;
                        $data[$x]['Bank Cleared Date'] = \Helper::dateFormat($val->bankClearedDate);
                        $x++;
                    }
                }

                 \Excel::create('bank_reconciliation', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), trans('custom.success_export'));
                break;
             case 'TCS': //// Treasury Cleared Report
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('bankID', 'bankAccountID'));
                $data = array();
                $output = $this->getBankReconciliationReportQry($request);

                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {

                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                        $data[$x]['Narration'] = $val->documentNarration;
                        $data[$x]['Payee Name'] = $val->payeeName;
                        $decimal = 3;
                        if ($val['bank_account']) {
                            if ($val['bank_account']['currency']) {
                                $data[$x]['Bank Currency'] = $val['bank_account']['currency']['CurrencyCode'];
                                $decimal = $val['bank_account']['currency']['DecimalPlaces'];
                            } else {
                                $data[$x]['Bank Currency'] = '';
                            }
                        } else {
                            $data[$x]['Bank Currency'] = '';
                        }
                        $data[$x]['Bank Amount'] = number_format($val->payAmountBank, $decimal);
                        $data[$x]['Treasury Cleared Status'] = ($val->trsClearedYN == -1) ? "Yes" : "No";
                        $data[$x]['Treasury Cleared Date'] = \Helper::dateFormat($val->trsClearedDate);
                        $data[$x]['Treasury Cleared By'] = $val->trsClearedByEmpName;
                        $data[$x]['Reconciliation Date'] = \Helper::dateFormat($val->bankReconciliationDate);
                        $data[$x]['Bank Cleared By'] = $val->bankClearedByEmpName;
                        $data[$x]['Bank Cleared Date'] = \Helper::dateFormat($val->bankClearedDate);
                        $x++;
                    }
                }

                 \Excel::create('bank_reconciliation', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');
                break;
            default:
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
        }
    }


    public function getBankReconciliationReportQry($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = [];
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $items = BankLedger::whereIn('companySystemID', $companyID)
                            ->where('bankAccountID', $request->bankAccountID)
                            ->where('bankID', $request->bankID)
                            ->orderBy('bankReconciliationDate', 'desc')
                            ->with(['bank_account.currency']);
        
        if ($request->reportID == "BRC") {
            $items = $items->where('bankClearedYN', -1)
                            ->whereBetween('bankReconciliationDate', [$fromDate, $toDate]);
        } else {
            $items = $items->whereBetween('documentDate', [$fromDate, $toDate]);

        }

        return $items->get();
    }

    public function bankRecReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['bankRecAutoID'];
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);
        $emails = array();
        if (empty($bankReconciliation)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation')]));
        }

        if ($bankReconciliation->approvedYN == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_bank_reconciliation_it_is_already_fully_approved'));
        }

        if ($bankReconciliation->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_bank_reconciliation_it_is_already_partially_approved'));
        }

        if ($bankReconciliation->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_bank_reconciliation_it_is_not_confirmed'));
        }

        $updateInput = ['confirmedYN' => 0,'confirmedByEmpSystemID' => null,'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null,'RollLevForApp_curr' => 1];

        $this->bankReconciliationRepository->update($updateInput,$id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $bankReconciliation->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $bankReconciliation->bankRecPrimaryCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $bankReconciliation->bankRecPrimaryCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('documentSystemCode', $bankReconciliation->bankRecAutoID)
            ->where('documentSystemID', $bankReconciliation->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $bankReconciliation->companySystemID)
                    ->where('documentSystemID', $bankReconciliation->documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => trans('custom.policy_not_found_for_this_document')];
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                }

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode);
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $bankReconciliation->companySystemID)
            ->where('documentSystemID', $bankReconciliation->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($bankReconciliation->documentSystemID,$id,$input['reopenComments'],'Reopened');

        return $this->sendResponse($bankReconciliation->toArray(), trans('custom.reopened', ['attribute' => trans('custom.bank_reconciliation')]));
    }

    public function bankReconciliationReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $bankReconciliation = $this->bankReconciliationRepository->find($id);
        if (empty($bankReconciliation)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation')]));
        }

        if ($bankReconciliation->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_bank_reconciliation'));
        }

        $bankReconciliationArray = $bankReconciliation->toArray();

        $storeHistory = BankReconciliationRefferedBack::insert($bankReconciliationArray);

        $fetchDetails = BankLedger::where('bankRecAutoID', $id)->get();

        if (!empty($fetchDetails)) {
            foreach ($fetchDetails as $detail) {
                $detail['timesReferred'] = $bankReconciliation->timesReferred;
            }
        }

        $bankReconciliationDetailArray = $fetchDetails->toArray();

        $storeDetailHistory = PaymentBankTransferDetailRefferedBack::insert($bankReconciliationDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $bankReconciliation->companySystemID)
            ->where('documentSystemID', $bankReconciliation->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $bankReconciliation->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $bankReconciliation->companySystemID)
            ->where('documentSystemID', $bankReconciliation->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0,'confirmedYN' => 0,'confirmedByEmpSystemID' => null,
                'confirmedByEmpID' => null,'confirmedByName' => null,'confirmedDate' => null,'RollLevForApp_curr' => 1];

            $this->bankReconciliationRepository->update($updateArray,$id);
        }

        return $this->sendResponse($bankReconciliation->toArray(), trans('custom.bank_reconciliation_amend_successfully'));
    }

    public function amendBankReconciliationReview(Request $request)
    {
        $input = $request->all();

        $id = $input['bankRecAutoID'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $masterData = BankReconciliation::find($id);

        if (empty($masterData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliations')]));
        }


        $checkBankReconcileGenerated = BankReconciliation::where('bankAccountAutoID', $masterData->bankAccountAutoID)
                                                         ->whereDate('bankRecAsOf', '>=', Carbon::parse($masterData->bankRecAsOf))
                                                         ->where('companySystemID', $masterData->companySystemID)
                                                         ->where('bankRecAutoID', '!=',$id)
                                                         ->first();

        if ($checkBankReconcileGenerated) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this_bank_reconciliation_upcoming_months_bank_reconciliation_is_already_created'));
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this_bank_reconciliation_it_is_not_confirmed'));
        }


        $emailBody = '<p>' . $masterData->bankRecPrimaryCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->bankRecPrimaryCode . ' has been return back to amend';

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($masterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $masterData->confirmedByEmpSystemID,
                    'companySystemID' => $masterData->companySystemID,
                    'docSystemID' => $masterData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $id,
                    'docCode' => $masterData->bankRecPrimaryCode
                );
            }

            $documentApproval = DocumentApproved::where('companySystemID', $masterData->companySystemID)
                                                ->where('documentSystemCode', $id)
                                                ->where('documentSystemID', $masterData->documentSystemID)
                                                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
                        'companySystemID' => $masterData->companySystemID,
                        'docSystemID' => $masterData->documentSystemID,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $id,
                        'docCode' => $masterData->bankRecPrimaryCode
                    );
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->where('documentSystemID', $masterData->documentSystemID)
                                            ->delete();

            // updating fields
            $masterData->confirmedYN = 0;
            $masterData->confirmedByEmpSystemID = null;
            $masterData->confirmedByEmpID = null;
            $masterData->confirmedByName = null;
            $masterData->confirmedDate = null;
            $masterData->RollLevForApp_curr = 1;

            $masterData->approvedYN = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedByUserID = null;
            $masterData->approvedDate = null;
            $masterData->save();

            DB::commit();
            return $this->sendResponse($masterData->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_reconciliation_amend')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getBankReconciliationAdditionalEntries(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $additionalEntryQuery = $this->bankReconciliationDocument->getAdditionalEntryView($request);

        $data['order'] = [];
        $data['search']['value'] = '';
        $request->merge($data);
        $request->request->remove('search.value');

        return \DataTables::of($additionalEntryQuery)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAllActiveSegments(Request $request)
    {
        $companyId = isset($request['companyId']) ? $request['companyId'] : 0;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }
        $segment = SegmentMaster::ofCompany($subCompanies)->IsActive()->get();
        $output = array(
            'segments' => $segment
        );
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function saveAdditionalEntry(Request $request)
    {
        $input = $request->all();
        $departmentSystemId = ($input['type'] == 1) ? 1 : 4;
        $documentDate = Carbon::parse($input['documentDate'])->format('Y-m-d');
        $documentDateYearActive = CompanyFinanceYear::active_finance_year($input['companySystemID'], $documentDate);
        if($documentDateYearActive) {
            if ($documentDateYearActive['isCurrent'] == -1) {
                $input['companyFinanceYearID'] = $documentDateYearActive['companyFinanceYearID'];
                $documentDateMonthActive = CompanyFinancePeriod::activeFinancePeriod($input['companySystemID'], $departmentSystemId, $documentDate);
                if(!$documentDateMonthActive) {
                    return $this->sendError('Document Date is not within the active Financial Period.',500);
                }
                $input['companyFinancePeriodID'] = $documentDateMonthActive['companyFinancePeriodID'];
            } else {
                return $this->sendError('Document Date is not within the current financial year.',500);
            }
        } else {
            return $this->sendError('Document Date is not within the active Financial Year.',500);
        }

        if($input['documentType'] == 1) {
            $document['bankRecAutoID'] = $input['bankRecAutoID'];
        } else if($input['documentType'] == 2) {
            $document['statementId'] = $input['statementId'];
        }
        
        $document['documentSystemID'] = ($input['type'] == 1) ? 4 : 21;
        if ($input['type'] == 1) {
            $resultData = PaymentVoucherServices::generatePaymentVoucher($input);
            if($resultData['status']){
                $document['documentAutoId'] = $resultData['data']['PayMasterAutoId'];
            }
            else {
                return $this->sendError($resultData['message']);
            }
        } else if ($input['type'] == 2) {
            $resultData = CustomerReceivePaymentService::generateCustomerReceivePayment($input);
            if($resultData['status']){
                $document['documentAutoId'] = $resultData['data']['custReceivePaymentAutoID'];
            }
            else {
                return $this->sendError($resultData['message']);
            }
        }
        $DataReturn = $this->bankReconciliationDocument->create($document);
        return $this->sendResponse($DataReturn->toArray(), 'Additional entry created successfully.');
    }

    public function uploadBankStatement(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'uploadBank' => 'required',
            'uploadBankAccount' => 'required',
            'transactionCount' => 'required',
            'uploadStatement' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $template = BankReconciliationTemplateMapping::with('bankAccount')
                                    ->where('bankAccountAutoID', $input['uploadBankAccount'])
                                    ->where('companySystemID', $input['companySystemID'])
                                    ->first();
        if(!$template) {
            /*** later need to modify to configure template and then continue */
            return $this->sendError('Template not configured', 500);
        }
        $template = $template->toArray();

        $excelUpload = $input['uploadStatement'];
        if(isset($excelUpload)) {
            $decodeFile = base64_decode($excelUpload['file']);
            $originalFileName = $excelUpload['filename'];
            $extension = $excelUpload['filetype'];
            $size = $excelUpload['size'];
            $allowedExtensions = ['xlsx','xls'];
        } else {
            return $this->sendError('Invalid File',500);
        }


        if (!in_array($extension, $allowedExtensions))
        {
            return $this->sendError('This type of file not allow to upload.you can only upload .xlsx or .xls',500);
        }

        if ($size > 20000000) {
            return $this->sendError('The maximum size allow to upload is 20 MB',500);
        }

        $disk = 'local';
        Storage::disk($disk)->put($originalFileName, $decodeFile);
        $filePath = Storage::disk($disk)->path($originalFileName);
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $bankStatementDate = isset($template['bankStatementDate']) ? $sheet->getCell($template['bankStatementDate'])->getValue() : null;
        $statementStartDate = isset($template['statementStartDate']) ? $sheet->getCell($template['statementStartDate'])->getValue() : null;
        $statementEndDate = isset($template['statementEndDate']) ? $sheet->getCell($template['statementEndDate'])->getValue() : null;
        if(is_null($bankStatementDate) || is_null($statementStartDate) || is_null($statementEndDate)) {
            return $this->sendError('Some header level dates are empty.',500);
        }
        $bankStatementDate = self::dateValidation($bankStatementDate);
        $statementStartDate = self::dateValidation($statementStartDate);
        $statementEndDate = self::dateValidation($statementEndDate);

        if(is_null($bankStatementDate) || is_null($statementStartDate) || is_null($statementEndDate)) {
            return $this->sendError('Wrong date format - Correct format "DD/MM/YYYY"',500);
        }

        $statementExists = $this->bankStatementMaster->where('companySystemID', $input['companySystemID'])
                                    ->where('bankAccountAutoID', $input['uploadBankAccount'])
                                    ->where('importStatus', 1)
                                    ->where('bankStatementDate', $bankStatementDate)->first();
        if($statementExists) {
            return $this->sendError('Bank Statement already uploaded!', 500);
        }

        /** bank validation */
        $bankName = trim($sheet->getCell($template['bankName'])->getValue());
        $bankAccount = trim($sheet->getCell($template['bankAccountNumber'])->getValue());
        if ($bankName != $template['bank_account']['bankName'] || $bankAccount != $template['bank_account']['AccountNo']) {
            return $this->sendError('Bank Account details not matched', 500);
        }

        /** Opening balance and closing balance validation */
        $openingBalance = $sheet->getCell($template['openingBalance'])->getCalculatedValue();
        $endingBalance = $sheet->getCell($template['endingBalance'])->getCalculatedValue();
        $openingBalance = str_replace(',', '', $openingBalance);
        $endingBalance = str_replace(',', '', $endingBalance);
        if (!is_numeric($openingBalance) || !is_numeric($endingBalance)) {
            return $this->sendError('Opening balance and closing balance amount should be numbers', 500);
        }

        /** dates validation */
        $bankReconciliationMonth = Carbon::createFromFormat('Y-m-d', $statementEndDate)->format('M');

        /*** create bank statement master record - tbl = bank_statement_master */
        $statementMaster['bankAccountAutoID'] = $input['uploadBankAccount'];
        $statementMaster['bankmasterAutoID'] = $input['uploadBank'];
        $statementMaster['companySystemID'] = $input['companySystemID'];
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $statementMaster['companyID'] = $company->CompanyID;
        }
        $statementMaster['transactionCount'] = $input['transactionCount'];
        $statementMaster['statementStartDate'] = $statementStartDate;
        $statementMaster['statementEndDate'] = $statementEndDate;
        $statementMaster['bankReconciliationMonth'] = $bankReconciliationMonth;
        $statementMaster['bankStatementDate'] = $bankStatementDate;
        $statementMaster['openingBalance'] = $openingBalance;
        $statementMaster['endingBalance'] = $endingBalance;
        $statementMaster['filePath'] = $originalFileName;

        $bankStatementMaster = $this->bankStatementMaster->create($statementMaster);
        if($bankStatementMaster) {
            $db = isset($request->db) ? $request->db : "";
            $objPHPExcel = PHPExcel_IOFactory::load(Storage::disk($disk)->path($originalFileName));
            if (Storage::disk($disk)->exists($originalFileName)) {
                Storage::disk($disk)->delete($originalFileName);
            }
            $uploadData = [
                'objPHPExcel' => $objPHPExcel,
                'uploadedCompany' =>  $input['companySystemID'],
                'template' => $template,
                'statementMaster' => $bankStatementMaster->toArray(),
                'transactionCount' => $input['transactionCount']
            ];
            UploadBankStatement::dispatch($db, $uploadData);
            return $this->sendResponse([], 'Statement Upload send to queue.');
        } else {
            return $this->sendError('Bank statement master not created', 500);
        }
    }

    function dateValidation($date)
    {
        if (is_numeric($date)) {
            return Date::excelToDateTimeObject($date)->format('Y-m-d');
        } else {
            try {
                return Carbon::createFromFormat('d/m/Y', trim($date))->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    public function getActiveBankAccountsByBankID(Request $request)
    {
        $input = $request->all();

        $selectedCompanyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $bankAccounts = BankAccount::whereIn('companySystemID', $subCompanies)
            ->where('isAccountActive', 1)
            ->where('bankmasterAutoID', $input['id'])
            ->get();

        return $this->sendResponse($bankAccounts, trans('custom.retrieve', ['attribute' => trans('custom.bank_accounts')]));
    }
}
