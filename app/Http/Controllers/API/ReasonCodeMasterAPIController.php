<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReasonCodeMasterAPIRequest;
use App\Http\Requests\API\UpdateReasonCodeMasterAPIRequest;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ReasonCodeMaster;
use App\Models\SalesReturnDetail;
use App\Repositories\ReasonCodeMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReasonCodeMasterController
 * @package App\Http\Controllers\API
 */

class ReasonCodeMasterAPIController extends AppBaseController
{
    /** @var  ReasonCodeMasterRepository */
    private $reasonCodeMasterRepository;

    public function __construct(ReasonCodeMasterRepository $reasonCodeMasterRepo)
    {
        $this->reasonCodeMasterRepository = $reasonCodeMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reasonCodeMasters",
     *      summary="Get a listing of the ReasonCodeMasters.",
     *      tags={"ReasonCodeMaster"},
     *      description="Get all ReasonCodeMasters",
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
     *                  @SWG\Items(ref="#/definitions/ReasonCodeMaster")
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
        $this->reasonCodeMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->reasonCodeMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reasonCodeMasters = $this->reasonCodeMasterRepository->all();

        return $this->sendResponse($reasonCodeMasters->toArray(), trans('custom.reason_code_masters_retrieved_successfully'));
    }

    /**
     * @param CreateReasonCodeMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reasonCodeMasters",
     *      summary="Store a newly created ReasonCodeMaster in storage",
     *      tags={"ReasonCodeMaster"},
     *      description="Store ReasonCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReasonCodeMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReasonCodeMaster")
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
     *                  ref="#/definitions/ReasonCodeMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReasonCodeMasterAPIRequest $request)
    {
        $input = $request->all();

        if($input['isPost'] == true){
            $input['glCode'] = null;
        }

        $reasonCodeMaster = $this->reasonCodeMasterRepository->create($input);

        return $this->sendResponse($reasonCodeMaster->toArray(), trans('custom.reason_code_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reasonCodeMasters/{id}",
     *      summary="Display the specified ReasonCodeMaster",
     *      tags={"ReasonCodeMaster"},
     *      description="Get ReasonCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReasonCodeMaster",
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
     *                  ref="#/definitions/ReasonCodeMaster"
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
        /** @var ReasonCodeMaster $reasonCodeMaster */
        $reasonCodeMaster = $this->reasonCodeMasterRepository->findWithoutFail($id);

        if (empty($reasonCodeMaster)) {
            return $this->sendError(trans('custom.reason_code_master_not_found'));
        }

        return $this->sendResponse($reasonCodeMaster->toArray(), trans('custom.reason_code_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateReasonCodeMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reasonCodeMasters/{id}",
     *      summary="Update the specified ReasonCodeMaster in storage",
     *      tags={"ReasonCodeMaster"},
     *      description="Update ReasonCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReasonCodeMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReasonCodeMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReasonCodeMaster")
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
     *                  ref="#/definitions/ReasonCodeMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update(Request $request)
    {
        $input = $request->all();

        /** @var ReasonCodeMaster $reasonCodeMaster */
        $reasonCodeMaster = $this->reasonCodeMasterRepository->findWithoutFail($input['id']);

        if (empty($reasonCodeMaster)) {
            return $this->sendError(trans('custom.reason_code_master_not_found'));
        }


        if($input['isPost'] == true){
            $input['glCode'] = null;
        }

        $input['glCode'] = isset($input['glCode'][0]) ? $input['glCode'][0] : $input['glCode'];

        if($input['isPost'] == false){
            if($input['glCode'] == null || $input['glCode'] == 0){
                return $this->sendError(trans('custom.gl_code_field_is_required'));
            };
        }



        $data =array_except($input, ['id','created_at']);

        $reasonCodeMaster = $this->reasonCodeMasterRepository->update($data, $input['id']);

        return $this->sendResponse($reasonCodeMaster->toArray(), trans('custom.reasoncodemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reasonCodeMasters/{id}",
     *      summary="Remove the specified ReasonCodeMaster from storage",
     *      tags={"ReasonCodeMaster"},
     *      description="Delete ReasonCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReasonCodeMaster",
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
        /** @var ReasonCodeMaster $reasonCodeMaster */
        $reasonCodeMaster = $this->reasonCodeMasterRepository->findWithoutFail($id);

        if (empty($reasonCodeMaster)) {
            return $this->sendError(trans('custom.reason_code_master_not_found'));
        }

        $salesReturn = SalesReturnDetail::where('reasonCode', $id)->first();
        if($salesReturn){
            return $this->sendError(trans('custom.reason_code_master_cannot_be_deleted_record_alread'));
        }

        $reasonCodeMaster->delete();
        return $this->sendResponse([],trans('custom.reason_code_master_deleted_successfully'));
    }

    /**
     * Get reason code master data for list
     * @param Request $request
     * @return mixed
     */
    public function getAllReasonCodeMaster(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $reasonCodeMasters = ReasonCodeMaster::with('glCodes')->select('*');

        $search = $request->input('search.value');
        if($search){
            $reasonCodeMasters =   $reasonCodeMasters->where(function ($q) use($search){
                $q->where('description','LIKE',"%{$search}%");
            });
        }


        return \DataTables::eloquent($reasonCodeMasters)
            ->order(function ($query) use ($input) {
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAllGLCodes(Request $request)
    {
        $glCodes = ChartOfAccountsAssigned::where('companySystemID', $request->get('companySystemID'))->where('isActive', 1)->where('isAssigned', -1)->get(['chartOfAccountSystemID', 'AccountCode', 'AccountDescription', 'controlAccounts']);

        return $this->sendResponse($glCodes, trans('custom.gl_codes_retrieved_successfully'));
    }

    public function reasonCodeMasterRecordSalesReturn($id){
        $salesReturn = SalesReturnDetail::where('reasonCode', $id)->first();
        return $this->sendResponse($salesReturn, 'Record exist in sales return detail');

    }
}
