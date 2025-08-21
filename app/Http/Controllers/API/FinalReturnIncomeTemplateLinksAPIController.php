<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinalReturnIncomeTemplateLinksAPIRequest;
use App\Http\Requests\API\UpdateFinalReturnIncomeTemplateLinksAPIRequest;
use App\Models\FinalReturnIncomeTemplateLinks;
use App\Repositories\FinalReturnIncomeTemplateLinksRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FinalReturnIncomeTemplateLinksController
 * @package App\Http\Controllers\API
 */

class FinalReturnIncomeTemplateLinksAPIController extends AppBaseController
{
    /** @var  FinalReturnIncomeTemplateLinksRepository */
    private $finalReturnIncomeTemplateLinksRepository;

    public function __construct(FinalReturnIncomeTemplateLinksRepository $finalReturnIncomeTemplateLinksRepo)
    {
        $this->finalReturnIncomeTemplateLinksRepository = $finalReturnIncomeTemplateLinksRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeTemplateLinks",
     *      summary="getFinalReturnIncomeTemplateLinksList",
     *      tags={"FinalReturnIncomeTemplateLinks"},
     *      description="Get all FinalReturnIncomeTemplateLinks",
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
     *                  @OA\Items(ref="#/definitions/FinalReturnIncomeTemplateLinks")
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
        $this->finalReturnIncomeTemplateLinksRepository->pushCriteria(new RequestCriteria($request));
        $this->finalReturnIncomeTemplateLinksRepository->pushCriteria(new LimitOffsetCriteria($request));
        $finalReturnIncomeTemplateLinks = $this->finalReturnIncomeTemplateLinksRepository->all();

        return $this->sendResponse($finalReturnIncomeTemplateLinks->toArray(), 'Final Return Income Template Links retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/finalReturnIncomeTemplateLinks",
     *      summary="createFinalReturnIncomeTemplateLinks",
     *      tags={"FinalReturnIncomeTemplateLinks"},
     *      description="Create FinalReturnIncomeTemplateLinks",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateLinks"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($request->all(), [
            'glAutoID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        foreach ($input['glAutoID'] as $key => $val) {
            $data['templateMasterID'] = $input['templateMasterID'];
            $data['templateDetailID'] = $input['templateDetailID'];
            $data['sortOrder'] = $key + 1;
            $data['glAutoID'] = $val['chartOfAccountSystemID'];
            $data['glCode'] = $val['AccountCode'];
            $data['glDescription'] = $val['AccountDescription'];
            $data['companySystemID'] = $input['companySystemID'];
            $data['createdPCID'] = gethostname();
            $data['createdUserID'] = \Helper::getEmployeeID();
            $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $data['createdDateTime'] = now();
            $finalReturnIncomeTemplateLinks = $this->finalReturnIncomeTemplateLinksRepository->create($data); 
        }

        $lastSortOrder = FinalReturnIncomeTemplateLinks::ofTemplate($input['templateMasterID'])
                ->where('templateDetailID',$input['templateDetailID'])
                ->orderBy('id','asc')->get();

        if(count($lastSortOrder) > 0){
            foreach ($lastSortOrder as $key => $val) {
                $data2['sortOrder'] = $key + 1;
                $this->finalReturnIncomeTemplateLinksRepository->update($data2, $val->id);
            }
        }

        return $this->sendResponse($finalReturnIncomeTemplateLinks->toArray(), 'Final Return Income Template Links saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeTemplateLinks/{id}",
     *      summary="getFinalReturnIncomeTemplateLinksItem",
     *      tags={"FinalReturnIncomeTemplateLinks"},
     *      description="Get FinalReturnIncomeTemplateLinks",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateLinks",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateLinks"
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
        /** @var FinalReturnIncomeTemplateLinks $finalReturnIncomeTemplateLinks */
        $finalReturnIncomeTemplateLinks = $this->finalReturnIncomeTemplateLinksRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplateLinks)) {
            return $this->sendError('Final Return Income Template Links not found');
        }

        return $this->sendResponse($finalReturnIncomeTemplateLinks->toArray(), 'Final Return Income Template Links retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/finalReturnIncomeTemplateLinks/{id}",
     *      summary="updateFinalReturnIncomeTemplateLinks",
     *      tags={"FinalReturnIncomeTemplateLinks"},
     *      description="Update FinalReturnIncomeTemplateLinks",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateLinks",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateLinks"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinalReturnIncomeTemplateLinksAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinalReturnIncomeTemplateLinks $finalReturnIncomeTemplateLinks */
        $finalReturnIncomeTemplateLinks = $this->finalReturnIncomeTemplateLinksRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplateLinks)) {
            return $this->sendError('Final Return Income Template Links not found');
        }

        $finalReturnIncomeTemplateLinks = $this->finalReturnIncomeTemplateLinksRepository->update($input, $id);

        return $this->sendResponse($finalReturnIncomeTemplateLinks->toArray(), 'FinalReturnIncomeTemplateLinks updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/finalReturnIncomeTemplateLinks/{id}",
     *      summary="deleteFinalReturnIncomeTemplateLinks",
     *      tags={"FinalReturnIncomeTemplateLinks"},
     *      description="Delete FinalReturnIncomeTemplateLinks",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateLinks",
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
        /** @var FinalReturnIncomeTemplateLinks $finalReturnIncomeTemplateLinks */
        $finalReturnIncomeTemplateLinks = $this->finalReturnIncomeTemplateLinksRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplateLinks)) {
            return $this->sendError('Final Return Income Template Links not found');
        }

        $finalReturnIncomeTemplateLinks->delete();

        return $this->sendSuccess('Final Return Income Template Links deleted successfully');
    }
}
