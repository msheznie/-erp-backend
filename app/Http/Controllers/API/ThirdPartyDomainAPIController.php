<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateThirdPartyDomainAPIRequest;
use App\Http\Requests\API\UpdateThirdPartyDomainAPIRequest;
use App\Models\ThirdPartyDomain;
use App\Repositories\ThirdPartyDomainRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ThirdPartyDomainController
 * @package App\Http\Controllers\API
 */

class ThirdPartyDomainAPIController extends AppBaseController
{
    /** @var  ThirdPartyDomainRepository */
    private $thirdPartyDomainRepository;

    public function __construct(ThirdPartyDomainRepository $thirdPartyDomainRepo)
    {
        $this->thirdPartyDomainRepository = $thirdPartyDomainRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/thirdPartyDomains",
     *      summary="getThirdPartyDomainList",
     *      tags={"ThirdPartyDomain"},
     *      description="Get all ThirdPartyDomains",
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
     *                  @OA\Items(ref="#/definitions/ThirdPartyDomain")
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
        $this->thirdPartyDomainRepository->pushCriteria(new RequestCriteria($request));
        $this->thirdPartyDomainRepository->pushCriteria(new LimitOffsetCriteria($request));
        $thirdPartyDomains = $this->thirdPartyDomainRepository->all();

        return $this->sendResponse($thirdPartyDomains->toArray(), 'Third Party Domains retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/thirdPartyDomains",
     *      summary="createThirdPartyDomain",
     *      tags={"ThirdPartyDomain"},
     *      description="Create ThirdPartyDomain",
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
     *                  ref="#/definitions/ThirdPartyDomain"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateThirdPartyDomainAPIRequest $request)
    {
        $input = $request->all();

        $thirdPartyDomain = $this->thirdPartyDomainRepository->create($input);

        return $this->sendResponse($thirdPartyDomain->toArray(), 'Third Party Domain saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/thirdPartyDomains/{id}",
     *      summary="getThirdPartyDomainItem",
     *      tags={"ThirdPartyDomain"},
     *      description="Get ThirdPartyDomain",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ThirdPartyDomain",
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
     *                  ref="#/definitions/ThirdPartyDomain"
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
        /** @var ThirdPartyDomain $thirdPartyDomain */
        $thirdPartyDomain = $this->thirdPartyDomainRepository->findWithoutFail($id);

        if (empty($thirdPartyDomain)) {
            return $this->sendError('Third Party Domain not found');
        }

        return $this->sendResponse($thirdPartyDomain->toArray(), 'Third Party Domain retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/thirdPartyDomains/{id}",
     *      summary="updateThirdPartyDomain",
     *      tags={"ThirdPartyDomain"},
     *      description="Update ThirdPartyDomain",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ThirdPartyDomain",
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
     *                  ref="#/definitions/ThirdPartyDomain"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateThirdPartyDomainAPIRequest $request)
    {
        $input = $request->all();

        /** @var ThirdPartyDomain $thirdPartyDomain */
        $thirdPartyDomain = $this->thirdPartyDomainRepository->findWithoutFail($id);

        if (empty($thirdPartyDomain)) {
            return $this->sendError('Third Party Domain not found');
        }

        $thirdPartyDomain = $this->thirdPartyDomainRepository->update($input, $id);

        return $this->sendResponse($thirdPartyDomain->toArray(), 'ThirdPartyDomain updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/thirdPartyDomains/{id}",
     *      summary="deleteThirdPartyDomain",
     *      tags={"ThirdPartyDomain"},
     *      description="Delete ThirdPartyDomain",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ThirdPartyDomain",
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
        /** @var ThirdPartyDomain $thirdPartyDomain */
        $thirdPartyDomain = $this->thirdPartyDomainRepository->findWithoutFail($id);

        if (empty($thirdPartyDomain)) {
            return $this->sendError('Third Party Domain not found');
        }

        $thirdPartyDomain->delete();

        return $this->sendSuccess('Third Party Domain deleted successfully');
    }
}
