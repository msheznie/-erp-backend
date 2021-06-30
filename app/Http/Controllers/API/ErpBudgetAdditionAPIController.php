<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateErpBudgetAdditionAPIRequest;
use App\Http\Requests\API\UpdateErpBudgetAdditionAPIRequest;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\ErpBudgetAddition;
use App\Repositories\ErpBudgetAdditionRepository;
use Illuminate\Http\Request;
use Response;

/**
 * Class ErpBudgetAdditionController
 *
 * @package App\Http\Controllers\API
 */
class ErpBudgetAdditionAPIController extends AppBaseController
{
    /** @var  ErpBudgetAdditionRepository */
    private $erpBudgetAdditionRepository;

    public function __construct(ErpBudgetAdditionRepository $erpBudgetAdditionRepo)
    {
        $this->erpBudgetAdditionRepository = $erpBudgetAdditionRepo;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpBudgetAdditions",
     *      summary="Get a listing of the ErpBudgetAdditions.",
     *      tags={"ErpBudgetAddition"},
     *      description="Get all ErpBudgetAdditions",
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
     *                  @SWG\Items(ref="#/definitions/ErpBudgetAddition")
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
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approvedYN', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $budgetTransfer = $this->erpBudgetAdditionRepository->budgetAdditionFormListQuery($request, $input, $search);

        return \DataTables::of($budgetTransfer)
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

    /**
     * @param CreateErpBudgetAdditionAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpBudgetAdditions",
     *      summary="Store a newly created ErpBudgetAddition in storage",
     *      tags={"ErpBudgetAddition"},
     *      description="Store ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpBudgetAddition that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpBudgetAddition")
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
     *                  ref="#/definitions/ErpBudgetAddition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpBudgetAdditionAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdDate'] = now();

        $validator = \Validator::make($input, [
            'year' => 'required|numeric|min:1',
            'comments' => 'required',
            'templatesMasterAutoID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['documentSystemID'] = 46;
        $input['documentID'] = 'BTN';

        $lastSerial = ErpBudgetAddition::where('companySystemID', $input['companySystemID'])
            ->orderBy('id', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]), 500);
        }

        $input['companyID'] = $company->CompanyID;
        $input['serialNo'] = $lastSerialNumber;
        $input['RollLevForApp_curr'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $code = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['additionVoucherNo'] = $code;
        }

        $budgetTransferForms = $this->erpBudgetAdditionRepository->create($input);

        return $this->sendResponse($budgetTransferForms->toArray(), 'Budget Addition Form saved successfully');
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpBudgetAdditions/{id}",
     *      summary="Display the specified ErpBudgetAddition",
     *      tags={"ErpBudgetAddition"},
     *      description="Get ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAddition",
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
     *                  ref="#/definitions/ErpBudgetAddition"
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
        /** @var ErpBudgetAddition $erpBudgetAddition */
        $erpBudgetAddition = $this->erpBudgetAdditionRepository->findWithoutFail($id);

        if (empty($erpBudgetAddition)) {
            return $this->sendError('Erp Budget Addition not found');
        }

        return $this->sendResponse($erpBudgetAddition->toArray(), 'Erp Budget Addition retrieved successfully');
    }

    /**
     * @param int                               $id
     * @param UpdateErpBudgetAdditionAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpBudgetAdditions/{id}",
     *      summary="Update the specified ErpBudgetAddition in storage",
     *      tags={"ErpBudgetAddition"},
     *      description="Update ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAddition",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpBudgetAddition that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpBudgetAddition")
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
     *                  ref="#/definitions/ErpBudgetAddition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpBudgetAdditionAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpBudgetAddition $erpBudgetAddition */
        $erpBudgetAddition = $this->erpBudgetAdditionRepository->findWithoutFail($id);

        if (empty($erpBudgetAddition)) {
            return $this->sendError('Erp Budget Addition not found');
        }

        $erpBudgetAddition = $this->erpBudgetAdditionRepository->update($input, $id);

        return $this->sendResponse($erpBudgetAddition->toArray(), 'ErpBudgetAddition updated successfully');
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpBudgetAdditions/{id}",
     *      summary="Remove the specified ErpBudgetAddition from storage",
     *      tags={"ErpBudgetAddition"},
     *      description="Delete ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAddition",
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
        /** @var ErpBudgetAddition $erpBudgetAddition */
        $erpBudgetAddition = $this->erpBudgetAdditionRepository->findWithoutFail($id);

        if (empty($erpBudgetAddition)) {
            return $this->sendError('Erp Budget Addition not found');
        }

        $erpBudgetAddition->delete();

        return $this->sendSuccess('Erp Budget Addition deleted successfully');
    }
}
