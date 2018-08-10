<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFreeBillingMasterPerformaAPIRequest;
use App\Http\Requests\API\UpdateFreeBillingMasterPerformaAPIRequest;
use App\Models\FreeBillingMasterPerforma;
use App\Repositories\FreeBillingMasterPerformaRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FreeBillingMasterPerformaController
 * @package App\Http\Controllers\API
 */

class FreeBillingMasterPerformaAPIController extends AppBaseController
{
    /** @var  FreeBillingMasterPerformaRepository */
    private $freeBillingMasterPerformaRepository;

    public function __construct(FreeBillingMasterPerformaRepository $freeBillingMasterPerformaRepo)
    {
        $this->freeBillingMasterPerformaRepository = $freeBillingMasterPerformaRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/freeBillingMasterPerformas",
     *      summary="Get a listing of the FreeBillingMasterPerformas.",
     *      tags={"FreeBillingMasterPerforma"},
     *      description="Get all FreeBillingMasterPerformas",
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
     *                  @SWG\Items(ref="#/definitions/FreeBillingMasterPerforma")
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
        $this->freeBillingMasterPerformaRepository->pushCriteria(new RequestCriteria($request));
        $this->freeBillingMasterPerformaRepository->pushCriteria(new LimitOffsetCriteria($request));
        $freeBillingMasterPerformas = $this->freeBillingMasterPerformaRepository->all();

        return $this->sendResponse($freeBillingMasterPerformas->toArray(), 'Free Billing Master Performas retrieved successfully');
    }

    /**
     * @param CreateFreeBillingMasterPerformaAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/freeBillingMasterPerformas",
     *      summary="Store a newly created FreeBillingMasterPerforma in storage",
     *      tags={"FreeBillingMasterPerforma"},
     *      description="Store FreeBillingMasterPerforma",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FreeBillingMasterPerforma that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FreeBillingMasterPerforma")
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
     *                  ref="#/definitions/FreeBillingMasterPerforma"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFreeBillingMasterPerformaAPIRequest $request)
    {
        $input = $request->all();

        $freeBillingMasterPerformas = $this->freeBillingMasterPerformaRepository->create($input);

        return $this->sendResponse($freeBillingMasterPerformas->toArray(), 'Free Billing Master Performa saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/freeBillingMasterPerformas/{id}",
     *      summary="Display the specified FreeBillingMasterPerforma",
     *      tags={"FreeBillingMasterPerforma"},
     *      description="Get FreeBillingMasterPerforma",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FreeBillingMasterPerforma",
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
     *                  ref="#/definitions/FreeBillingMasterPerforma"
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
        /** @var FreeBillingMasterPerforma $freeBillingMasterPerforma */
        $freeBillingMasterPerforma = $this->freeBillingMasterPerformaRepository->findWithoutFail($id);

        if (empty($freeBillingMasterPerforma)) {
            return $this->sendError('Free Billing Master Performa not found');
        }

        return $this->sendResponse($freeBillingMasterPerforma->toArray(), 'Free Billing Master Performa retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFreeBillingMasterPerformaAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/freeBillingMasterPerformas/{id}",
     *      summary="Update the specified FreeBillingMasterPerforma in storage",
     *      tags={"FreeBillingMasterPerforma"},
     *      description="Update FreeBillingMasterPerforma",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FreeBillingMasterPerforma",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FreeBillingMasterPerforma that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FreeBillingMasterPerforma")
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
     *                  ref="#/definitions/FreeBillingMasterPerforma"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFreeBillingMasterPerformaAPIRequest $request)
    {
        $input = $request->all();

        /** @var FreeBillingMasterPerforma $freeBillingMasterPerforma */
        $freeBillingMasterPerforma = $this->freeBillingMasterPerformaRepository->findWithoutFail($id);

        if (empty($freeBillingMasterPerforma)) {
            return $this->sendError('Free Billing Master Performa not found');
        }

        $freeBillingMasterPerforma = $this->freeBillingMasterPerformaRepository->update($input, $id);

        return $this->sendResponse($freeBillingMasterPerforma->toArray(), 'FreeBillingMasterPerforma updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/freeBillingMasterPerformas/{id}",
     *      summary="Remove the specified FreeBillingMasterPerforma from storage",
     *      tags={"FreeBillingMasterPerforma"},
     *      description="Delete FreeBillingMasterPerforma",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FreeBillingMasterPerforma",
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
        /** @var FreeBillingMasterPerforma $freeBillingMasterPerforma */
        $freeBillingMasterPerforma = $this->freeBillingMasterPerformaRepository->findWithoutFail($id);

        if (empty($freeBillingMasterPerforma)) {
            return $this->sendError('Free Billing Master Performa not found');
        }

        $freeBillingMasterPerforma->delete();

        return $this->sendResponse($id, 'Free Billing Master Performa deleted successfully');
    }
}
