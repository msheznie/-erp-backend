<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSRMPublicLinkAPIRequest;
use App\Http\Requests\API\UpdateSRMPublicLinkAPIRequest;
use App\Http\Requests\SrmPublicLinkRequest;
use App\Models\SRMPublicLink;
use App\Repositories\SRMPublicLinkRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Exception;

/**
 * Class SRMPublicLinkController
 * @package App\Http\Controllers\API
 */

class SRMPublicLinkAPIController extends AppBaseController
{
    /** @var  SRMPublicLinkRepository */
    private $sRMPublicLinkRepository;

    public function __construct(SRMPublicLinkRepository $sRMPublicLinkRepo)
    {
        $this->sRMPublicLinkRepository = $sRMPublicLinkRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMPublicLinks",
     *      summary="getSRMPublicLinkList",
     *      tags={"SRMPublicLink"},
     *      description="Get all SRMPublicLinks",
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
     *                  @OA\Items(ref="#/definitions/SRMPublicLink")
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
        $this->sRMPublicLinkRepository->pushCriteria(new RequestCriteria($request));
        $this->sRMPublicLinkRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sRMPublicLinks = $this->sRMPublicLinkRepository->all();

        return $this->sendResponse($sRMPublicLinks->toArray(), 'S R M Public Links retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/sRMPublicLinks",
     *      summary="createSRMPublicLink",
     *      tags={"SRMPublicLink"},
     *      description="Create SRMPublicLink",
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
     *                  ref="#/definitions/SRMPublicLink"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSRMPublicLinkAPIRequest $request)
    {
        $input = $request->all();

        $sRMPublicLink = $this->sRMPublicLinkRepository->create($input);

        return $this->sendResponse($sRMPublicLink->toArray(), 'S R M Public Link saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMPublicLinks/{id}",
     *      summary="getSRMPublicLinkItem",
     *      tags={"SRMPublicLink"},
     *      description="Get SRMPublicLink",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMPublicLink",
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
     *                  ref="#/definitions/SRMPublicLink"
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
        /** @var SRMPublicLink $sRMPublicLink */
        $sRMPublicLink = $this->sRMPublicLinkRepository->findWithoutFail($id);

        if (empty($sRMPublicLink)) {
            return $this->sendError('S R M Public Link not found');
        }

        return $this->sendResponse($sRMPublicLink->toArray(), 'S R M Public Link retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/sRMPublicLinks/{id}",
     *      summary="updateSRMPublicLink",
     *      tags={"SRMPublicLink"},
     *      description="Update SRMPublicLink",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMPublicLink",
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
     *                  ref="#/definitions/SRMPublicLink"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSRMPublicLinkAPIRequest $request)
    {
        $input = $request->all();

        /** @var SRMPublicLink $sRMPublicLink */
        $sRMPublicLink = $this->sRMPublicLinkRepository->findWithoutFail($id);

        if (empty($sRMPublicLink)) {
            return $this->sendError('S R M Public Link not found');
        }

        $sRMPublicLink = $this->sRMPublicLinkRepository->update($input, $id);

        return $this->sendResponse($sRMPublicLink->toArray(), 'SRMPublicLink updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/sRMPublicLinks/{id}",
     *      summary="deleteSRMPublicLink",
     *      tags={"SRMPublicLink"},
     *      description="Delete SRMPublicLink",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMPublicLink",
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
        /** @var SRMPublicLink $sRMPublicLink */
        $sRMPublicLink = $this->sRMPublicLinkRepository->findWithoutFail($id);

        if (empty($sRMPublicLink)) {
            return $this->sendError('S R M Public Link not found');
        }

        $sRMPublicLink->delete();

        return $this->sendSuccess('S R M Public Link deleted successfully');
    }

    public function getPublicSupplierLinkData(Request $request)
    {
       try
       {
            return $this->sRMPublicLinkRepository->getPublicLinkSupplierData($request);
       }
       catch (Exception $e)
       {
           return $this->sendError('An error occurred while retrieving data.');
       }
    }

    public function saveSupplierPublicLink(SrmPublicLinkRequest $request)
    {
        $request->validated();
        try
        {
             $this->sRMPublicLinkRepository->saveSupplierPublicLink($request);
            return $this->sendResponse([],'Link Successfully Created');
        }
        catch (Exception $e)
       {
           return $this->sendError($e->getMessage());
       }
    }
}
