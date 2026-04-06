<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSRMDocumentMasterAPIRequest;
use App\Http\Requests\API\UpdateSRMDocumentMasterAPIRequest;
use App\Http\Requests\SRM\DocumentMasterRemoveRequest;
use App\Http\Requests\SRM\DocumentMasterRequest;
use App\Models\SRMDocumentMaster;
use App\Repositories\SRMDocumentMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SRMDocumentMasterController
 * @package App\Http\Controllers\API
 */

class SRMDocumentMasterAPIController extends AppBaseController
{
    /** @var  SRMDocumentMasterRepository */
    private $sRMDocumentMasterRepository;

    public function __construct(SRMDocumentMasterRepository $sRMDocumentMasterRepo)
    {
        $this->sRMDocumentMasterRepository = $sRMDocumentMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMDocumentMasters",
     *      summary="getSRMDocumentMasterList",
     *      tags={"SRMDocumentMaster"},
     *      description="Get all SRMDocumentMasters",
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
     *                  @OA\Items(ref="#/definitions/SRMDocumentMaster")
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
        $this->sRMDocumentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->sRMDocumentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sRMDocumentMasters = $this->sRMDocumentMasterRepository->all();

        return $this->sendResponse($sRMDocumentMasters->toArray(), 'S R M Document Masters retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/sRMDocumentMasters",
     *      summary="createSRMDocumentMaster",
     *      tags={"SRMDocumentMaster"},
     *      description="Create SRMDocumentMaster",
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
     *                  ref="#/definitions/SRMDocumentMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSRMDocumentMasterAPIRequest $request)
    {
        $input = $request->all();

        $sRMDocumentMaster = $this->sRMDocumentMasterRepository->create($input);

        return $this->sendResponse($sRMDocumentMaster->toArray(), 'S R M Document Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMDocumentMasters/{id}",
     *      summary="getSRMDocumentMasterItem",
     *      tags={"SRMDocumentMaster"},
     *      description="Get SRMDocumentMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMDocumentMaster",
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
     *                  ref="#/definitions/SRMDocumentMaster"
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
        /** @var SRMDocumentMaster $sRMDocumentMaster */
        $sRMDocumentMaster = $this->sRMDocumentMasterRepository->findWithoutFail($id);

        if (empty($sRMDocumentMaster)) {
            return $this->sendError('S R M Document Master not found');
        }

        return $this->sendResponse($sRMDocumentMaster->toArray(), 'S R M Document Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/sRMDocumentMasters/{id}",
     *      summary="updateSRMDocumentMaster",
     *      tags={"SRMDocumentMaster"},
     *      description="Update SRMDocumentMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMDocumentMaster",
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
     *                  ref="#/definitions/SRMDocumentMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, DocumentMasterRequest $request)
    {
        $input = $request->all();

        /** @var SRMDocumentMaster $sRMDocumentMaster */
        $sRMDocumentMaster = $this->sRMDocumentMasterRepository->getDocumentMaster($id);

        if (empty($sRMDocumentMaster)) {
            return $this->sendError('Document Master not found');
        }

        $sRMDocumentMaster = $this->sRMDocumentMasterRepository->update($input, $sRMDocumentMaster->id);

        return $this->sendResponse([], 'Document master updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/sRMDocumentMasters/{id}",
     *      summary="deleteSRMDocumentMaster",
     *      tags={"SRMDocumentMaster"},
     *      description="Delete SRMDocumentMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMDocumentMaster",
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
        /** @var SRMDocumentMaster $sRMDocumentMaster */
        $sRMDocumentMaster = $this->sRMDocumentMasterRepository->findWithoutFail($id);

        if (empty($sRMDocumentMaster)) {
            return $this->sendError('S R M Document Master not found');
        }

        $sRMDocumentMaster->delete();

        return $this->sendSuccess('S R M Document Master deleted successfully');
    }

    public function getAllDocumentMaster(Request $request)
    {
        try {
            return $this->sRMDocumentMasterRepository->getAllDocumentMaster($request);
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong'.$e->getMessage());
        }
    }

    public function getDocumentDropData(Request $request)
    {
        try {
            $data = $this->sRMDocumentMasterRepository->getDocumentFormData();
            return $this->sendResponse($data, 'Document form data fetched successfully');
        } catch (\Throwable $e) {
            return $this->sendError('Something went wrong', $e->getMessage());
        }
    }

    public function documentMasterCrud(DocumentMasterRequest $request)
    {
        try {
            $document = $this->sRMDocumentMasterRepository->documentMasterCrud($request);
            if(!$document['success']){
                return $this->sendError($document['message']);
            } else {
                return $this->sendResponse([], $document['message']);
            }
        } catch (\Exception $e) {
            return $this->sendError('Error creating document', $e->getMessage());
        }
    }

    public function getTenderDocumentMaster(Request $request)
    {
        try {
            $data = $this->sRMDocumentMasterRepository->getTenderDocumentMaster($request);
            return $this->sendResponse($data['data'], 'Document master data fetched successfully');
        } catch (\Throwable $e) {
            return $this->sendError('Something went wrong', $e->getMessage());
        }
    }

    public function removeDocMasterDelete(DocumentMasterRemoveRequest $request)
    {
        try {
            $document = $this->sRMDocumentMasterRepository->removeDocMasterDelete($request);
            if(!$document['success']){
                return $this->sendError($document['message']);
            } else {
                return $this->sendResponse([], $document['message']);
            }
        } catch (\Exception $e) {
            return $this->sendError('Error creating document', $e->getMessage());
        }
    }
}
