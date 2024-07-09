<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTemplateSectionTableRowAPIRequest;
use App\Http\Requests\API\UpdateTemplateSectionTableRowAPIRequest;
use App\Models\TemplateSectionTableRow;
use App\Repositories\TemplateSectionTableRowRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TemplateSectionTableRowController
 * @package App\Http\Controllers\API
 */

class TemplateSectionTableRowAPIController extends AppBaseController
{
    /** @var  TemplateSectionTableRowRepository */
    private $templateSectionTableRowRepository;

    public function __construct(TemplateSectionTableRowRepository $templateSectionTableRowRepo)
    {
        $this->templateSectionTableRowRepository = $templateSectionTableRowRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/templateSectionTableRows",
     *      summary="getTemplateSectionTableRowList",
     *      tags={"TemplateSectionTableRow"},
     *      description="Get all TemplateSectionTableRows",
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
     *                  @OA\Items(ref="#/definitions/TemplateSectionTableRow")
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
        $this->templateSectionTableRowRepository->pushCriteria(new RequestCriteria($request));
        $this->templateSectionTableRowRepository->pushCriteria(new LimitOffsetCriteria($request));
        $templateSectionTableRows = $this->templateSectionTableRowRepository->all();

        return $this->sendResponse($templateSectionTableRows->toArray(), 'Template Section Table Rows retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/templateSectionTableRows",
     *      summary="createTemplateSectionTableRow",
     *      tags={"TemplateSectionTableRow"},
     *      description="Create TemplateSectionTableRow",
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
     *                  ref="#/definitions/TemplateSectionTableRow"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTemplateSectionTableRowAPIRequest $request)
    {
        $input = $request->all();

        $templateSectionTableRow = $this->templateSectionTableRowRepository->create($input);

        return $this->sendResponse($templateSectionTableRow->toArray(), 'Template Section Table Row saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/templateSectionTableRows/{id}",
     *      summary="getTemplateSectionTableRowItem",
     *      tags={"TemplateSectionTableRow"},
     *      description="Get TemplateSectionTableRow",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TemplateSectionTableRow",
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
     *                  ref="#/definitions/TemplateSectionTableRow"
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
        /** @var TemplateSectionTableRow $templateSectionTableRow */
        $templateSectionTableRow = $this->templateSectionTableRowRepository->findWithoutFail($id);

        if (empty($templateSectionTableRow)) {
            return $this->sendError('Template Section Table Row not found');
        }

        return $this->sendResponse($templateSectionTableRow->toArray(), 'Template Section Table Row retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/templateSectionTableRows/{id}",
     *      summary="updateTemplateSectionTableRow",
     *      tags={"TemplateSectionTableRow"},
     *      description="Update TemplateSectionTableRow",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TemplateSectionTableRow",
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
     *                  ref="#/definitions/TemplateSectionTableRow"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTemplateSectionTableRowAPIRequest $request)
    {
        $input = $request->all();

        /** @var TemplateSectionTableRow $templateSectionTableRow */
        $templateSectionTableRow = $this->templateSectionTableRowRepository->findWithoutFail($id);

        if (empty($templateSectionTableRow)) {
            return $this->sendError('Template Section Table Row not found');
        }

        $templateSectionTableRow = $this->templateSectionTableRowRepository->update($input, $id);

        return $this->sendResponse($templateSectionTableRow->toArray(), 'TemplateSectionTableRow updated successfully');
    }

    public function updateRow(Request $request)  {
        $input = $request->all();
        
        // Retrieve rowData from the input
        $rowData = $input['rowData'];

        // Convert the rowData array to a JSON string
        $rowDataJson = json_encode($rowData);


    // Save the JSON string to the database
        $row = TemplateSectionTableRow::find($input['id']);
        if ($row) {
            $row->rowData = $rowDataJson; 
            $row->save();
        }

        return $this->sendResponse($input, 'Row column updated successfully');

    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/templateSectionTableRows/{id}",
     *      summary="deleteTemplateSectionTableRow",
     *      tags={"TemplateSectionTableRow"},
     *      description="Delete TemplateSectionTableRow",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TemplateSectionTableRow",
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
        /** @var TemplateSectionTableRow $templateSectionTableRow */
        $templateSectionTableRow = $this->templateSectionTableRowRepository->findWithoutFail($id);

        if (empty($templateSectionTableRow)) {
            return $this->sendError('Template Section Table Row not found');
        }

        $templateSectionTableRow->delete();

        return $this->sendSuccess('Template Section Table Row deleted successfully');
    }
}
