<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMobileMasterAPIRequest;
use App\Http\Requests\API\UpdateMobileMasterAPIRequest;
use App\Models\MobileMaster;
use App\Repositories\MobileMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MobileMasterController
 * @package App\Http\Controllers\API
 */

class MobileMasterAPIController extends AppBaseController
{
    /** @var  MobileMasterRepository */
    private $mobileMasterRepository;

    public function __construct(MobileMasterRepository $mobileMasterRepo)
    {
        $this->mobileMasterRepository = $mobileMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileMasters",
     *      summary="Get a listing of the MobileMasters.",
     *      tags={"MobileMaster"},
     *      description="Get all MobileMasters",
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
     *                  @SWG\Items(ref="#/definitions/MobileMaster")
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
        $this->mobileMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->mobileMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $mobileMasters = $this->mobileMasterRepository->all();

        return $this->sendResponse($mobileMasters->toArray(), 'Mobile Masters retrieved successfully');
    }

    /**
     * @param CreateMobileMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/mobileMasters",
     *      summary="Store a newly created MobileMaster in storage",
     *      tags={"MobileMaster"},
     *      description="Store MobileMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileMaster")
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
     *                  ref="#/definitions/MobileMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMobileMasterAPIRequest $request)
    {
        $input = $request->all();

        $mobileMaster = $this->mobileMasterRepository->create($input);

        return $this->sendResponse($mobileMaster->toArray(), 'Mobile Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileMasters/{id}",
     *      summary="Display the specified MobileMaster",
     *      tags={"MobileMaster"},
     *      description="Get MobileMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileMaster",
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
     *                  ref="#/definitions/MobileMaster"
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
        /** @var MobileMaster $mobileMaster */
        $mobileMaster = $this->mobileMasterRepository->findWithoutFail($id);

        if (empty($mobileMaster)) {
            return $this->sendError('Mobile Master not found');
        }

        return $this->sendResponse($mobileMaster->toArray(), 'Mobile Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMobileMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/mobileMasters/{id}",
     *      summary="Update the specified MobileMaster in storage",
     *      tags={"MobileMaster"},
     *      description="Update MobileMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileMaster")
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
     *                  ref="#/definitions/MobileMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMobileMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var MobileMaster $mobileMaster */
        $mobileMaster = $this->mobileMasterRepository->findWithoutFail($id);

        if (empty($mobileMaster)) {
            return $this->sendError('Mobile Master not found');
        }

        $mobileMaster = $this->mobileMasterRepository->update($input, $id);

        return $this->sendResponse($mobileMaster->toArray(), 'MobileMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/mobileMasters/{id}",
     *      summary="Remove the specified MobileMaster from storage",
     *      tags={"MobileMaster"},
     *      description="Delete MobileMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileMaster",
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
        /** @var MobileMaster $mobileMaster */
        $mobileMaster = $this->mobileMasterRepository->findWithoutFail($id);

        if (empty($mobileMaster)) {
            return $this->sendError('Mobile Master not found');
        }

        $mobileMaster->delete();

        return $this->sendSuccess('Mobile Master deleted successfully');
    }
}
