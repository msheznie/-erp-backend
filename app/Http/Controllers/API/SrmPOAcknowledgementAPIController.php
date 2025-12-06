<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrmPOAcknowledgementAPIRequest;
use App\Http\Requests\API\UpdateSrmPOAcknowledgementAPIRequest;
use App\Models\SrmPOAcknowledgement;
use App\Repositories\SrmPOAcknowledgementRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrmPOAcknowledgementController
 * @package App\Http\Controllers\API
 */

class SrmPOAcknowledgementAPIController extends AppBaseController
{
    /** @var  SrmPOAcknowledgementRepository */
    private $srmPOAcknowledgementRepository;

    public function __construct(SrmPOAcknowledgementRepository $srmPOAcknowledgementRepo)
    {
        $this->srmPOAcknowledgementRepository = $srmPOAcknowledgementRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/srmPOAcknowledgements",
     *      summary="getSrmPOAcknowledgementList",
     *      tags={"SrmPOAcknowledgement"},
     *      description="Get all SrmPOAcknowledgements",
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
     *                  @OA\Items(ref="#/definitions/SrmPOAcknowledgement")
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
        $this->srmPOAcknowledgementRepository->pushCriteria(new RequestCriteria($request));
        $this->srmPOAcknowledgementRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srmPOAcknowledgements = $this->srmPOAcknowledgementRepository->all();

        return $this->sendResponse($srmPOAcknowledgements->toArray(), 'Srm P O Acknowledgements retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/srmPOAcknowledgements",
     *      summary="createSrmPOAcknowledgement",
     *      tags={"SrmPOAcknowledgement"},
     *      description="Create SrmPOAcknowledgement",
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
     *                  ref="#/definitions/SrmPOAcknowledgement"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrmPOAcknowledgementAPIRequest $request)
    {
        $input = $request->all();

        $srmPOAcknowledgement = $this->srmPOAcknowledgementRepository->create($input);

        return $this->sendResponse($srmPOAcknowledgement->toArray(), 'Srm P O Acknowledgement saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/srmPOAcknowledgements/{id}",
     *      summary="getSrmPOAcknowledgementItem",
     *      tags={"SrmPOAcknowledgement"},
     *      description="Get SrmPOAcknowledgement",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmPOAcknowledgement",
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
     *                  ref="#/definitions/SrmPOAcknowledgement"
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
        /** @var SrmPOAcknowledgement $srmPOAcknowledgement */
        $srmPOAcknowledgement = $this->srmPOAcknowledgementRepository->findWithoutFail($id);

        if (empty($srmPOAcknowledgement)) {
            return $this->sendError('Srm P O Acknowledgement not found');
        }

        return $this->sendResponse($srmPOAcknowledgement->toArray(), 'Srm P O Acknowledgement retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/srmPOAcknowledgements/{id}",
     *      summary="updateSrmPOAcknowledgement",
     *      tags={"SrmPOAcknowledgement"},
     *      description="Update SrmPOAcknowledgement",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmPOAcknowledgement",
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
     *                  ref="#/definitions/SrmPOAcknowledgement"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrmPOAcknowledgementAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrmPOAcknowledgement $srmPOAcknowledgement */
        $srmPOAcknowledgement = $this->srmPOAcknowledgementRepository->findWithoutFail($id);

        if (empty($srmPOAcknowledgement)) {
            return $this->sendError('Srm P O Acknowledgement not found');
        }

        $srmPOAcknowledgement = $this->srmPOAcknowledgementRepository->update($input, $id);

        return $this->sendResponse($srmPOAcknowledgement->toArray(), 'SrmPOAcknowledgement updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/srmPOAcknowledgements/{id}",
     *      summary="deleteSrmPOAcknowledgement",
     *      tags={"SrmPOAcknowledgement"},
     *      description="Delete SrmPOAcknowledgement",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmPOAcknowledgement",
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
        /** @var SrmPOAcknowledgement $srmPOAcknowledgement */
        $srmPOAcknowledgement = $this->srmPOAcknowledgementRepository->findWithoutFail($id);

        if (empty($srmPOAcknowledgement)) {
            return $this->sendError('Srm P O Acknowledgement not found');
        }

        $srmPOAcknowledgement->delete();

        return $this->sendSuccess('Srm P O Acknowledgement deleted successfully');
    }
}
