<?php
/**
 * =============================================
 * -- File Name : AddressTypeAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Address Type
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for Address Type
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAddressTypeAPIRequest;
use App\Http\Requests\API\UpdateAddressTypeAPIRequest;
use App\Models\AddressType;
use App\Repositories\AddressTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AddressTypeController
 * @package App\Http\Controllers\API
 */

class AddressTypeAPIController extends AppBaseController
{
    /** @var  AddressTypeRepository */
    private $addressTypeRepository;

    public function __construct(AddressTypeRepository $addressTypeRepo)
    {
        $this->addressTypeRepository = $addressTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/addressTypes",
     *      summary="Get a listing of the AddressTypes.",
     *      tags={"AddressType"},
     *      description="Get all AddressTypes",
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
     *                  @SWG\Items(ref="#/definitions/AddressType")
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
        $this->addressTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->addressTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $addressTypes = $this->addressTypeRepository->all();

        return $this->sendResponse($addressTypes->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.address_types')]));
    }

    /**
     * @param CreateAddressTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/addressTypes",
     *      summary="Store a newly created AddressType in storage",
     *      tags={"AddressType"},
     *      description="Store AddressType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AddressType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AddressType")
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
     *                  ref="#/definitions/AddressType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAddressTypeAPIRequest $request)
    {
        $input = $request->all();

        $addressTypes = $this->addressTypeRepository->create($input);

        return $this->sendResponse($addressTypes->toArray(), trans('custom.save', ['attribute' => trans('custom.address_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/addressTypes/{id}",
     *      summary="Display the specified AddressType",
     *      tags={"AddressType"},
     *      description="Get AddressType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AddressType",
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
     *                  ref="#/definitions/AddressType"
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
        /** @var AddressType $addressType */
        $addressType = $this->addressTypeRepository->findWithoutFail($id);

        if (empty($addressType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.address_types')]));
        }

        return $this->sendResponse($addressType->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.address_types')]));
    }

    /**
     * @param int $id
     * @param UpdateAddressTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/addressTypes/{id}",
     *      summary="Update the specified AddressType in storage",
     *      tags={"AddressType"},
     *      description="Update AddressType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AddressType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AddressType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AddressType")
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
     *                  ref="#/definitions/AddressType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAddressTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var AddressType $addressType */
        $addressType = $this->addressTypeRepository->findWithoutFail($id);

        if (empty($addressType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.address_types')]));
        }

        $addressType = $this->addressTypeRepository->update($input, $id);

        return $this->sendResponse($addressType->toArray(), trans('custom.update', ['attribute' => trans('custom.address_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/addressTypes/{id}",
     *      summary="Remove the specified AddressType from storage",
     *      tags={"AddressType"},
     *      description="Delete AddressType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AddressType",
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
        /** @var AddressType $addressType */
        $addressType = $this->addressTypeRepository->findWithoutFail($id);

        if (empty($addressType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.address_types')]));
        }

        $addressType->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.address_types')]));
    }
}
