<?php
/**
 * =============================================
 * -- File Name : JvDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  JvDetail
 * -- Author : Mohamed Nazir
 * -- Create date : 25-September 2018
 * -- Description : This file contains the all CRUD for Jv Detail
 * -- REVISION HISTORY
 * -- Date: 25-September 2018 By: Nazir Description: Added new functions named as getJournalVoucherDetails()
 * -- Date: 27-September 2018 By: Nazir Description: Added new functions named as getJournalVoucherContracts()
 * -- Date: 05-October 2018 By: Nazir Description: Added new functions named as journalVoucherDeleteAllAJ()
 * -- Date: 15-October 2018 By: Nazir Description: Added new functions named as journalVoucherDeleteAllPOAJ()
 * -- Date: 20-December 2018 By: Nazir Description: Added new functions named as journalVoucherDeleteAllDetails()
 * -- Date: 27-February 2020 By: Zakeeul Description: Added new functions named as generateAllocation()
 * -- Date: 27-February 2020 By: Zakeeul Description: Added new functions named as generateSpaceUsageAndFixedRateAllocation()
 * -- Date: 27-February 2020 By: Zakeeul Description: Added new functions named as generateRevenueBasicAllocation()
 * -- Date: 27-February 2020 By: Zakeeul Description: Added new functions named as getGeneralLedgerDataForAllocation()
 * -- Date: 27-February 2020 By: Zakeeul Description: Added new functions named as getChartOfAccountAllocationDetails()
 * -- Date: 27-February 2020 By: Zakeeul Description: Added new functions named as getJvSaveDetailsArray()
 * -- Date: 27-February 2020 By: Zakeeul Description: Added new functions named as generateBasicOfStaffProductLineAllocation()
 * -- Date: 27-February 2020 By: Zakeeul Description: Added new functions named as addAllocationDetailToJvAllocation()
 */

namespace App\Http\Controllers\API;

use App\Models\AccruavalFromOPMaster;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Contract;
use App\Models\FinanceItemCategorySub;
use App\Models\HRMSJvDetails;
use App\Models\HRMSJvMaster;
use App\Models\JvDetail;
use App\Models\JvMaster;
use App\Models\ProcumentOrderDetail;
use App\Models\SegmentMaster;
use App\Models\ChartOfAccountAllocationMaster;
use App\Models\ChartOfAccountAllocationDetailHistory;
use App\Models\GeneralLedger;
use App\Models\Employee;
use App\Models\ServiceLine;
use App\Models\Company;
use App\Models\ChartOfAccountAllocationDetail;
use App\Models\SystemGlCodeScenario;
use App\Models\SystemGlCodeScenarioDetail;
use App\Repositories\JvDetailRepository;
use App\Repositories\ChartOfAccountAllocationDetailHistoryRepository;
use App\Services\UserTypeService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Response;
use App\Models\CompanyPolicyMaster;
use App\Models\ErpProjectMaster;

/**
 * Class JvDetailController
 * @package App\Http\Controllers\API
 */
class JvDetailAPIController extends AppBaseController
{
    /** @var  JvDetailRepository */
    private $jvDetailRepository;
    private $userRepository;
    private $allocationHistoryRepository;

    public function __construct(JvDetailRepository $jvDetailRepo, UserRepository $userRepo, ChartOfAccountAllocationDetailHistoryRepository $allocationHistoryRepo)
    {
        $this->jvDetailRepository = $jvDetailRepo;
        $this->userRepository = $userRepo;
        $this->allocationHistoryRepository = $allocationHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvDetails",
     *      summary="Get a listing of the JvDetails.",
     *      tags={"JvDetail"},
     *      description="Get all JvDetails",
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
     *                  @SWG\Items(ref="#/definitions/JvDetail")
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
        $this->jvDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->jvDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $jvDetails = $this->jvDetailRepository->all();

        return $this->sendResponse($jvDetails->toArray(), 'Jv Details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Post(
     *      path="/jvDetails",
     *      summary="Store a newly created JvDetail in storage",
     *      tags={"JvDetail"},
     *      description="Store JvDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvDetail")
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
     *                  ref="#/definitions/JvDetail"
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
        $input = $this->convertArrayToValue($input);

        $jvMaster = JvMaster::find($input['jvMasterAutoId']);

        if (empty($jvMaster)) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => "Journal Voucher not found"
                ];
            }
            else{
                return $this->sendError('Journal Voucher not found');
            }
        }

        $messages = [
            'currencyID' => 'Currency is required',
        ];
        $validator = \Validator::make($jvMaster->toArray(), [
            'jvType' => 'required|numeric',
            'currencyID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => $messages['currencyID']
                ];
            }
            else{
                return $this->sendError($messages, 422);
            }
        }

        $input['documentSystemID'] = $jvMaster->documentSystemID;
        $input['documentID'] = $jvMaster->documentID;
        $input['companySystemID'] = $jvMaster->companySystemID;
        $input['companyID'] = $jvMaster->companyID;

        $chartOfAccount = ChartOfAccount::find($input['chartOfAccountSystemID']);
        if (empty($chartOfAccount)) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => "Chart of Account not found"
                ];
            }
            else{
                return $this->sendError('Chart of Account not found');
            }
        }

        $input['glAccount'] = $chartOfAccount->AccountCode;
        $input['glAccountDescription'] = $chartOfAccount->AccountDescription;

        $input['currencyID'] = $jvMaster->currencyID;
        $input['currencyER'] = $jvMaster->currencyER;
        $input['comments'] = $jvMaster->JVNarration;

        $input['createdPcID'] = gethostname();

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $employee = UserTypeService::getSystemEmployee();
            $input['createdUserID'] = $employee->empID;
            $input['createdUserSystemID'] = $employee->employeeSystemID;
        }
        else{
            $id = Auth::id();
            $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

            $input['createdUserID'] = $user->employee['empID'];
            $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        }

        $jvDetails = $this->jvDetailRepository->create($input);

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            return [
                "success" => true,
                "data" => $jvDetails->toArray()
            ];
        }
        else{
            return $this->sendResponse($jvDetails->toArray(), 'Jv Detail saved successfully');
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvDetails/{id}",
     *      summary="Display the specified JvDetail",
     *      tags={"JvDetail"},
     *      description="Get JvDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetail",
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
     *                  ref="#/definitions/JvDetail"
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
        /** @var JvDetail $jvDetail */
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            return $this->sendError('Jv Detail not found');
        }

        return $this->sendResponse($jvDetail->toArray(), 'Jv Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return array
     *
     * @SWG\Put(
     *      path="/jvDetails/{id}",
     *      summary="Update the specified JvDetail in storage",
     *      tags={"JvDetail"},
     *      description="Update JvDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvDetail")
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
     *                  ref="#/definitions/JvDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, Request $request)
    {
        $input = $request->all();
        $input = array_except($input, ['segment', 'currency_by', 'console_company','chartofaccount']);
        $input = $this->convertArrayToValue($input);
        $serviceLineError = array('type' => 'serviceLine');

        /** @var JvDetail $jvDetail */
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => "Jv Detail not found"
                ];
            }
            else{
                return $this->sendError('Jv Detail not found');
            }
        }

        $jvMaster = JvMaster::find($input['jvMasterAutoId']);

        if (empty($jvMaster)) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => "Journal Voucher not found"
                ];
            }
            else{
                return $this->sendError('Journal Voucher not found');
            }
        }

        if ($input['creditAmount'] == '') {
            $input['creditAmount'] = 0;
        }
        if ($input['debitAmount'] == '') {
            $input['debitAmount'] = 0;
        }

        if (isset($input['serviceLineSystemID'])) {

            if ($input['serviceLineSystemID'] > 0) {
                $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
                if (empty($checkDepartmentActive)) {
                    if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                        return [
                            "success" => false,
                            "message" => "Department not found"
                        ];
                    }
                    else{
                        return $this->sendError('Department not found');
                    }
                }

                if ($checkDepartmentActive->isActive == 0) {
                    $this->$jvDetail->update(['serviceLineSystemID' => null, 'serviceLineCode' => null], $id);
                    if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                        return [
                            "success" => false,
                            "message" => "Please select an active department"
                        ];
                    }
                    else{
                        return $this->sendError('Please select an active department', 500, $serviceLineError);
                    }
                }

                $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
            }
        }

        if (isset($input['contractUID'])) {

            $input['clientContractID'] = NULL;

            $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob')
                ->where('contractUID', $input['contractUID'])
                ->first();
            
            if(!empty($contract)) {
                $input['clientContractID'] = $contract['ContractNumber'];
            }   

        }

        $jvDetail = $this->jvDetailRepository->update($input, $id);

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            return [
                "success" => true,
                "data" => $jvDetail->toArray()
            ];
        }
        else{
            return $this->sendResponse($jvDetail->toArray(), 'JvDetail updated successfully');
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/jvDetails/{id}",
     *      summary="Remove the specified JvDetail from storage",
     *      tags={"JvDetail"},
     *      description="Delete JvDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetail",
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
        /** @var JvDetail $jvDetail */
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            return $this->sendError('Jv Detail not found');
        }

        $jvDetail->delete();

        return $this->sendResponse($id, 'Jv Detail deleted successfully');
    }

    public function getJournalVoucherDetails(Request $request)
    {
        $input = $request->all();
        $id = $input['jvMasterAutoId'];

        $items = JvDetail::where('jvMasterAutoId', $id)
            ->with(['segment', 'currency_by', 'console_company','chartofaccount'])
            ->orderBy('jvDetailAutoID', 'ASC')
            ->get();

        return $this->sendResponse($items->toArray(), 'Jv Detail retrieved successfully');
    }

    public function getJournalVoucherContracts(Request $request)
    {
        $input = $request->all();
        $jvDetailAutoID = isset($input['jvDetailAutoID']) ? $input['jvDetailAutoID']:0;
        $contract = [];

        $detail = JvDetail::where('jvDetailAutoID', $jvDetailAutoID)->first();

        if(!empty($detail)){
            $master = JvMaster::where('jvMasterAutoId', $detail->jvMasterAutoId)->first();

            $contractID = 0;
            if ($detail->contractUID != '' && $detail->contractUID != 0) {
                $contractID = $detail->contractUID;
            }

            if(!empty($master)){
                $contract = Contract::select('contractUID', 'ContractNumber')
                                     ->where('companySystemID',$master->companySystemID)
                                     ->get();
            }
        }

        return $this->sendResponse($contract, 'Record retrived successfully');
    }

    public function journalVoucherSalaryJVDetailStore(Request $request)
    {
        $input = $request->all();
        $detail_arr = array();
        $validator = array();

        if (isset($input['jvMasterAutoId'])) {
            $jvMasterAutoId = $input['jvMasterAutoId'];
        }

        if (isset($input['accruvalMasterID'])) {
            $accruvalMasterID = $input['accruvalMasterID'];
        }


        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $checkItems = JvDetail::where('jvMasterAutoId', $jvMasterAutoId)
            ->count();
        if ($checkItems > 0) {
            return $this->sendError('All ready items are added, You cannot add more.');
        }

        if (empty($input['detailTable'])) {
            return $this->sendError("No items selected to add.");
        }

        $jvMasterData = JvMaster::find($jvMasterAutoId);

        if (empty($jvMasterData)) {
            return $this->sendError('Jv Master not found');
        }


        $output = DB::select("SELECT
        COALESCE(
            (SELECT sep.documentCode FROM srp_erp_payrollmaster sep WHERE hrms_jvmaster.jvDoc = 'SP' AND hrms_jvmaster.salaryProcessMasterID = sep.payrollMasterID),
            (SELECT f.documentCode FROM srp_erp_pay_finalsettlementmaster f WHERE hrms_jvmaster.jvDoc = 'FS' AND hrms_jvmaster.salaryProcessMasterID = f.masterID)
        ) AS payrollCode
    FROM
        hrms_jvmaster
    WHERE
        hrms_jvmaster.accruvalMasterID = '" . $accruvalMasterID . "'
        AND hrms_jvmaster.accConfirmedYN = 1
        AND hrms_jvmaster.accJVSelectedYN = 0
        AND hrms_jvmaster.accJVpostedYN = 0");


        $payrollCode = null;

        if(!empty($output) && isset($output[0]))
            $payrollCode = $output[0]->payrollCode;

        foreach ($input['detailTable'] as $new) {

            $detail_arr['jvMasterAutoId'] = $jvMasterAutoId;
            $detail_arr['documentSystemID'] = $jvMasterData->documentSystemID;
            $detail_arr['documentID'] = $jvMasterData->documentID;
            $detail_arr['recurringjvMasterAutoId'] = $new['accMasterID'];
            $detail_arr['recurringjvDetailAutoID'] = $new['accruvalDetID'];
            $detail_arr['serviceLineSystemID'] = $new['serviceLineSystemID'];
            if($new['serviceLineSystemID']){
                $detail_arr['isServiceLineExist'] = 1;
            }
            $detail_arr['serviceLineCode'] = $new['serviceLine'];
            $detail_arr['companySystemID'] = $jvMasterData->companySystemID;
            $detail_arr['companyID'] = $jvMasterData->companyID;
            $detail_arr['chartOfAccountSystemID'] = $new['chartOfAccountSystemID'];
            $detail_arr['glAccount'] = $new['GlCode'];
            $detail_arr['glAccountDescription'] = $new['AccountDescription'];
            $detail_arr['comments'] = 'Staff cost (Salary direct + Job bonus + Social insurance ) for the month of ' . date('F Y', strtotime($jvMasterData->JVdate)) . ' - '.$payrollCode;
            $detail_arr['currencyID'] = $jvMasterData->currencyID;
            $detail_arr['currencyER'] = $jvMasterData->currencyER;
            $detail_arr['createdPcID'] = gethostname();
            $detail_arr['createdUserID'] = $user->employee['empID'];
            $detail_arr['createdUserSystemID'] = $user->employee['employeeSystemID'];

            if ($new['DebitAmount'] != 0) {
                $detail_arr['debitAmount'] = $new['DebitAmount'];
                $detail_arr['creditAmount'] = 0;
                $store = $this->jvDetailRepository->create($detail_arr);
            }
            if ($new['CreditAmount'] != 0) {
                $detail_arr['debitAmount'] = 0;
                $detail_arr['creditAmount'] = $new['CreditAmount'];
                $store = $this->jvDetailRepository->create($detail_arr);
            }

            // updating HRMS JvDetailtable
            $updateHRMSJvMaster = HRMSJvDetails::find($new['accruvalDetID'])
                ->update([
                    'jvMasterAutoID' => $jvMasterAutoId
                ]);

        }

        // updating HRMS JvMaster table
        $updateHRMSJvMaster = HRMSJvMaster::find($accruvalMasterID)
            ->update([
                'jvMasterAutoID' => $jvMasterAutoId,
                'accJVSelectedYN' => -1
            ]);

        //updating JV master
        $updateJvMaster = JvMaster::find($jvMasterAutoId)
            ->update([
                'JVNarration' => 'Staff cost (Salary direct + Job bonus + Social insurance ) for the month of ' . date('F Y', strtotime($jvMasterData->JVdate)) . ' - ' .$payrollCode
            ]);

        return $this->sendResponse('', 'JV Details saved successfully');

    }

    public function journalVoucherDeleteAllDetails(Request $request)
    {
        $input = $request->all();

        $jvMasterAutoId = $input['jvMasterAutoId'];

        $jvMaster = JvMaster::find($jvMasterAutoId);

        if (empty($jvMaster)) {
            return $this->sendError('Journal Voucher not found');
        }

        $detailExistAll = JvDetail::where('jvMasterAutoId', $jvMasterAutoId)
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete');
        }

        if (!empty($detailExistAll)) {
            $deleteDetails = JvDetail::where('jvMasterAutoId', $jvMasterAutoId)->delete();
        }

        $allocationHistoryData = ChartOfAccountAllocationDetailHistory::where('jvMasterAutoId', $jvMasterAutoId)
            ->get();

        if (!empty($allocationHistoryData)) {
            $deleteHistoryDetails = ChartOfAccountAllocationDetailHistory::where('jvMasterAutoId', $jvMasterAutoId)->delete();
        }

        return $this->sendResponse($jvMasterAutoId, 'Details deleted successfully');
    }

    public function journalVoucherDeleteAllSJ(Request $request)
    {
        $input = $request->all();

        $jvMasterAutoId = $input['jvMasterAutoId'];

        $jvMaster = JvMaster::find($jvMasterAutoId);

        if (empty($jvMaster)) {
            return $this->sendError('Journal Voucher not found');
        }

        $detailExistAll = JvDetail::where('jvMasterAutoId', $jvMasterAutoId)
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete');
        }
        $accruvalMasterID = 0;
        DB::beginTransaction();
        try {
            if (!empty($detailExistAll)) {
                foreach ($detailExistAll as $cvDeatil) {

                    $accruvalMasterID = $cvDeatil['recurringjvMasterAutoId'];

                    // Fetching HRMS JvDetailtable
                    $updateHRMSJvDetailData = HRMSJvDetails::find($cvDeatil['recurringjvDetailAutoID']);

                    if(!empty($updateHRMSJvDetailData)){
                        // updating fields
                        $updateHRMSJvDetailData->jvMasterAutoID = 0;
                        $updateHRMSJvDetailData->save();
                    }

                    JvDetail::where('jvDetailAutoID', $cvDeatil['jvDetailAutoID'])->delete();
                }

                if ($accruvalMasterID != 0) {
                    // updating HRMS JvMaster table
                    $updateHRMSJvMasterData = HRMSJvMaster::find($accruvalMasterID);

                    // updating fields
                    $updateHRMSJvMasterData->jvMasterAutoID = 0;
                    $updateHRMSJvMasterData->accJVSelectedYN = 0;
                    $updateHRMSJvMasterData->save();
                }

            }

            DB::commit();
            return $this->sendResponse($jvMasterAutoId, 'Details deleted successfully');
        } catch (\Exception $exception) {
            DB::rollback();
            //return $this->sendError($exception->getMessage() . 'Line :' . $exception->getLine());
            return $this->sendError('Error occurred in detail deleting',500);
        }

    }


    public function journalVoucherAccrualJVDetailStore(Request $request)
    {
        $input = $request->all();
        $detail_arr = array();
        $detail_debitArr = array();
        $validator = array();
        $jvMasterAutoId = $input['jvMasterAutoId'];
        $accruvalMasterID = $input['accruvalMasterID'];
        $totalRevenueAmount = 0;
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $checkItems = JvDetail::where('jvMasterAutoId', $jvMasterAutoId)
            ->count();
        if ($checkItems > 0) {
            return $this->sendError('All ready items are added, You cannot add more.');
        }

        $jvMasterData = JvMaster::find($jvMasterAutoId);

        if (empty($jvMasterData)) {
            return $this->sendError('Jv Master not found');
        }

        $formattedDate = Carbon::parse($jvMasterData->JVdate)->format('M Y');

        $detailRecordGrouping = DB::select("SELECT
	accruvalfromop.accMasterID,
	accruvalfromop.contractID as accrualNarration,
	accruvalfromop.accrualDateAsOF,
	accruvalfromop.companyID,
	accruvalfromop.contractID,
	serviceline.serviceLineSystemID,
	contractmaster.serviceLineCode AS serviceLine,
	Sum(accruvalfromop.stdAmount) AS SumOfstdAmount,
	Sum(accruvalfromop.opAmount) AS SumOfopAmount,
	accruvalfromop.glCodeStd,
	accruvalfromop.glCodeOp,
	accruvalfromop.glCodeusage,
	accruvalfromop.glCodeLIH,
	accruvalfromop.glCodeDBR,
	accruavalfromopmaster.accConfirmedYN,
	chartofaccounts.chartOfAccountSystemID,
	accruvalfromop.GlCode,
	chartofaccounts.AccountDescription,
	contractmaster.contractUID AS contractSystemID,
/*	Sum(
		accruvalfromop.accrualAmount
	) AS SumOfaccrualAmount,*/
		Sum(
			IFNULL(accruvalfromop.rptAmount,0)
	) AS SumOfaccrualAmount

FROM
	accruvalfromop
INNER JOIN accruavalfromopmaster ON accruvalfromop.accMasterID = accruavalfromopmaster.accruvalMasterID
INNER JOIN erp_months ON accruavalfromopmaster.accmonth = erp_months.monthsID
LEFT JOIN contractmaster ON accruvalfromop.contractID = contractmaster.ContractNumber
LEFT JOIN serviceline ON contractmaster.serviceLineCode = serviceline.ServiceLineCode
LEFT JOIN chartofaccounts ON accruvalfromop.GlCode = chartofaccounts.AccountCode
WHERE
	accruavalfromopmaster.accConfirmedYN = 1 AND accruvalfromop.companyID = '" . $jvMasterData->companyID . "' AND accMasterID = $accruvalMasterID
GROUP BY
	accruvalfromop.accMasterID,
	accruvalfromop.companyID,
	accruvalfromop.contractID,
	contractmaster.serviceLineCode,
	accruvalfromop.glCodeStd,
	accruvalfromop.glCodeOp,
	accruvalfromop.glCodeusage,
	accruvalfromop.glCodeLIH,
	accruvalfromop.glCodeDBR,
	accruavalfromopmaster.accConfirmedYN,
	accruvalfromop.GlCode");

        if (!empty($detailRecordGrouping)) {
            foreach ($detailRecordGrouping as $rowData) {

                $detail_arr['jvMasterAutoId'] = $jvMasterAutoId;
                $detail_arr['documentSystemID'] = $jvMasterData->documentSystemID;
                $detail_arr['documentID'] = $jvMasterData->documentID;
                $detail_arr['recurringjvMasterAutoId'] = $rowData->accMasterID;
                $detail_arr['recurringjvDetailAutoID'] = 0;
                $detail_arr['serviceLineSystemID'] = $rowData->serviceLineSystemID;
                $detail_arr['serviceLineCode'] = $rowData->serviceLine;
                $detail_arr['companySystemID'] = $jvMasterData->companySystemID;
                $detail_arr['companyID'] = $jvMasterData->companyID;
                $detail_arr['chartOfAccountSystemID'] = $rowData->chartOfAccountSystemID;
                $detail_arr['glAccount'] = $rowData->GlCode;
                $detail_arr['glAccountDescription'] = $rowData->AccountDescription;
                $detail_arr['comments'] = 'Revenue Accrual for the month of ' . $formattedDate . '';
                $detail_arr['contractUID'] = $rowData->contractSystemID;
                $detail_arr['clientContractID'] = $rowData->accrualNarration;
                $detail_arr['currencyID'] = $jvMasterData->currencyID;
                $detail_arr['currencyER'] = $jvMasterData->currencyER;
                $detail_arr['createdPcID'] = gethostname();
                $detail_arr['createdUserID'] = $user->employee['empID'];
                $detail_arr['createdUserSystemID'] = $user->employee['employeeSystemID'];

                if ($rowData->SumOfaccrualAmount < 0) {
                    $detail_arr['debitAmount'] = $rowData->SumOfaccrualAmount * -1;
                    $detail_arr['creditAmount'] = 0;
                } else {
                    $detail_arr['debitAmount'] = 0;
                    $detail_arr['creditAmount'] = $rowData->SumOfaccrualAmount;
                }
                $totalRevenueAmount += $rowData->SumOfaccrualAmount;

                if ($detail_arr['debitAmount'] == 0 && $detail_arr['creditAmount'] == 0) {

                } else {
                    $store = $this->jvDetailRepository->create($detail_arr);
                }

            }

            // updating hardcoded value
            $detail_debitArr['jvMasterAutoId'] = $jvMasterAutoId;
            $detail_debitArr['documentSystemID'] = $jvMasterData->documentSystemID;
            $detail_debitArr['documentID'] = $jvMasterData->documentID;
            $detail_debitArr['recurringjvMasterAutoId'] = $accruvalMasterID;
            $detail_debitArr['recurringjvDetailAutoID'] = 0;
            $detail_debitArr['serviceLineSystemID'] = 24;
            $detail_debitArr['serviceLineCode'] = 'X';
            $detail_debitArr['companySystemID'] = $jvMasterData->companySystemID;
            $detail_debitArr['companyID'] = $jvMasterData->companyID;
            $detail_debitArr['chartOfAccountSystemID'] = 112;
            $detail_debitArr['glAccount'] = 21011;
            $detail_debitArr['glAccountDescription'] = 'Accrued Income';
            $detail_debitArr['comments'] = 'Revenue Accrual for the month of ' . $formattedDate . '';
            $detail_debitArr['currencyID'] = $jvMasterData->currencyID;
            $detail_debitArr['currencyER'] = $jvMasterData->currencyER;
            $detail_debitArr['debitAmount'] = $totalRevenueAmount;
            $detail_debitArr['creditAmount'] = 0;
            $detail_debitArr['createdPcID'] = gethostname();
            $detail_debitArr['createdUserID'] = $user->employee['empID'];
            $detail_debitArr['createdUserSystemID'] = $user->employee['employeeSystemID'];

            $store = $this->jvDetailRepository->create($detail_debitArr);
        }

        // updating AccruavalFromOPMaster table
        $updateAccruavalFromOPMaster = AccruavalFromOPMaster::find($accruvalMasterID)
            ->update([
                'jvMasterAutoID' => $jvMasterAutoId,
                'accJVpostedYN' => -1
            ]);


        //updating JV master
        $updateJvMaster = JvMaster::find($jvMasterAutoId)
            ->update([
                'JVNarration' => 'Revenue Accrual for the month of '.$formattedDate
            ]);

        return $this->sendResponse('', 'JV Details saved successfully');

    }

    public function journalVoucherDeleteAllAJ(Request $request)
    {
        $input = $request->all();

        $jvMasterAutoId = $input['jvMasterAutoId'];

        $jvMaster = JvMaster::find($jvMasterAutoId);

        if (empty($jvMaster)) {
            return $this->sendError('Journal Voucher not found');
        }

        $detailExistAll = JvDetail::where('jvMasterAutoId', $jvMasterAutoId)
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete');
        }
        $accruvalMasterID = 0;

        if (!empty($detailExistAll)) {

            foreach ($detailExistAll as $cvDeatil) {
                $accruvalMasterID = $cvDeatil['recurringjvMasterAutoId'];
                $deleteDetails = JvDetail::where('jvDetailAutoID', $cvDeatil['jvDetailAutoID'])->delete();
            }
        }

        if ($accruvalMasterID != 0) {
            $updateAccruavalFromOPMaster = AccruavalFromOPMaster::find($accruvalMasterID)
                ->update([
                    'jvMasterAutoID' => 0,
                    'accJVpostedYN' => 0
                ]);

        }

        return $this->sendResponse($jvMasterAutoId, 'Details deleted successfully');
    }

    public function journalVoucherPOAccrualJVDetailStore(Request $request)
    {
        $input = $request->all();
        $detail_arr = array();
        $validator = array();
        $totalRevenueAmount = 0;
        $temp_serviceLineSystemID = 0;
        $temp_serviceLineCode = '';
        $jvMasterAutoId = $input['jvMasterAutoId'];

        $testAmount = 0;
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $checkItems = JvDetail::where('jvMasterAutoId', $jvMasterAutoId)
            ->count();
        if ($checkItems > 0) {
            return $this->sendError('All ready items are added, You cannot add more.');
        }

        if (empty($input['detailTable'])) {
            return $this->sendError("No items selected to add.");
        }

        $jvMasterData = JvMaster::find($jvMasterAutoId);

        if (empty($jvMasterData)) {
            return $this->sendError('Jv Master not found');
        }

        $systemGlCodeScenario = SystemGlCodeScenario::where('slug','po-accrual-liability')->first();

        if($systemGlCodeScenario)
        {
            $glCodeScenarioDetails = SystemGlCodeScenarioDetail::where('systemGlScenarioID',$systemGlCodeScenario->id)->where('companySystemID',$jvMasterData->companySystemID)->first();

            if(!$glCodeScenarioDetails || ($glCodeScenarioDetails && is_null($glCodeScenarioDetails->chartOfAccountSystemID)) || ($glCodeScenarioDetails && $glCodeScenarioDetails->chartOfAccountSystemID == 0))
            {
                return $this->sendError("Please configure PO accrual account for this company.");
            }
        }else {
            return $this->sendError("Gl Code scenario not found for PO Accrual");
        }

        foreach ($input['detailTable'] as $new) {


            if (isset($new['isChecked']) && $new['isChecked']) {
                if(isset($new['purchaseOrderDetailsID']))
                {
                    $puchaseOrderDetails = ProcumentOrderDetail::where('purchaseOrderDetailsID',$new['purchaseOrderDetailsID'])->first();
                    if($puchaseOrderDetails && $puchaseOrderDetails->itemFinanceCategorySubID)
                    {
                        $itemFianceCategorySub = FinanceItemCategorySub::with(['cogs_gl_code_pl'])->where('itemCategorySubID',$puchaseOrderDetails->itemFinanceCategorySubID)->first();
                        if($itemFianceCategorySub)
                        {
                            $chartOfAccount = ($itemFianceCategorySub->cogs_gl_code_pl) ? $itemFianceCategorySub->cogs_gl_code_pl : null;
                        }else {
                            $chartOfAccount = null;
                        }
                    }else {
                        $chartOfAccount = null;
                    }
                }else {
                    $chartOfAccount = null;
                }
                $testAmount = 1;
                $detail_arr['jvMasterAutoId'] = $jvMasterAutoId;
                $detail_arr['documentSystemID'] = $jvMasterData->documentSystemID;
                $detail_arr['documentID'] = $jvMasterData->documentID;
                $detail_arr['contractUID'] = 159;
                $detail_arr['clientContractID'] = 'X';
                $detail_arr['serviceLineSystemID'] = $new['serviceLineSystemID'];
                $detail_arr['serviceLineCode'] = $new['serviceLine'];
                $detail_arr['companySystemID'] = $jvMasterData->companySystemID;
                $detail_arr['companyID'] = $jvMasterData->companyID;
                $detail_arr['chartOfAccountSystemID'] = ($itemFianceCategorySub) ? $itemFianceCategorySub->financeCogsGLcodePLSystemID : null;
                $detail_arr['glAccount'] = ($chartOfAccount) ? $chartOfAccount->AccountCode : null;
                $detail_arr['glAccountDescription'] = ($chartOfAccount) ? $chartOfAccount->AccountDescription : null;
                $detail_arr['comments'] = $new['purchaseOrderCode'] . ' - ' . $new['itemPrimaryCode'] . ' - ' . $new['itemDescription'];
                $detail_arr['currencyID'] = $jvMasterData->currencyID;
                $detail_arr['currencyER'] = $jvMasterData->currencyER;
                $detail_arr['createdPcID'] = gethostname();
                $detail_arr['createdUserID'] = $user->employee['empID'];
                $detail_arr['createdUserSystemID'] = $user->employee['employeeSystemID'];
                $detail_arr['debitAmount'] = $new['balanceCost'];
                $detail_arr['creditAmount'] = 0;

                $totalRevenueAmount += $new['balanceCost'];

                $temp_serviceLineSystemID = $new['serviceLineSystemID'];
                $temp_serviceLineCode = $new['serviceLine'];

                $store = $this->jvDetailRepository->create($detail_arr);

            }

        }

        if ($testAmount == 1) {

            $systemGlCodeScenario = SystemGlCodeScenario::where('slug',"po-accrual-liability")->first();
            $systemGlCodeScenarioDetail = SystemGlCodeScenarioDetail::where('systemGlScenarioID',$systemGlCodeScenario->id)->where('companySystemID',$jvMasterData->companySystemID)->first();
            $chartOfAccountDetails = ChartOfAccount::where('chartOfAccountSystemID',$systemGlCodeScenarioDetail->chartOfAccountSystemID)->first();


            if($chartOfAccountDetails)
            {
                $detail_debitArr['chartOfAccountSystemID'] = $chartOfAccountDetails->chartOfAccountSystemID;
                $detail_debitArr['glAccount'] = $chartOfAccountDetails->AccountCode;
                $detail_debitArr['glAccountDescription'] = $chartOfAccountDetails->AccountDescription;
            }

            // updating hardcoded value
            $detail_debitArr['jvMasterAutoId'] = $jvMasterAutoId;
            $detail_debitArr['documentSystemID'] = $jvMasterData->documentSystemID;
            $detail_debitArr['documentID'] = $jvMasterData->documentID;
            $detail_debitArr['recurringjvMasterAutoId'] = 0;
            $detail_debitArr['recurringjvMasterAutoId'] = 0;
            $detail_debitArr['contractUID'] = 159;
            $detail_debitArr['clientContractID'] = 'X';
            $detail_debitArr['serviceLineSystemID'] = $temp_serviceLineSystemID;
            $detail_debitArr['serviceLineCode'] = $temp_serviceLineCode;
            $detail_debitArr['companySystemID'] = $jvMasterData->companySystemID;
            $detail_debitArr['companyID'] = $jvMasterData->companyID;
            $detail_debitArr['comments'] = $temp_serviceLineCode;
            $detail_debitArr['currencyID'] = $jvMasterData->currencyID;
            $detail_debitArr['currencyER'] = $jvMasterData->currencyER;
            $detail_debitArr['debitAmount'] = 0;
            $detail_debitArr['creditAmount'] = $totalRevenueAmount;
            $detail_debitArr['createdPcID'] = gethostname();
            $detail_debitArr['createdUserID'] = $user->employee['empID'];
            $detail_debitArr['createdUserSystemID'] = $user->employee['employeeSystemID'];

            $store = $this->jvDetailRepository->create($detail_debitArr);
        }

        $formattedDate = Carbon::parse($jvMasterData->JVdate)->format('M Y');

        //updating JV master
        $updateJvMaster = JvMaster::find($jvMasterAutoId)
            ->update([
                'JVNarration' => 'PO Accrual for the month of '.$formattedDate
            ]);

        return $this->sendResponse('', 'JV Details saved successfully');

    }


    public function journalVoucherDeleteAllPOAJ(Request $request)
    {
        $input = $request->all();

        $jvMasterAutoId = $input['jvMasterAutoId'];

        $jvMaster = JvMaster::find($jvMasterAutoId);

        if (empty($jvMaster)) {
            return $this->sendError('Journal Voucher not found');
        }

        $detailExistAll = JvDetail::where('jvMasterAutoId', $jvMasterAutoId)
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete');
        }
        $accruvalMasterID = 0;

        if (!empty($detailExistAll)) {

            foreach ($detailExistAll as $cvDeatil) {
                $accruvalMasterID = $cvDeatil['recurringjvMasterAutoId'];
                $deleteDetails = JvDetail::where('jvDetailAutoID', $cvDeatil['jvDetailAutoID'])->delete();
            }
        }


        return $this->sendResponse($jvMasterAutoId, 'Details deleted successfully');
    }


    public function jvDetailsExportToCSV(Request $request){
        $input = $request->all();
        $id = isset($input['id'])?$input['id']:0;
        $jvMaster = JvMaster::find($id);
        $data = array();
        $type = isset($input['type'])?$input['type']:'csv';
        if (empty($jvMaster)) {
            return $this->sendError('Journal Voucher not found');
        }

        $checkProjectSelectionPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
				->where('companySystemID', $input['companySystemID'])
				->first();

        $x = 0;
        foreach ($jvMaster->detail as $item){
            $decimal = 2;
            if($item->currency_by){
                $decimal = $item->currency_by->DecimalPlaces;
            }
            $data[$x]['GL Code'] = $item->glAccount;
            $data[$x]['GL Description'] = $item->glAccountDescription;
            if ($checkProjectSelectionPolicy->isYesNO == 1) {
                $project = ErpProjectMaster::find($item->detail_project_id);
                if(!empty($project)) {
                    $data[$x]['Project'] = $project->projectCode . ' - ' .  $project->description;
                }else{
                    $data[$x]['Project'] = '';
                }       
            }
            $data[$x]['Department'] = $item->segment?$item->segment->ServiceLineDes:'-';
            $data[$x]['Contract'] = $item->clientContractID;
            $data[$x]['Comment'] = $item->comments;
            $data[$x]['Currency'] = $item->currency_by?$item->currency_by->CurrencyCode:'-';
            $data[$x]['Debit'] = sprintf("%.".$decimal."f", $item->debitAmount);
            $data[$x]['Credit'] = sprintf("%.".$decimal."f", $item->creditAmount);
            $x++;
        }

         \Excel::create('jv_details', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data);
                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'C' => '@',
                    'D' => '@',
                    'E' => '@',
                    'F' => '@',
                    'G' => '@',
                    'H' => '@'
                ));
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('A1:H1')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:H1' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }

    public function generateAllocation(Request $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $jvMaster = JvMaster::find($input['jvMasterAutoId']);

        if (empty($jvMaster)) {
            return $this->sendError('Journal Voucher not found');
        }
        $messages = [
            'currencyID' => 'Currency is required',
        ];
        $validator = \Validator::make($jvMaster->toArray(), [
            'jvType' => 'required|numeric',
            'currencyID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($messages, 422);
        }

        DB::beginTransaction();
        try {
            $jvDetails['fixed_rate'] = $this->generateSpaceUsageAndFixedRateAllocation($input, $jvMaster->toArray(), $user);
            // $jvDetails['basic_staff'] = $this->generateBasicOfStaffProductLineAllocation($input, $jvMaster->toArray(), $user);
            // $jvDetails['revenue'] = $this->generateRevenueBasicAllocation($input, $jvMaster->toArray(), $user);
            
            $monthOfJV = Carbon::parse($jvMaster->toArray()['JVdate'])->format('m');
            $yearOfJV = Carbon::parse($jvMaster->toArray()['JVdate'])->format('Y');
            $jvMaster->recurringMonth = $monthOfJV;
            $jvMaster->recurringYear = $yearOfJV;
            $jvMaster->save();
            
            DB::commit();
            return $this->sendResponse($jvDetails, 'Allocation made successfully');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($exception->getMessage() . ' Line Number ' . $exception->getLine(),500);
        }

    }

    public function generateSpaceUsageAndFixedRateAllocation($input, $jvMaster, $user)
    {
        $chartofaccounts = $this->getChartOfAccountAllocationDetails(1, [5,6], $input['companySystemID']);

        $accountFilterArray = [];
        foreach ($chartofaccounts as $key => $value) {
            $temp['chartOfAccountSystemID'] = $value['chartOfAccountSystemID'];
            $temp['serviceLineSystemID'] = $value['serviceLineSystemID'];

            $accountFilterArray[] = $temp;
        }

        $generalLedgerData = $this->getGeneralLedgerDataForAllocation($jvMaster, $accountFilterArray);

        $jvDetails = [];
        $company = Company::where('companySystemID', $jvMaster['companySystemID'])->first();
        foreach ($generalLedgerData['generalLedgerGroupData'] as $key => $gLvalue) {
            if($company->reportingCurrency == $jvMaster['currencyID']) {
                $generalLedgerAmount = ($gLvalue['generalLedgerRptAmount']);
            } else if ($company->localCurrencyID == $jvMaster['currencyID']) {
                $generalLedgerAmount = ($gLvalue['generalLedgerLocalAmount']);
            } else {
                $generalLedgerAmount = ($gLvalue['generalLedgerTransAmount']);
            }

            $creditFlag = false;
            if ($generalLedgerAmount < 0) {
                $creditFlag = true;
            }

            $AccountDescription = $gLvalue['charofaccount']['AccountDescription'];
            foreach ($chartofaccounts as $key => $allocationValue) {
                if ($gLvalue['chartOfAccountSystemID'] == $allocationValue['chartOfAccountSystemID']) {
                    foreach ($allocationValue['detail'] as $key => $allocationDetailvalue) {
                            $jvAmount = $generalLedgerAmount * ($allocationDetailvalue['percentage'] / 100);
                            $jvDetails[] = $this->getJvSaveDetailsArrayForFixedRate($jvMaster, $gLvalue, $allocationValue,  $allocationDetailvalue['productLineID'], abs($jvAmount), $AccountDescription, $user, $generalLedgerAmount, $creditFlag);
                            $this->addAllocationDetailToJvAllocation($allocationDetailvalue, $jvMaster['jvMasterAutoId']);
                    }
                    $jvAmount = $generalLedgerAmount;
                    $jvDetails[] = $this->getJvSaveDetailsArrayForFixedRate($jvMaster, $gLvalue, $allocationValue, $allocationValue['serviceLineSystemID'], abs($jvAmount), $AccountDescription, $user, $generalLedgerAmount, !$creditFlag);
                }
            }
        }
        
        foreach ($jvDetails as $key => $value) {
            $jvDetail = $this->jvDetailRepository->create($value);
        }

        return ['status' => true, 'data' => $jvDetails];
    }

     public function getJvSaveDetailsArrayForFixedRate($jvMaster, $gLvalue, $allocationValue, $productLineID, $jvAmount, $AccountDescription, $user, $generalLedgerAmount, $creditFlag = false)
    {

        $serviceline = ServiceLine::where('serviceLineSystemID', $productLineID)->first();

        $temp['jvMasterAutoId'] = $jvMaster['jvMasterAutoId'];
        $temp['chartOfAccountSystemID'] = $gLvalue['chartOfAccountSystemID'];
        $temp['companySystemID'] = $allocationValue['companySystemID'];
        $temp['serviceLineSystemID'] = $productLineID;
        $temp['serviceLineCode'] = $serviceline->ServiceLineCode;
        $temp['clientContractID'] = null;
        $temp['comments'] = $jvMaster['JVNarration'];
        $temp['debitAmount'] = (!$creditFlag) ? \Helper::roundValue($jvAmount) : 0;
        $temp['creditAmount'] = (!$creditFlag) ? 0 : \Helper::roundValue($jvAmount);
        $temp['cuurencyname'] = null;
        $temp['documentSystemID'] = 17;
        $temp['documentID'] = "JV";
        $temp['companyID'] = $allocationValue['companyID'];
        $temp['glAccount'] = $allocationValue['chartOfAccountCode'];
        $temp['glAccountDescription'] = $AccountDescription;
        $temp['currencyID'] = $jvMaster['currencyID'];
        $temp['currencyER'] = $jvMaster['currencyER'];
        $temp['createdPcID'] = gethostname();
        $temp['createdUserID'] = $user->employee['empID'];
        $temp['createdUserSystemID'] = $user->employee['employeeSystemID'];

        return $temp;
    }

    public function generateRevenueBasicAllocation($input, $jvMaster, $user)
    {
        $chartofaccounts = $this->getChartOfAccountAllocationDetails(2, [4], $input['companySystemID']);

        $accountFilterArray = [];
        foreach ($chartofaccounts as $key => $value) {
            $temp['chartOfAccountSystemID'] = $value['chartOfAccountSystemID'];
            $temp['serviceLineSystemID'] = $value['serviceLineSystemID'];

            $accountFilterArray[] = $temp;
        }
        $generalLedgerData = $this->getGeneralLedgerDataForAllocation($jvMaster, $accountFilterArray);

        $pliChartOfAccount = ChartOfAccountsAssigned::select('chartOfAccountSystemID')
                                                                             ->where('isActive',1)
                                                                             ->where('controlAccountsSystemID',1)
                                                                             ->where('companySystemID',$jvMaster['companySystemID'])
                                                                             ->get()
                                                                             ->toArray();
        $pliChartOfAccountIds =  collect($pliChartOfAccount)->pluck('chartOfAccountSystemID')->toArray();

        $company = Company::where('companySystemID', $jvMaster['companySystemID'])->first();
        $pliGeneralLedgerTotal = $this->getGeneralLedgerDataForAllocationForRevenueBasis($jvMaster, $pliChartOfAccountIds, "glID");
        if($company->reportingCurrency == $jvMaster['currencyID']) {
            $pliGeneralLedgerTotal = (sizeof($pliGeneralLedgerTotal) > 0 ) ? ($pliGeneralLedgerTotal[0]['generalLedgerRptAmount']) : 0;
        } else if ($company->localCurrencyID == $jvMaster['currencyID']) {
            $pliGeneralLedgerTotal = (sizeof($pliGeneralLedgerTotal) > 0 ) ? ($pliGeneralLedgerTotal[0]['generalLedgerLocalAmount']) : 0;
        } else {
            $pliGeneralLedgerTotal = (sizeof($pliGeneralLedgerTotal) > 0 ) ? ($pliGeneralLedgerTotal[0]['generalLedgerTransAmount']) : 0;
        }
        $pliGeneralLedgerTotalByServiceLine = $this->getGeneralLedgerDataForAllocationForRevenueBasis($jvMaster, $pliChartOfAccountIds, "serviceline");
        $jvDetails = [];
        foreach ($generalLedgerData['generalLedgerGroupData'] as $key => $gLvalue) {
            if($company->reportingCurrency == $jvMaster['currencyID']) {
                $generalLedgerAmount = ($gLvalue['generalLedgerRptAmount']);
            } else if ($company->localCurrencyID == $jvMaster['currencyID']) {
                $generalLedgerAmount = ($gLvalue['generalLedgerLocalAmount']);
            } else {
                $generalLedgerAmount = ($gLvalue['generalLedgerTransAmount']);
            }
            $AccountDescription = $gLvalue['charofaccount']['AccountDescription'];
            foreach ($chartofaccounts as $key => $allocationValue) {
                if ($gLvalue['chartOfAccountSystemID'] == $allocationValue['chartOfAccountSystemID']) {
                    $deleteAllocationDetailData = ChartOfAccountAllocationDetail::where('chartOfAccountAllocationMasterID', $allocationValue['chartOfAccountAllocationMasterID'])->delete();
                    $jvAmount = 0;
                    foreach ($pliGeneralLedgerTotalByServiceLine as $key => $segmentValue) {
                        if($company->reportingCurrency == $jvMaster['currencyID']) {
                            $segmentValueGeneralLedgerAmount = $segmentValue['generalLedgerRptAmount'];
                        } else if ($company->localCurrencyID == $jvMaster['currencyID']) {
                            $segmentValueGeneralLedgerAmount = $segmentValue['generalLedgerLocalAmount'];
                        } else {
                            $segmentValueGeneralLedgerAmount = $segmentValue['generalLedgerTransAmount'];
                        }
                        $percentage = (($segmentValueGeneralLedgerAmount) / $pliGeneralLedgerTotal) * 100;
                        $jvAmount = $generalLedgerAmount * (($segmentValueGeneralLedgerAmount) / $pliGeneralLedgerTotal);

                        $allocationDetailNewData['percentage'] = $percentage;
                        $allocationDetailNewData['productLineID'] = $segmentValue['serviceLineSystemID'];
                        $allocationDetailNewData['productLineCode'] = $segmentValue['serviceLineCode'];
                        $allocationDetailNewData['allocationmaid'] = 4;
                        $allocationDetailNewData['companySystemID'] = $input['companySystemID'];
                        $allocationDetailNewData['companyid'] = $segmentValue['companyID'];
                        $allocationDetailNewData['chartOfAccountAllocationMasterID'] = $allocationValue['chartOfAccountAllocationMasterID'];

                        $res = ChartOfAccountAllocationDetail::create($allocationDetailNewData);
                        $this->addAllocationDetailToJvAllocation($allocationDetailNewData, $jvMaster['jvMasterAutoId']);

                        $jvDetails[] = $this->getJvSaveDetailsArray($jvMaster, $gLvalue, $allocationValue,  $segmentValue['serviceLineSystemID'], $jvAmount, $AccountDescription, $user, $generalLedgerAmount, false);
                    }
                    $jvDetails[] = $this->getJvSaveDetailsArray($jvMaster, $gLvalue, $allocationValue, $allocationValue['serviceLineSystemID'], $jvAmount, $AccountDescription, $user, $generalLedgerAmount, true);
                }
            }
        }

        foreach ($jvDetails as $key => $value) {
            $jvDetail = $this->jvDetailRepository->create($value);
        }

        return ['status' => true, 'data' => $jvDetails];
    }

    public function getGeneralLedgerDataForAllocation($jvMaster, $chartOfAccountIds, $groupBY = "chartOfAccountSystemID")
    {
        $monthOfJV = Carbon::parse($jvMaster['JVdate'])->format('m');
        $yearOfJV = Carbon::parse($jvMaster['JVdate'])->format('Y');
        $company = Company::where('companySystemID', $jvMaster['companySystemID'])->first();
        $glData = GeneralLedger::with(['charofaccount'])
                                            ->selectRaw('SUM(documentTransAmount) AS generalLedgerTransAmount,SUM(documentLocalAmount) AS generalLedgerLocalAmount,SUM(documentRptAmount) AS generalLedgerRptAmount, chartOfAccountSystemID, serviceLineSystemID, serviceLineCode, companyID, GeneralLedgerID')
                                            ->where('companySystemID',$jvMaster['companySystemID'])
                                            ->whereMonth('documentDate', $monthOfJV)
                                            ->whereYear('documentDate', $yearOfJV)
                                            ->where(function ($query) use ($chartOfAccountIds) {
                                                foreach ($chartOfAccountIds as $key => $value) {
                                                    if($key == 0){
                                                       $query->where(function($q1) use($value){
                                                              $q1->where('chartOfAccountSystemID',$value['chartOfAccountSystemID'])
                                                                ->where('serviceLineSystemID', $value['serviceLineSystemID']);
                                                            });
                                                    }else{
                                                       $query->orWhere(function($q1) use($value){
                                                              $q1->where('chartOfAccountSystemID',$value['chartOfAccountSystemID'])
                                                                ->where('serviceLineSystemID', $value['serviceLineSystemID']);
                                                        });
                                                    }
                                                 
                                                }
                                            });
      
        if($company->reportingCurrency != $jvMaster['currencyID'] && $company->localCurrencyID != $jvMaster['currencyID']) {
            $glData = $glData->where('documentTransCurrencyID',$jvMaster['currencyID']);
        }                         

        if ($groupBY == "chartOfAccountSystemID") {
            $generalLedgerGroupData = $glData->where('isAllocationJV',0)
                                             ->groupBy('chartOfAccountSystemID')
                                             ->get()
                                             ->toArray();

            $generalLedgerData = $glData->get()->toArray();

            return ['generalLedgerGroupData' => $generalLedgerGroupData, 'generalLedgerData' => $generalLedgerData];
        } else if ($groupBY == "glID") {
            return $glData->get()
                            ->toArray();
        } else {
            return $glData->groupBy('serviceLineSystemID')
                            ->get()
                            ->toArray();
        }
    }


    public function getGeneralLedgerDataForAllocationForRevenueBasis($jvMaster, $chartOfAccountIds, $groupBY = "chartOfAccountSystemID")
    {
        $monthOfJV = Carbon::parse($jvMaster['JVdate'])->format('m');
        $yearOfJV = Carbon::parse($jvMaster['JVdate'])->format('Y');
        $company = Company::where('companySystemID', $jvMaster['companySystemID'])->first();
        $glData = GeneralLedger::with(['charofaccount'])
                                            ->selectRaw('SUM(documentTransAmount) AS generalLedgerTransAmount,SUM(documentLocalAmount) AS generalLedgerLocalAmount,SUM(documentRptAmount) AS generalLedgerRptAmount, chartOfAccountSystemID, serviceLineSystemID, serviceLineCode, companyID, GeneralLedgerID')
                                            ->where('companySystemID',$jvMaster['companySystemID'])
                                            ->whereMonth('documentDate', $monthOfJV)
                                            ->whereYear('documentDate', $yearOfJV)
                                            ->whereIn('chartOfAccountSystemID', $chartOfAccountIds);
        
        if($company->reportingCurrency != $jvMaster['currencyID'] && $company->localCurrencyID != $jvMaster['currencyID']) {
            $glData = $glData->where('documentTransCurrencyID',$jvMaster['currencyID']);
        }     

        if ($groupBY == "chartOfAccountSystemID") {
            $generalLedgerGroupData = $glData->where('isAllocationJV',0)
                                             ->groupBy('chartOfAccountSystemID')
                                             ->get()
                                             ->toArray();

            $generalLedgerData = $glData->get()->toArray();

            return ['generalLedgerGroupData' => $generalLedgerGroupData, 'generalLedgerData' => $generalLedgerData];
        } else if ($groupBY == "glID") {
            return $glData->get()
                            ->toArray();
        } else {
            return $glData->groupBy('serviceLineSystemID')
                            ->get()
                            ->toArray();
        }
    }

    public function getChartOfAccountAllocationDetails($type, $allocationmaids, $companySystemID)
    {
        $chartofaccounts = ChartOfAccountAllocationMaster::with(['detail','segment'])
                                                            ->whereIn('allocationmaid',$allocationmaids)
                                                            ->where('companySystemID',$companySystemID);
        if ($type == 1) {
            $chartofaccounts = $chartofaccounts->whereHas('detail', function($q){
                                                    $q->havingRaw('SUM(percentage) = ?', array(100));
                                                })
                                                ->get()
                                                ->toArray();
        } else {
            $chartofaccounts = $chartofaccounts->get()
                                               ->toArray();
        }

        return $chartofaccounts;
    }

    public function getJvSaveDetailsArray($jvMaster, $gLvalue, $allocationValue, $productLineID, $jvAmount, $AccountDescription, $user, $generalLedgerAmount, $creditFlag = false)
    {

        $serviceline = ServiceLine::where('serviceLineSystemID', $productLineID)->first();

        $temp['jvMasterAutoId'] = $jvMaster['jvMasterAutoId'];
        $temp['chartOfAccountSystemID'] = $gLvalue['chartOfAccountSystemID'];
        $temp['companySystemID'] = $allocationValue['companySystemID'];
        $temp['serviceLineSystemID'] = $productLineID;
        $temp['serviceLineCode'] = $serviceline->ServiceLineCode;
        $temp['clientContractID'] = null;
        $temp['comments'] = $jvMaster['JVNarration'];
        $temp['debitAmount'] = (!$creditFlag) ? \Helper::roundValue($jvAmount) : 0;
        $temp['creditAmount'] = (!$creditFlag) ? 0 : $generalLedgerAmount;
        $temp['cuurencyname'] = null;
        $temp['documentSystemID'] = 17;
        $temp['documentID'] = "JV";
        $temp['companyID'] = $allocationValue['companyID'];
        $temp['glAccount'] = $allocationValue['chartOfAccountCode'];
        $temp['glAccountDescription'] = $AccountDescription;
        $temp['currencyID'] = $jvMaster['currencyID'];
        $temp['currencyER'] = $jvMaster['currencyER'];
        $temp['createdPcID'] = gethostname();
        $temp['createdUserID'] = $user->employee['empID'];
        $temp['createdUserSystemID'] = $user->employee['employeeSystemID'];

        return $temp;
    }

    public function generateBasicOfStaffProductLineAllocation($input, $jvMaster, $user)
    {
        $chartofaccounts = $this->getChartOfAccountAllocationDetails(2, [1], $input['companySystemID']);

        $accountFilterArray = [];
        foreach ($chartofaccounts as $key => $value) {
            $temp['chartOfAccountSystemID'] = $value['chartOfAccountSystemID'];
            $temp['serviceLineSystemID'] = $value['serviceLineSystemID'];

            $accountFilterArray[] = $temp;
        }

        $generalLedgerData = $this->getGeneralLedgerDataForAllocation($jvMaster, $accountFilterArray);

        $startDateOfMonth = Carbon::parse($jvMaster['JVdate'])->startOfMonth()->format('Y-m-d');
        $totalEmployees = Employee::with(['details'])
                                   ->whereHas('details', function($q) use ($input) {
                                        $q->whereHas('hrmsDepartmentMaster', function($query) {
                                            $query->whereHas('serviceline');
                                        });
                                    })
                                    ->where('empCompanySystemID', $input['companySystemID'])
                                    ->whereDate('empDateRegistered','<=',$jvMaster['JVdate'])
                                    ->where(function($q) use ($startDateOfMonth){
                                        $q->where('discharegedYN',0)
                                          ->orWhere(function($q1) use ($startDateOfMonth) {
                                                $q1->where('discharegedYN', -1)
                                                ->whereDate('empDateTerminated','>=',$startDateOfMonth);
                                          });
                                    })
                                    ->get()->count();

        $segmentWiseEmployees = SegmentMaster::with(['department' => function($q1) use ($input, $jvMaster, $startDateOfMonth) {
                                                    $q1->with(['employeeDetail' => function($query) use ($input, $jvMaster, $startDateOfMonth) {
                                                        $query->with(['employeeMaster' => function($query1) use ($input, $jvMaster, $startDateOfMonth){
                                                            $query1->where('empCompanySystemID', $input['companySystemID'])
                                                                   ->whereDate('empDateRegistered','<=',$jvMaster['JVdate'])
                                                                   ->where(function($query7) use ($startDateOfMonth){
                                                                                $query7->where('discharegedYN',0)
                                                                                  ->orWhere(function($query8) use ($startDateOfMonth) {
                                                                                        $query8->where('discharegedYN', -1)
                                                                                        ->whereDate('empDateTerminated','>=',$startDateOfMonth);
                                                                                  });
                                                                            });
                                                                    
                                                        }])->whereHas('employeeMaster', function($query6) use ($input, $jvMaster, $startDateOfMonth){
                                                                $query6->where('empCompanySystemID', $input['companySystemID'])
                                                                        ->whereDate('empDateRegistered','<=',$jvMaster['JVdate'])
                                                                        ->where(function($query7) use ($startDateOfMonth){
                                                                            $query7->where('discharegedYN',0)
                                                                              ->orWhere(function($query8) use ($startDateOfMonth) {
                                                                                    $query8->where('discharegedYN', -1)
                                                                                    ->whereDate('empDateTerminated','>=',$startDateOfMonth);
                                                                              });
                                                                        });
                                                            });
                                                    }])->whereHas('employeeDetail', function($query5) use ($input, $jvMaster, $startDateOfMonth) {
                                                              $query5->whereHas('employeeMaster', function($query6) use ($input, $jvMaster, $startDateOfMonth){
                                                                $query6->where('empCompanySystemID', $input['companySystemID'])
                                                                        ->whereDate('empDateRegistered','<=',$jvMaster['JVdate'])
                                                                        ->where(function($query7) use ($startDateOfMonth){
                                                                            $query7->where('discharegedYN',0)
                                                                              ->orWhere(function($query8) use ($startDateOfMonth) {
                                                                                    $query8->where('discharegedYN', -1)
                                                                                    ->whereDate('empDateTerminated','>=',$startDateOfMonth);
                                                                              });
                                                                        });
                                                            });
                                                        });
                                                }])
                                                ->whereHas('department', function($query4) use ($input, $jvMaster, $startDateOfMonth) {
                                                    $query4->whereHas('employeeDetail', function($query5) use ($input, $jvMaster, $startDateOfMonth) {
                                                        $query5->whereHas('employeeMaster', function($query6) use ($input, $jvMaster, $startDateOfMonth){
                                                            $query6->where('empCompanySystemID', $input['companySystemID'])
                                                                    ->whereDate('empDateRegistered','<=',$jvMaster['JVdate'])
                                                                    ->where(function($query7) use ($startDateOfMonth){
                                                                        $query7->where('discharegedYN',0)
                                                                          ->orWhere(function($query8) use ($startDateOfMonth) {
                                                                                $query8->where('discharegedYN', -1)
                                                                                ->whereDate('empDateTerminated','>=',$startDateOfMonth);
                                                                          });
                                                                    });
                                                        });
                                                    });
                                                })
                                                ->get()->toArray();
        $segemntData = [];
        foreach ($segmentWiseEmployees as $key => $value) {
            $segmentEmployees = 0;
            foreach ($value['department'] as $key => $department) {
                  $segmentEmployees += count($department['employee_detail']);              
            }
            $temp['segmentEmployees'] = $segmentEmployees;
            $temp['segmentData'] = $value;

            $segemntData[] = $temp;
        }
        $jvDetails = [];
        $company = Company::where('companySystemID', $jvMaster['companySystemID'])->first();
        foreach ($generalLedgerData['generalLedgerGroupData'] as $key => $gLvalue) {
            if($company->reportingCurrency == $jvMaster['currencyID']) {
                $generalLedgerAmount = ($gLvalue['generalLedgerRptAmount']);
            } else if ($company->localCurrencyID == $jvMaster['currencyID']) {
                $generalLedgerAmount = ($gLvalue['generalLedgerLocalAmount']);
            } else {
                $generalLedgerAmount = ($gLvalue['generalLedgerTransAmount']);
            }
            $AccountDescription = $gLvalue['charofaccount']['AccountDescription'];
            foreach ($chartofaccounts as $key => $allocationValue) {
                if ($gLvalue['chartOfAccountSystemID'] == $allocationValue['chartOfAccountSystemID']) {
                    $deleteAllocationDetailData = ChartOfAccountAllocationDetail::where('chartOfAccountAllocationMasterID', $allocationValue['chartOfAccountAllocationMasterID'])->delete();
                    foreach ($segemntData as $key => $segmentValue) {
                        $percentage = ($segmentValue['segmentEmployees'] / $totalEmployees) * 100;
                        $jvAmount = $generalLedgerAmount * ($segmentValue['segmentEmployees'] / $totalEmployees);

                        $allocationDetailNewData['percentage'] = $percentage;
                        $allocationDetailNewData['productLineID'] = $segmentValue['segmentData']['serviceLineSystemID'];
                        $allocationDetailNewData['productLineCode'] = $segmentValue['segmentData']['ServiceLineCode'];
                        $allocationDetailNewData['allocationmaid'] = 1;
                        $allocationDetailNewData['allocationmaid'] = 1;
                        $allocationDetailNewData['companySystemID'] = $input['companySystemID'];
                        $allocationDetailNewData['companyid'] = $segmentValue['segmentData']['companyID'];
                        $allocationDetailNewData['chartOfAccountAllocationMasterID'] = $allocationValue['chartOfAccountAllocationMasterID'];

                        $res = ChartOfAccountAllocationDetail::create($allocationDetailNewData);
                        $this->addAllocationDetailToJvAllocation($allocationDetailNewData, $jvMaster['jvMasterAutoId']);

                        $jvDetails[] = $this->getJvSaveDetailsArray($jvMaster, $gLvalue, $allocationValue,  $segmentValue['segmentData']['serviceLineSystemID'], $jvAmount, $AccountDescription, $user, $generalLedgerAmount, false);
                    }
                    $jvDetails[] = $this->getJvSaveDetailsArray($jvMaster, $gLvalue, $allocationValue, $allocationValue['serviceLineSystemID'], $jvAmount, $AccountDescription, $user, $generalLedgerAmount, true);
                }
            }
        }

        foreach ($jvDetails as $key => $value) {
            $jvDetail = $this->jvDetailRepository->create($value);
        }

        return ['status' => true, 'data' => $jvDetails];
    }

    public function addAllocationDetailToJvAllocation($allocationData, $jvAutoID)
    {
        unset($allocationData['chartOfAccountAllocationDetailID']);
        $allocationData['jvMasterAutoId'] = $jvAutoID;
        return $this->allocationHistoryRepository->create($allocationData);
    }
}
