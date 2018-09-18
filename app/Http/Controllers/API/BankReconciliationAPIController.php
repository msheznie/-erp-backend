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
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankReconciliationAPIRequest;
use App\Http\Requests\API\UpdateBankReconciliationAPIRequest;
use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\Company;
use App\Repositories\BankReconciliationRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankReconciliationController
 * @package App\Http\Controllers\API
 */
class BankReconciliationAPIController extends AppBaseController
{
    /** @var  BankReconciliationRepository */
    private $bankReconciliationRepository;

    public function __construct(BankReconciliationRepository $bankReconciliationRepo)
    {
        $this->bankReconciliationRepository = $bankReconciliationRepo;
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

        return $this->sendResponse($bankReconciliations->toArray(), 'Bank Reconciliations retrieved successfully');
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
            'bankRecAsOf' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['bankRecAsOf'] = new Carbon($input['bankRecAsOf']);

        $input['documentSystemID'] = 62;
        $input['documentID'] = 'BRC';

        $bankAccount = BankAccount::find($input['bankAccountAutoID']);

        if (!empty($bankAccount)) {
            $input['bankGLAutoID'] = $bankAccount->chartOfAccountSystemID;
            $input['companySystemID'] = $bankAccount->companySystemID;
        } else {
            return $this->sendError('bank Account not found.!', 500);
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

        $bankReconciliations = $this->bankReconciliationRepository->create($input);

        return $this->sendResponse($bankReconciliations->toArray(), 'Bank Reconciliation saved successfully');
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
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            return $this->sendError('Bank Reconciliation not found');
        }

        return $this->sendResponse($bankReconciliation->toArray(), 'Bank Reconciliation retrieved successfully');
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

        /** @var BankReconciliation $bankReconciliation */
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            return $this->sendError('Bank Reconciliation not found');
        }

        $bankReconciliation = $this->bankReconciliationRepository->update($input, $id);

        return $this->sendResponse($bankReconciliation->toArray(), 'BankReconciliation updated successfully');
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
            return $this->sendError('Bank Reconciliation not found');
        }

        $bankReconciliation->delete();

        return $this->sendResponse($id, 'Bank Reconciliation deleted successfully');
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

        $logistics = BankReconciliation::whereIn('companySystemID', $subCompanies)
                                               ->where("bankAccountAutoID",$input['bankAccountAutoID'])
                                               ->with(['month','created_by','bank_account']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $logistics = $logistics->where(function ($query) use ($search) {
                $query->where('bankRecPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($logistics)
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

    public function getcheckBeforeCreate(Request $request)
    {



    }

}
