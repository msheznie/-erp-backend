<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderCircularsAPIRequest;
use App\Http\Requests\API\UpdateTenderCircularsAPIRequest;
use App\Models\TenderCirculars;
use App\Repositories\TenderCircularsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderCircularsController
 * @package App\Http\Controllers\API
 */

class TenderCircularsAPIController extends AppBaseController
{
    /** @var  TenderCircularsRepository */
    private $tenderCircularsRepository;

    public function __construct(TenderCircularsRepository $tenderCircularsRepo)
    {
        $this->tenderCircularsRepository = $tenderCircularsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderCirculars",
     *      summary="Get a listing of the TenderCirculars.",
     *      tags={"TenderCirculars"},
     *      description="Get all TenderCirculars",
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
     *                  @SWG\Items(ref="#/definitions/TenderCirculars")
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
        $this->tenderCircularsRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderCircularsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderCirculars = $this->tenderCircularsRepository->all();

        return $this->sendResponse($tenderCirculars->toArray(), 'Tender Circulars retrieved successfully');
    }

    /**
     * @param CreateTenderCircularsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderCirculars",
     *      summary="Store a newly created TenderCirculars in storage",
     *      tags={"TenderCirculars"},
     *      description="Store TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderCirculars that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderCirculars")
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
     *                  ref="#/definitions/TenderCirculars"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderCircularsAPIRequest $request)
    {
        $input = $request->all();

        $tenderCirculars = $this->tenderCircularsRepository->create($input);

        return $this->sendResponse($tenderCirculars->toArray(), 'Tender Circulars saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderCirculars/{id}",
     *      summary="Display the specified TenderCirculars",
     *      tags={"TenderCirculars"},
     *      description="Get TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCirculars",
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
     *                  ref="#/definitions/TenderCirculars"
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
        /** @var TenderCirculars $tenderCirculars */
        $tenderCirculars = $this->tenderCircularsRepository->findWithoutFail($id);

        if (empty($tenderCirculars)) {
            return $this->sendError('Tender Circulars not found');
        }

        return $this->sendResponse($tenderCirculars->toArray(), 'Tender Circulars retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderCircularsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderCirculars/{id}",
     *      summary="Update the specified TenderCirculars in storage",
     *      tags={"TenderCirculars"},
     *      description="Update TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCirculars",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderCirculars that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderCirculars")
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
     *                  ref="#/definitions/TenderCirculars"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderCircularsAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderCirculars $tenderCirculars */
        $tenderCirculars = $this->tenderCircularsRepository->findWithoutFail($id);

        if (empty($tenderCirculars)) {
            return $this->sendError('Tender Circulars not found');
        }

        $tenderCirculars = $this->tenderCircularsRepository->update($input, $id);

        return $this->sendResponse($tenderCirculars->toArray(), 'TenderCirculars updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderCirculars/{id}",
     *      summary="Remove the specified TenderCirculars from storage",
     *      tags={"TenderCirculars"},
     *      description="Delete TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCirculars",
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
        /** @var TenderCirculars $tenderCirculars */
        $tenderCirculars = $this->tenderCircularsRepository->findWithoutFail($id);

        if (empty($tenderCirculars)) {
            return $this->sendError('Tender Circulars not found');
        }

        $tenderCirculars->delete();

        return $this->sendSuccess('Tender Circulars deleted successfully');
    }
}
