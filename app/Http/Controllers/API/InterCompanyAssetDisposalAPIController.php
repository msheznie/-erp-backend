<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInterCompanyAssetDisposalAPIRequest;
use App\Http\Requests\API\UpdateInterCompanyAssetDisposalAPIRequest;
use App\Models\InterCompanyAssetDisposal;
use App\Repositories\InterCompanyAssetDisposalRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InterCompanyAssetDisposalController
 * @package App\Http\Controllers\API
 */

class InterCompanyAssetDisposalAPIController extends AppBaseController
{
    /** @var  InterCompanyAssetDisposalRepository */
    private $interCompanyAssetDisposalRepository;

    public function __construct(InterCompanyAssetDisposalRepository $interCompanyAssetDisposalRepo)
    {
        $this->interCompanyAssetDisposalRepository = $interCompanyAssetDisposalRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/interCompanyAssetDisposals",
     *      summary="Get a listing of the InterCompanyAssetDisposals.",
     *      tags={"InterCompanyAssetDisposal"},
     *      description="Get all InterCompanyAssetDisposals",
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
     *                  @SWG\Items(ref="#/definitions/InterCompanyAssetDisposal")
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
        $this->interCompanyAssetDisposalRepository->pushCriteria(new RequestCriteria($request));
        $this->interCompanyAssetDisposalRepository->pushCriteria(new LimitOffsetCriteria($request));
        $interCompanyAssetDisposals = $this->interCompanyAssetDisposalRepository->all();

        return $this->sendResponse($interCompanyAssetDisposals->toArray(), trans('custom.inter_company_asset_disposals_retrieved_successful'));
    }

    /**
     * @param CreateInterCompanyAssetDisposalAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/interCompanyAssetDisposals",
     *      summary="Store a newly created InterCompanyAssetDisposal in storage",
     *      tags={"InterCompanyAssetDisposal"},
     *      description="Store InterCompanyAssetDisposal",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InterCompanyAssetDisposal that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InterCompanyAssetDisposal")
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
     *                  ref="#/definitions/InterCompanyAssetDisposal"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInterCompanyAssetDisposalAPIRequest $request)
    {
        $input = $request->all();

        $interCompanyAssetDisposal = $this->interCompanyAssetDisposalRepository->create($input);

        return $this->sendResponse($interCompanyAssetDisposal->toArray(), trans('custom.inter_company_asset_disposal_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/interCompanyAssetDisposals/{id}",
     *      summary="Display the specified InterCompanyAssetDisposal",
     *      tags={"InterCompanyAssetDisposal"},
     *      description="Get InterCompanyAssetDisposal",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InterCompanyAssetDisposal",
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
     *                  ref="#/definitions/InterCompanyAssetDisposal"
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
        /** @var InterCompanyAssetDisposal $interCompanyAssetDisposal */
        $interCompanyAssetDisposal = $this->interCompanyAssetDisposalRepository->findWithoutFail($id);

        if (empty($interCompanyAssetDisposal)) {
            return $this->sendError(trans('custom.inter_company_asset_disposal_not_found'));
        }

        return $this->sendResponse($interCompanyAssetDisposal->toArray(), trans('custom.inter_company_asset_disposal_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateInterCompanyAssetDisposalAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/interCompanyAssetDisposals/{id}",
     *      summary="Update the specified InterCompanyAssetDisposal in storage",
     *      tags={"InterCompanyAssetDisposal"},
     *      description="Update InterCompanyAssetDisposal",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InterCompanyAssetDisposal",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InterCompanyAssetDisposal that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InterCompanyAssetDisposal")
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
     *                  ref="#/definitions/InterCompanyAssetDisposal"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInterCompanyAssetDisposalAPIRequest $request)
    {
        $input = $request->all();

        /** @var InterCompanyAssetDisposal $interCompanyAssetDisposal */
        $interCompanyAssetDisposal = $this->interCompanyAssetDisposalRepository->findWithoutFail($id);

        if (empty($interCompanyAssetDisposal)) {
            return $this->sendError(trans('custom.inter_company_asset_disposal_not_found'));
        }

        $interCompanyAssetDisposal = $this->interCompanyAssetDisposalRepository->update($input, $id);

        return $this->sendResponse($interCompanyAssetDisposal->toArray(), trans('custom.intercompanyassetdisposal_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/interCompanyAssetDisposals/{id}",
     *      summary="Remove the specified InterCompanyAssetDisposal from storage",
     *      tags={"InterCompanyAssetDisposal"},
     *      description="Delete InterCompanyAssetDisposal",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InterCompanyAssetDisposal",
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
        /** @var InterCompanyAssetDisposal $interCompanyAssetDisposal */
        $interCompanyAssetDisposal = $this->interCompanyAssetDisposalRepository->findWithoutFail($id);

        if (empty($interCompanyAssetDisposal)) {
            return $this->sendError(trans('custom.inter_company_asset_disposal_not_found'));
        }

        $interCompanyAssetDisposal->delete();

        return $this->sendSuccess('Inter Company Asset Disposal deleted successfully');
    }
}
