<?php
/**
 * =============================================
 * -- File Name : SalesPersonTargetAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  SalesPersonTarget
 * -- Author : Mohamed Nazir
 * -- Create date : 21 - January 2019
 * -- Description : This file contains the all CRUD for Sales Person Target
 * -- REVISION HISTORY
 * -- Date: 21-January 2019 By: Nazir Description: Added new function checkSalesPersonLastTarget(),
 * -- Date: 21-January 2019 By: Nazir Description: Added new function getSalesPersonTargetDetails(),
 */


namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalesPersonTargetAPIRequest;
use App\Http\Requests\API\UpdateSalesPersonTargetAPIRequest;
use App\Models\SalesPersonMaster;
use App\Models\SalesPersonTarget;
use App\Models\Company;
use App\Repositories\SalesPersonTargetRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SalesPersonTargetController
 * @package App\Http\Controllers\API
 */
class SalesPersonTargetAPIController extends AppBaseController
{
    /** @var  SalesPersonTargetRepository */
    private $salesPersonTargetRepository;

    public function __construct(SalesPersonTargetRepository $salesPersonTargetRepo)
    {
        $this->salesPersonTargetRepository = $salesPersonTargetRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesPersonTargets",
     *      summary="Get a listing of the SalesPersonTargets.",
     *      tags={"SalesPersonTarget"},
     *      description="Get all SalesPersonTargets",
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
     *                  @SWG\Items(ref="#/definitions/SalesPersonTarget")
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
        $this->salesPersonTargetRepository->pushCriteria(new RequestCriteria($request));
        $this->salesPersonTargetRepository->pushCriteria(new LimitOffsetCriteria($request));
        $salesPersonTargets = $this->salesPersonTargetRepository->all();

        return $this->sendResponse($salesPersonTargets->toArray(), trans('custom.sales_person_targets_retrieved_successfully'));
    }

    /**
     * @param CreateSalesPersonTargetAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/salesPersonTargets",
     *      summary="Store a newly created SalesPersonTarget in storage",
     *      tags={"SalesPersonTarget"},
     *      description="Store SalesPersonTarget",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesPersonTarget that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesPersonTarget")
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
     *                  ref="#/definitions/SalesPersonTarget"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSalesPersonTargetAPIRequest $request)
    {
        $input = $request->all();

        $employee = \Helper::getEmployeeInfo();

        $masterData = SalesPersonMaster::find($input['salesPersonID']);

        if (empty($masterData)) {
            return $this->sendError(trans('custom.sales_person_master_not_found'));
        }

        if ($input["percentage"] > 100) {
            return $this->sendError('Percentage % should be between 0 - 100');
        }

        if ($input["toTargetAmount"] <= $input["fromTargetAmount"]) {
            return $this->sendError(trans('custom.start_amount_cannot_be_greater_than_end_amount'));
        }

        if (isset($request->targetID) && !empty($request->targetID)) {

            $salesPersonTargetUpdate = SalesPersonTarget::find($request->targetID);
            $salesPersonTargetUpdate->percentage = $input["percentage"];
            $salesPersonTargetUpdate->toTargetAmount = $input["toTargetAmount"];
            $salesPersonTargetUpdate->modifiedPCID = gethostname();
            $salesPersonTargetUpdate->modifiedUserID = $employee->empID;
            $salesPersonTargetUpdate->save();

        } else {
            $companyMaster = Company::find($input['companySystemID']);

            $input['currencyID'] = $masterData->salesPersonCurrencyID;
            $input['companyID'] = $companyMaster->CompanyID;
            $input['createdPCID'] = gethostname();
            $input['createdUserID'] = $employee->empID;
            $salesPersonTargets = $this->salesPersonTargetRepository->create($input);
        }


        return $this->sendResponse($masterData->toArray(), trans('custom.sales_person_target_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesPersonTargets/{id}",
     *      summary="Display the specified SalesPersonTarget",
     *      tags={"SalesPersonTarget"},
     *      description="Get SalesPersonTarget",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesPersonTarget",
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
     *                  ref="#/definitions/SalesPersonTarget"
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
        /** @var SalesPersonTarget $salesPersonTarget */
        $salesPersonTarget = $this->salesPersonTargetRepository->findWithoutFail($id);

        if (empty($salesPersonTarget)) {
            return $this->sendError(trans('custom.sales_person_target_not_found'));
        }

        return $this->sendResponse($salesPersonTarget->toArray(), trans('custom.sales_person_target_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSalesPersonTargetAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/salesPersonTargets/{id}",
     *      summary="Update the specified SalesPersonTarget in storage",
     *      tags={"SalesPersonTarget"},
     *      description="Update SalesPersonTarget",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesPersonTarget",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesPersonTarget that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesPersonTarget")
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
     *                  ref="#/definitions/SalesPersonTarget"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSalesPersonTargetAPIRequest $request)
    {
        $input = $request->all();

        /** @var SalesPersonTarget $salesPersonTarget */
        $salesPersonTarget = $this->salesPersonTargetRepository->findWithoutFail($id);

        if (empty($salesPersonTarget)) {
            return $this->sendError(trans('custom.sales_person_target_not_found'));
        }

        $salesPersonTarget = $this->salesPersonTargetRepository->update($input, $id);

        return $this->sendResponse($salesPersonTarget->toArray(), trans('custom.salespersontarget_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/salesPersonTargets/{id}",
     *      summary="Remove the specified SalesPersonTarget from storage",
     *      tags={"SalesPersonTarget"},
     *      description="Delete SalesPersonTarget",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesPersonTarget",
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
        /** @var SalesPersonTarget $salesPersonTarget */
        $salesPersonTarget = $this->salesPersonTargetRepository->findWithoutFail($id);

        if (empty($salesPersonTarget)) {
            return $this->sendError(trans('custom.sales_person_target_not_found'));
        }

        $salesPersonTarget->delete();

        return $this->sendResponse($id, trans('custom.sales_person_target_deleted_successfully'));
    }

    public function checkSalesPersonLastTarget(Request $request)
    {
        $input = $request->all();

        $lastValue = 0;
        $checkTaxExist = SalesPersonTarget::where('salesPersonID', $input['salesPersonID'])
            ->orderBy('targetID', 'desc')
            ->first();
        if ($checkTaxExist) {
            $lastValue = ($checkTaxExist->toTargetAmount + 1);
        }

        return $this->sendResponse($lastValue, 'data retrived');

    }

    public function getSalesPersonTargetDetails(Request $request)
    {
        $input = $request->all();
        $salesPersonID = $input['salesPersonID'];

        $items = SalesPersonTarget::where('salesPersonID', $salesPersonID)
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.sales_person_target_retrieved_successfully_1'));
    }
}
