<?php
/**
 * =============================================
 * -- File Name : BudgetTransferFormAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Budget Transfer
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - August 2018
 * -- Description : This file contains the all CRUD for Budget Transfer
 * -- REVISION HISTORY
 * -- Date: 08-August 2018 By: Nazir Description: Added new function getBudgetTransferMasterByCompany()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetTransferFormAPIRequest;
use App\Http\Requests\API\UpdateBudgetTransferFormAPIRequest;
use App\Models\BudgetTransferForm;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\Year;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\BudgetTransferFormRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetTransferFormController
 * @package App\Http\Controllers\API
 */

class BudgetTransferFormAPIController extends AppBaseController
{
    /** @var  BudgetTransferFormRepository */
    private $budgetTransferFormRepository;

    public function __construct(BudgetTransferFormRepository $budgetTransferFormRepo)
    {
        $this->budgetTransferFormRepository = $budgetTransferFormRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferForms",
     *      summary="Get a listing of the BudgetTransferForms.",
     *      tags={"BudgetTransferForm"},
     *      description="Get all BudgetTransferForms",
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
     *                  @SWG\Items(ref="#/definitions/BudgetTransferForm")
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
        $this->budgetTransferFormRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetTransferFormRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetTransferForms = $this->budgetTransferFormRepository->all();

        return $this->sendResponse($budgetTransferForms->toArray(), 'Budget Transfer Forms retrieved successfully');
    }

    /**
     * @param CreateBudgetTransferFormAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetTransferForms",
     *      summary="Store a newly created BudgetTransferForm in storage",
     *      tags={"BudgetTransferForm"},
     *      description="Store BudgetTransferForm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferForm that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferForm")
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
     *                  ref="#/definitions/BudgetTransferForm"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetTransferFormAPIRequest $request)
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
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['documentSystemID'] = 46;
        $input['documentID'] = 'BTN';

        $lastSerial = BudgetTransferForm::where('companySystemID', $input['companySystemID'])
            ->orderBy('budgetTransferFormAutoID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError('Company not found', 500);
        }

        $input['companyID'] = $company->CompanyID;
        $input['serialNo'] = $lastSerialNumber;
        $input['RollLevForApp_curr'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $code = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['transferVoucherNo'] = $code;
        }

        $budgetTransferForms = $this->budgetTransferFormRepository->create($input);

        return $this->sendResponse($budgetTransferForms->toArray(), 'Budget Transfer Form saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferForms/{id}",
     *      summary="Display the specified BudgetTransferForm",
     *      tags={"BudgetTransferForm"},
     *      description="Get BudgetTransferForm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferForm",
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
     *                  ref="#/definitions/BudgetTransferForm"
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
        /** @var BudgetTransferForm $budgetTransferForm */
        $budgetTransferForm = $this->budgetTransferFormRepository->with(['company.reportingcurrency','created_by','confirmed_by'])->findWithoutFail($id);

        if (empty($budgetTransferForm)) {
            return $this->sendError('Budget Transfer Form not found');
        }

        return $this->sendResponse($budgetTransferForm->toArray(), 'Budget Transfer Form retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBudgetTransferFormAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetTransferForms/{id}",
     *      summary="Update the specified BudgetTransferForm in storage",
     *      tags={"BudgetTransferForm"},
     *      description="Update BudgetTransferForm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferForm",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferForm that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferForm")
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
     *                  ref="#/definitions/BudgetTransferForm"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetTransferFormAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by','confirmed_by','company']);
        $input = $this->convertArrayToValue($input);
        /** @var BudgetTransferForm $budgetTransferForm */
        $budgetTransferForm = $this->budgetTransferFormRepository->findWithoutFail($id);

        if (empty($budgetTransferForm)) {
            return $this->sendError('Budget Transfer Form not found');
        }

        $budgetTransferForm = $this->budgetTransferFormRepository->update(array_only($input, ['comments','year']), $id);

        return $this->sendResponse($budgetTransferForm->toArray(), 'Budget Transfer updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetTransferForms/{id}",
     *      summary="Remove the specified BudgetTransferForm from storage",
     *      tags={"BudgetTransferForm"},
     *      description="Delete BudgetTransferForm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferForm",
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
        /** @var BudgetTransferForm $budgetTransferForm */
        $budgetTransferForm = $this->budgetTransferFormRepository->findWithoutFail($id);

        if (empty($budgetTransferForm)) {
            return $this->sendError('Budget Transfer Form not found');
        }

        $budgetTransferForm->delete();

        return $this->sendResponse($id, 'Budget Transfer Form deleted successfully');
    }

    public function getBudgetTransferMasterByCompany(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approvedYN', 'year'));

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

        $budgetTransfer = BudgetTransferForm::whereIn('companySystemID', $subCompanies)
            ->with('created_by','confirmed_by')
            ->where('documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $budgetTransfer = $budgetTransfer->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $budgetTransfer = $budgetTransfer->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $budgetTransfer = $budgetTransfer->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $budgetTransfer = $budgetTransfer->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $budgetTransfer = $budgetTransfer->where(function ($query) use ($search) {
                $query->where('transferVoucherNo', 'LIKE', "%{$search}%")
                      ->orWhere('comments', 'like', "%{$search}%");
            });
        }

        return \DataTables::of($budgetTransfer)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('budgetTransferFormAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBudgetTransferFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = Year::orderBy('year','desc')->get();

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $segments = SegmentMaster::where("companySystemID", $companyId)
                                 ->where('isActive', 1)->get();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'companyFinanceYear' => $companyFinanceYear,
            'segments' => $segments
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

}
