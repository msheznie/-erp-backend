<?php

namespace App\Http\Controllers\API;

use App\helper\email;
use App\helper\Helper;
use App\Http\Requests\API\CreateVatReturnFillingMasterAPIRequest;
use App\Http\Requests\API\UpdateVatReturnFillingMasterAPIRequest;
use App\Models\VatReturnFillingMaster;
use App\Models\VatReturnFillingCategory;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\VatReturnFillingDetailsRefferedback;
use App\Models\VatReturnFillingMasterRefferedback;
use App\Models\VatReturnFilledCategoryRefferedback;
use App\Models\DocumentReferedHistory;
use App\Models\CompanyDocumentAttachment;
use App\Models\EmployeesDepartment;
use App\Models\YesNoSelection;
use App\Models\DocumentApproved;
use App\Models\VatReturnFillingDetail;
use App\Models\Company;
use App\Models\TaxLedgerDetail;
use App\Models\VatReturnFilledCategory;
use App\Repositories\VatReturnFillingMasterRepository;
use App\Services\ValidateDocumentAmend;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class VatReturnFillingMasterController
 * @package App\Http\Controllers\API
 */

class VatReturnFillingMasterAPIController extends AppBaseController
{
    /** @var  VatReturnFillingMasterRepository */
    private $vatReturnFillingMasterRepository;

    public function __construct(VatReturnFillingMasterRepository $vatReturnFillingMasterRepo)
    {
        $this->vatReturnFillingMasterRepository = $vatReturnFillingMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingMasters",
     *      summary="Get a listing of the VatReturnFillingMasters.",
     *      tags={"VatReturnFillingMaster"},
     *      description="Get all VatReturnFillingMasters",
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
     *                  @SWG\Items(ref="#/definitions/VatReturnFillingMaster")
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
        $this->vatReturnFillingMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->vatReturnFillingMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $vatReturnFillingMasters = $this->vatReturnFillingMasterRepository->all();

        return $this->sendResponse($vatReturnFillingMasters->toArray(), 'Vat Return Filling Masters retrieved successfully');
    }

    /**
     * @param CreateVatReturnFillingMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/vatReturnFillingMasters",
     *      summary="Store a newly created VatReturnFillingMaster in storage",
     *      tags={"VatReturnFillingMaster"},
     *      description="Store VatReturnFillingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingMaster")
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
     *                  ref="#/definitions/VatReturnFillingMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'date' => 'required',
            'companySystemID' => 'required',
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $categories = VatReturnFillingCategory::where('isActive', 1)
                                              ->whereNull('masterID')
                                              ->get();

        $input['date'] = Carbon::parse($input['date']);
        $input['documentSystemID'] = 104;

        DB::beginTransaction();
        try {
            $lastSerial = VatReturnFillingMaster::where('companySystemID', $input['companySystemID'])
                                        ->orderBy('serialNo', 'desc')
                                        ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }
            $company = Company::where('companySystemID', $input['companySystemID'])->first()->toArray();
            $y = date('Y', strtotime($input['date']));
            $input['returnFillingCode'] = ($company['CompanyID'] . '\\' . $y . '\\VRF' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['serialNo'] = $lastSerialNumber;

            $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->create($input);

            if ($vatReturnFillingMaster) {
                foreach ($categories as $key => $value) {
                    $filledCategoryData = [
                        'categoryID' => $value->id,
                        'vatReturnFillingID' => $vatReturnFillingMaster->id,
                    ];

                    $saveResCategory = VatReturnFilledCategory::create($filledCategoryData);

                    if ($saveResCategory) {
                        $subCategories = VatReturnFillingCategory::where('isActive', 1)
                                                                  ->where('masterID', $value->id)
                                                                  ->get();

                        foreach ($subCategories as $key1 => $value1) {
                            $res = $this->vatReturnFillingMasterRepository->generateFilling($input['date'], $value1->id, $input['companySystemID'], false, null, 0, $vatReturnFillingMaster->id);
                            if ($res['status']) {
                                $detailData = [
                                    'vatReturnFilledCategoryID' => $saveResCategory->id,
                                    'vatReturnFillingID' => $vatReturnFillingMaster->id,
                                    'vatReturnFillingSubCatgeoryID' => $value1->id,
                                    'taxAmount' => $res['data']['taxAmount'],
                                    'taxableAmount' => $res['data']['taxableAmount']
                                ];

                                $detailRes = VatReturnFillingDetail::create($detailData);

                                if ($detailRes) {
                                    if(isset($res['data']['linkedTaxLedgerDetails']) && count($res['data']['linkedTaxLedgerDetails']) > 0) {
                                        $updateVATDetail = TaxLedgerDetail::whereIn('id', $res['data']['linkedTaxLedgerDetails'])
                                                            ->update(['returnFilledDetailID' => $detailRes->id]);
                                    }
                                }
                            } else {
                                DB::rollback();
                                return $this->sendError("Error occured while getting tax details", 500);
                            }
                        }

                    }
                }
            }

            DB::commit();
            return $this->sendResponse($vatReturnFillingMaster->toArray(), 'Vat Return Filling Master saved successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage().$e->getLine(), 500);
        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingMasters/{id}",
     *      summary="Display the specified VatReturnFillingMaster",
     *      tags={"VatReturnFillingMaster"},
     *      description="Get VatReturnFillingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingMaster",
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
     *                  ref="#/definitions/VatReturnFillingMaster"
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
        /** @var VatReturnFillingMaster $vatReturnFillingMaster */
        $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->findWithoutFail($id);

        if (empty($vatReturnFillingMaster)) {
            return $this->sendError('Vat Return Filling Master not found');
        }

        return $this->sendResponse($vatReturnFillingMaster->toArray(), 'Vat Return Filling Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateVatReturnFillingMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/vatReturnFillingMasters/{id}",
     *      summary="Update the specified VatReturnFillingMaster in storage",
     *      tags={"VatReturnFillingMaster"},
     *      description="Update VatReturnFillingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingMaster")
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
     *                  ref="#/definitions/VatReturnFillingMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVatReturnFillingMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var VatReturnFillingMaster $vatReturnFillingMaster */
        $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->findWithoutFail($id);

        if (empty($vatReturnFillingMaster)) {
            return $this->sendError('Vat Return Filling Master not found');
        }

        if ($vatReturnFillingMaster->confirmedYN == 1) {
            return $this->sendError('This document already confirmed.', 500);
        }

        if ($vatReturnFillingMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $params = array('autoID' => $id,
                'company' => $vatReturnFillingMaster->companySystemID,
                'document' => $vatReturnFillingMaster->documentSystemID,
                'segment' => null,
                'category' => 0,
                'amount' => 0
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        return $this->sendResponse($vatReturnFillingMaster->toArray(), 'VatReturnFillingMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/vatReturnFillingMasters/{id}",
     *      summary="Remove the specified VatReturnFillingMaster from storage",
     *      tags={"VatReturnFillingMaster"},
     *      description="Delete VatReturnFillingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingMaster",
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
        /** @var VatReturnFillingMaster $vatReturnFillingMaster */
        $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->findWithoutFail($id);

        if (empty($vatReturnFillingMaster)) {
            return $this->sendError('Vat Return Filling Master not found');
        }
        DB::beginTransaction();
        try {
            $deletCat = VatReturnFilledCategory::where('vatReturnFillingID', $id)
                                               ->delete();

            $subCatgeories = VatReturnFillingDetail::where('vatReturnFillingID', $id)
                                                   ->get();


            $detailsIds = collect($subCatgeories)->pluck('id')->toArray();


            $subCatgeoriesDelete = VatReturnFillingDetail::where('vatReturnFillingID', $id)
                                                         ->delete();

            $updateTaxDetails = TaxLedgerDetail::whereIn('returnFilledDetailID', $detailsIds)
                                                                      ->update(['returnFilledDetailID' => null]);

            $vatReturnFillingMaster->delete();
            DB::commit();
            return $this->sendResponse([], 'Vat Return Filling Master deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage().$e->getLine(), 500);
        }
    }

    public function getVatReturnFillings(Request $request)
    {

        $input = $request->all();

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

        $results = VatReturnFillingMaster::whereIn('companySystemID', $subCompanies);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $results = $results->where(function ($query) use ($search) {
                $query->where('comment', 'like', "%{$search}%")
                      ->orWhere('returnFillingCode', 'like', "%{$search}%");
            });
        }

        $results = $results->selectRaw('*, CASE WHEN serialNo = (SELECT MAX(serialNo) FROM vat_return_filling_master) THEN 1 ELSE 0 END AS isLast');

        return \DataTables::of($results)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getVatReturnFillingDetails(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $vatReturnFillingMaster = VatReturnFillingMaster::find($input['vatReturnFillingID']);

        $res = $this->vatReturnFillingMasterRepository->generateFilling($vatReturnFillingMaster->date, $input['vatReturnFillingSubCatgeoryID'], $input['companyId'], true, $input['returnFilledDetailID'], $vatReturnFillingMaster->confirmedYN,null,false);

        $results = $res;

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $results = $results->where(function ($query) use ($search) {
                $query->where('documentNumber', 'LIKE', "%{$search}%")
                    ->orWhereHas('supplier', function ($q1) use($search){
                       $q1->where('supplierName','LIKE',"%{$search}%");
                    })
                    ->orWhereHas('customer', function ($q1) use($search){
                        $q1->where('CustomerName','LIKE',"%{$search}%");
                    });
            });

        }
        return \DataTables::of($results)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        // $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getVATReturnFillingData(Request $request)
    {
        $input = $request->all();

        $vatReturnFilling = VatReturnFillingMaster::with(['filled_master_categories' => function($query) {
                                                    $query->with(['filled_details' => function($query) {
                                                        $query->with(['category']);
                                                    }, 'category']);
                                                }, 'confirmed_by'])
                                                ->where('id', $input['ID'])
                                                ->first();

        if($vatReturnFilling){
            $lastItem = VatReturnFillingMaster::where('companySystemID', $vatReturnFilling->companySystemID)->max('serialNo');
            $vatReturnFilling->isLastItem = $lastItem == $vatReturnFilling->serialNo ? 1 : 0;
        }

        return $this->sendResponse($vatReturnFilling, "VAT return filling data retrieved successfully");

    }

    public function getVATReturnFillingFormData(Request $request)
    {
        $companyId = $request['companyId'];
        $yesNoSelection = YesNoSelection::all();

        $company = Company::where('companySystemID', $request['companyId'])->first();

        $localCurrency = CurrencyMaster::where('currencyID', $company->localCurrencyID)->first();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'currency' => $localCurrency
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function updateVatReturnFillingDetails(Request $request)
    {
        $input = $request->all();

        if (isset($input['isClaimed']) && $input['isClaimed']) {
            $updateData = [
                'returnFilledDetailID' => $input['returnFilledDetailID']
            ];    
        } else {
            $updateData = [
                'returnFilledDetailID' => null
            ];    
        }

        TaxLedgerDetail::where('id', $input['id'])->update($updateData);

        $this->vatReturnFillingMasterRepository->updateVatReturnFillingDetails($input['returnFilledDetailID']);

        return $this->sendResponse([], 'Record updated successfully');
    }

    public function vatReturnFillingReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['returnFillingID'];
        $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->findWithoutFail($id);
        $emails = array();
        if (empty($vatReturnFillingMaster)) {
            return $this->sendError('VAT Return filling not found');
        }

        if ($vatReturnFillingMaster->approvedYN == -1) {
            return $this->sendError('You cannot reopen this VAT Return filling it is already fully approved');
        }

        if ($vatReturnFillingMaster->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this VAT Return filling it is already partially approved');
        }

        if ($vatReturnFillingMaster->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this VAT Return filling, it is not confirmed');
        }

        $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
            'confirmedByEmpName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1];

        $this->vatReturnFillingMasterRepository->update($updateInput, $id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $vatReturnFillingMaster->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $vatReturnFillingMaster->returnFillingCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $vatReturnFillingMaster->returnFillingCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $vatReturnFillingMaster->companySystemID)
                                            ->where('documentSystemCode', $vatReturnFillingMaster->id)
                                            ->where('documentSystemID', $vatReturnFillingMaster->documentSystemID)
                                            ->where('rollLevelOrder', 1)
                                            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $vatReturnFillingMaster->companySystemID)
                    ->where('documentSystemID', $vatReturnFillingMaster->documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

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
            ->where('companySystemID', $vatReturnFillingMaster->companySystemID)
            ->where('documentSystemID', $vatReturnFillingMaster->documentSystemID)
            ->delete();

        return $this->sendResponse($vatReturnFillingMaster->toArray(), 'VAT Return filling reopened successfully');
    }

     public function getVRFApprovedByUser(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $budgets = DB::table('erp_documentapproved')
            ->select(
                'vat_return_filling_master.*',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('vat_return_filling_master', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->where('vat_return_filling_master.companySystemID', $companyId)
                    ->where('vat_return_filling_master.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [104])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $budgets = $budgets->where(function ($query) use ($search) {
                $query->where('returnFillingCode', 'like', "%{$search}%")
                    ->orWhere('vat_return_filling_master.comment', 'like', "%{$search}%");
            });
        }

        return \DataTables::of($budgets)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    public function getVRFApprovalByUser(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $budgets = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'vat_return_filling_master.*',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', 104)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [104])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('vat_return_filling_master', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('vat_return_filling_master.companySystemID', $companyId)
                    ->where('vat_return_filling_master.approvedYN', 0)
                    ->where('vat_return_filling_master.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [104])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $budgets = $budgets->where(function ($query) use ($search) {
                $query->where('returnFillingCode', 'like', "%{$search}%")
                      ->orWhere('vat_return_filling_master.comment', 'like', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $budgets = [];
        }

        return \DataTables::of($budgets)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


     public function getVRFAmend(Request $request)
    {
        $input = $request->all();

        $returnFillingID = $input['returnFillingID'];

        $vrfData = VatReturnFillingMaster::find($returnFillingID);
        if (empty($vrfData)) {
            return $this->sendError('VAT Return Filling not found');
        }

        if ($vrfData->refferedBackYN != -1) {
            return $this->sendError('You cannot amend this VAT Return Filling');
        }

        $vrfArray = $vrfData->toArray();
        $vrfArray['returnFillingID'] = $vrfArray['id'];
        unset($vrfArray['id']);
        $storeDeliveryOrderHistory = VatReturnFillingMasterRefferedback::insert($vrfArray);

        $fetchVatReturnFilledCategory = VatReturnFilledCategory::where('vatReturnFillingID', $returnFillingID)
                                                               ->get();

        $vrfFilledArray = $fetchVatReturnFilledCategory->toArray();
        $vrfFilledArrayNew = [];
        foreach ($vrfFilledArray as $key => $value) {
            $value['returnFilledCategoryID'] = $value['id'];
            unset($value['id']);
            $vrfFilledArrayNew[] = $value;
        }

        $storeDeliveryOrderDetaillHistory = VatReturnFilledCategoryRefferedback::insert($vrfFilledArrayNew);

        $fetchVatReturnFillingDetails = VatReturnFillingDetail::where('vatReturnFillingID', $returnFillingID)
                                                               ->get();

        $vrfFillingDetailsArray = $fetchVatReturnFillingDetails->toArray();
        $vrfFilledDetailsArrayNew = [];
        foreach ($vrfFillingDetailsArray as $key => $value) {
            $value['returnFillingDetailID'] = $value['id'];
            unset($value['id']);
            $vrfFilledDetailsArrayNew[] = $value;
        }
        $storeDeliveryOrderDetaillHistory = VatReturnFillingDetailsRefferedback::insert($vrfFilledDetailsArrayNew);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $returnFillingID)
            ->where('companySystemID', $vrfData->companySystemID)
            ->where('documentSystemID', $vrfData->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $vrfData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $returnFillingID)
            ->where('companySystemID', $vrfData->companySystemID)
            ->where('documentSystemID', $vrfData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $vrfData->refferedBackYN = 0;
            $vrfData->confirmedYN = 0;
            $vrfData->confirmedByEmpSystemID = null;
            $vrfData->confirmedByEmpID = null;
            $vrfData->confirmedByEmpName = null;
            $vrfData->confirmedDate = null;
            $vrfData->RollLevForApp_curr = 1;
            $vrfData->save();
        }

        return $this->sendResponse($vrfData->toArray(), 'VAT Return Filling Amend successfully');
    }

    public function vatReturnFillingAmend(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $employee = Helper::getEmployeeInfo();
        $emails = array();

        $masterData = VatReturnFillingMaster::find($id);

        if (empty($masterData)) {
            return $this->sendError('Vat return filling not found');
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend this Vat Return Filling, it is not confirmed');
        }

        $emailBody = '<p>' . $masterData->returnFillingCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->returnFillingCode . ' has been return back to amend';

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($masterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $masterData->confirmedByEmpSystemID,
                    'companySystemID' => $masterData->companySystemID,
                    'docSystemID' => $masterData->documentSystemID,
                    'docSystemCode' => $id,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docCode' => $masterData->returnFillingCode
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
                        'docSystemCode' => $id,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docCode' => $masterData->returnFillingCode
                    );
                }
            }

            $sendEmail = Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->delete();

            // updating fields
            $masterData->confirmedYN = 0;
            $masterData->confirmedByEmpSystemID = null;
            $masterData->confirmedByEmpID = null;
            $masterData->confirmedByEmpName = null;
            $masterData->confirmedDate = null;
            $masterData->RollLevForApp_curr = 1;

            $masterData->approvedYN = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedEmpID = null;
            $masterData->approvedDate = null;
            $masterData->save();

            AuditTrial::createAuditTrial($masterData->documentSystemID,$id,$input['returnComment'],'returned back to amend');

            DB::commit();
            return $this->sendResponse($masterData->toArray(), 'Vat Return Filling amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
}
