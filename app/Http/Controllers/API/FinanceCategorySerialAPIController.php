<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinanceCategorySerialAPIRequest;
use App\Http\Requests\API\UpdateFinanceCategorySerialAPIRequest;
use App\Models\FinanceCategorySerial;
use App\Repositories\FinanceCategorySerialRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FinanceCategorySerialController
 * @package App\Http\Controllers\API
 */

class FinanceCategorySerialAPIController extends AppBaseController
{
    /** @var  FinanceCategorySerialRepository */
    private $financeCategorySerialRepository;

    public function __construct(FinanceCategorySerialRepository $financeCategorySerialRepo)
    {
        $this->financeCategorySerialRepository = $financeCategorySerialRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/financeCategorySerials",
     *      summary="Get a listing of the FinanceCategorySerials.",
     *      tags={"FinanceCategorySerial"},
     *      description="Get all FinanceCategorySerials",
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
     *                  @SWG\Items(ref="#/definitions/FinanceCategorySerial")
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
        $this->financeCategorySerialRepository->pushCriteria(new RequestCriteria($request));
        $this->financeCategorySerialRepository->pushCriteria(new LimitOffsetCriteria($request));
        $financeCategorySerials = $this->financeCategorySerialRepository->all();

        return $this->sendResponse($financeCategorySerials->toArray(), trans('custom.finance_category_serials_retrieved_successfully'));
    }

    /**
     * @param CreateFinanceCategorySerialAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/financeCategorySerials",
     *      summary="Store a newly created FinanceCategorySerial in storage",
     *      tags={"FinanceCategorySerial"},
     *      description="Store FinanceCategorySerial",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FinanceCategorySerial that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FinanceCategorySerial")
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
     *                  ref="#/definitions/FinanceCategorySerial"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFinanceCategorySerialAPIRequest $request)
    {
        $input = $request->all();

        $financeCategorySerial = $this->financeCategorySerialRepository->create($input);

        return $this->sendResponse($financeCategorySerial->toArray(), trans('custom.finance_category_serial_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/financeCategorySerials/{id}",
     *      summary="Display the specified FinanceCategorySerial",
     *      tags={"FinanceCategorySerial"},
     *      description="Get FinanceCategorySerial",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FinanceCategorySerial",
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
     *                  ref="#/definitions/FinanceCategorySerial"
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
        /** @var FinanceCategorySerial $financeCategorySerial */
        $financeCategorySerial = $this->financeCategorySerialRepository->findWithoutFail($id);

        if (empty($financeCategorySerial)) {
            return $this->sendError(trans('custom.finance_category_serial_not_found'));
        }

        return $this->sendResponse($financeCategorySerial->toArray(), trans('custom.finance_category_serial_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateFinanceCategorySerialAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/financeCategorySerials/{id}",
     *      summary="Update the specified FinanceCategorySerial in storage",
     *      tags={"FinanceCategorySerial"},
     *      description="Update FinanceCategorySerial",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FinanceCategorySerial",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FinanceCategorySerial that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FinanceCategorySerial")
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
     *                  ref="#/definitions/FinanceCategorySerial"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinanceCategorySerialAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinanceCategorySerial $financeCategorySerial */
        $financeCategorySerial = $this->financeCategorySerialRepository->findWithoutFail($id);

        if (empty($financeCategorySerial)) {
            return $this->sendError(trans('custom.finance_category_serial_not_found'));
        }

        $financeCategorySerial = $this->financeCategorySerialRepository->update($input, $id);

        return $this->sendResponse($financeCategorySerial->toArray(), trans('custom.financecategoryserial_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/financeCategorySerials/{id}",
     *      summary="Remove the specified FinanceCategorySerial from storage",
     *      tags={"FinanceCategorySerial"},
     *      description="Delete FinanceCategorySerial",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FinanceCategorySerial",
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
        /** @var FinanceCategorySerial $financeCategorySerial */
        $financeCategorySerial = $this->financeCategorySerialRepository->findWithoutFail($id);

        if (empty($financeCategorySerial)) {
            return $this->sendError(trans('custom.finance_category_serial_not_found'));
        }

        $financeCategorySerial->delete();

        return $this->sendSuccess('Finance Category Serial deleted successfully');
    }
}
