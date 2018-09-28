<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDirectInvoiceDetailsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateDirectInvoiceDetailsRefferedBackAPIRequest;
use App\Models\DirectInvoiceDetailsRefferedBack;
use App\Repositories\DirectInvoiceDetailsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DirectInvoiceDetailsRefferedBackController
 * @package App\Http\Controllers\API
 */

class DirectInvoiceDetailsRefferedBackAPIController extends AppBaseController
{
    /** @var  DirectInvoiceDetailsRefferedBackRepository */
    private $directInvoiceDetailsRefferedBackRepository;

    public function __construct(DirectInvoiceDetailsRefferedBackRepository $directInvoiceDetailsRefferedBackRepo)
    {
        $this->directInvoiceDetailsRefferedBackRepository = $directInvoiceDetailsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directInvoiceDetailsRefferedBacks",
     *      summary="Get a listing of the DirectInvoiceDetailsRefferedBacks.",
     *      tags={"DirectInvoiceDetailsRefferedBack"},
     *      description="Get all DirectInvoiceDetailsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/DirectInvoiceDetailsRefferedBack")
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
        $this->directInvoiceDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->directInvoiceDetailsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directInvoiceDetailsRefferedBacks = $this->directInvoiceDetailsRefferedBackRepository->all();

        return $this->sendResponse($directInvoiceDetailsRefferedBacks->toArray(), 'Direct Invoice Details Reffered Backs retrieved successfully');
    }

    /**
     * @param CreateDirectInvoiceDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directInvoiceDetailsRefferedBacks",
     *      summary="Store a newly created DirectInvoiceDetailsRefferedBack in storage",
     *      tags={"DirectInvoiceDetailsRefferedBack"},
     *      description="Store DirectInvoiceDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectInvoiceDetailsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectInvoiceDetailsRefferedBack")
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
     *                  ref="#/definitions/DirectInvoiceDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectInvoiceDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $directInvoiceDetailsRefferedBacks = $this->directInvoiceDetailsRefferedBackRepository->create($input);

        return $this->sendResponse($directInvoiceDetailsRefferedBacks->toArray(), 'Direct Invoice Details Reffered Back saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directInvoiceDetailsRefferedBacks/{id}",
     *      summary="Display the specified DirectInvoiceDetailsRefferedBack",
     *      tags={"DirectInvoiceDetailsRefferedBack"},
     *      description="Get DirectInvoiceDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetailsRefferedBack",
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
     *                  ref="#/definitions/DirectInvoiceDetailsRefferedBack"
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
        /** @var DirectInvoiceDetailsRefferedBack $directInvoiceDetailsRefferedBack */
        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($directInvoiceDetailsRefferedBack)) {
            return $this->sendError('Direct Invoice Details Reffered Back not found');
        }

        return $this->sendResponse($directInvoiceDetailsRefferedBack->toArray(), 'Direct Invoice Details Reffered Back retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDirectInvoiceDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directInvoiceDetailsRefferedBacks/{id}",
     *      summary="Update the specified DirectInvoiceDetailsRefferedBack in storage",
     *      tags={"DirectInvoiceDetailsRefferedBack"},
     *      description="Update DirectInvoiceDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetailsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectInvoiceDetailsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectInvoiceDetailsRefferedBack")
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
     *                  ref="#/definitions/DirectInvoiceDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectInvoiceDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var DirectInvoiceDetailsRefferedBack $directInvoiceDetailsRefferedBack */
        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($directInvoiceDetailsRefferedBack)) {
            return $this->sendError('Direct Invoice Details Reffered Back not found');
        }

        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($directInvoiceDetailsRefferedBack->toArray(), 'DirectInvoiceDetailsRefferedBack updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directInvoiceDetailsRefferedBacks/{id}",
     *      summary="Remove the specified DirectInvoiceDetailsRefferedBack from storage",
     *      tags={"DirectInvoiceDetailsRefferedBack"},
     *      description="Delete DirectInvoiceDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetailsRefferedBack",
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
        /** @var DirectInvoiceDetailsRefferedBack $directInvoiceDetailsRefferedBack */
        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($directInvoiceDetailsRefferedBack)) {
            return $this->sendError('Direct Invoice Details Reffered Back not found');
        }

        $directInvoiceDetailsRefferedBack->delete();

        return $this->sendResponse($id, 'Direct Invoice Details Reffered Back deleted successfully');
    }
}
