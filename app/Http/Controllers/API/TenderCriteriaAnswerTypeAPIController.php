<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderCriteriaAnswerTypeAPIRequest;
use App\Http\Requests\API\UpdateTenderCriteriaAnswerTypeAPIRequest;
use App\Models\TenderCriteriaAnswerType;
use App\Repositories\TenderCriteriaAnswerTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderCriteriaAnswerTypeController
 * @package App\Http\Controllers\API
 */

class TenderCriteriaAnswerTypeAPIController extends AppBaseController
{
    /** @var  TenderCriteriaAnswerTypeRepository */
    private $tenderCriteriaAnswerTypeRepository;

    public function __construct(TenderCriteriaAnswerTypeRepository $tenderCriteriaAnswerTypeRepo)
    {
        $this->tenderCriteriaAnswerTypeRepository = $tenderCriteriaAnswerTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderCriteriaAnswerTypes",
     *      summary="Get a listing of the TenderCriteriaAnswerTypes.",
     *      tags={"TenderCriteriaAnswerType"},
     *      description="Get all TenderCriteriaAnswerTypes",
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
     *                  @SWG\Items(ref="#/definitions/TenderCriteriaAnswerType")
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
        $this->tenderCriteriaAnswerTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderCriteriaAnswerTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderCriteriaAnswerTypes = $this->tenderCriteriaAnswerTypeRepository->all();

        return $this->sendResponse($tenderCriteriaAnswerTypes->toArray(), trans('custom.tender_criteria_answer_types_retrieved_successfull'));
    }

    /**
     * @param CreateTenderCriteriaAnswerTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderCriteriaAnswerTypes",
     *      summary="Store a newly created TenderCriteriaAnswerType in storage",
     *      tags={"TenderCriteriaAnswerType"},
     *      description="Store TenderCriteriaAnswerType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderCriteriaAnswerType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderCriteriaAnswerType")
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
     *                  ref="#/definitions/TenderCriteriaAnswerType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderCriteriaAnswerTypeAPIRequest $request)
    {
        $input = $request->all();

        $tenderCriteriaAnswerType = $this->tenderCriteriaAnswerTypeRepository->create($input);

        return $this->sendResponse($tenderCriteriaAnswerType->toArray(), trans('custom.tender_criteria_answer_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderCriteriaAnswerTypes/{id}",
     *      summary="Display the specified TenderCriteriaAnswerType",
     *      tags={"TenderCriteriaAnswerType"},
     *      description="Get TenderCriteriaAnswerType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCriteriaAnswerType",
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
     *                  ref="#/definitions/TenderCriteriaAnswerType"
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
        /** @var TenderCriteriaAnswerType $tenderCriteriaAnswerType */
        $tenderCriteriaAnswerType = $this->tenderCriteriaAnswerTypeRepository->findWithoutFail($id);

        if (empty($tenderCriteriaAnswerType)) {
            return $this->sendError(trans('custom.tender_criteria_answer_type_not_found'));
        }

        return $this->sendResponse($tenderCriteriaAnswerType->toArray(), trans('custom.tender_criteria_answer_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderCriteriaAnswerTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderCriteriaAnswerTypes/{id}",
     *      summary="Update the specified TenderCriteriaAnswerType in storage",
     *      tags={"TenderCriteriaAnswerType"},
     *      description="Update TenderCriteriaAnswerType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCriteriaAnswerType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderCriteriaAnswerType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderCriteriaAnswerType")
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
     *                  ref="#/definitions/TenderCriteriaAnswerType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderCriteriaAnswerTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderCriteriaAnswerType $tenderCriteriaAnswerType */
        $tenderCriteriaAnswerType = $this->tenderCriteriaAnswerTypeRepository->findWithoutFail($id);

        if (empty($tenderCriteriaAnswerType)) {
            return $this->sendError(trans('custom.tender_criteria_answer_type_not_found'));
        }

        $tenderCriteriaAnswerType = $this->tenderCriteriaAnswerTypeRepository->update($input, $id);

        return $this->sendResponse($tenderCriteriaAnswerType->toArray(), trans('custom.tendercriteriaanswertype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderCriteriaAnswerTypes/{id}",
     *      summary="Remove the specified TenderCriteriaAnswerType from storage",
     *      tags={"TenderCriteriaAnswerType"},
     *      description="Delete TenderCriteriaAnswerType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCriteriaAnswerType",
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
        /** @var TenderCriteriaAnswerType $tenderCriteriaAnswerType */
        $tenderCriteriaAnswerType = $this->tenderCriteriaAnswerTypeRepository->findWithoutFail($id);

        if (empty($tenderCriteriaAnswerType)) {
            return $this->sendError(trans('custom.tender_criteria_answer_type_not_found'));
        }

        $tenderCriteriaAnswerType->delete();

        return $this->sendSuccess('Tender Criteria Answer Type deleted successfully');
    }
}
