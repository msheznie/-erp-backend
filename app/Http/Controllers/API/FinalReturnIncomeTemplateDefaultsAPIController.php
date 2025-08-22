<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinalReturnIncomeTemplateDefaultsAPIRequest;
use App\Http\Requests\API\UpdateFinalReturnIncomeTemplateDefaultsAPIRequest;
use App\Models\FinalReturnIncomeTemplateDefaults;
use App\Repositories\FinalReturnIncomeTemplateDefaultsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\FinalReturnIncomeTemplateDetails;
use App\Models\FinalReturnIncomeTemplateLinks;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FinalReturnIncomeTemplateDefaultsController
 * @package App\Http\Controllers\API
 */

class FinalReturnIncomeTemplateDefaultsAPIController extends AppBaseController
{
    /** @var  FinalReturnIncomeTemplateDefaultsRepository */
    private $finalReturnIncomeTemplateDefaultsRepository;

    public function __construct(FinalReturnIncomeTemplateDefaultsRepository $finalReturnIncomeTemplateDefaultsRepo)
    {
        $this->finalReturnIncomeTemplateDefaultsRepository = $finalReturnIncomeTemplateDefaultsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeTemplateDefaults",
     *      summary="getFinalReturnIncomeTemplateDefaultsList",
     *      tags={"FinalReturnIncomeTemplateDefaults"},
     *      description="Get all FinalReturnIncomeTemplateDefaults",
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
     *                  @OA\Items(ref="#/definitions/FinalReturnIncomeTemplateDefaults")
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
        $input = $request->all();
        
        $this->finalReturnIncomeTemplateDefaultsRepository->pushCriteria(new RequestCriteria($request));
        $this->finalReturnIncomeTemplateDefaultsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $details = FinalReturnIncomeTemplateDetails::where('id', $input['id'])->first();
        $usedLinks = FinalReturnIncomeTemplateLinks::where('templateDetailID', $input['id'])
            ->where('templateMasterID', $details->templateMasterID)
            ->where('companySystemID', $details->companySystemID)
            ->pluck('rawId');
        $usedRaws = FinalReturnIncomeTemplateDetails::where('masterID', $input['id'])
            ->where('templateMasterID', $details->templateMasterID)
             ->where('companySystemID', $details->companySystemID)
            ->pluck('rawId');

        $usedRaws->push(1);
        $usedRaws = collect($usedRaws)->merge($usedLinks)->unique()->toArray();

          $this->finalReturnIncomeTemplateDefaultsRepository->scopeQuery(function($query) use ($input, $usedRaws) {
            if (isset($input['type']) && isset($input['itemType']) 
                && $input['type'] == 3 && $input['itemType'] == 3) {
                
                return $query->whereIn('type', [1, 2])
                            ->where('sectionType', 1)
                            ->whereNotIn('id', $usedRaws);
            }

            if (isset($input['rawIdType']) && isset($input['itemType']) && isset($input['type'])
                && $input['rawIdType'] == 2 && $input['itemType'] == 3 && $input['type'] == 2) {

                return $query->whereIn('type', [2])
                            ->where('sectionType', 1)
                            ->whereNotIn('id', $usedRaws);
            }

            if (isset($input['rawIdType']) && isset($input['itemType']) && isset($input['type'])
                && $input['rawIdType'] == 2 && $input['itemType'] == 3 && $input['type'] == 1) {

                return $query->whereIn('type', [1])
                            ->where('sectionType', 1)
                            ->whereNotIn('id', $usedRaws);
            }

            if (isset($input['type'])) {
                return $query->where('type', $input['type'])->whereNotIn('id', $usedRaws);
            }

            return $query;
        });

        $finalReturnIncomeTemplateDefaults = $this->finalReturnIncomeTemplateDefaultsRepository->all();

        return $this->sendResponse($finalReturnIncomeTemplateDefaults->toArray(), 'Final Return Income Template Defaults retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/finalReturnIncomeTemplateDefaults",
     *      summary="createFinalReturnIncomeTemplateDefaults",
     *      tags={"FinalReturnIncomeTemplateDefaults"},
     *      description="Create FinalReturnIncomeTemplateDefaults",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateDefaults"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFinalReturnIncomeTemplateDefaultsAPIRequest $request)
    {
        $input = $request->all();

        $finalReturnIncomeTemplateDefaults = $this->finalReturnIncomeTemplateDefaultsRepository->create($input);

        return $this->sendResponse($finalReturnIncomeTemplateDefaults->toArray(), 'Final Return Income Template Defaults saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeTemplateDefaults/{id}",
     *      summary="getFinalReturnIncomeTemplateDefaultsItem",
     *      tags={"FinalReturnIncomeTemplateDefaults"},
     *      description="Get FinalReturnIncomeTemplateDefaults",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateDefaults",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateDefaults"
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
        /** @var FinalReturnIncomeTemplateDefaults $finalReturnIncomeTemplateDefaults */
        $finalReturnIncomeTemplateDefaults = $this->finalReturnIncomeTemplateDefaultsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplateDefaults)) {
            return $this->sendError('Final Return Income Template Defaults not found');
        }

        return $this->sendResponse($finalReturnIncomeTemplateDefaults->toArray(), 'Final Return Income Template Defaults retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/finalReturnIncomeTemplateDefaults/{id}",
     *      summary="updateFinalReturnIncomeTemplateDefaults",
     *      tags={"FinalReturnIncomeTemplateDefaults"},
     *      description="Update FinalReturnIncomeTemplateDefaults",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateDefaults",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateDefaults"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinalReturnIncomeTemplateDefaultsAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinalReturnIncomeTemplateDefaults $finalReturnIncomeTemplateDefaults */
        $finalReturnIncomeTemplateDefaults = $this->finalReturnIncomeTemplateDefaultsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplateDefaults)) {
            return $this->sendError('Final Return Income Template Defaults not found');
        }

        $finalReturnIncomeTemplateDefaults = $this->finalReturnIncomeTemplateDefaultsRepository->update($input, $id);

        return $this->sendResponse($finalReturnIncomeTemplateDefaults->toArray(), 'FinalReturnIncomeTemplateDefaults updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/finalReturnIncomeTemplateDefaults/{id}",
     *      summary="deleteFinalReturnIncomeTemplateDefaults",
     *      tags={"FinalReturnIncomeTemplateDefaults"},
     *      description="Delete FinalReturnIncomeTemplateDefaults",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateDefaults",
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
        /** @var FinalReturnIncomeTemplateDefaults $finalReturnIncomeTemplateDefaults */
        $finalReturnIncomeTemplateDefaults = $this->finalReturnIncomeTemplateDefaultsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplateDefaults)) {
            return $this->sendError('Final Return Income Template Defaults not found');
        }

        $finalReturnIncomeTemplateDefaults->delete();

        return $this->sendSuccess('Final Return Income Template Defaults deleted successfully');
    }
}
