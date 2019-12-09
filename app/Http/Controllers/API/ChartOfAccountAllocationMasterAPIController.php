<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChartOfAccountAllocationMasterAPIRequest;
use App\Http\Requests\API\UpdateChartOfAccountAllocationMasterAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountAllocationMaster;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\SegmentMaster;
use App\Repositories\ChartOfAccountAllocationMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ChartOfAccountAllocationMasterController
 * @package App\Http\Controllers\API
 */

class ChartOfAccountAllocationMasterAPIController extends AppBaseController
{
    /** @var  ChartOfAccountAllocationMasterRepository */
    private $chartOfAccountAllocationMasterRepository;

    public function __construct(ChartOfAccountAllocationMasterRepository $chartOfAccountAllocationMasterRepo)
    {
        $this->chartOfAccountAllocationMasterRepository = $chartOfAccountAllocationMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chartOfAccountAllocationMasters",
     *      summary="Get a listing of the ChartOfAccountAllocationMasters.",
     *      tags={"ChartOfAccountAllocationMaster"},
     *      description="Get all ChartOfAccountAllocationMasters",
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
     *                  @SWG\Items(ref="#/definitions/ChartOfAccountAllocationMaster")
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
        $this->chartOfAccountAllocationMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->chartOfAccountAllocationMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chartOfAccountAllocationMasters = $this->chartOfAccountAllocationMasterRepository->all();

        return $this->sendResponse($chartOfAccountAllocationMasters->toArray(), 'Chart Of Account Allocation Masters retrieved successfully');
    }

    /**
     * @param CreateChartOfAccountAllocationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chartOfAccountAllocationMasters",
     *      summary="Store a newly created ChartOfAccountAllocationMaster in storage",
     *      tags={"ChartOfAccountAllocationMaster"},
     *      description="Store ChartOfAccountAllocationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChartOfAccountAllocationMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChartOfAccountAllocationMaster")
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
     *                  ref="#/definitions/ChartOfAccountAllocationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChartOfAccountAllocationMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $messages = [
            'allocationmaid.required' => 'Allocation Master ID is required.'
        ];
        $validator = \Validator::make($input, [
            'allocationmaid' => 'required|numeric|min:1',
            'chartOfAccountSystemID' => 'required|numeric|min:1',
            'companySystemID' => 'required|numeric|min:1',
            'serviceLineSystemID' => 'required|numeric|min:1'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $company = Company::find($input['companySystemID']);
        if(!empty($company)){
            $input['companyID'] = $company->CompanyID;
        }else{
            return $this->sendError('Company Data Not Found');
        }

        $serviceLine = SegmentMaster::find($input['serviceLineSystemID']);
        if(!empty($serviceLine)){
            $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
        }else{
            return $this->sendError('ServiceLine Data Not Found');
        }

        $chartOfAccount = ChartOfAccount::find($input['chartOfAccountSystemID']);
        if(!empty($chartOfAccount)){
            $input['chartOfAccountCode'] = $chartOfAccount->AccountCode;
        }else{
            return $this->sendError('Chart Of Account Data Not Found');
        }
        $input['timestamp'] = now();
        $chartOfAccountAllocationMaster = $this->chartOfAccountAllocationMasterRepository->create($input);

        return $this->sendResponse($chartOfAccountAllocationMaster->toArray(), 'Chart Of Account Allocation Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chartOfAccountAllocationMasters/{id}",
     *      summary="Display the specified ChartOfAccountAllocationMaster",
     *      tags={"ChartOfAccountAllocationMaster"},
     *      description="Get ChartOfAccountAllocationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountAllocationMaster",
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
     *                  ref="#/definitions/ChartOfAccountAllocationMaster"
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
        /** @var ChartOfAccountAllocationMaster $chartOfAccountAllocationMaster */
        $chartOfAccountAllocationMaster = $this->chartOfAccountAllocationMasterRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationMaster)) {
            return $this->sendError('Chart Of Account Allocation Master not found');
        }

        return $this->sendResponse($chartOfAccountAllocationMaster->toArray(), 'Chart Of Account Allocation Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateChartOfAccountAllocationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chartOfAccountAllocationMasters/{id}",
     *      summary="Update the specified ChartOfAccountAllocationMaster in storage",
     *      tags={"ChartOfAccountAllocationMaster"},
     *      description="Update ChartOfAccountAllocationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountAllocationMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChartOfAccountAllocationMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChartOfAccountAllocationMaster")
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
     *                  ref="#/definitions/ChartOfAccountAllocationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChartOfAccountAllocationMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $company = Company::find($input['companySystemID']);
        if(!empty($company)){
            $input['companyID'] = $company->CompanyID;
        }else{
            return $this->sendError('Company Data Not Found');
        }

        $serviceLine = SegmentMaster::find($input['serviceLineSystemID']);
        if(!empty($serviceLine)){
            $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
        }else{
            return $this->sendError('ServiceLine Data Not Found');
        }

        $chartOfAccount = ChartOfAccount::find($input['chartOfAccountSystemID']);
        if(!empty($chartOfAccount)){
            $input['chartOfAccountCode'] = $chartOfAccount->AccountCode;
        }else{
            return $this->sendError('Chart Of Account Data Not Found');
        }
        /** @var ChartOfAccountAllocationMaster $chartOfAccountAllocationMaster */
        $chartOfAccountAllocationMaster = $this->chartOfAccountAllocationMasterRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationMaster)) {
            return $this->sendError('Chart Of Account Allocation Master not found');
        }

        $chartOfAccountAllocationMaster = $this->chartOfAccountAllocationMasterRepository->update($input, $id);

        return $this->sendResponse($chartOfAccountAllocationMaster->toArray(), 'ChartOfAccountAllocationMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/chartOfAccountAllocationMasters/{id}",
     *      summary="Remove the specified ChartOfAccountAllocationMaster from storage",
     *      tags={"ChartOfAccountAllocationMaster"},
     *      description="Delete ChartOfAccountAllocationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountAllocationMaster",
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
        /** @var ChartOfAccountAllocationMaster $chartOfAccountAllocationMaster */
        $chartOfAccountAllocationMaster = $this->chartOfAccountAllocationMasterRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationMaster)) {
            return $this->sendError('Chart Of Account Allocation Master not found');
        }

        $chartOfAccountAllocationMaster->delete();

        return $this->sendResponse($id, 'Chart Of Account Allocation Master deleted successfully');
    }

    public function getAllocationConfigurationAssignFormData(Request $request) {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'chartOfAccountsAssignedID' => 'required|numeric|min:1',
            'companyId' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $chartOfAccount = ChartOfAccountsAssigned::find($input['chartOfAccountsAssignedID']);
        if(empty($chartOfAccount)){
            return $this->sendError('Chart of Account Not Found');
        }

        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $output['serviceLine'] = SegmentMaster::whereIn('companySystemID',$childCompanies)
                                        ->where('isActive',1)
                                        ->where('isServiceLine',0)
                                        ->get();

        $output['productLine'] = SegmentMaster::whereIn('companySystemID',$childCompanies)
                                        ->where('isActive',1)
                                        ->get();

        $output['allocation'] = ChartOfAccountAllocationMaster::where('companySystemID',$companyId)
                                        ->where('chartOfAccountSystemID',$chartOfAccount->chartOfAccountSystemID)
                                        ->where('allocationmaid',$chartOfAccount->AllocationID)
                                        ->with(['detail','segment','detail.segment'])
                                        ->first();
        $output['chartOfAccount'] = (!empty($chartOfAccount))?$chartOfAccount:[];
        return $this->sendResponse($output, 'Chart Of Account Allocation Form Data Retrieved Successfully');
    }
}
