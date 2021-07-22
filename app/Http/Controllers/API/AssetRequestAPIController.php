<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetRequestAPIRequest;
use App\Http\Requests\API\UpdateAssetRequestAPIRequest;
use App\Models\AssetRequest;
use App\Repositories\AssetRequestRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetRequestController
 * @package App\Http\Controllers\API
 */

class AssetRequestAPIController extends AppBaseController
{
    /** @var  AssetRequestRepository */
    private $assetRequestRepository;

    public function __construct(AssetRequestRepository $assetRequestRepo)
    {
        $this->assetRequestRepository = $assetRequestRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetRequests",
     *      summary="Get a listing of the AssetRequests.",
     *      tags={"AssetRequest"},
     *      description="Get all AssetRequests",
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
     *                  @SWG\Items(ref="#/definitions/AssetRequest")
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
        $this->assetRequestRepository->pushCriteria(new RequestCriteria($request));
        $this->assetRequestRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetRequests = $this->assetRequestRepository->all();

        return $this->sendResponse($assetRequests->toArray(), 'Asset Requests retrieved successfully');
    }

    /**
     * @param CreateAssetRequestAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetRequests",
     *      summary="Store a newly created AssetRequest in storage",
     *      tags={"AssetRequest"},
     *      description="Store AssetRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetRequest that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetRequest")
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
     *                  ref="#/definitions/AssetRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetRequestAPIRequest $request)
    {
        $input = $request->all();

        $assetRequest = $this->assetRequestRepository->create($input);

        return $this->sendResponse($assetRequest->toArray(), 'Asset Request saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetRequests/{id}",
     *      summary="Display the specified AssetRequest",
     *      tags={"AssetRequest"},
     *      description="Get AssetRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetRequest",
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
     *                  ref="#/definitions/AssetRequest"
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
        /** @var AssetRequest $assetRequest */
        $assetRequest = $this->assetRequestRepository->findWithoutFail($id);

        if (empty($assetRequest)) {
            return $this->sendError('Asset Request not found');
        }

        return $this->sendResponse($assetRequest->toArray(), 'Asset Request retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAssetRequestAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetRequests/{id}",
     *      summary="Update the specified AssetRequest in storage",
     *      tags={"AssetRequest"},
     *      description="Update AssetRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetRequest",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetRequest that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetRequest")
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
     *                  ref="#/definitions/AssetRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetRequestAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetRequest $assetRequest */
        $assetRequest = $this->assetRequestRepository->findWithoutFail($id);

        if (empty($assetRequest)) {
            return $this->sendError('Asset Request not found');
        }

        $assetRequest = $this->assetRequestRepository->update($input, $id);

        return $this->sendResponse($assetRequest->toArray(), 'AssetRequest updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetRequests/{id}",
     *      summary="Remove the specified AssetRequest from storage",
     *      tags={"AssetRequest"},
     *      description="Delete AssetRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetRequest",
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
        /** @var AssetRequest $assetRequest */
        $assetRequest = $this->assetRequestRepository->findWithoutFail($id);

        if (empty($assetRequest)) {
            return $this->sendError('Asset Request not found');
        }

        $assetRequest->delete();

        return $this->sendSuccess('Asset Request deleted successfully');
    }
    public function getAllAssetRequestList(Request $request){ 
        $input = $request->all();
        $companyID = $input['companyID'];
         $search = $request->input('search.value');
        $input = $request->all();
       
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $AssetRequestMaster = AssetRequest::with(['employee','employeeApproved'])->where('company_id', $companyID)->where('approved_yn',1);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
           $AssetRequestMaster->where(function ($query) use ($search) {
                $query->where('document_code', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%"); 
            });
        }

        return \DataTables::eloquent($AssetRequestMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

}
