<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUnbilledGrvGroupByAPIRequest;
use App\Http\Requests\API\UpdateUnbilledGrvGroupByAPIRequest;
use App\Models\UnbilledGrvGroupBy;
use App\Repositories\UnbilledGrvGroupByRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UnbilledGrvGroupByController
 * @package App\Http\Controllers\API
 */

class UnbilledGrvGroupByAPIController extends AppBaseController
{
    /** @var  UnbilledGrvGroupByRepository */
    private $unbilledGrvGroupByRepository;

    public function __construct(UnbilledGrvGroupByRepository $unbilledGrvGroupByRepo)
    {
        $this->unbilledGrvGroupByRepository = $unbilledGrvGroupByRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/unbilledGrvGroupBies",
     *      summary="Get a listing of the UnbilledGrvGroupBies.",
     *      tags={"UnbilledGrvGroupBy"},
     *      description="Get all UnbilledGrvGroupBies",
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
     *                  @SWG\Items(ref="#/definitions/UnbilledGrvGroupBy")
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
        $this->unbilledGrvGroupByRepository->pushCriteria(new RequestCriteria($request));
        $this->unbilledGrvGroupByRepository->pushCriteria(new LimitOffsetCriteria($request));
        $unbilledGrvGroupBies = $this->unbilledGrvGroupByRepository->all();

        return $this->sendResponse($unbilledGrvGroupBies->toArray(), 'Unbilled Grv Group Bies retrieved successfully');
    }

    /**
     * @param CreateUnbilledGrvGroupByAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/unbilledGrvGroupBies",
     *      summary="Store a newly created UnbilledGrvGroupBy in storage",
     *      tags={"UnbilledGrvGroupBy"},
     *      description="Store UnbilledGrvGroupBy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UnbilledGrvGroupBy that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UnbilledGrvGroupBy")
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
     *                  ref="#/definitions/UnbilledGrvGroupBy"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUnbilledGrvGroupByAPIRequest $request)
    {
        $input = $request->all();

        $unbilledGrvGroupBies = $this->unbilledGrvGroupByRepository->create($input);

        return $this->sendResponse($unbilledGrvGroupBies->toArray(), 'Unbilled Grv Group By saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/unbilledGrvGroupBies/{id}",
     *      summary="Display the specified UnbilledGrvGroupBy",
     *      tags={"UnbilledGrvGroupBy"},
     *      description="Get UnbilledGrvGroupBy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UnbilledGrvGroupBy",
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
     *                  ref="#/definitions/UnbilledGrvGroupBy"
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
        /** @var UnbilledGrvGroupBy $unbilledGrvGroupBy */
        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->findWithoutFail($id);

        if (empty($unbilledGrvGroupBy)) {
            return $this->sendError('Unbilled Grv Group By not found');
        }

        return $this->sendResponse($unbilledGrvGroupBy->toArray(), 'Unbilled Grv Group By retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateUnbilledGrvGroupByAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/unbilledGrvGroupBies/{id}",
     *      summary="Update the specified UnbilledGrvGroupBy in storage",
     *      tags={"UnbilledGrvGroupBy"},
     *      description="Update UnbilledGrvGroupBy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UnbilledGrvGroupBy",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UnbilledGrvGroupBy that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UnbilledGrvGroupBy")
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
     *                  ref="#/definitions/UnbilledGrvGroupBy"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUnbilledGrvGroupByAPIRequest $request)
    {
        $input = $request->all();

        /** @var UnbilledGrvGroupBy $unbilledGrvGroupBy */
        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->findWithoutFail($id);

        if (empty($unbilledGrvGroupBy)) {
            return $this->sendError('Unbilled Grv Group By not found');
        }

        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->update($input, $id);

        return $this->sendResponse($unbilledGrvGroupBy->toArray(), 'UnbilledGrvGroupBy updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/unbilledGrvGroupBies/{id}",
     *      summary="Remove the specified UnbilledGrvGroupBy from storage",
     *      tags={"UnbilledGrvGroupBy"},
     *      description="Delete UnbilledGrvGroupBy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UnbilledGrvGroupBy",
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
        /** @var UnbilledGrvGroupBy $unbilledGrvGroupBy */
        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->findWithoutFail($id);

        if (empty($unbilledGrvGroupBy)) {
            return $this->sendError('Unbilled Grv Group By not found');
        }

        $unbilledGrvGroupBy->delete();

        return $this->sendResponse($id, 'Unbilled Grv Group By deleted successfully');
    }
}
