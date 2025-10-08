<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDeliveryTermsMasterAPIRequest;
use App\Http\Requests\API\UpdateDeliveryTermsMasterAPIRequest;
use App\Models\DeliveryTermsMaster;
use App\Repositories\DeliveryTermsMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DeliveryTermsMasterController
 * @package App\Http\Controllers\API
 */

class DeliveryTermsMasterAPIController extends AppBaseController
{
    /** @var  DeliveryTermsMasterRepository */
    private $deliveryTermsMasterRepository;

    public function __construct(DeliveryTermsMasterRepository $deliveryTermsMasterRepo)
    {
        $this->deliveryTermsMasterRepository = $deliveryTermsMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryTermsMasters",
     *      summary="Get a listing of the DeliveryTermsMasters.",
     *      tags={"DeliveryTermsMaster"},
     *      description="Get all DeliveryTermsMasters",
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
     *                  @SWG\Items(ref="#/definitions/DeliveryTermsMaster")
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
        $this->deliveryTermsMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->deliveryTermsMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $deliveryTermsMasters = $this->deliveryTermsMasterRepository->all();

        return $this->sendResponse($deliveryTermsMasters->toArray(), trans('custom.delivery_terms_masters_retrieved_successfully'));
    }

    /**
     * @param CreateDeliveryTermsMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/deliveryTermsMasters",
     *      summary="Store a newly created DeliveryTermsMaster in storage",
     *      tags={"DeliveryTermsMaster"},
     *      description="Store DeliveryTermsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeliveryTermsMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeliveryTermsMaster")
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
     *                  ref="#/definitions/DeliveryTermsMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDeliveryTermsMasterAPIRequest $request)
    {
        $input = $request->all();
        
        $masterData = ['description'=>$input['description']];

        if(isset($input['id'])){
            $deliveryTermsMaster = DeliveryTermsMaster::where('id', $input['id'])->update($masterData);
            return $this->sendResponse($deliveryTermsMaster, trans('custom.delivery_terms_master_updated_successfully'));
        }

        $deliveryTermsMaster = $this->deliveryTermsMasterRepository->create($input);

        return $this->sendResponse($deliveryTermsMaster->toArray(), trans('custom.delivery_terms_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryTermsMasters/{id}",
     *      summary="Display the specified DeliveryTermsMaster",
     *      tags={"DeliveryTermsMaster"},
     *      description="Get DeliveryTermsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryTermsMaster",
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
     *                  ref="#/definitions/DeliveryTermsMaster"
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
        /** @var DeliveryTermsMaster $deliveryTermsMaster */
        $deliveryTermsMaster = $this->deliveryTermsMasterRepository->findWithoutFail($id);

        if (empty($deliveryTermsMaster)) {
            return $this->sendError(trans('custom.delivery_terms_master_not_found'));
        }

        return $this->sendResponse($deliveryTermsMaster->toArray(), trans('custom.delivery_terms_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDeliveryTermsMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/deliveryTermsMasters/{id}",
     *      summary="Update the specified DeliveryTermsMaster in storage",
     *      tags={"DeliveryTermsMaster"},
     *      description="Update DeliveryTermsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryTermsMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeliveryTermsMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeliveryTermsMaster")
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
     *                  ref="#/definitions/DeliveryTermsMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDeliveryTermsMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var DeliveryTermsMaster $deliveryTermsMaster */
        $deliveryTermsMaster = $this->deliveryTermsMasterRepository->findWithoutFail($id);

        if (empty($deliveryTermsMaster)) {
            return $this->sendError(trans('custom.delivery_terms_master_not_found'));
        }

        $deliveryTermsMaster = $this->deliveryTermsMasterRepository->update($input, $id);

        return $this->sendResponse($deliveryTermsMaster->toArray(), trans('custom.deliverytermsmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/deliveryTermsMasters/{id}",
     *      summary="Remove the specified DeliveryTermsMaster from storage",
     *      tags={"DeliveryTermsMaster"},
     *      description="Delete DeliveryTermsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryTermsMaster",
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
        /** @var DeliveryTermsMaster $deliveryTermsMaster */
        $deliveryTermsMaster = $this->deliveryTermsMasterRepository->findWithoutFail($id);

        if (empty($deliveryTermsMaster)) {
            return $this->sendError(trans('custom.delivery_terms_master_not_found'));
        }

        $deliveryTermsMaster->delete();

        return $this->sendSuccess('Delivery Terms Master deleted successfully');
    }
    

    public function getAllDeliveryTerms(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $deliveryTermsMaster = DeliveryTermsMaster::where('is_deleted', 0)->get();
        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $deliveryTermsMaster = $deliveryTermsMaster->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($deliveryTermsMaster)
        ->addIndexColumn()
        ->with('orderCondition', $sort)
        ->addColumn('Actions', 'Actions', "Actions")
        //->addColumn('Index', 'Index', "Index")
        ->make(true);
    }

    public function deleteDeliveryTerms(Request $request){
        $input = $request->all();
        $deliveryTermsID = $input['id'];

        $deleteData = ['is_deleted'=>1];
        $deliveryTermsMaster = DeliveryTermsMaster::where('id',$deliveryTermsID )->update($deleteData);
        return $this->sendResponse($deliveryTermsMaster, trans('custom.delivery_terms_deleted_successfully'));
    }

}
