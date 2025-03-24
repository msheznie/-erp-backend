<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocCodeSetupTypeBasedAPIRequest;
use App\Http\Requests\API\UpdateDocCodeSetupTypeBasedAPIRequest;
use App\Models\DocCodeSetupTypeBased;
use App\Repositories\DocCodeSetupTypeBasedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentCodeMaster;
use App\Models\DocumentCodePrefix;
use Carbon\Carbon;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Services\DocumentCodeConfigurationService;

/**
 * Class DocCodeSetupTypeBasedController
 * @package App\Http\Controllers\API
 */

class DocCodeSetupTypeBasedAPIController extends AppBaseController
{
    /** @var  DocCodeSetupTypeBasedRepository */
    private $docCodeSetupTypeBasedRepository;
    private $documentCodeConfigurationService;

    public function __construct(DocumentCodeConfigurationService $documentCodeConfigurationService ,DocCodeSetupTypeBasedRepository $docCodeSetupTypeBasedRepo)
    {
        $this->docCodeSetupTypeBasedRepository = $docCodeSetupTypeBasedRepo;
        $this->documentCodeConfigurationService = $documentCodeConfigurationService;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/docCodeSetupTypeBaseds",
     *      summary="getDocCodeSetupTypeBasedList",
     *      tags={"DocCodeSetupTypeBased"},
     *      description="Get all DocCodeSetupTypeBaseds",
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
     *                  @OA\Items(ref="#/definitions/DocCodeSetupTypeBased")
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
        $this->docCodeSetupTypeBasedRepository->pushCriteria(new RequestCriteria($request));
        $this->docCodeSetupTypeBasedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $docCodeSetupTypeBaseds = $this->docCodeSetupTypeBasedRepository->all();

        return $this->sendResponse($docCodeSetupTypeBaseds->toArray(), 'Doc Code Setup Type Baseds retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/docCodeSetupTypeBaseds",
     *      summary="createDocCodeSetupTypeBased",
     *      tags={"DocCodeSetupTypeBased"},
     *      description="Create DocCodeSetupTypeBased",
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
     *                  ref="#/definitions/DocCodeSetupTypeBased"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocCodeSetupTypeBasedAPIRequest $request)
    {
        $input = $request->all();

        $docCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepository->create($input);

        return $this->sendResponse($docCodeSetupTypeBased->toArray(), 'Doc Code Setup Type Based saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/docCodeSetupTypeBaseds/{id}",
     *      summary="getDocCodeSetupTypeBasedItem",
     *      tags={"DocCodeSetupTypeBased"},
     *      description="Get DocCodeSetupTypeBased",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeSetupTypeBased",
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
     *                  ref="#/definitions/DocCodeSetupTypeBased"
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
        /** @var DocCodeSetupTypeBased $docCodeSetupTypeBased */
        $docCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepository->findWithoutFail($id);

        if (empty($docCodeSetupTypeBased)) {
            return $this->sendError('Doc Code Setup Type Based not found');
        }

        return $this->sendResponse($docCodeSetupTypeBased->toArray(), 'Doc Code Setup Type Based retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/docCodeSetupTypeBaseds/{id}",
     *      summary="updateDocCodeSetupTypeBased",
     *      tags={"DocCodeSetupTypeBased"},
     *      description="Update DocCodeSetupTypeBased",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeSetupTypeBased",
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
     *                  ref="#/definitions/DocCodeSetupTypeBased"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocCodeSetupTypeBasedAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocCodeSetupTypeBased $docCodeSetupTypeBased */
        $docCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepository->findWithoutFail($id);

        if (empty($docCodeSetupTypeBased)) {
            return $this->sendError('Doc Code Setup Type Based not found');
        }

        $docCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepository->update($input, $id);

        return $this->sendResponse($docCodeSetupTypeBased->toArray(), 'DocCodeSetupTypeBased updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/docCodeSetupTypeBaseds/{id}",
     *      summary="deleteDocCodeSetupTypeBased",
     *      tags={"DocCodeSetupTypeBased"},
     *      description="Delete DocCodeSetupTypeBased",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeSetupTypeBased",
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
        /** @var DocCodeSetupTypeBased $docCodeSetupTypeBased */
        $docCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepository->findWithoutFail($id);

        if (empty($docCodeSetupTypeBased)) {
            return $this->sendError('Doc Code Setup Type Based not found');
        }

        $docCodeSetupTypeBased->delete();

        return $this->sendSuccess('Doc Code Setup Type Based deleted successfully');
    }

    public function getDocumentCodeSetupTypeBased(Request $request)
    {
        $input = $request->all();
        $master_id = $input['master_id'];

        
        $company_id = $input['company_id'];

        $docCodeSetupTypeBased = DocCodeSetupTypeBased::with('type')
                                ->where('master_id', $master_id)
                                ->where('company_id', $company_id)
                                ->get();

        
        $documentCodeMaster = DocumentCodeMaster::with([
                                                        'document_code_transactions' => function ($query) use ($company_id) {
                                                            $query->where('company_id', $company_id);
                                                        },
                                                        'doc_code_numbering_sequences'
                                                    ])
                                                    ->where('id', $master_id)
                                                    ->where('company_id', $company_id)
                                                    ->first();
                                                    
        $lastSerial = $documentCodeMaster->last_serial;
        $serialLength = $documentCodeMaster->serial_length;
        $documentSerial = str_pad($lastSerial, $serialLength, '0', STR_PAD_LEFT);
        $documentSystemID = $documentCodeMaster->document_code_transactions->document_system_id;

        if($docCodeSetupTypeBased){
            foreach ($docCodeSetupTypeBased as $key => $value) {
                // Get the formats array from the service function
                $formats = $this->documentCodeConfigurationService->getDocumentCodeSetupValues($company_id, 'SEG', $master_id, $isPreview = 1, $documentSystemID);
            
                $formatsArray = [];
            
                for ($i = 1; $i <= 12; $i++) {
                    $format = 'format' . $i;
                    if ($value->$format == 5) {
                        $documentCodePrefix = DocumentCodePrefix::where('type_based_id', $value->id)
                            ->where('format', $format)
                            ->first();
            
                        if ($documentCodePrefix) {
                            $formats[$value->$format] = $documentCodePrefix->description;
                        } else {
                            $formats[$value->$format] = $value->type->type_prefix;
                        }
                    }
            
                    $formatsArray[] = $formats[$value->$format] ?? '';
                }
            
                // Generate codePreview
                $docCodeSetupTypeBased[$key]->codePreview = implode('', $formatsArray) . $documentSerial;
            }
        }

        return $this->sendResponse($docCodeSetupTypeBased->toArray(), 'Doc Code Setup Type Based retrieved successfully');


    }

    public function updateTypeBasedFormat(Request $request)
    {
        $input = $request->all();
        unset($input['type']);
        $input = $this->convertArrayToValue($input);
        $id = $input['id'];

        /** @var DocCodeSetupTypeBased $DocCodeSetupTypeBased */
        $docCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepository->findWithoutFail($id);

        if (empty($docCodeSetupTypeBased)) {
            return $this->sendError('Document Code Master not found');
        }
        unset($input['type']);

        $docCodeSetupTypeBased = $this->docCodeSetupTypeBasedRepository->update($input, $id);

        return $this->sendResponse($docCodeSetupTypeBased->toArray(), 'Doc Code Setup Type Based updated successfully');
    }
}
