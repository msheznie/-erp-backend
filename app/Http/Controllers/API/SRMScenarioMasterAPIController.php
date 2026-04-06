<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSRMScenarioMasterAPIRequest;
use App\Http\Requests\API\UpdateSRMScenarioMasterAPIRequest;
use App\Models\SRMScenarioMaster;
use App\Repositories\SRMScenarioMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SRMScenarioMasterController
 * @package App\Http\Controllers\API
 */
class SRMScenarioMasterAPIController extends AppBaseController
{
    /** @var  SRMScenarioMasterRepository */
    private $sRMScenarioMasterRepository;

    public function __construct(SRMScenarioMasterRepository $sRMScenarioMasterRepo)
    {
        $this->sRMScenarioMasterRepository = $sRMScenarioMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMScenarioMasters",
     *      summary="getSRMScenarioMasterList",
     *      tags={"SRMScenarioMaster"},
     *      description="Get all SRMScenarioMasters",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/SRMScenarioMaster")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->sRMScenarioMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->sRMScenarioMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sRMScenarioMasters = $this->sRMScenarioMasterRepository->all();

        return $this->sendResponse($sRMScenarioMasters->toArray(), 'S R M Scenario Masters retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/sRMScenarioMasters",
     *      summary="createSRMScenarioMaster",
     *      tags={"SRMScenarioMaster"},
     *      description="Create SRMScenarioMaster",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SRMScenarioMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSRMScenarioMasterAPIRequest $request)
    {
        $input = $request->all();

        $sRMScenarioMaster = $this->sRMScenarioMasterRepository->create($input);

        return $this->sendResponse($sRMScenarioMaster->toArray(), 'S R M Scenario Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMScenarioMasters/{id}",
     *      summary="getSRMScenarioMasterItem",
     *      tags={"SRMScenarioMaster"},
     *      description="Get SRMScenarioMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMScenarioMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SRMScenarioMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var SRMScenarioMaster $sRMScenarioMaster */
        $sRMScenarioMaster = $this->sRMScenarioMasterRepository->findWithoutFail($id);

        if (empty($sRMScenarioMaster)) {
            return $this->sendError('S R M Scenario Master not found');
        }

        return $this->sendResponse($sRMScenarioMaster->toArray(), 'S R M Scenario Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/sRMScenarioMasters/{id}",
     *      summary="updateSRMScenarioMaster",
     *      tags={"SRMScenarioMaster"},
     *      description="Update SRMScenarioMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMScenarioMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SRMScenarioMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSRMScenarioMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SRMScenarioMaster $sRMScenarioMaster */
        $sRMScenarioMaster = $this->sRMScenarioMasterRepository->findWithoutFail($id);

        if (empty($sRMScenarioMaster)) {
            return $this->sendError('S R M Scenario Master not found');
        }

        $sRMScenarioMaster = $this->sRMScenarioMasterRepository->update($input, $id);

        return $this->sendResponse($sRMScenarioMaster->toArray(), 'SRMScenarioMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/sRMScenarioMasters/{id}",
     *      summary="deleteSRMScenarioMaster",
     *      tags={"SRMScenarioMaster"},
     *      description="Delete SRMScenarioMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMScenarioMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var SRMScenarioMaster $sRMScenarioMaster */
        $sRMScenarioMaster = $this->sRMScenarioMasterRepository->findWithoutFail($id);

        if (empty($sRMScenarioMaster)) {
            return $this->sendError('S R M Scenario Master not found');
        }
        $sRMScenarioMaster->delete();

        return $this->sendSuccess('S R M Scenario Master deleted successfully');
    }

    public function getAllEmailMaster(Request $request)
    {
        try {
            return $this->sRMScenarioMasterRepository->getAllEmailMaster($request);
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong'.$e->getMessage());
        }
    }
}
