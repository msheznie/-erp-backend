<?php
/**
 * =============================================
 * -- File Name : RequestRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Request Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 06-December 2018
 * -- Description : This file contains the all CRUD for Request Reffered Back
 * -- REVISION HISTORY
 * -- Date: 06-December 2018 By: Fayas Description: Added new functions named as getReferBackHistoryByRequest()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRequestRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateRequestRefferedBackAPIRequest;
use App\Models\RequestRefferedBack;
use App\Repositories\RequestRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RequestRefferedBackController
 * @package App\Http\Controllers\API
 */

class RequestRefferedBackAPIController extends AppBaseController
{
    /** @var  RequestRefferedBackRepository */
    private $requestRefferedBackRepository;

    public function __construct(RequestRefferedBackRepository $requestRefferedBackRepo)
    {
        $this->requestRefferedBackRepository = $requestRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/requestRefferedBacks",
     *      summary="Get a listing of the RequestRefferedBacks.",
     *      tags={"RequestRefferedBack"},
     *      description="Get all RequestRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/RequestRefferedBack")
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
        $this->requestRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->requestRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $requestRefferedBacks = $this->requestRefferedBackRepository->all();

        return $this->sendResponse($requestRefferedBacks->toArray(), trans('custom.request_reffered_backs_retrieved_successfully'));
    }

    /**
     * @param CreateRequestRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/requestRefferedBacks",
     *      summary="Store a newly created RequestRefferedBack in storage",
     *      tags={"RequestRefferedBack"},
     *      description="Store RequestRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RequestRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RequestRefferedBack")
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
     *                  ref="#/definitions/RequestRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRequestRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $requestRefferedBacks = $this->requestRefferedBackRepository->create($input);

        return $this->sendResponse($requestRefferedBacks->toArray(), trans('custom.request_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/requestRefferedBacks/{id}",
     *      summary="Display the specified RequestRefferedBack",
     *      tags={"RequestRefferedBack"},
     *      description="Get RequestRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RequestRefferedBack",
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
     *                  ref="#/definitions/RequestRefferedBack"
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
        /** @var RequestRefferedBack $requestRefferedBack */
        $requestRefferedBack = $this->requestRefferedBackRepository->with(['segment_by','created_by','confirmed_by'])->findWithoutFail($id);

        if (empty($requestRefferedBack)) {
            return $this->sendError(trans('custom.request_reffered_back_not_found'));
        }

        return $this->sendResponse($requestRefferedBack->toArray(), trans('custom.request_reffered_back_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateRequestRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/requestRefferedBacks/{id}",
     *      summary="Update the specified RequestRefferedBack in storage",
     *      tags={"RequestRefferedBack"},
     *      description="Update RequestRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RequestRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RequestRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RequestRefferedBack")
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
     *                  ref="#/definitions/RequestRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRequestRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var RequestRefferedBack $requestRefferedBack */
        $requestRefferedBack = $this->requestRefferedBackRepository->findWithoutFail($id);

        if (empty($requestRefferedBack)) {
            return $this->sendError(trans('custom.request_reffered_back_not_found'));
        }

        $requestRefferedBack = $this->requestRefferedBackRepository->update($input, $id);

        return $this->sendResponse($requestRefferedBack->toArray(), trans('custom.requestrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/requestRefferedBacks/{id}",
     *      summary="Remove the specified RequestRefferedBack from storage",
     *      tags={"RequestRefferedBack"},
     *      description="Delete RequestRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RequestRefferedBack",
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
        /** @var RequestRefferedBack $requestRefferedBack */
        $requestRefferedBack = $this->requestRefferedBackRepository->findWithoutFail($id);

        if (empty($requestRefferedBack)) {
            return $this->sendError(trans('custom.request_reffered_back_not_found'));
        }

        $requestRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.request_reffered_back_deleted_successfully'));
    }

    public function getReferBackHistoryByRequest(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved'));

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

        $materielRequests = RequestRefferedBack::whereIn('companySystemID', $subCompanies)
            ->where('RequestID',$input['id'])
            ->with(['created_by', 'priority_by', 'warehouse_by','segment_by']);



        if (array_key_exists('ConfirmedYN', $input)) {

            if(($input['ConfirmedYN'] == 0 || $input['ConfirmedYN'] == 1)  && !is_null($input['ConfirmedYN'])) {
                $materielRequests->where('ConfirmedYN', $input['ConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if(($input['approved'] == 0 || $input['approved'] == -1 ) && !is_null($input['approved'])) {
                $materielRequests->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $materielRequests->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }


        $materielRequests = $materielRequests->select(
            ['RequestRefferedBackID',
             'RequestID',
                'RequestCode',
                'comments',
                'location',
                'RequestedDate',
                'priority',
                'ConfirmedYN',
                'approved',
                'serviceLineSystemID',
                'documentSystemID',
                'timesReferred'
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $materielRequests = $materielRequests->where(function ($query) use ($search) {
                $query->where('RequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($materielRequests)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('RequestRefferedBackID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
