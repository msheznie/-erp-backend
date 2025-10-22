<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRegisteredSupplierContactDetailAPIRequest;
use App\Http\Requests\API\UpdateRegisteredSupplierContactDetailAPIRequest;
use App\Models\RegisteredSupplierContactDetail;
use App\Repositories\RegisteredSupplierContactDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RegisteredSupplierContactDetailController
 * @package App\Http\Controllers\API
 */

class RegisteredSupplierContactDetailAPIController extends AppBaseController
{
    /** @var  RegisteredSupplierContactDetailRepository */
    private $registeredSupplierContactDetailRepository;

    public function __construct(RegisteredSupplierContactDetailRepository $registeredSupplierContactDetailRepo)
    {
        $this->registeredSupplierContactDetailRepository = $registeredSupplierContactDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/registeredSupplierContactDetails",
     *      summary="Get a listing of the RegisteredSupplierContactDetails.",
     *      tags={"RegisteredSupplierContactDetail"},
     *      description="Get all RegisteredSupplierContactDetails",
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
     *                  @SWG\Items(ref="#/definitions/RegisteredSupplierContactDetail")
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
        $this->registeredSupplierContactDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->registeredSupplierContactDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $registeredSupplierContactDetails = $this->registeredSupplierContactDetailRepository->all();

        return $this->sendResponse($registeredSupplierContactDetails->toArray(), trans('custom.registered_supplier_contact_details_retrieved_succ'));
    }

    /**
     * @param CreateRegisteredSupplierContactDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/registeredSupplierContactDetails",
     *      summary="Store a newly created RegisteredSupplierContactDetail in storage",
     *      tags={"RegisteredSupplierContactDetail"},
     *      description="Store RegisteredSupplierContactDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RegisteredSupplierContactDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RegisteredSupplierContactDetail")
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
     *                  ref="#/definitions/RegisteredSupplierContactDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRegisteredSupplierContactDetailAPIRequest $request)
    {
        $input = $request->all();
        unset($input['contact_type']);
        $input = $this->convertArrayToValue($input);

        $input['isDefault'] = ($input['isDefault'] == 1) ? -1 : 0;
        if( array_key_exists ('id' , $input )){
            $registeredSupplierContactDetail = $this->registeredSupplierContactDetailRepository->update($input,$input['id']);
        }
        return $this->sendResponse([], trans('custom.registered_supplier_contact_detail_saved_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/registeredSupplierContactDetails/{id}",
     *      summary="Display the specified RegisteredSupplierContactDetail",
     *      tags={"RegisteredSupplierContactDetail"},
     *      description="Get RegisteredSupplierContactDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplierContactDetail",
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
     *                  ref="#/definitions/RegisteredSupplierContactDetail"
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
        /** @var RegisteredSupplierContactDetail $registeredSupplierContactDetail */
        $registeredSupplierContactDetail = $this->registeredSupplierContactDetailRepository->findWithoutFail($id);

        if (empty($registeredSupplierContactDetail)) {
            return $this->sendError(trans('custom.registered_supplier_contact_detail_not_found'));
        }

        return $this->sendResponse($registeredSupplierContactDetail->toArray(), trans('custom.registered_supplier_contact_detail_retrieved_succe'));
    }

    /**
     * @param int $id
     * @param UpdateRegisteredSupplierContactDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/registeredSupplierContactDetails/{id}",
     *      summary="Update the specified RegisteredSupplierContactDetail in storage",
     *      tags={"RegisteredSupplierContactDetail"},
     *      description="Update RegisteredSupplierContactDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplierContactDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RegisteredSupplierContactDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RegisteredSupplierContactDetail")
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
     *                  ref="#/definitions/RegisteredSupplierContactDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRegisteredSupplierContactDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var RegisteredSupplierContactDetail $registeredSupplierContactDetail */
        $registeredSupplierContactDetail = $this->registeredSupplierContactDetailRepository->findWithoutFail($id);

        if (empty($registeredSupplierContactDetail)) {
            return $this->sendError(trans('custom.registered_supplier_contact_detail_not_found'));
        }

        $registeredSupplierContactDetail = $this->registeredSupplierContactDetailRepository->update($input, $id);

        return $this->sendResponse($registeredSupplierContactDetail->toArray(), trans('custom.registeredsuppliercontactdetail_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/registeredSupplierContactDetails/{id}",
     *      summary="Remove the specified RegisteredSupplierContactDetail from storage",
     *      tags={"RegisteredSupplierContactDetail"},
     *      description="Delete RegisteredSupplierContactDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplierContactDetail",
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
        /** @var RegisteredSupplierContactDetail $registeredSupplierContactDetail */
        $registeredSupplierContactDetail = $this->registeredSupplierContactDetailRepository->findWithoutFail($id);

        if (empty($registeredSupplierContactDetail)) {
            return $this->sendError(trans('custom.registered_supplier_contact_detail_not_found'));
        }

        $registeredSupplierContactDetail->delete();

        return $this->sendResponse([], trans('custom.registered_supplier_contact_detail_deleted_success'));
    }
}
