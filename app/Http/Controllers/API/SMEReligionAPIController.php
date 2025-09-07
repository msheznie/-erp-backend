<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMEReligionAPIRequest;
use App\Http\Requests\API\UpdateSMEReligionAPIRequest;
use App\Models\SMEReligion;
use App\Repositories\SMEReligionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMEReligionController
 * @package App\Http\Controllers\API
 */

class SMEReligionAPIController extends AppBaseController
{
    /** @var  SMEReligionRepository */
    private $sMEReligionRepository;

    public function __construct(SMEReligionRepository $sMEReligionRepo)
    {
        $this->sMEReligionRepository = $sMEReligionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEReligions",
     *      summary="Get a listing of the SMEReligions.",
     *      tags={"SMEReligion"},
     *      description="Get all SMEReligions",
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
     *                  @SWG\Items(ref="#/definitions/SMEReligion")
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
        $this->sMEReligionRepository->pushCriteria(new RequestCriteria($request));
        $this->sMEReligionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMEReligions = $this->sMEReligionRepository->all();

        return $this->sendResponse($sMEReligions->toArray(), trans('custom.s_m_e_religions_retrieved_successfully'));
    }

    /**
     * @param CreateSMEReligionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMEReligions",
     *      summary="Store a newly created SMEReligion in storage",
     *      tags={"SMEReligion"},
     *      description="Store SMEReligion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEReligion that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEReligion")
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
     *                  ref="#/definitions/SMEReligion"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMEReligionAPIRequest $request)
    {
        $input = $request->all();

        $sMEReligion = $this->sMEReligionRepository->create($input);

        return $this->sendResponse($sMEReligion->toArray(), trans('custom.s_m_e_religion_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEReligions/{id}",
     *      summary="Display the specified SMEReligion",
     *      tags={"SMEReligion"},
     *      description="Get SMEReligion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEReligion",
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
     *                  ref="#/definitions/SMEReligion"
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
        /** @var SMEReligion $sMEReligion */
        $sMEReligion = $this->sMEReligionRepository->findWithoutFail($id);

        if (empty($sMEReligion)) {
            return $this->sendError(trans('custom.s_m_e_religion_not_found'));
        }

        return $this->sendResponse($sMEReligion->toArray(), trans('custom.s_m_e_religion_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMEReligionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMEReligions/{id}",
     *      summary="Update the specified SMEReligion in storage",
     *      tags={"SMEReligion"},
     *      description="Update SMEReligion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEReligion",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEReligion that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEReligion")
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
     *                  ref="#/definitions/SMEReligion"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMEReligionAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMEReligion $sMEReligion */
        $sMEReligion = $this->sMEReligionRepository->findWithoutFail($id);

        if (empty($sMEReligion)) {
            return $this->sendError(trans('custom.s_m_e_religion_not_found'));
        }

        $sMEReligion = $this->sMEReligionRepository->update($input, $id);

        return $this->sendResponse($sMEReligion->toArray(), trans('custom.smereligion_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMEReligions/{id}",
     *      summary="Remove the specified SMEReligion from storage",
     *      tags={"SMEReligion"},
     *      description="Delete SMEReligion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEReligion",
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
        /** @var SMEReligion $sMEReligion */
        $sMEReligion = $this->sMEReligionRepository->findWithoutFail($id);

        if (empty($sMEReligion)) {
            return $this->sendError(trans('custom.s_m_e_religion_not_found'));
        }

        $sMEReligion->delete();

        return $this->sendSuccess('S M E Religion deleted successfully');
    }
}
