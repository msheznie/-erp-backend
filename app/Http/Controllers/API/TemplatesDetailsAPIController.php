<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTemplatesDetailsAPIRequest;
use App\Http\Requests\API\UpdateTemplatesDetailsAPIRequest;
use App\Models\TemplatesDetails;
use App\Repositories\TemplatesDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TemplatesDetailsController
 * @package App\Http\Controllers\API
 */

class TemplatesDetailsAPIController extends AppBaseController
{
    /** @var  TemplatesDetailsRepository */
    private $templatesDetailsRepository;

    public function __construct(TemplatesDetailsRepository $templatesDetailsRepo)
    {
        $this->templatesDetailsRepository = $templatesDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/templatesDetails",
     *      summary="Get a listing of the TemplatesDetails.",
     *      tags={"TemplatesDetails"},
     *      description="Get all TemplatesDetails",
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
     *                  @SWG\Items(ref="#/definitions/TemplatesDetails")
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
        $this->templatesDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->templatesDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $templatesDetails = $this->templatesDetailsRepository->all();

        return $this->sendResponse($templatesDetails->toArray(), 'Templates Details retrieved successfully');
    }

    /**
     * @param CreateTemplatesDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/templatesDetails",
     *      summary="Store a newly created TemplatesDetails in storage",
     *      tags={"TemplatesDetails"},
     *      description="Store TemplatesDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TemplatesDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TemplatesDetails")
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
     *                  ref="#/definitions/TemplatesDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTemplatesDetailsAPIRequest $request)
    {
        $input = $request->all();

        $templatesDetails = $this->templatesDetailsRepository->create($input);

        return $this->sendResponse($templatesDetails->toArray(), 'Templates Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/templatesDetails/{id}",
     *      summary="Display the specified TemplatesDetails",
     *      tags={"TemplatesDetails"},
     *      description="Get TemplatesDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesDetails",
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
     *                  ref="#/definitions/TemplatesDetails"
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
        /** @var TemplatesDetails $templatesDetails */
        $templatesDetails = $this->templatesDetailsRepository->findWithoutFail($id);

        if (empty($templatesDetails)) {
            return $this->sendError('Templates Details not found');
        }

        return $this->sendResponse($templatesDetails->toArray(), 'Templates Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTemplatesDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/templatesDetails/{id}",
     *      summary="Update the specified TemplatesDetails in storage",
     *      tags={"TemplatesDetails"},
     *      description="Update TemplatesDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TemplatesDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TemplatesDetails")
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
     *                  ref="#/definitions/TemplatesDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTemplatesDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var TemplatesDetails $templatesDetails */
        $templatesDetails = $this->templatesDetailsRepository->findWithoutFail($id);

        if (empty($templatesDetails)) {
            return $this->sendError('Templates Details not found');
        }

        $templatesDetails = $this->templatesDetailsRepository->update($input, $id);

        return $this->sendResponse($templatesDetails->toArray(), 'TemplatesDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/templatesDetails/{id}",
     *      summary="Remove the specified TemplatesDetails from storage",
     *      tags={"TemplatesDetails"},
     *      description="Delete TemplatesDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesDetails",
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
        /** @var TemplatesDetails $templatesDetails */
        $templatesDetails = $this->templatesDetailsRepository->findWithoutFail($id);

        if (empty($templatesDetails)) {
            return $this->sendError('Templates Details not found');
        }

        $templatesDetails->delete();

        return $this->sendResponse($id, 'Templates Details deleted successfully');
    }
}
