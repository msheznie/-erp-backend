<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateThirdPartySystemsAPIRequest;
use App\Http\Requests\API\UpdateThirdPartySystemsAPIRequest;
use App\Models\ThirdPartySystems;
use App\Repositories\ThirdPartySystemsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ThirdPartySystemsController
 * @package App\Http\Controllers\API
 */

class ThirdPartySystemsAPIController extends AppBaseController
{
    /** @var  ThirdPartySystemsRepository */
    private $thirdPartySystemsRepository;

    public function __construct(ThirdPartySystemsRepository $thirdPartySystemsRepo)
    {
        $this->thirdPartySystemsRepository = $thirdPartySystemsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/thirdPartySystems",
     *      summary="Get a listing of the ThirdPartySystems.",
     *      tags={"ThirdPartySystems"},
     *      description="Get all ThirdPartySystems",
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
     *                  @SWG\Items(ref="#/definitions/ThirdPartySystems")
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
        $this->thirdPartySystemsRepository->pushCriteria(new RequestCriteria($request));
        $this->thirdPartySystemsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $thirdPartySystems = $this->thirdPartySystemsRepository->all();

        return $this->sendResponse($thirdPartySystems->toArray(), trans('custom.third_party_systems_retrieved_successfully'));
    }

    /**
     * @param CreateThirdPartySystemsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/thirdPartySystems",
     *      summary="Store a newly created ThirdPartySystems in storage",
     *      tags={"ThirdPartySystems"},
     *      description="Store ThirdPartySystems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ThirdPartySystems that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ThirdPartySystems")
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
     *                  ref="#/definitions/ThirdPartySystems"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateThirdPartySystemsAPIRequest $request)
    {
        $input = $request->all();

        $thirdPartySystems = $this->thirdPartySystemsRepository->create($input);

        return $this->sendResponse($thirdPartySystems->toArray(), trans('custom.third_party_systems_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/thirdPartySystems/{id}",
     *      summary="Display the specified ThirdPartySystems",
     *      tags={"ThirdPartySystems"},
     *      description="Get ThirdPartySystems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ThirdPartySystems",
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
     *                  ref="#/definitions/ThirdPartySystems"
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
        /** @var ThirdPartySystems $thirdPartySystems */
        $thirdPartySystems = $this->thirdPartySystemsRepository->findWithoutFail($id);

        if (empty($thirdPartySystems)) {
            return $this->sendError(trans('custom.third_party_systems_not_found'));
        }

        return $this->sendResponse($thirdPartySystems->toArray(), trans('custom.third_party_systems_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateThirdPartySystemsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/thirdPartySystems/{id}",
     *      summary="Update the specified ThirdPartySystems in storage",
     *      tags={"ThirdPartySystems"},
     *      description="Update ThirdPartySystems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ThirdPartySystems",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ThirdPartySystems that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ThirdPartySystems")
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
     *                  ref="#/definitions/ThirdPartySystems"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateThirdPartySystemsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ThirdPartySystems $thirdPartySystems */
        $thirdPartySystems = $this->thirdPartySystemsRepository->findWithoutFail($id);

        if (empty($thirdPartySystems)) {
            return $this->sendError(trans('custom.third_party_systems_not_found'));
        }

        $thirdPartySystems = $this->thirdPartySystemsRepository->update($input, $id);

        return $this->sendResponse($thirdPartySystems->toArray(), trans('custom.thirdpartysystems_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/thirdPartySystems/{id}",
     *      summary="Remove the specified ThirdPartySystems from storage",
     *      tags={"ThirdPartySystems"},
     *      description="Delete ThirdPartySystems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ThirdPartySystems",
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
        /** @var ThirdPartySystems $thirdPartySystems */
        $thirdPartySystems = $this->thirdPartySystemsRepository->findWithoutFail($id);

        if (empty($thirdPartySystems)) {
            return $this->sendError(trans('custom.third_party_systems_not_found'));
        }

        $thirdPartySystems->delete();

        return $this->sendSuccess('Third Party Systems deleted successfully');
    }
}
