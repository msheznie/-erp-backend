<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMENationalityAPIRequest;
use App\Http\Requests\API\UpdateSMENationalityAPIRequest;
use App\Models\SMENationality;
use App\Repositories\SMENationalityRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMENationalityController
 * @package App\Http\Controllers\API
 */

class SMENationalityAPIController extends AppBaseController
{
    /** @var  SMENationalityRepository */
    private $sMENationalityRepository;

    public function __construct(SMENationalityRepository $sMENationalityRepo)
    {
        $this->sMENationalityRepository = $sMENationalityRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMENationalities",
     *      summary="Get a listing of the SMENationalities.",
     *      tags={"SMENationality"},
     *      description="Get all SMENationalities",
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
     *                  @SWG\Items(ref="#/definitions/SMENationality")
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
        $this->sMENationalityRepository->pushCriteria(new RequestCriteria($request));
        $this->sMENationalityRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMENationalities = $this->sMENationalityRepository->all();

        return $this->sendResponse($sMENationalities->toArray(), trans('custom.s_m_e_nationalities_retrieved_successfully'));
    }

    /**
     * @param CreateSMENationalityAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMENationalities",
     *      summary="Store a newly created SMENationality in storage",
     *      tags={"SMENationality"},
     *      description="Store SMENationality",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMENationality that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMENationality")
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
     *                  ref="#/definitions/SMENationality"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMENationalityAPIRequest $request)
    {
        $input = $request->all();

        $sMENationality = $this->sMENationalityRepository->create($input);

        return $this->sendResponse($sMENationality->toArray(), trans('custom.s_m_e_nationality_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMENationalities/{id}",
     *      summary="Display the specified SMENationality",
     *      tags={"SMENationality"},
     *      description="Get SMENationality",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMENationality",
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
     *                  ref="#/definitions/SMENationality"
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
        /** @var SMENationality $sMENationality */
        $sMENationality = $this->sMENationalityRepository->findWithoutFail($id);

        if (empty($sMENationality)) {
            return $this->sendError(trans('custom.s_m_e_nationality_not_found'));
        }

        return $this->sendResponse($sMENationality->toArray(), trans('custom.s_m_e_nationality_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMENationalityAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMENationalities/{id}",
     *      summary="Update the specified SMENationality in storage",
     *      tags={"SMENationality"},
     *      description="Update SMENationality",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMENationality",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMENationality that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMENationality")
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
     *                  ref="#/definitions/SMENationality"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMENationalityAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMENationality $sMENationality */
        $sMENationality = $this->sMENationalityRepository->findWithoutFail($id);

        if (empty($sMENationality)) {
            return $this->sendError(trans('custom.s_m_e_nationality_not_found'));
        }

        $sMENationality = $this->sMENationalityRepository->update($input, $id);

        return $this->sendResponse($sMENationality->toArray(), trans('custom.smenationality_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMENationalities/{id}",
     *      summary="Remove the specified SMENationality from storage",
     *      tags={"SMENationality"},
     *      description="Delete SMENationality",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMENationality",
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
        /** @var SMENationality $sMENationality */
        $sMENationality = $this->sMENationalityRepository->findWithoutFail($id);

        if (empty($sMENationality)) {
            return $this->sendError(trans('custom.s_m_e_nationality_not_found'));
        }

        $sMENationality->delete();

        return $this->sendSuccess('S M E Nationality deleted successfully');
    }
}
