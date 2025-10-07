<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSRMTenderUserAccessAPIRequest;
use App\Http\Requests\API\UpdateSRMTenderUserAccessAPIRequest;
use App\Models\SRMTenderUserAccess;
use App\Repositories\SRMTenderUserAccessRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SRMTenderUserAccessController
 * @package App\Http\Controllers\API
 */

class SRMTenderUserAccessAPIController extends AppBaseController
{
    /** @var  SRMTenderUserAccessRepository */
    private $sRMTenderUserAccessRepository;

    public function __construct(SRMTenderUserAccessRepository $sRMTenderUserAccessRepo)
    {
        $this->sRMTenderUserAccessRepository = $sRMTenderUserAccessRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMTenderUserAccesses",
     *      summary="getSRMTenderUserAccessList",
     *      tags={"SRMTenderUserAccess"},
     *      description="Get all SRMTenderUserAccesses",
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
     *                  @OA\Items(ref="#/definitions/SRMTenderUserAccess")
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
        $this->sRMTenderUserAccessRepository->pushCriteria(new RequestCriteria($request));
        $this->sRMTenderUserAccessRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sRMTenderUserAccesses = $this->sRMTenderUserAccessRepository->all();

        return $this->sendResponse($sRMTenderUserAccesses->toArray(), trans('custom.s_r_m_tender_user_accesses_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/sRMTenderUserAccesses",
     *      summary="createSRMTenderUserAccess",
     *      tags={"SRMTenderUserAccess"},
     *      description="Create SRMTenderUserAccess",
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
     *                  ref="#/definitions/SRMTenderUserAccess"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSRMTenderUserAccessAPIRequest $request)
    {
        $input = $request->all();

        $sRMTenderUserAccess = $this->sRMTenderUserAccessRepository->create($input);

        return $this->sendResponse($sRMTenderUserAccess->toArray(), trans('custom.s_r_m_tender_user_access_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMTenderUserAccesses/{id}",
     *      summary="getSRMTenderUserAccessItem",
     *      tags={"SRMTenderUserAccess"},
     *      description="Get SRMTenderUserAccess",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMTenderUserAccess",
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
     *                  ref="#/definitions/SRMTenderUserAccess"
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
        /** @var SRMTenderUserAccess $sRMTenderUserAccess */
        $sRMTenderUserAccess = $this->sRMTenderUserAccessRepository->findWithoutFail($id);

        if (empty($sRMTenderUserAccess)) {
            return $this->sendError(trans('custom.s_r_m_tender_user_access_not_found'));
        }

        return $this->sendResponse($sRMTenderUserAccess->toArray(), trans('custom.s_r_m_tender_user_access_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/sRMTenderUserAccesses/{id}",
     *      summary="updateSRMTenderUserAccess",
     *      tags={"SRMTenderUserAccess"},
     *      description="Update SRMTenderUserAccess",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMTenderUserAccess",
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
     *                  ref="#/definitions/SRMTenderUserAccess"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSRMTenderUserAccessAPIRequest $request)
    {
        $input = $request->all();

        /** @var SRMTenderUserAccess $sRMTenderUserAccess */
        $sRMTenderUserAccess = $this->sRMTenderUserAccessRepository->findWithoutFail($id);

        if (empty($sRMTenderUserAccess)) {
            return $this->sendError(trans('custom.s_r_m_tender_user_access_not_found'));
        }

        $sRMTenderUserAccess = $this->sRMTenderUserAccessRepository->update($input, $id);

        return $this->sendResponse($sRMTenderUserAccess->toArray(), trans('custom.srmtenderuseraccess_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/sRMTenderUserAccesses/{id}",
     *      summary="deleteSRMTenderUserAccess",
     *      tags={"SRMTenderUserAccess"},
     *      description="Delete SRMTenderUserAccess",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMTenderUserAccess",
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
        /** @var SRMTenderUserAccess $sRMTenderUserAccess */
        $sRMTenderUserAccess = $this->sRMTenderUserAccessRepository->findWithoutFail($id);

        if (empty($sRMTenderUserAccess)) {
            return $this->sendError(trans('custom.s_r_m_tender_user_access_not_found'));
        }

        $sRMTenderUserAccess->delete();

        return $this->sendSuccess('S R M Tender User Access deleted successfully');
    }
}
