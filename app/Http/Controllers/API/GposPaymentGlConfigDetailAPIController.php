<?php
/**
 * =============================================
 * -- File Name : GposPaymentGlConfigDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  General pos payment gl config detail
 * -- Author : Mohamed Fayas
 * -- Create date : 08 - January 2019
 * -- Description : This file contains the all CRUD for general pos payment gl config detail
 * -- REVISION HISTORY
 * -- Date: 08 - January 2019 By: Fayas Description: Added new function getConfigByCompany(),getFormData()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGposPaymentGlConfigDetailAPIRequest;
use App\Http\Requests\API\UpdateGposPaymentGlConfigDetailAPIRequest;
use App\Models\GposPaymentGlConfigDetail;
use App\Models\GposPaymentGlConfigMaster;
use App\Models\WarehouseMaster;
use App\Repositories\GposPaymentGlConfigDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GposPaymentGlConfigDetailController
 * @package App\Http\Controllers\API
 */
class GposPaymentGlConfigDetailAPIController extends AppBaseController
{
    /** @var  GposPaymentGlConfigDetailRepository */
    private $gposPaymentGlConfigDetailRepository;

    public function __construct(GposPaymentGlConfigDetailRepository $gposPaymentGlConfigDetailRepo)
    {
        $this->gposPaymentGlConfigDetailRepository = $gposPaymentGlConfigDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/gposPaymentGlConfigDetails",
     *      summary="Get a listing of the GposPaymentGlConfigDetails.",
     *      tags={"GposPaymentGlConfigDetail"},
     *      description="Get all GposPaymentGlConfigDetails",
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
     *                  @SWG\Items(ref="#/definitions/GposPaymentGlConfigDetail")
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
        $this->gposPaymentGlConfigDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->gposPaymentGlConfigDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gposPaymentGlConfigDetails = $this->gposPaymentGlConfigDetailRepository->all();

        return $this->sendResponse($gposPaymentGlConfigDetails->toArray(), trans('custom.gpos_payment_gl_config_details_retrieved_successfu'));
    }

    /**
     * @param CreateGposPaymentGlConfigDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/gposPaymentGlConfigDetails",
     *      summary="Store a newly created GposPaymentGlConfigDetail in storage",
     *      tags={"GposPaymentGlConfigDetail"},
     *      description="Store GposPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GposPaymentGlConfigDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GposPaymentGlConfigDetail")
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
     *                  ref="#/definitions/GposPaymentGlConfigDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGposPaymentGlConfigDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $messages = array(
            'warehouseID.required' => trans('custom.validation_outlet_field_required'),
            'paymentConfigMasterID.required' => trans('custom.validation_payment_type_field_required'),
            'GLCode.required' => trans('custom.validation_account_name_field_required'),
            'GLCode.min' => trans('custom.validation_account_name_field_required')
        );

        $validator = \Validator::make($input, [
            'paymentConfigMasterID' => 'required',
            'companyID' => 'required',
            'GLCode' => 'required|numeric|min:1',
            'warehouseID' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $checkPaymentType = GposPaymentGlConfigDetail::where('companyID', $input['companyID'])
            ->where('warehouseID', $input['warehouseID'])
            ->where('paymentConfigMasterID', $input['paymentConfigMasterID'])
            ->count();

        if ($checkPaymentType > 0) {
            return $this->sendError(trans('custom.payment_type_already_exists_in_selected_outlet'), 500);
        }

        $input['companyCode'] = \Helper::getCompanyById($input['companyID']);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdUserName'] = $employee->empName;

        $gposPaymentGlConfigDetails = $this->gposPaymentGlConfigDetailRepository->create($input);

        return $this->sendResponse($gposPaymentGlConfigDetails->toArray(), trans('custom.payment_gl_config_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/gposPaymentGlConfigDetails/{id}",
     *      summary="Display the specified GposPaymentGlConfigDetail",
     *      tags={"GposPaymentGlConfigDetail"},
     *      description="Get GposPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposPaymentGlConfigDetail",
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
     *                  ref="#/definitions/GposPaymentGlConfigDetail"
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
        /** @var GposPaymentGlConfigDetail $gposPaymentGlConfigDetail */
        $gposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigDetail)) {
            return $this->sendError(trans('custom.gpos_payment_gl_config_detail_not_found'));
        }

        return $this->sendResponse($gposPaymentGlConfigDetail->toArray(), trans('custom.gpos_payment_gl_config_detail_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdateGposPaymentGlConfigDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/gposPaymentGlConfigDetails/{id}",
     *      summary="Update the specified GposPaymentGlConfigDetail in storage",
     *      tags={"GposPaymentGlConfigDetail"},
     *      description="Update GposPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposPaymentGlConfigDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GposPaymentGlConfigDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GposPaymentGlConfigDetail")
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
     *                  ref="#/definitions/GposPaymentGlConfigDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGposPaymentGlConfigDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['warehouse','account','type']);
        $input = $this->convertArrayToValue($input);
        /** @var GposPaymentGlConfigDetail $gposPaymentGlConfigDetail */
        $gposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigDetail)) {
            return $this->sendError(trans('custom.payment_gl_config_not_found'));
        }

        $messages = array(
            'warehouseID.required' => trans('custom.validation_outlet_field_required'),
            'paymentConfigMasterID.required' => trans('custom.validation_payment_type_field_required'),
            'GLCode.required' => trans('custom.validation_account_name_field_required'),
            'GLCode.min' => trans('custom.validation_account_name_field_required')
        );

        $validator = \Validator::make($input, [
            'paymentConfigMasterID' => 'required',
            'companyID' => 'required',
            'GLCode' => 'required|numeric|min:1',
            'warehouseID' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $checkPaymentType = GposPaymentGlConfigDetail::where('companyID', $input['companyID'])
            ->where('ID','!=', $id)
            ->where('warehouseID', $input['warehouseID'])
            ->where('paymentConfigMasterID', $input['paymentConfigMasterID'])
            ->count();

        if ($checkPaymentType > 0) {
            return $this->sendError(trans('custom.payment_type_already_exists_in_selected_outlet'), 500);
        }

        $input['companyCode'] = \Helper::getCompanyById($input['companyID']);
        $employee = \Helper::getEmployeeInfo();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifiedUserName'] = $employee->empName;
        $input['timestamp'] = now();

        $gposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepository->update($input, $id);

        return $this->sendResponse($gposPaymentGlConfigDetail->toArray(), trans('custom.gpospaymentglconfigdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/gposPaymentGlConfigDetails/{id}",
     *      summary="Remove the specified GposPaymentGlConfigDetail from storage",
     *      tags={"GposPaymentGlConfigDetail"},
     *      description="Delete GposPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposPaymentGlConfigDetail",
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
        /** @var GposPaymentGlConfigDetail $gposPaymentGlConfigDetail */
        $gposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigDetail)) {
            return $this->sendError(trans('custom.gpos_payment_gl_config_detail_not_found'));
        }

        $gposPaymentGlConfigDetail->delete();

        return $this->sendResponse($id, trans('custom.gpos_payment_gl_config_detail_deleted_successfully'));
    }

    public function getConfigByCompany(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('warehouseID','paymentConfigMasterID'));

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

        $counters = GposPaymentGlConfigDetail::whereIn('companyID', $subCompanies)
            ->with(['warehouse','account','type']);

        if (array_key_exists('warehouseID', $input)) {
            if ($input['warehouseID'] && !is_null($input['warehouseID'])) {
                $counters->where('warehouseID', $input['warehouseID']);
            }
        }

        if (array_key_exists('paymentConfigMasterID', $input)) {
            if ($input['paymentConfigMasterID'] && !is_null($input['paymentConfigMasterID'])) {
                $counters->where('paymentConfigMasterID', $input['paymentConfigMasterID']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $counters = $counters->where(function ($query) use ($search) {
                $query->whereHas('account', function ($q1) use ($search) {
                    $q1->where('AccountCode', 'like', "%{$search}%")
                        //->orWhere('AccountDescription', 'like', "%{$search}%")
                        ->orWhere('AccountDescription', 'like', "%{$search}%");
                })->orWhereHas('warehouse', function ($q1) use ($search) {
                    $q1->where('wareHouseCode', 'like', "%{$search}%")
                        ->orWhere('wareHouseDescription', 'like', "%{$search}%");
                });
            });
        }

        return \DataTables::of($counters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('ID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $outlets = WarehouseMaster::where("companySystemID", $companyId)
            ->where('isPosLocation', -1)
            ->when(request('isFilter') == 0, function ($q) {
                return $q->where('isActive', 1);
            })
            ->get();

        $paymentTypes = GposPaymentGlConfigMaster::all();

        $output = array(
            'outlets' => $outlets,
            'paymentTypes' => $paymentTypes
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }
}
