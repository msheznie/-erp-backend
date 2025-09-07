<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSInvoiceSourceAPIRequest;
use App\Http\Requests\API\UpdatePOSInvoiceSourceAPIRequest;
use App\Models\POSInvoiceSource;
use App\Repositories\POSInvoiceSourceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSInvoiceSourceController
 * @package App\Http\Controllers\API
 */

class POSInvoiceSourceAPIController extends AppBaseController
{
    /** @var  POSInvoiceSourceRepository */
    private $pOSInvoiceSourceRepository;

    public function __construct(POSInvoiceSourceRepository $pOSInvoiceSourceRepo)
    {
        $this->pOSInvoiceSourceRepository = $pOSInvoiceSourceRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSInvoiceSources",
     *      summary="Get a listing of the POSInvoiceSources.",
     *      tags={"POSInvoiceSource"},
     *      description="Get all POSInvoiceSources",
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
     *                  @SWG\Items(ref="#/definitions/POSInvoiceSource")
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
        $this->pOSInvoiceSourceRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSInvoiceSourceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSInvoiceSources = $this->pOSInvoiceSourceRepository->all();

        return $this->sendResponse($pOSInvoiceSources->toArray(), trans('custom.p_o_s_invoice_sources_retrieved_successfully'));
    }

    /**
     * @param CreatePOSInvoiceSourceAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSInvoiceSources",
     *      summary="Store a newly created POSInvoiceSource in storage",
     *      tags={"POSInvoiceSource"},
     *      description="Store POSInvoiceSource",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSInvoiceSource that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSInvoiceSource")
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
     *                  ref="#/definitions/POSInvoiceSource"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSInvoiceSourceAPIRequest $request)
    {
        $input = $request->all();

        $pOSInvoiceSource = $this->pOSInvoiceSourceRepository->create($input);

        return $this->sendResponse($pOSInvoiceSource->toArray(), trans('custom.p_o_s_invoice_source_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSInvoiceSources/{id}",
     *      summary="Display the specified POSInvoiceSource",
     *      tags={"POSInvoiceSource"},
     *      description="Get POSInvoiceSource",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSInvoiceSource",
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
     *                  ref="#/definitions/POSInvoiceSource"
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
        /** @var POSInvoiceSource $pOSInvoiceSource */
        $pOSInvoiceSource = $this->pOSInvoiceSourceRepository->findWithoutFail($id);

        if (empty($pOSInvoiceSource)) {
            return $this->sendError(trans('custom.p_o_s_invoice_source_not_found'));
        }

        return $this->sendResponse($pOSInvoiceSource->toArray(), trans('custom.p_o_s_invoice_source_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSInvoiceSourceAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSInvoiceSources/{id}",
     *      summary="Update the specified POSInvoiceSource in storage",
     *      tags={"POSInvoiceSource"},
     *      description="Update POSInvoiceSource",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSInvoiceSource",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSInvoiceSource that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSInvoiceSource")
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
     *                  ref="#/definitions/POSInvoiceSource"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSInvoiceSourceAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSInvoiceSource $pOSInvoiceSource */
        $pOSInvoiceSource = $this->pOSInvoiceSourceRepository->findWithoutFail($id);

        if (empty($pOSInvoiceSource)) {
            return $this->sendError(trans('custom.p_o_s_invoice_source_not_found'));
        }

        $pOSInvoiceSource = $this->pOSInvoiceSourceRepository->update($input, $id);

        return $this->sendResponse($pOSInvoiceSource->toArray(), trans('custom.posinvoicesource_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSInvoiceSources/{id}",
     *      summary="Remove the specified POSInvoiceSource from storage",
     *      tags={"POSInvoiceSource"},
     *      description="Delete POSInvoiceSource",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSInvoiceSource",
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
        /** @var POSInvoiceSource $pOSInvoiceSource */
        $pOSInvoiceSource = $this->pOSInvoiceSourceRepository->findWithoutFail($id);

        if (empty($pOSInvoiceSource)) {
            return $this->sendError(trans('custom.p_o_s_invoice_source_not_found'));
        }

        $pOSInvoiceSource->delete();

        return $this->sendSuccess('P O S Invoice Source deleted successfully');
    }
}
