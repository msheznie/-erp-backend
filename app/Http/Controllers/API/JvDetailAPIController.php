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
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateJvDetailAPIRequest;
use App\Http\Requests\API\UpdateJvDetailAPIRequest;
use App\Models\AccruavalFromOPMaster;
use App\Models\ChartOfAccount;
use App\Models\HRMSJvDetails;
use App\Models\HRMSJvMaster;
use App\Models\JvDetail;
use App\Models\JvMaster;
use App\Models\SegmentMaster;
use App\Repositories\JvDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class JvDetailController
 * @package App\Http\Controllers\API
 */
class JvDetailAPIController extends AppBaseController
{
    /** @var  JvDetailRepository */
    private $jvDetailRepository;
    private $userRepository;

    public function __construct(JvDetailRepository $jvDetailRepo, UserRepository $userRepo)
    {
        $this->jvDetailRepository = $jvDetailRepo;
        $this->userRepository = $userRepo;
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
     * @param CreateJvDetailAPIRequest $request
     * @return Response
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
    public function store(CreateJvDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $jvMaster = JvMaster::find($input['jvMasterAutoId']);

        if (empty($jvMaster)) {
            return $this->sendError('Journal Voucher not found');
        }

        $validator = \Validator::make($jvMaster->toArray(), [
            'jvType' => 'required|numeric',
            'currencyID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['documentSystemID'] = $jvMaster->documentSystemID;
        $input['documentID'] = $jvMaster->documentID;
        $input['companySystemID'] = $jvMaster->companySystemID;
        $input['companyID'] = $jvMaster->companyID;

        $chartOfAccount = ChartOfAccount::find($input['chartOfAccountSystemID']);
        if (empty($chartOfAccount)) {
            return $this->sendError('Chart of Account not found');
        }

        $input['glAccount'] = $chartOfAccount->AccountCode;
        $input['glAccountDescription'] = $chartOfAccount->AccountDescription;

        $input['currencyID'] = $jvMaster->currencyID;
        $input['currencyER'] = $jvMaster->currencyER;

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];

        $jvDetails = $this->jvDetailRepository->create($input);

        return $this->sendResponse($jvDetails->toArray(), 'Jv Detail saved successfully');
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
     * @param UpdateJvDetailAPIRequest $request
     * @return Response
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
    public function update($id, UpdateJvDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['segment', 'currency_by']);
        $input = $this->convertArrayToValue($input);
        $serviceLineError = array('type' => 'serviceLine');

        /** @var JvDetail $jvDetail */
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            return $this->sendError('Jv Detail not found');
        }

        $jvMaster = JvMaster::find($input['jvMasterAutoId']);

        if (empty($jvMaster)) {
            return $this->sendError('Journal Voucher not found');
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
                    return $this->sendError('Department not found');
                }

                if ($checkDepartmentActive->isActive == 0) {
                    $this->$jvDetail->update(['serviceLineSystemID' => null, 'serviceLineCode' => null], $id);
                    return $this->sendError('Please select an active department', 500, $serviceLineError);
                }

                $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
            }
        }

        $jvDetail = $this->jvDetailRepository->update($input, $id);

        return $this->sendResponse($jvDetail->toArray(), 'JvDetail updated successfully');
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
            ->with(['segment', 'currency_by'])
            ->get();

        return $this->sendResponse($items->toArray(), 'Jv Detail retrieved successfully');
    }

    public function getJournalVoucherContracts(Request $request)
    {
        $input = $request->all();
        $jvDetailAutoID = $input['jvDetailAutoID'];
        $detail = JvDetail::where('jvDetailAutoID', $jvDetailAutoID)->first();
        $master = JvMaster::where('jvMasterAutoId', $detail->jvMasterAutoId)->first();

        $contractID = 0;
        if ($detail->contractUID != '' && $detail->contractUID != 0) {
            $contractID = $detail->contractUID;
        }

        $qry = "SELECT * FROM ( SELECT contractUID, ContractNumber FROM contractmaster WHERE ServiceLineCode = '{$detail->serviceLineCode}' AND companySystemID = $master->companySystemID UNION ALL SELECT contractUID, ContractNumber FROM contractmaster WHERE contractUID = $contractID ) t GROUP BY contractUID, ContractNumber";
        $contract = DB::select($qry);

        return $this->sendResponse($contract, 'Record retrived successfully');
    }

    public function journalVoucherSalaryJVDetailStore(Request $request)
    {
        $input = $request->all();
        $detail_arr = array();
        $validator = array();
        $jvMasterAutoId = $input['jvMasterAutoId'];
        $accruvalMasterID = $input['accruvalMasterID'];

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

        foreach ($input['detailTable'] as $new) {

            $detail_arr['jvMasterAutoId'] = $jvMasterAutoId;
            $detail_arr['documentSystemID'] = $jvMasterData->documentSystemID;
            $detail_arr['documentID'] = $jvMasterData->documentID;
            $detail_arr['recurringjvMasterAutoId'] = $new['accMasterID'];
            $detail_arr['recurringjvDetailAutoID'] = $new['accruvalDetID'];
            $detail_arr['serviceLineSystemID'] = $new['serviceLineSystemID'];
            $detail_arr['serviceLineCode'] = $new['serviceLine'];
            $detail_arr['companySystemID'] = $jvMasterData->companySystemID;
            $detail_arr['companyID'] = $jvMasterData->companyID;
            $detail_arr['chartOfAccountSystemID'] = $new['chartOfAccountSystemID'];
            $detail_arr['glAccount'] = $new['GlCode'];
            $detail_arr['glAccountDescription'] = $new['AccountDescription'];
            $detail_arr['comments'] = 'Staff cost (Salary direct + Job bonus + Social insurance ) for the month of ' . date('F Y') . '';
            $detail_arr['currencyID'] = $jvMasterData->currencyID;
            $detail_arr['currencyER'] = $jvMasterData->currencyER;
            $detail_arr['createdPcID'] = gethostname();
            $detail_arr['createdUserID'] = $user->employee['empID'];
            $detail_arr['createdUserSystemID'] = $user->employee['employeeSystemID'];

            if ($new['DebitAmount'] != 0 && $new['CreditAmount'] != 0) {
                $detail_arr['debitAmount'] = 0;
                $detail_arr['creditAmount'] = 0;
                $store = $this->jvDetailRepository->create($detail_arr);
            } else {
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
                'JVNarration' => 'Staff cost (Salary direct + Job bonus + Social insurance ) for the month of ' . date('F Y') . ''
            ]);

        return $this->sendResponse('', 'JV Details saved successfully');

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
        if (!empty($detailExistAll)) {

            foreach ($detailExistAll as $cvDeatil) {
                $accruvalMasterID = $cvDeatil['recurringjvMasterAutoId'];

                // updating HRMS JvDetailtable
                $updateHRMSJvMaster = HRMSJvDetails::find($cvDeatil['recurringjvDetailAutoID'])
                    ->update([
                        'jvMasterAutoID' => 0
                    ]);

                $deleteDetails = JvDetail::where('jvDetailAutoID', $cvDeatil['jvDetailAutoID'])->delete();

            }
        }

        if ($accruvalMasterID != 0) {
            // updating HRMS JvMaster table
            $updateHRMSJvMaster = HRMSJvMaster::find($accruvalMasterID)
                ->update([
                    'jvMasterAutoID' => 0,
                    'accJVSelectedYN' => 0
                ]);
        }

        return $this->sendResponse($jvMasterAutoId, 'Details deleted successfully');
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

        $detailRecordGrouping = DB::select("SELECT
	accruvalfromop.accMasterID,
	accruvalfromop.contractID as  accrualNarration,
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
	Sum(
		accruvalfromop.accrualAmount
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
                $detail_arr['comments'] = 'Revenue Accrual for the month of ' . date('F Y') . '';
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
                $store = $this->jvDetailRepository->create($detail_arr);
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
            $detail_debitArr['comments'] = 'Revenue Accrual for the month of ' . date('F Y') . '';
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
                'JVNarration' => 'Revenue Accrual for the month of ' . date('F Y') . ''
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

        if($accruvalMasterID != 0){
            $updateAccruavalFromOPMaster = AccruavalFromOPMaster::find($accruvalMasterID)
                ->update([
                    'jvMasterAutoID' => 0,
                    'accJVpostedYN' => 0
                ]);

        }

        return $this->sendResponse($jvMasterAutoId, 'Details deleted successfully');
    }


}
