<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderEditLogMasterAPIRequest;
use App\Http\Requests\API\UpdateTenderEditLogMasterAPIRequest;
use App\Models\TenderEditLogMaster;
use App\Repositories\TenderEditLogMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderEditLogMasterController
 * @package App\Http\Controllers\API
 */

class TenderEditLogMasterAPIController extends AppBaseController
{
    /** @var  TenderEditLogMasterRepository */
    private $tenderEditLogMasterRepository;

    public function __construct(TenderEditLogMasterRepository $tenderEditLogMasterRepo)
    {
        $this->tenderEditLogMasterRepository = $tenderEditLogMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderEditLogMasters",
     *      summary="getTenderEditLogMasterList",
     *      tags={"TenderEditLogMaster"},
     *      description="Get all TenderEditLogMasters",
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
     *                  @OA\Items(ref="#/definitions/TenderEditLogMaster")
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
        $this->tenderEditLogMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderEditLogMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderEditLogMasters = $this->tenderEditLogMasterRepository->all();

        return $this->sendResponse($tenderEditLogMasters->toArray(), 'Tender Edit Log Masters retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderEditLogMasters",
     *      summary="createTenderEditLogMaster",
     *      tags={"TenderEditLogMaster"},
     *      description="Create TenderEditLogMaster",
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
     *                  ref="#/definitions/TenderEditLogMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderEditLogMasterAPIRequest $request)
    {
        $input = $request->all();

        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->create($input);

        return $this->sendResponse($tenderEditLogMaster->toArray(), 'Tender Edit Log Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderEditLogMasters/{id}",
     *      summary="getTenderEditLogMasterItem",
     *      tags={"TenderEditLogMaster"},
     *      description="Get TenderEditLogMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderEditLogMaster",
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
     *                  ref="#/definitions/TenderEditLogMaster"
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
        /** @var TenderEditLogMaster $tenderEditLogMaster */
        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->findWithoutFail($id);

        if (empty($tenderEditLogMaster)) {
            return $this->sendError('Tender Edit Log Master not found');
        }

        return $this->sendResponse($tenderEditLogMaster->toArray(), 'Tender Edit Log Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderEditLogMasters/{id}",
     *      summary="updateTenderEditLogMaster",
     *      tags={"TenderEditLogMaster"},
     *      description="Update TenderEditLogMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderEditLogMaster",
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
     *                  ref="#/definitions/TenderEditLogMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderEditLogMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderEditLogMaster $tenderEditLogMaster */
        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->findWithoutFail($id);

        if (empty($tenderEditLogMaster)) {
            return $this->sendError('Tender Edit Log Master not found');
        }

        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->update($input, $id);

        return $this->sendResponse($tenderEditLogMaster->toArray(), 'TenderEditLogMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderEditLogMasters/{id}",
     *      summary="deleteTenderEditLogMaster",
     *      tags={"TenderEditLogMaster"},
     *      description="Delete TenderEditLogMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderEditLogMaster",
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
        /** @var TenderEditLogMaster $tenderEditLogMaster */
        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->findWithoutFail($id);

        if (empty($tenderEditLogMaster)) {
            return $this->sendError('Tender Edit Log Master not found');
        }

        $tenderEditLogMaster->delete();

        return $this->sendSuccess('Tender Edit Log Master deleted successfully');
    }

    public function createTenderEditRequest(Request $request)
    {
       
        $input = $request->all();

        $params = array('autoID' => $input['tenderid'], 'company' => $input["companySystemID"], 'document' => 108);
        $confirm = \Helper::confirmDocument($params);


        return $this->sendResponse($confirm, 'TenderEditLogMaster updated successfully');

    }
}
