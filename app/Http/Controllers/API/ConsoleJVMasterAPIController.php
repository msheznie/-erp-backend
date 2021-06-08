<?php
/**
 * =============================================
 * -- File Name : ConsoleJVMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  General Ledger
 * -- Author : Mohamed Mubashir
 * -- Create date : 06 - March 2019
 * -- Description : This file contains the all CRUD for Console JV
 * -- REVISION HISTORY
 * -- Date: 06 - March 2019 By: Mubashir Description: Added new functions named as getAllConsoleJV()
 * -- Date: 07 - March 2019 By: Mubashir Description: Added new functions named as getConsoleJVMasterFormData()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateConsoleJVMasterAPIRequest;
use App\Http\Requests\API\UpdateConsoleJVMasterAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\ConsoleJVDetail;
use App\Models\ConsoleJVMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\JvMaster;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ConsoleJVMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ConsoleJVMasterController
 * @package App\Http\Controllers\API
 */

class ConsoleJVMasterAPIController extends AppBaseController
{
    /** @var  ConsoleJVMasterRepository */
    private $consoleJVMasterRepository;

    public function __construct(ConsoleJVMasterRepository $consoleJVMasterRepo)
    {
        $this->consoleJVMasterRepository = $consoleJVMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/consoleJVMasters",
     *      summary="Get a listing of the ConsoleJVMasters.",
     *      tags={"ConsoleJVMaster"},
     *      description="Get all ConsoleJVMasters",
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
     *                  @SWG\Items(ref="#/definitions/ConsoleJVMaster")
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
        $this->consoleJVMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->consoleJVMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $consoleJVMasters = $this->consoleJVMasterRepository->all();

        return $this->sendResponse($consoleJVMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.console_j_v_masters')]));
    }

    /**
     * @param CreateConsoleJVMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/consoleJVMasters",
     *      summary="Store a newly created ConsoleJVMaster in storage",
     *      tags={"ConsoleJVMaster"},
     *      description="Store ConsoleJVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ConsoleJVMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ConsoleJVMaster")
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
     *                  ref="#/definitions/ConsoleJVMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateConsoleJVMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($input, [
            'consoleJVdate' => 'required|date',
            'currencyID' => 'required',
            'consoleJVNarration' => 'required',
            'jvType' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['consoleJVdate'] = new Carbon($input['consoleJVdate']);
        $input['currencyER'] = 1;

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $documentMaster = DocumentMaster::find($input['documentSystemID']);
        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

        $lastSerial = ConsoleJVMaster::orderBy('serialNo', 'desc')->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        if ($documentMaster) {
            $documentCode = ($company->CompanyID . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['consoleJVcode'] = $documentCode;
        }
        $input['serialNo'] = $lastSerialNumber;

        $companyCurrency = \Helper::companyCurrency($input['companySystemID']);
        if ($companyCurrency) {
            $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
            $input['rptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['currencyID'], $input['currencyID'], 0);
            if ($companyCurrencyConversion) {
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $input['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
            }
        }

        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['createdPcID'] = gethostname();

        $consoleJVMasters = $this->consoleJVMasterRepository->create($input);

        return $this->sendResponse($consoleJVMasters->toArray(), trans('custom.save', ['attribute' => trans('custom.console_j_v_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/consoleJVMasters/{id}",
     *      summary="Display the specified ConsoleJVMaster",
     *      tags={"ConsoleJVMaster"},
     *      description="Get ConsoleJVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ConsoleJVMaster",
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
     *                  ref="#/definitions/ConsoleJVMaster"
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
        /** @var ConsoleJVMaster $consoleJVMaster */
        $consoleJVMaster = $this->consoleJVMasterRepository->with(['confirmed_by'])->findWithoutFail($id);

        if (empty($consoleJVMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_masters')]));
        }

        return $this->sendResponse($consoleJVMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.console_j_v_masters')]));
    }

    /**
     * @param int $id
     * @param UpdateConsoleJVMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/consoleJVMasters/{id}",
     *      summary="Update the specified ConsoleJVMaster in storage",
     *      tags={"ConsoleJVMaster"},
     *      description="Update ConsoleJVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ConsoleJVMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ConsoleJVMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ConsoleJVMaster")
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
     *                  ref="#/definitions/ConsoleJVMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateConsoleJVMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($input, [
            'consoleJVdate' => 'required|date',
            'currencyID' => 'required',
            'consoleJVNarration' => 'required',
            'jvType' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        /** @var ConsoleJVMaster $consoleJVMaster */
        $consoleJVMaster = $this->consoleJVMasterRepository->findWithoutFail($id);

        if (empty($consoleJVMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_masters')]));
        }

        $input['consoleJVdate'] = new Carbon($input['consoleJVdate']);

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $documentMaster = DocumentMaster::find($input['documentSystemID']);
        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

        $companyCurrency = \Helper::companyCurrency($input['companySystemID']);
        if ($companyCurrency) {
            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['currencyID'], $input['currencyID'], 0);
            if ($companyCurrencyConversion) {
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $input['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
            }
        }

        if ($consoleJVMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'consoleJVdate' => 'required',
                'consoleJVNarration' => 'required',
                'jvType' => 'required|not_in:0',
                'currencyID' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $finalError = array(
                'required_serviceLine' => array(),
                'active_serviceLine' => array(),
                'required_glCode' => array(),
                'active_glCode' => array(),
            );

            $error_count = 0;

            $consoleJVDetail = ConsoleJVDetail::ofMaster($id)->get();
            foreach ($consoleJVDetail as $item) {
                if ($item->serviceLineSystemID && !is_null($item->serviceLineSystemID)) {
                    $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $item->serviceLineSystemID)
                        ->where('isActive', 1)
                        ->first();
                    if (empty($checkDepartmentActive)) {
                        $item->serviceLineSystemID = null;
                        $item->serviceLineCode = null;
                        array_push($finalError['active_serviceLine'], $item->companyID);
                        $error_count++;
                    }
                } else {
                    array_push($finalError['required_serviceLine'], $item->companyID);
                    $error_count++;
                }

                if ($item->glAccountSystemID && !is_null($item->glAccountSystemID)) {
                    $checkChartOfAccountActive = ChartOfAccount::where('chartOfAccountSystemID', $item->glAccountSystemID)
                        ->where('isActive', 1)
                        ->first();
                    if (empty($checkChartOfAccountActive)) {
                        $item->glAccountSystemID = null;
                        $item->glAccount = null;
                        $item->glAccountDescription = null;
                        array_push($finalError['active_glCode'], $item->companyID);
                        $error_count++;
                    }
                }
                else if (is_null($item->glAccountSystemID) || $item->glAccountSystemID == 0) {
                    array_push($finalError['required_glCode'], $item->companyID);
                    $error_count++;
                }
            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError(trans('custom.you_cannot_confirm_this_document'), 500, $confirm_error);
            }

            $jvDetail = ConsoleJVDetail::selectRAW('SUM(debitAmount) as debitAmount,SUM(creditAmount) as creditAmount,SUM(debitAmount) - SUM(creditAmount) as balance')->ofMaster($id)->first();
            if($jvDetail){
                if($jvDetail->balance != 0){
                    return $this->sendError('Debit and Credit amount not matching',500,['type' => 'confirm']);
                }

                if($jvDetail->debitAmount == 0 && $jvDetail->creditAmount == 0){
                    return $this->sendError('Total debit and credit amount cannot be zero',500,['type' => 'confirm']);
                }
            }

            $input['confirmedYN'] = 1;
            $input['confirmedByEmpSystemID'] = \Helper::getEmployeeSystemID();
            $input['confirmedByEmpID'] = \Helper::getEmployeeID();
            $input['confirmedByName'] = \Helper::getEmployeeName();
            $input['confirmedDate'] = NOW();
        }

        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedPc'] = gethostname();

        $consoleJVMaster = $this->consoleJVMasterRepository->update($input, $id);

        return $this->sendResponse($consoleJVMaster->toArray(), trans('custom.save', ['attribute' => trans('custom.console_j_v_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/consoleJVMasters/{id}",
     *      summary="Remove the specified ConsoleJVMaster from storage",
     *      tags={"ConsoleJVMaster"},
     *      description="Delete ConsoleJVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ConsoleJVMaster",
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
        /** @var ConsoleJVMaster $consoleJVMaster */
        $consoleJVMaster = $this->consoleJVMasterRepository->findWithoutFail($id);

        if (empty($consoleJVMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_masters')]));
        }

        $consoleJVMaster->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.console_j_v_masters')]));
    }


    public function getAllConsoleJV(Request $request){
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year', 'confirmedYN'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $consoleJV = $this->consoleJVMasterRepository->consoleJVMasterListQuery($request, $input, $search);

        return \DataTables::eloquent($consoleJV)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('consoleJvMasterAutoId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getConsoleJVGL(request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];

        $items = ChartOfAccount::where('controllAccountYN', 0)
            ->where('isActive', 1)
            ->where('isBank', 0)
            ->where('isApproved', 1);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('AccountCode', 'LIKE', "%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.data')]));

    }

    public function getConsoleJVMasterFormData(Request $request)
    {
        $companyId = $request['companyId'];

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();
        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();
        $month = Months::all();

        $years = ConsoleJVMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();
        $company = Company::where('masterCompanySystemIDReorting',$companyId)->get();

        $segment = SegmentMaster::where('isActive',1)->get();

        $output = array('yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'company' => $company,
            'segments' => $segment,
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

}
