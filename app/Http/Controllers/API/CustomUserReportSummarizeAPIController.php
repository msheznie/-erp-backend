<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomUserReportSummarizeAPIRequest;
use App\Http\Requests\API\UpdateCustomUserReportSummarizeAPIRequest;
use App\Models\CustomUserReportSummarize;
use App\Repositories\CustomUserReportSummarizeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomUserReportSummarizeController
 * @package App\Http\Controllers\API
 */

class CustomUserReportSummarizeAPIController extends AppBaseController
{
    /** @var  CustomUserReportSummarizeRepository */
    private $customUserReportSummarizeRepository;

    public function __construct(CustomUserReportSummarizeRepository $customUserReportSummarizeRepo)
    {
        $this->customUserReportSummarizeRepository = $customUserReportSummarizeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customUserReportSummarizes",
     *      summary="Get a listing of the CustomUserReportSummarizes.",
     *      tags={"CustomUserReportSummarize"},
     *      description="Get all CustomUserReportSummarizes",
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
     *                  @SWG\Items(ref="#/definitions/CustomUserReportSummarize")
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
        $this->customUserReportSummarizeRepository->pushCriteria(new RequestCriteria($request));
        $this->customUserReportSummarizeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customUserReportSummarizes = $this->customUserReportSummarizeRepository->all();

        return $this->sendResponse($customUserReportSummarizes->toArray(), 'Custom User Report Summarizes retrieved successfully');
    }

    /**
     * @param CreateCustomUserReportSummarizeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customUserReportSummarizes",
     *      summary="Store a newly created CustomUserReportSummarize in storage",
     *      tags={"CustomUserReportSummarize"},
     *      description="Store CustomUserReportSummarize",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomUserReportSummarize that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomUserReportSummarize")
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
     *                  ref="#/definitions/CustomUserReportSummarize"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomUserReportSummarizeAPIRequest $request)
    {
        $input = $request->all();

        $customUserReportSummarize = $this->customUserReportSummarizeRepository->create($input);

        return $this->sendResponse($customUserReportSummarize->toArray(), 'Custom User Report Summarize saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customUserReportSummarizes/{id}",
     *      summary="Display the specified CustomUserReportSummarize",
     *      tags={"CustomUserReportSummarize"},
     *      description="Get CustomUserReportSummarize",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomUserReportSummarize",
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
     *                  ref="#/definitions/CustomUserReportSummarize"
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
        /** @var CustomUserReportSummarize $customUserReportSummarize */
        $customUserReportSummarize = $this->customUserReportSummarizeRepository->findWithoutFail($id);

        if (empty($customUserReportSummarize)) {
            return $this->sendError('Custom User Report Summarize not found');
        }

        return $this->sendResponse($customUserReportSummarize->toArray(), 'Custom User Report Summarize retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomUserReportSummarizeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customUserReportSummarizes/{id}",
     *      summary="Update the specified CustomUserReportSummarize in storage",
     *      tags={"CustomUserReportSummarize"},
     *      description="Update CustomUserReportSummarize",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomUserReportSummarize",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomUserReportSummarize that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomUserReportSummarize")
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
     *                  ref="#/definitions/CustomUserReportSummarize"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomUserReportSummarizeAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomUserReportSummarize $customUserReportSummarize */
        $customUserReportSummarize = $this->customUserReportSummarizeRepository->findWithoutFail($id);

        if (empty($customUserReportSummarize)) {
            return $this->sendError('Custom User Report Summarize not found');
        }

        $customUserReportSummarize = $this->customUserReportSummarizeRepository->update($input, $id);

        return $this->sendResponse($customUserReportSummarize->toArray(), 'CustomUserReportSummarize updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customUserReportSummarizes/{id}",
     *      summary="Remove the specified CustomUserReportSummarize from storage",
     *      tags={"CustomUserReportSummarize"},
     *      description="Delete CustomUserReportSummarize",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomUserReportSummarize",
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
        /** @var CustomUserReportSummarize $customUserReportSummarize */
        $customUserReportSummarize = $this->customUserReportSummarizeRepository->findWithoutFail($id);

        if (empty($customUserReportSummarize)) {
            return $this->sendError('Custom User Report Summarize not found');
        }

        $customUserReportSummarize->delete();

        return $this->sendSuccess('Custom User Report Summarize deleted successfully');
    }
}
