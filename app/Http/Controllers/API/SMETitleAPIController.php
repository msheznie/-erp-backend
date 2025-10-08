<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMETitleAPIRequest;
use App\Http\Requests\API\UpdateSMETitleAPIRequest;
use App\Models\SMETitle;
use App\Repositories\SMETitleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMETitleController
 * @package App\Http\Controllers\API
 */

class SMETitleAPIController extends AppBaseController
{
    /** @var  SMETitleRepository */
    private $sMETitleRepository;

    public function __construct(SMETitleRepository $sMETitleRepo)
    {
        $this->sMETitleRepository = $sMETitleRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMETitles",
     *      summary="Get a listing of the SMETitles.",
     *      tags={"SMETitle"},
     *      description="Get all SMETitles",
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
     *                  @SWG\Items(ref="#/definitions/SMETitle")
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
        $this->sMETitleRepository->pushCriteria(new RequestCriteria($request));
        $this->sMETitleRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMETitles = $this->sMETitleRepository->all();

        return $this->sendResponse($sMETitles->toArray(), trans('custom.s_m_e_titles_retrieved_successfully'));
    }

    /**
     * @param CreateSMETitleAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMETitles",
     *      summary="Store a newly created SMETitle in storage",
     *      tags={"SMETitle"},
     *      description="Store SMETitle",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMETitle that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMETitle")
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
     *                  ref="#/definitions/SMETitle"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMETitleAPIRequest $request)
    {
        $input = $request->all();

        $sMETitle = $this->sMETitleRepository->create($input);

        return $this->sendResponse($sMETitle->toArray(), trans('custom.s_m_e_title_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMETitles/{id}",
     *      summary="Display the specified SMETitle",
     *      tags={"SMETitle"},
     *      description="Get SMETitle",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMETitle",
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
     *                  ref="#/definitions/SMETitle"
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
        /** @var SMETitle $sMETitle */
        $sMETitle = $this->sMETitleRepository->findWithoutFail($id);

        if (empty($sMETitle)) {
            return $this->sendError(trans('custom.s_m_e_title_not_found'));
        }

        return $this->sendResponse($sMETitle->toArray(), trans('custom.s_m_e_title_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMETitleAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMETitles/{id}",
     *      summary="Update the specified SMETitle in storage",
     *      tags={"SMETitle"},
     *      description="Update SMETitle",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMETitle",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMETitle that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMETitle")
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
     *                  ref="#/definitions/SMETitle"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMETitleAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMETitle $sMETitle */
        $sMETitle = $this->sMETitleRepository->findWithoutFail($id);

        if (empty($sMETitle)) {
            return $this->sendError(trans('custom.s_m_e_title_not_found'));
        }

        $sMETitle = $this->sMETitleRepository->update($input, $id);

        return $this->sendResponse($sMETitle->toArray(), trans('custom.smetitle_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMETitles/{id}",
     *      summary="Remove the specified SMETitle from storage",
     *      tags={"SMETitle"},
     *      description="Delete SMETitle",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMETitle",
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
        /** @var SMETitle $sMETitle */
        $sMETitle = $this->sMETitleRepository->findWithoutFail($id);

        if (empty($sMETitle)) {
            return $this->sendError(trans('custom.s_m_e_title_not_found'));
        }

        $sMETitle->delete();

        return $this->sendSuccess('S M E Title deleted successfully');
    }
}
