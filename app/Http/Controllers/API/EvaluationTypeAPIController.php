<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvaluationTypeAPIRequest;
use App\Http\Requests\API\UpdateEvaluationTypeAPIRequest;
use App\Models\EvaluationType;
use App\Repositories\EvaluationTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EvaluationTypeController
 * @package App\Http\Controllers\API
 */

class EvaluationTypeAPIController extends AppBaseController
{
    /** @var  EvaluationTypeRepository */
    private $evaluationTypeRepository;

    public function __construct(EvaluationTypeRepository $evaluationTypeRepo)
    {
        $this->evaluationTypeRepository = $evaluationTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/evaluationTypes",
     *      summary="Get a listing of the EvaluationTypes.",
     *      tags={"EvaluationType"},
     *      description="Get all EvaluationTypes",
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
     *                  @SWG\Items(ref="#/definitions/EvaluationType")
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
        $this->evaluationTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->evaluationTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $evaluationTypes = $this->evaluationTypeRepository->all();

        return $this->sendResponse($evaluationTypes->toArray(), trans('custom.evaluation_types_retrieved_successfully'));
    }

    /**
     * @param CreateEvaluationTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/evaluationTypes",
     *      summary="Store a newly created EvaluationType in storage",
     *      tags={"EvaluationType"},
     *      description="Store EvaluationType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EvaluationType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EvaluationType")
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
     *                  ref="#/definitions/EvaluationType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEvaluationTypeAPIRequest $request)
    {
        $input = $request->all();

        $evaluationType = $this->evaluationTypeRepository->create($input);

        return $this->sendResponse($evaluationType->toArray(), trans('custom.evaluation_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/evaluationTypes/{id}",
     *      summary="Display the specified EvaluationType",
     *      tags={"EvaluationType"},
     *      description="Get EvaluationType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationType",
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
     *                  ref="#/definitions/EvaluationType"
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
        /** @var EvaluationType $evaluationType */
        $evaluationType = $this->evaluationTypeRepository->findWithoutFail($id);

        if (empty($evaluationType)) {
            return $this->sendError(trans('custom.evaluation_type_not_found'));
        }

        return $this->sendResponse($evaluationType->toArray(), trans('custom.evaluation_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEvaluationTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/evaluationTypes/{id}",
     *      summary="Update the specified EvaluationType in storage",
     *      tags={"EvaluationType"},
     *      description="Update EvaluationType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EvaluationType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EvaluationType")
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
     *                  ref="#/definitions/EvaluationType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEvaluationTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var EvaluationType $evaluationType */
        $evaluationType = $this->evaluationTypeRepository->findWithoutFail($id);

        if (empty($evaluationType)) {
            return $this->sendError(trans('custom.evaluation_type_not_found'));
        }

        $evaluationType = $this->evaluationTypeRepository->update($input, $id);

        return $this->sendResponse($evaluationType->toArray(), trans('custom.evaluationtype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/evaluationTypes/{id}",
     *      summary="Remove the specified EvaluationType from storage",
     *      tags={"EvaluationType"},
     *      description="Delete EvaluationType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationType",
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
        /** @var EvaluationType $evaluationType */
        $evaluationType = $this->evaluationTypeRepository->findWithoutFail($id);

        if (empty($evaluationType)) {
            return $this->sendError(trans('custom.evaluation_type_not_found'));
        }

        $evaluationType->delete();

        return $this->sendSuccess('Evaluation Type deleted successfully');
    }
}
