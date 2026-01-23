<?php
/**
 * =============================================
 * -- File Name : ConsoleJVMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  General Ledger
 * -- Author : Mohamed Mubashir
 * -- Create date : 06 - March 2019
 * -- Description : This file contains the all CRUD for Console JV
 * -- Date: 07 - March 2019 By: Mubashir Description: Added new functions named as getConsoleJVDetailByMaster()
 * -- Date: 10 - March 2019 By: Mubashir Description: Added new functions named as deleteAllConsoleJVDet()
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateConsoleJVDetailAPIRequest;
use App\Http\Requests\API\UpdateConsoleJVDetailAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\ConsoleJVDetail;
use App\Models\ConsoleJVMaster;
use App\Models\SegmentMaster;
use App\Repositories\ConsoleJVDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Arr;

/**
 * Class ConsoleJVDetailController
 * @package App\Http\Controllers\API
 */

class ConsoleJVDetailAPIController extends AppBaseController
{
    /** @var  ConsoleJVDetailRepository */
    private $consoleJVDetailRepository;

    public function __construct(ConsoleJVDetailRepository $consoleJVDetailRepo)
    {
        $this->consoleJVDetailRepository = $consoleJVDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/consoleJVDetails",
     *      summary="Get a listing of the ConsoleJVDetails.",
     *      tags={"ConsoleJVDetail"},
     *      description="Get all ConsoleJVDetails",
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
     *                  @SWG\Items(ref="#/definitions/ConsoleJVDetail")
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
        $this->consoleJVDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->consoleJVDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $consoleJVDetails = $this->consoleJVDetailRepository->all();

        return $this->sendResponse($consoleJVDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.console_j_v_details')]));
    }

    /**
     * @param CreateConsoleJVDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/consoleJVDetails",
     *      summary="Store a newly created ConsoleJVDetail in storage",
     *      tags={"ConsoleJVDetail"},
     *      description="Store ConsoleJVDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ConsoleJVDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ConsoleJVDetail")
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
     *                  ref="#/definitions/ConsoleJVDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateConsoleJVDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $jvMaster = ConsoleJVMaster::find($input['consoleJvMasterAutoId']);

        if (empty($jvMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_details')]));
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $input['serviceLineSystemID'] = null;
        $input['glAccountSystemID'] = null;
        $input['glDate'] = $jvMaster->consoleJVdate;

        $input['currencyID'] = $jvMaster->currencyID;
        $input['currencyER'] = $jvMaster->currencyER;

        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['createdPcID'] = gethostname();

        $consoleJVDetails = $this->consoleJVDetailRepository->create($input);

        return $this->sendResponse($consoleJVDetails->toArray(), trans('custom.save', ['attribute' => trans('custom.console_j_v_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/consoleJVDetails/{id}",
     *      summary="Display the specified ConsoleJVDetail",
     *      tags={"ConsoleJVDetail"},
     *      description="Get ConsoleJVDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ConsoleJVDetail",
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
     *                  ref="#/definitions/ConsoleJVDetail"
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
        /** @var ConsoleJVDetail $consoleJVDetail */
        $consoleJVDetail = $this->consoleJVDetailRepository->findWithoutFail($id);

        if (empty($consoleJVDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_details')]));
        }

        return $this->sendResponse($consoleJVDetail->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.console_j_v_details')]));
    }

    /**
     * @param int $id
     * @param UpdateConsoleJVDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/consoleJVDetails/{id}",
     *      summary="Update the specified ConsoleJVDetail in storage",
     *      tags={"ConsoleJVDetail"},
     *      description="Update ConsoleJVDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ConsoleJVDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ConsoleJVDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ConsoleJVDetail")
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
     *                  ref="#/definitions/ConsoleJVDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateConsoleJVDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = Arr::except($request->all(), ['segment','company','segmentList','glOption']);
        $input = $this->convertArrayToValue($input);

        /** @var ConsoleJVDetail $consoleJVDetail */
        $consoleJVDetail = $this->consoleJVDetailRepository->findWithoutFail($id);

        if (empty($consoleJVDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_details')]));
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $serviceline = SegmentMaster::find($input['serviceLineSystemID']);
        if ($serviceline) {
            $input['serviceLineCode'] = $serviceline->ServiceLineCode;
        }

        $chartOfAccount = ChartOfAccount::find($input['glAccountSystemID']);
        if ($chartOfAccount) {
            $input['glAccount'] = $chartOfAccount->AccountCode;
            $input['glAccountDescription'] = $chartOfAccount->AccountDescription;
        }

        if($input['debitAmount']){
            $conversionAmount = \Helper::convertAmountToLocalRpt(69, $input["consoleJvMasterAutoId"], $input['debitAmount']);
            $input["localDebitAmount"] = $conversionAmount["localAmount"];
            $input["rptDebitAmount"] = $conversionAmount["reportingAmount"];
        }else{
            $input['debitAmount'] = 0;
        }

        if($input['creditAmount']){
            $conversionAmount = \Helper::convertAmountToLocalRpt(69, $input["consoleJvMasterAutoId"], $input["creditAmount"]);
            $input["localCreditAmount"] = $conversionAmount["localAmount"];
            $input["rptCreditAmount"] = $conversionAmount["reportingAmount"];
        }else{
            $input['creditAmount'] = 0;
        }

        $consoleJVDetail = $this->consoleJVDetailRepository->update($input, $id);

        return $this->sendResponse($consoleJVDetail->toArray(), trans('custom.update', ['attribute' => trans('custom.console_j_v_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/consoleJVDetails/{id}",
     *      summary="Remove the specified ConsoleJVDetail from storage",
     *      tags={"ConsoleJVDetail"},
     *      description="Delete ConsoleJVDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ConsoleJVDetail",
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
        /** @var ConsoleJVDetail $consoleJVDetail */
        $consoleJVDetail = $this->consoleJVDetailRepository->findWithoutFail($id);

        if (empty($consoleJVDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_details')]));
        }

        $consoleJVDetail->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.console_j_v_details')]));
    }

    public function getConsoleJVDetailByMaster(Request $request)
    {
        $consoleJVDetail = ConsoleJVDetail::with(['segment','company'])->ofMaster($request->consoleJvMasterAutoId)->get();

        if (empty($consoleJVDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_details')]));
        }

        return $this->sendResponse($consoleJVDetail->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.console_j_v_details')]));
    }

    public function deleteAllConsoleJVDet(Request $request){
        $consoleJVDetail = ConsoleJVDetail::ofMaster($request->consoleJvMasterAutoId)->delete();
        return $this->sendResponse($consoleJVDetail, trans('custom.delete', ['attribute' => trans('custom.console_j_v_details')]));
    }
}
