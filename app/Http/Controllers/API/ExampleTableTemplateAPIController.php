<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExampleTableTemplateAPIRequest;
use App\Http\Requests\API\UpdateExampleTableTemplateAPIRequest;
use App\Models\ExampleTableTemplate;
use App\Repositories\ExampleTableTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExampleTableTemplateController
 * @package App\Http\Controllers\API
 */

class ExampleTableTemplateAPIController extends AppBaseController
{
    /** @var  ExampleTableTemplateRepository */
    private $exampleTableTemplateRepository;

    public function __construct(ExampleTableTemplateRepository $exampleTableTemplateRepo)
    {
        $this->exampleTableTemplateRepository = $exampleTableTemplateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/exampleTableTemplates",
     *      summary="Get a listing of the ExampleTableTemplates.",
     *      tags={"ExampleTableTemplate"},
     *      description="Get all ExampleTableTemplates",
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
     *                  @SWG\Items(ref="#/definitions/ExampleTableTemplate")
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
        $this->exampleTableTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->exampleTableTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $exampleTableTemplates = $this->exampleTableTemplateRepository->all();

        return $this->sendResponse($exampleTableTemplates->toArray(), 'Example Table Templates retrieved successfully');
    }

    /**
     * @param CreateExampleTableTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/exampleTableTemplates",
     *      summary="Store a newly created ExampleTableTemplate in storage",
     *      tags={"ExampleTableTemplate"},
     *      description="Store ExampleTableTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExampleTableTemplate that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExampleTableTemplate")
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
     *                  ref="#/definitions/ExampleTableTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExampleTableTemplateAPIRequest $request)
    {
        $input = $request->all();

        $exampleTableTemplate = $this->exampleTableTemplateRepository->create($input);

        return $this->sendResponse($exampleTableTemplate->toArray(), 'Example Table Template saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/exampleTableTemplates/{id}",
     *      summary="Display the specified ExampleTableTemplate",
     *      tags={"ExampleTableTemplate"},
     *      description="Get ExampleTableTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExampleTableTemplate",
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
     *                  ref="#/definitions/ExampleTableTemplate"
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
        /** @var ExampleTableTemplate $exampleTableTemplate */
        $exampleTableTemplate = $this->exampleTableTemplateRepository->findWithoutFail($id);

        if (empty($exampleTableTemplate)) {
            return $this->sendError('Example Table Template not found');
        }

        return $this->sendResponse($exampleTableTemplate->toArray(), 'Example Table Template retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateExampleTableTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/exampleTableTemplates/{id}",
     *      summary="Update the specified ExampleTableTemplate in storage",
     *      tags={"ExampleTableTemplate"},
     *      description="Update ExampleTableTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExampleTableTemplate",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExampleTableTemplate that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExampleTableTemplate")
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
     *                  ref="#/definitions/ExampleTableTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExampleTableTemplateAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExampleTableTemplate $exampleTableTemplate */
        $exampleTableTemplate = $this->exampleTableTemplateRepository->findWithoutFail($id);

        if (empty($exampleTableTemplate)) {
            return $this->sendError('Example Table Template not found');
        }

        $exampleTableTemplate = $this->exampleTableTemplateRepository->update($input, $id);

        return $this->sendResponse($exampleTableTemplate->toArray(), 'ExampleTableTemplate updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/exampleTableTemplates/{id}",
     *      summary="Remove the specified ExampleTableTemplate from storage",
     *      tags={"ExampleTableTemplate"},
     *      description="Delete ExampleTableTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExampleTableTemplate",
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
        /** @var ExampleTableTemplate $exampleTableTemplate */
        $exampleTableTemplate = $this->exampleTableTemplateRepository->findWithoutFail($id);

        if (empty($exampleTableTemplate)) {
            return $this->sendError('Example Table Template not found');
        }

        $exampleTableTemplate->delete();

        return $this->sendSuccess('Example Table Template deleted successfully');
    }

    public function getExampleTableData(Request $request)  {
        $input = $request->all();
        $documentSystemID = $input['documentSystemID'];
        $exampleTableData = ExampleTableTemplate::where('documentSystemID',$documentSystemID )->first();
        if ($exampleTableData){
            $tableData = $exampleTableData->data;
            $jsonData = json_decode($tableData);
        } else {
            $jsonData = " ";
        }
        return $this->sendResponse($jsonData, 'Example Table Template retrieved successfully');

    }
}
