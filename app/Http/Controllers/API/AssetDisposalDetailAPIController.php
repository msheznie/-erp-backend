<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetDisposalDetailAPIRequest;
use App\Http\Requests\API\UpdateAssetDisposalDetailAPIRequest;
use App\Models\AssetDisposalDetail;
use App\Models\AssetDisposalMaster;
use App\Models\FixedAssetMaster;
use App\Models\ItemAssigned;
use App\Repositories\AssetDisposalDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetDisposalDetailController
 * @package App\Http\Controllers\API
 */
class AssetDisposalDetailAPIController extends AppBaseController
{
    /** @var  AssetDisposalDetailRepository */
    private $assetDisposalDetailRepository;

    public function __construct(AssetDisposalDetailRepository $assetDisposalDetailRepo)
    {
        $this->assetDisposalDetailRepository = $assetDisposalDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalDetails",
     *      summary="Get a listing of the AssetDisposalDetails.",
     *      tags={"AssetDisposalDetail"},
     *      description="Get all AssetDisposalDetails",
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
     *                  @SWG\Items(ref="#/definitions/AssetDisposalDetail")
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
        $this->assetDisposalDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->assetDisposalDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetDisposalDetails = $this->assetDisposalDetailRepository->all();

        return $this->sendResponse($assetDisposalDetails->toArray(), 'Asset Disposal Details retrieved successfully');
    }

    /**
     * @param CreateAssetDisposalDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetDisposalDetails",
     *      summary="Store a newly created AssetDisposalDetail in storage",
     *      tags={"AssetDisposalDetail"},
     *      description="Store AssetDisposalDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalDetail")
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
     *                  ref="#/definitions/AssetDisposalDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetDisposalDetailAPIRequest $request)
    {
        $input = $request->all();
        $assetDisposalMaster = AssetDisposalMaster::find($input["assetdisposalMasterAutoID"]);
        DB::beginTransaction();
        try {
            $finalError = array(
                'disposal_asset_already_exist' => array(),
            );
            $error_count = 0;

            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {
                    $alreadyExistChk = AssetDisposalDetail::OfMaster($input["assetdisposalMasterAutoID"])->where('faID', $new['faID'])->first();
                    if ($alreadyExistChk) {
                        array_push($finalError['disposal_asset_already_exist'], 'FA' . ' | ' . $new['faCode']);
                        $error_count++;
                    }
                }
            }

            $confirm_error = array('type' => 'disposal_asset_already_exist', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("Error", 500, $confirm_error);
            }

            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {
                    $depAmountLocal = 0;
                    $depAmountRpt = 0;
                    if (count($new['depperiod_by']) > 0) {
                        $depAmountLocal = $new['depperiod_by'][0]['depAmountLocal'];
                    } else {
                        $depAmountLocal = 0;
                    }
                    if (count($new['depperiod_by']) > 0) {
                        $depAmountRpt = $new['depperiod_by'][0]['depAmountRpt'];
                    } else {
                        $depAmountRpt = 0;
                    }
                    count($new['depperiod_by']) > 0 ? $new['depperiod_by'][0]['depAmountRpt'] : 0;
                    $tempArray["assetdisposalMasterAutoID"] = $input["assetdisposalMasterAutoID"];
                    $tempArray["companySystemID"] = $new["companySystemID"];
                    $tempArray["companyID"] = $new["companyID"];
                    $tempArray["serviceLineSystemID"] = $new["serviceLineSystemID"];
                    $tempArray["serviceLineCode"] = $new["serviceLineCode"];
                    $tempArray["itemCode"] = $new["itemCode"];
                    $tempArray["faID"] = $new["faID"];
                    $tempArray["faCode"] = $new["faCode"];
                    $tempArray["faUnitSerialNo"] = $new["faUnitSerialNo"];
                    $tempArray["assetDescription"] = $new["assetDescription"];
                    $tempArray["COSTUNIT"] = $new["COSTUNIT"];
                    $tempArray["costUnitRpt"] = $new["costUnitRpt"];
                    $tempArray["depAmountLocal"] = $depAmountLocal;
                    $tempArray["depAmountRpt"] = $depAmountRpt;
                    $tempArray["netBookValueLocal"] = $new["COSTUNIT"] - $depAmountLocal;
                    $tempArray["netBookValueRpt"] = $new["costUnitRpt"] - $depAmountRpt;
                    $tempArray["COSTGLCODESystemID"] = $new["costglCodeSystemID"];
                    $tempArray["COSTGLCODE"] = $new["COSTGLCODE"];
                    $tempArray["ACCDEPGLCODESystemID"] = $new["accdepglCodeSystemID"];
                    $tempArray["ACCDEPGLCODE"] = $new["ACCDEPGLCODE"];
                    $tempArray["DISPOGLCODESystemID"] = $new["dispglCodeSystemID"];
                    $tempArray["DISPOGLCODE"] = $new["DISPOGLCODE"];
                    $assetDisposalDetails = $this->assetDisposalDetailRepository->create($tempArray);

                    $updateAsset = FixedAssetMaster::find($new["faID"])
                        ->update(['DIPOSED' => -1, 'selectedForDisposal' => -1, 'disposedDate' => $assetDisposalMaster->disposalDocumentDate, 'assetdisposalMasterAutoID' => $input["assetdisposalMasterAutoID"]]);
                }
            }
            DB::commit();
            return $this->sendResponse('', 'Asset Disposal Detail saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalDetails/{id}",
     *      summary="Display the specified AssetDisposalDetail",
     *      tags={"AssetDisposalDetail"},
     *      description="Get AssetDisposalDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalDetail",
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
     *                  ref="#/definitions/AssetDisposalDetail"
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
        /** @var AssetDisposalDetail $assetDisposalDetail */
        $assetDisposalDetail = $this->assetDisposalDetailRepository->findWithoutFail($id);

        if (empty($assetDisposalDetail)) {
            return $this->sendError('Asset Disposal Detail not found');
        }

        return $this->sendResponse($assetDisposalDetail->toArray(), 'Asset Disposal Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAssetDisposalDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetDisposalDetails/{id}",
     *      summary="Update the specified AssetDisposalDetail in storage",
     *      tags={"AssetDisposalDetail"},
     *      description="Update AssetDisposalDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalDetail")
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
     *                  ref="#/definitions/AssetDisposalDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetDisposalDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);


        /** @var AssetDisposalDetail $assetDisposalDetail */
        $assetDisposalDetail = $this->assetDisposalDetailRepository->findWithoutFail($id);

        if (empty($assetDisposalDetail)) {
            return $this->sendError('Asset Disposal Detail not found');
        }

        $disposalMaster = AssetDisposalMaster::find($assetDisposalDetail->assetdisposalMasterAutoID);

        if (empty($disposalMaster)) {
            return $this->sendError('Asset Disposal Master not found');
        }

        if($disposalMaster->disposalType == 1){
            $itemAssign = ItemAssigned::where('companySystemID',$disposalMaster->toCompanySystemID)->where('itemCodeSystem',$input['itemCode'])->where('isActive',1)->where('isAssigned',-1)->first();
            if(empty($itemAssign)){
                return $this->sendError('This item is not assigned to '.$disposalMaster->toCompanyID. ' Company',500);
            }
        }

        $assetDisposalDetail = $this->assetDisposalDetailRepository->update($input, $id);

        return $this->sendResponse($assetDisposalDetail->toArray(), 'AssetDisposalDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetDisposalDetails/{id}",
     *      summary="Remove the specified AssetDisposalDetail from storage",
     *      tags={"AssetDisposalDetail"},
     *      description="Delete AssetDisposalDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalDetail",
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
        /** @var AssetDisposalDetail $assetDisposalDetail */
        DB::beginTransaction();
        try {
            $assetDisposalDetail = $this->assetDisposalDetailRepository->findWithoutFail($id);
            $assetDisposalDetail2 = $this->assetDisposalDetailRepository->findWithoutFail($id);

            if (empty($assetDisposalDetail)) {
                return $this->sendError('Asset Disposal Detail not found');
            }

            $assetDisposalDetail->delete();

            $updateAsset = FixedAssetMaster::find($assetDisposalDetail2->faID)
                ->update(['DIPOSED' => 0, 'selectedForDisposal' => 0, 'disposedDate' => null, 'assetdisposalMasterAutoID' => null]);
            DB::commit();
            return $this->sendResponse($id, 'Asset Disposal Detail deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

    function getAssetDisposalDetail(Request $request)
    {
        $assetDisposalDetail = AssetDisposalDetail::OfMaster($request->assetdisposalMasterAutoID)->with('segment_by', 'item_by')->get();
        if (empty($assetDisposalDetail)) {
            return $this->sendError('Asset Disposal Detail not found');
        }
        return $this->sendResponse($assetDisposalDetail->toArray(), 'Asset Disposal Detail retrieved successfully');
    }

    public function deleteAllDisposalDetail(Request $request)
    {
        $assetdisposalMasterAutoID = $request->assetdisposalMasterAutoID;

        DB::beginTransaction();
        try {
            $assetDisposalDetail = $this->assetDisposalDetailRepository->findWhere(['assetdisposalMasterAutoID' => $assetdisposalMasterAutoID]);

            if (empty($assetDisposalDetail)) {
                return $this->sendError('Asset Disposal Detail not found');
            }

            foreach ($assetDisposalDetail as $val) {
                $detail = $this->assetDisposalDetailRepository->find($val->assetDisposalDetailAutoID);
                $detail->delete();

                $updateAsset = FixedAssetMaster::find($val->faID)
                    ->update(['DIPOSED' => 0, 'selectedForDisposal' => 0, 'disposedDate' => null, 'assetdisposalMasterAutoID' => null]);
            }

            DB::commit();
            return $this->sendResponse($assetdisposalMasterAutoID, 'Asset Disposal Detail deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }
    }
}
