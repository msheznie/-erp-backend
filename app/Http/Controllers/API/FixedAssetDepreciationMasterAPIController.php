<?php
/**
 * =============================================
 * -- File Name : FixedAssetDepreciationMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Asset depreciation
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetDepreciationMasterAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetDepreciationMasterAPIRequest;
use App\Jobs\CreateDepreciation;
use App\Jobs\CreateDepreciationAmend;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DepreciationMasterReferredHistory;
use App\Models\DepreciationPeriodsReferredHistory;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Models\GeneralLedger;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\FixedAssetDepreciationMasterRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\AssetDisposalMaster;
use App\Services\ValidateDocumentAmend;

/**
 * Class FixedAssetDepreciationMasterController
 * @package App\Http\Controllers\API
 */
class FixedAssetDepreciationMasterAPIController extends AppBaseController
{
    /** @var  FixedAssetDepreciationMasterRepository */
    private $fixedAssetDepreciationMasterRepository;

    public function __construct(FixedAssetDepreciationMasterRepository $fixedAssetDepreciationMasterRepo)
    {
        $this->fixedAssetDepreciationMasterRepository = $fixedAssetDepreciationMasterRepo;

    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetDepreciationMasters",
     *      summary="Get a listing of the FixedAssetDepreciationMasters.",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Get all FixedAssetDepreciationMasters",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetDepreciationMaster")
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
        $this->fixedAssetDepreciationMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetDepreciationMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetDepreciationMasters = $this->fixedAssetDepreciationMasterRepository->all();

        return $this->sendResponse($fixedAssetDepreciationMasters->toArray(), 'Fixed Asset Depreciation Masters retrieved successfully');
    }

    /**
     * @param CreateFixedAssetDepreciationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetDepreciationMasters",
     *      summary="Store a newly created FixedAssetDepreciationMaster in storage",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Store FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetDepreciationMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetDepreciationMaster")
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
     *                  ref="#/definitions/FixedAssetDepreciationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetDepreciationMasterAPIRequest $request)
    {
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', -1);

        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        
        $dataBase = isset($input['db']) ? $input['db'] : "";

        $alreadyExist = $this->fixedAssetDepreciationMasterRepository
            ->where('is_acc_dep', 0)
            ->where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where(function ($query) use ($input) {
                $query->where('companyFinancePeriodID', $input['companyFinancePeriodID'])
                    ->orWhere('companyFinancePeriodID', '>', $input['companyFinancePeriodID']);
            })
            ->get();

        if (count($alreadyExist) > 0) {
            return $this->sendError('Depreciation already processed for the selected month', 500);
        }

        DB::beginTransaction();
        try {

            $is_pending_job_exist = FixedAssetDepreciationMaster::where('approved','=',0)->where('is_cancel','=',0)->where('companySystemID' ,'=', $input['companySystemID'])->count();

            if($is_pending_job_exist == 0)
            {

                $doc_date = CompanyFinancePeriod::where('companyFinancePeriodID',$input['companyFinancePeriodID'])->select('dateTo')->first();
                $to_date = $doc_date->dateTo;
                $depeciatedAssets = FixedAssetMaster::whereHas('depperiod_by',function($query)use ($to_date){
                    $query->where('depForFYperiodEndDate','=',$to_date);
                 })
                ->WhereDate('dateDEP','<',$to_date)
                ->ofCompany([$input['companySystemID']])
                ->assetType(1)
                ->where('approved',-1)
                ->count();

                if(($depeciatedAssets) > 0 && $input['depAssets'])
                {
                    return $this->sendError('Depreciation will be processed only for the assets for which no depreciation has been recorded for the selected year and month', 300,['type' => 'depreciatedAssets']);
                }   
                
                $disposelMaster = AssetDisposalMaster::selectRaw("erp_fa_asset_disposalmaster.disposalDocumentCode,erp_fa_asset_master.faID,erp_fa_asset_disposalmaster.assetdisposalMasterAutoID,erp_fa_asset_disposaldetail.faCode")
                                ->join('erp_fa_asset_disposaldetail', 'erp_fa_asset_disposaldetail.assetdisposalMasterAutoID', '=', 'erp_fa_asset_disposalmaster.assetdisposalMasterAutoID')
                                ->join('erp_fa_asset_master', 'erp_fa_asset_master.faID', '=', 'erp_fa_asset_disposaldetail.faID')
                                ->where(function($query) {
                                    $query->where('erp_fa_asset_disposalmaster.confirmedYN', 0)
                                            ->orWhere('erp_fa_asset_disposalmaster.approvedYN', 0);
                                })
                                ->where('erp_fa_asset_master.dateDEP','<',$doc_date->dateTo)
                                ->where('erp_fa_asset_master.approved','=',-1)
                                ->where('erp_fa_asset_master.assetType','=', 1)
                                ->where('erp_fa_asset_master.companySystemID','=', $input['companySystemID']);

                             //   GUTech\\2023\\FADS000006
                $futureDates = AssetDisposalMaster::selectRaw("erp_fa_asset_disposalmaster.disposalDocumentCode,erp_fa_asset_master.faID,erp_fa_asset_disposalmaster.assetdisposalMasterAutoID,erp_fa_asset_disposaldetail.faCode")
                ->join('erp_fa_asset_disposaldetail', 'erp_fa_asset_disposaldetail.assetdisposalMasterAutoID', '=', 'erp_fa_asset_disposalmaster.assetdisposalMasterAutoID')
                ->join('erp_fa_asset_master', 'erp_fa_asset_master.faID', '=', 'erp_fa_asset_disposaldetail.faID')
                ->where('erp_fa_asset_disposalmaster.disposalDocumentDate','>',$doc_date->dateTo)
                //->where('erp_fa_asset_disposalmaster.approvedYN','=',-1)
                ->where('erp_fa_asset_master.approved','=',-1)
                ->where('erp_fa_asset_master.assetType','=', 1)
                ->where('erp_fa_asset_master.companySystemID','=', $input['companySystemID']);
                
                $disposelMaster = $disposelMaster->get()->toArray();
                $futureDates = $futureDates->get()->toArray();
                $disposlaMerge = array_merge ($disposelMaster, $futureDates);
                $disposlaUnique =   array_unique($disposlaMerge,SORT_REGULAR);

                if(count($disposlaUnique) > 0 && $input['dispose'])
                {
                    $body = '';
                  
                    $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: center;border: 1px solid black;">Disposal Code</th> 
                            <th style="text-align: center;border: 1px solid black;">Asset Code</th>
                        </tr>
                    </thead>';
                    $body .= '<tbody>';
                    foreach ($disposlaUnique as $val) {
                        
                        $body .= '<tr>
                            <td style="text-align:center;border: 1px solid black;">' . $val['disposalDocumentCode'] . '</td>  
                            <td style="text-align:center;border: 1px solid black;">' . $val['faCode'] . '</td>  
                        </tr>';
                    
                    }
                    $body .= '</tbody>
                    </table>';


                    return $this->sendError("The following assets will not be added to depreciation as they are linked to Disposal </br></br>  $body  </br> Are you sure you want to proceed ?", 300,['type' => 'dispoasalAsset']);

                }

                $unconfirmedAssest = $this->getAssests($doc_date->dateTo,0,$input['companySystemID']);

                if(count($unconfirmedAssest) > 0 && $input['unConfirm'])
                {
                    return $this->sendError('There  are assets to be approved. Are you sure you want to proceed ?', 300,['type' => 'UnconfirmAsset']);
                }    
                
                $assest_fixds =  $this->getAssests($doc_date->dateTo,-1,$input['companySystemID']);
           
                foreach($assest_fixds as $key=>$val)
                {
                    $cos_unit = $val->COSTUNIT;
                    $dep_amount = $val->depperiod_by;

                  
                    if(isset($dep_amount) && !empty($dep_amount) && count($dep_amount) > 0)
                    {
                      
                        $dep_local_amount = $dep_amount[0]->depAmountLocal;
                        if($cos_unit == $dep_local_amount ||$cos_unit < $dep_local_amount )
                        {
                            unset($assest_fixds[$key]);
                        }
                    
                    }

              
                }  
         
            
               $count_assest = count($assest_fixds);

      
          
               if($count_assest > 0)
               {    

                $validator = \Validator::make($request->all(), [
                    'companyFinanceYearID' => 'required',
                    'companyFinancePeriodID' => 'required',
                ]);
    
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
    
                $alreadyExist = $this->fixedAssetDepreciationMasterRepository
                                        ->where('is_acc_dep', 0)
                                        ->where('companySystemID', $input['companySystemID'])
                                        ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                        ->where(function ($query) use ($input) {
                                            $query->where('companyFinancePeriodID', $input['companyFinancePeriodID'])
                                                ->orWhere('companyFinancePeriodID', '>', $input['companyFinancePeriodID']);
                                        })
                                        ->get();

                if (count($alreadyExist) > 0) {
                    return $this->sendError('Depreciation already processed for the selected month', 500);
                }

                $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
                if (!$companyFinanceYear["success"]) {
                    return $this->sendError($companyFinanceYear["message"], 500);
                } else {
                    $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
                    $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
                }
    
                $inputParam = $input;
                $inputParam["departmentSystemID"] = 9;
                $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
                if (!$companyFinancePeriod["success"]) {
                    return $this->sendError($companyFinancePeriod["message"], 500);
                } else {
                    $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
                    $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
                }
                unset($inputParam);
    
                $subMonth = new Carbon($input['FYPeriodDateFrom']);
                $subMonthStart = $subMonth->subMonth()->startOfMonth()->format('Y-m-d');
                $subMonthStartCarbon = new Carbon($subMonthStart);
                $subMonthEnd = $subMonthStartCarbon->endOfMonth()->format('Y-m-d');
    
                $lastMonthRun = FixedAssetDepreciationMaster::where('companySystemID', $input['companySystemID'])->where('companyFinanceYearID', $input['companyFinanceYearID'])->where('FYPeriodDateFrom', $subMonthStart)->where('FYPeriodDateTo', $subMonthEnd)->first();
    
                if (!empty($lastMonthRun)) {
                    if ($lastMonthRun->approved == 0) {
                        return $this->sendError('Last month depreciation is not approved. Please approve it before you run for this month', 500);
                    }
                }
    
                $company = Company::find($input['companySystemID']);
                if ($company) {
                    $input['companyID'] = $company->CompanyID;
                }
    
                $documentMaster = DocumentMaster::find($input['documentSystemID']);
                if ($documentMaster) {
                    $input['documentID'] = $documentMaster->documentID;
                }
    
                if ($companyFinanceYear["message"]) {
                    $startYear = $companyFinanceYear["message"]['bigginingDate'];
                    $finYearExp = explode('-', $startYear);
                    $finYear = $finYearExp[0];
                } else {
                    $finYear = date("Y");
                }
    
                $lastSerial = FixedAssetDepreciationMaster::where('companySystemID', $input['companySystemID'])
                    ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                    ->orderBy('depMasterAutoID', 'desc')
                    ->first();
    
                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                }



                $documentCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                $input['depCode'] = $documentCode;
                $input['serialNo'] = $lastSerialNumber;
                $depDate = Carbon::parse($input['FYPeriodDateTo']);
                $input['depDate'] = $input['FYPeriodDateTo'];
                $input['depMonthYear'] = $depDate->month . '/' . $depDate->year;
                $input['depLocalCur'] = $company->localCurrencyID;
                $input['depRptCur'] = $company->reportingCurrency;
                $input['createdPCID'] = gethostname();
                $input['createdUserID'] = \Helper::getEmployeeID();
                $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $fixedAssetDepreciationMasters = $this->fixedAssetDepreciationMasterRepository->create($input);

                if ($fixedAssetDepreciationMasters) {
                    CreateDepreciation::dispatch($fixedAssetDepreciationMasters->depMasterAutoID, $dataBase);
                }

                DB::commit();
                return $this->sendResponse($input, 'Fixed Asset Depreciation Master saved successfully');

               }
               else{

                return $this->sendError('There is a no assest for this date period. please choose different date period', 500);
               }
                

            }
            else
            {
                return $this->sendError('There is a unapproved depreciation running. please confirm and proceed', 500);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }


    }

    public function getAssests($date,$val,$companyId)
    {
        return FixedAssetMaster::with(['depperiod_by' => function ($query) {
            $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,round((SUM(depAmountLocal))) as depAmountLocal,faID');
            $query->whereHas('master_by', function ($query) {
                $query->where('approved', -1);
            });
            $query->groupBy('faID');
        }])
        ->WhereDate('dateDEP','<',$date)
        ->ofCompany([$companyId])
        ->assetType(1)
        ->where('approved',$val)
        ->isDisposed()->get();

    }
    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetDepreciationMasters/{id}",
     *      summary="Display the specified FixedAssetDepreciationMaster",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Get FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationMaster",
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
     *                  ref="#/definitions/FixedAssetDepreciationMaster"
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
        /** @var FixedAssetDepreciationMaster $fixedAssetDepreciationMaster */
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->with(['confirmed_by'])->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        return $this->sendResponse($fixedAssetDepreciationMaster->toArray(), 'Fixed Asset Depreciation Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetDepreciationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetDepreciationMasters/{id}",
     *      summary="Update the specified FixedAssetDepreciationMaster in storage",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Update FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetDepreciationMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetDepreciationMaster")
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
     *                  ref="#/definitions/FixedAssetDepreciationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetDepreciationMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        /** @var FixedAssetDepreciationMaster $fixedAssetDepreciationMaster */
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        if ($fixedAssetDepreciationMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {
            $params = array('autoID' => $id, 'company' => $fixedAssetDepreciationMaster->companySystemID, 'document' => $fixedAssetDepreciationMaster->documentSystemID, 'segment' => '', 'category' => '', 'amount' => 0);
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500, ['type' => 'confirm']);
            }
        }

        /*$input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input["timestamp"] = date('Y-m-d H:i:s');*/

        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->update($input, $id);

        return $this->sendResponse($fixedAssetDepreciationMaster->toArray(), 'FixedAssetDepreciationMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetDepreciationMasters/{id}",
     *      summary="Remove the specified FixedAssetDepreciationMaster from storage",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Delete FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationMaster",
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

        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', -1);
        /** @var FixedAssetDepreciationMaster $fixedAssetDepreciationMaster */
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        if($fixedAssetDepreciationMaster->confirmedYN == 1){
            return $this->sendError('You cannot delete confirmed document');
        }

        if($fixedAssetDepreciationMaster->isDepProcessingYN == 0){
            return $this->sendError('Depreciation is still running', 500);
        }

        $fixedAssetDepreciationMaster->delete();

        return $this->sendResponse($id, 'Fixed Asset Depreciation Master deleted successfully');
    }

    public function getAllDepreciationByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $assetCositng = $this->fixedAssetDepreciationMasterRepository->fixedAssetDepreciationListQuery($request, $input, $search);

        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('depMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getDepreciationFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $companyCurrency = \Helper::companyCurrency($companyId);

        $companyFinanceYear = \Helper::companyFinanceYear($companyId,1);

        $output = array(
            'financialYears' => $financialYears,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'companyCurrency' => $companyCurrency,
            'companyFinanceYear' => $companyFinanceYear,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function assetDepreciationByID($id)
    {
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->with(['confirmed_by'])->findWithoutFail($id);
        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        $detail = FixedAssetDepreciationPeriod::with(['maincategory_by', 'financecategory_by', 'serviceline_by'])->ofDepreciation($id)->get();

        $output = ['master' => $fixedAssetDepreciationMaster, 'detail' => $detail];

        return $this->sendResponse($output, 'Fixed Asset Master retrieved successfully');
    }

    public function assetDepreciationMaster(Request $request)
    {
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->with(['approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 23);
        }, 'confirmed_by', 'created_by','audit_trial.modified_by'])->findWithoutFail($request['depMasterAutoID']);
        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        return $this->sendResponse($fixedAssetDepreciationMaster->toArray(), 'Fixed Asset Master retrieved successfully');
    }


    function assetDepreciationReopen(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            $id = $input['faID'];
            $fixedAssetDep = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);
            $emails = array();
            if (empty($fixedAssetDep)) {
                return $this->sendError('Fixed Asset Master not found');
            }


            if ($fixedAssetDep->approved == -1) {
                return $this->sendError('You cannot reopen this Asset Depreciation it is already fully approved');
            }

            if ($fixedAssetDep->RollLevForApp_curr > 1) {
                return $this->sendError('You cannot reopen this Asset Depreciation it is already partially approved');
            }

            if ($fixedAssetDep->confirmedYN == 0) {
                return $this->sendError('You cannot reopen this Asset Depreciation, it is not confirmed');
            }

            $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
                'confirmedDate' => null, 'RollLevForApp_curr' => 1];

            $this->fixedAssetDepreciationMasterRepository->update($updateInput, $id);

            $employee = \Helper::getEmployeeInfo();

            $document = DocumentMaster::where('documentSystemID', $fixedAssetDep->documentSystemID)->first();

            $cancelDocNameBody = $document->documentDescription . ' <b>' . $fixedAssetDep->depCode . '</b>';
            $cancelDocNameSubject = $document->documentDescription . ' ' . $fixedAssetDep->depCode;

            $subject = $cancelDocNameSubject . ' is reopened';

            $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

            $documentApproval = DocumentApproved::where('companySystemID', $fixedAssetDep->companySystemID)
                ->where('documentSystemCode', $fixedAssetDep->depMasterAutoID)
                ->where('documentSystemID', $fixedAssetDep->documentSystemID)
                ->where('rollLevelOrder', 1)
                ->first();

            if ($documentApproval) {
                if ($documentApproval->approvedYN == 0) {
                    $companyDocument = CompanyDocumentAttachment::where('companySystemID', $fixedAssetDep->companySystemID)
                        ->where('documentSystemID', $fixedAssetDep->documentSystemID)
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
                ->where('companySystemID', $fixedAssetDep->companySystemID)
                ->where('documentSystemID', $fixedAssetDep->documentSystemID)
                ->delete();

            /*Audit entry*/
            AuditTrial::createAuditTrial($fixedAssetDep->documentSystemID,$id,$input['reopenComments'],'Reopened');

            DB::commit();
            return $this->sendResponse($fixedAssetDep->toArray(), 'Asset depreciation reopened successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function getAssetDepApprovalByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $assetCost = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_fa_depmaster.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [23])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_fa_depmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'depMasterAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_fa_depmaster.companySystemID', $companyId)
                    ->where('erp_fa_depmaster.approved', 0)
                    ->where('erp_fa_depmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [23])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCost = $assetCost->where(function ($query) use ($search) {
                $query->where('depCode', 'LIKE', "%{$search}%")
                    ->orWhere('employees.empName', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $assetCost = [];
        }

        return \DataTables::of($assetCost)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('depMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getAssetDepApprovedByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $assetCost = DB::table('erp_documentapproved')
            ->select(
                'erp_fa_depmaster.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_fa_depmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'depMasterAutoID')
                    ->where('erp_fa_depmaster.companySystemID', $companyId)
                    ->where('erp_fa_depmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [23])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCost = $assetCost->where(function ($query) use ($search) {
                $query->where('depCode', 'LIKE', "%{$search}%")
                    ->orWhere('employees.empName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($assetCost)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('depMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    function referBackDepreciation(Request $request){

        DB::beginTransaction();
        try {
            $input = $request->all();
            $depMasterAutoID = $input['depMasterAutoID'];

            $fixedAssetDep = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($depMasterAutoID);
            if (empty($fixedAssetDep)) {
                return $this->sendError('Fixed Asset Depreciation not found');
            }

            if ($fixedAssetDep->refferedBackYN != -1) {
                return $this->sendError('You cannot amend this document');
            }


            $fixedAssetDepArray = $fixedAssetDep->toArray();

            $storefixedAssetDepHistory = DepreciationMasterReferredHistory::create($fixedAssetDepArray);

            $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $depMasterAutoID)
                ->where('companySystemID', $fixedAssetDep->companySystemID)
                ->where('documentSystemID', $fixedAssetDep->documentSystemID)
                ->get();

            if (!empty($fetchDocumentApproved)) {
                foreach ($fetchDocumentApproved as $DocumentApproved) {
                    $DocumentApproved['refTimes'] = $fixedAssetDep->timesReferred;
                }
            }

            $DocumentApprovedArray = $fetchDocumentApproved->toArray();

            $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

            $deleteApproval = DocumentApproved::where('documentSystemCode', $depMasterAutoID)
                ->where('companySystemID', $fixedAssetDep->companySystemID)
                ->where('documentSystemID', $fixedAssetDep->documentSystemID)
                ->delete();

            if ($deleteApproval) {
                $fixedAssetDep->refferedBackYN = 0;
                $fixedAssetDep->confirmedYN = 0;
                $fixedAssetDep->confirmedByEmpSystemID = null;
                $fixedAssetDep->confirmedByEmpID = null;
                $fixedAssetDep->confirmedDate = null;
                $fixedAssetDep->RollLevForApp_curr = 1;
                $fixedAssetDep->save();
            }

            CreateDepreciationAmend::dispatch($depMasterAutoID);

            DB::commit();
            return $this->sendResponse($fixedAssetDep->toArray(), 'Fixed asset depreciation amended successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function amendAssetDepreciationReview(Request $request)
    {
        $input = $request->all();
        
        $id = isset($input['id'])?$input['id']:0;

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $masterData = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);
        if (empty($masterData)) {
            return $this->sendError('Asset depreciation not found');
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend this asset depreciation, it is not confirmed');
        }

        $documentAutoId = $id;
        $documentSystemID = $masterData->documentSystemID;
        if($masterData->approved == -1){
            $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId, $documentSystemID);
            if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                    return $this->sendError($validatePendingGlPost['message']);
                }
            }
        }

        // checking document matched in depreciation
        
        $maxDepAsset = FixedAssetDepreciationMaster::where('companySystemID',$masterData->companySystemID)
                                                  ->where('is_acc_dep','=',0)
                                                  ->where('depMasterAutoID','>',$id)
                                                  ->count();

        if(!$masterData->is_acc_dep)
        {
            if($maxDepAsset > 0)
            {
                return $this->sendError('You cannot return back to amend this asset depreciation.You can reverse only the last depreciation. ');
            }
        }
        
        if($masterData->is_acc_dep)
        {
            $faID = FixedAssetDepreciationPeriod::where('depMasterAutoID',$id)->select('faID')->first();

            $isMonthlyExists = FixedAssetDepreciationPeriod::ofAsset($faID->faID)->whereHas('master_by', function ($q) {
                $q->where('is_acc_dep', 0);
            })->exists();

            if($isMonthlyExists)
            {
                return $this->sendError('You cannot return back to amend ! This asset already has monthly depreciation processed against it. ');

            }

        }

        $emailBody = '<p>' . $masterData->faCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->faCode . ' has been return back to amend';

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($masterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $masterData->confirmedByEmpSystemID,
                    'companySystemID' => $masterData->companySystemID,
                    'docSystemID' => $masterData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $id);
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
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $id);
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->delete();

            //deleting from general ledger table
            $deleteGLData = GeneralLedger::where('documentSystemCode', $id)
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

            $masterData->approved = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedByUserID = null;
            $masterData->approvedDate = null;
            $masterData->save();

            AuditTrial::createAuditTrial($masterData->documentSystemID,$id,$input['returnComment'],'returned back to amend');

            DB::commit();
            return $this->sendResponse($masterData->toArray(), 'Asset costing amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
}
