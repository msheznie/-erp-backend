<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocCodeSetupCommonAPIRequest;
use App\Http\Requests\API\UpdateDocCodeSetupCommonAPIRequest;
use App\Models\DocCodeSetupCommon;
use App\Repositories\DocCodeSetupCommonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentCodeMaster;
use App\Models\DocumentCodePrefix;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Services\DocumentCodeConfigurationService;
use Response;

/**
 * Class DocCodeSetupCommonController
 * @package App\Http\Controllers\API
 */

class DocCodeSetupCommonAPIController extends AppBaseController
{
    /** @var  DocCodeSetupCommonRepository */
    private $documentCodeConfigurationService;
    private $docCodeSetupCommonRepository;

    public function __construct(DocumentCodeConfigurationService $documentCodeConfigurationService ,DocCodeSetupCommonRepository $docCodeSetupCommonRepo)
    {
        $this->documentCodeConfigurationService = $documentCodeConfigurationService;
        $this->docCodeSetupCommonRepository = $docCodeSetupCommonRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/docCodeSetupCommons",
     *      summary="getDocCodeSetupCommonList",
     *      tags={"DocCodeSetupCommon"},
     *      description="Get all DocCodeSetupCommons",
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
     *                  @OA\Items(ref="#/definitions/DocCodeSetupCommon")
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
        $this->docCodeSetupCommonRepository->pushCriteria(new RequestCriteria($request));
        $this->docCodeSetupCommonRepository->pushCriteria(new LimitOffsetCriteria($request));
        $docCodeSetupCommons = $this->docCodeSetupCommonRepository->all();

        return $this->sendResponse($docCodeSetupCommons->toArray(), 'Doc Code Setup Commons retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/docCodeSetupCommons",
     *      summary="createDocCodeSetupCommon",
     *      tags={"DocCodeSetupCommon"},
     *      description="Create DocCodeSetupCommon",
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
     *                  ref="#/definitions/DocCodeSetupCommon"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocCodeSetupCommonAPIRequest $request)
    {
        $input = $request->all();

        $docCodeSetupCommon = $this->docCodeSetupCommonRepository->create($input);

        return $this->sendResponse($docCodeSetupCommon->toArray(), 'Doc Code Setup Common saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/docCodeSetupCommons/{id}",
     *      summary="getDocCodeSetupCommonItem",
     *      tags={"DocCodeSetupCommon"},
     *      description="Get DocCodeSetupCommon",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeSetupCommon",
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
     *                  ref="#/definitions/DocCodeSetupCommon"
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
        /** @var DocCodeSetupCommon $docCodeSetupCommon */
        $docCodeSetupCommon = $this->docCodeSetupCommonRepository->findWithoutFail($id);

        if (empty($docCodeSetupCommon)) {
            return $this->sendError('Doc Code Setup Common not found');
        }

        return $this->sendResponse($docCodeSetupCommon->toArray(), 'Doc Code Setup Common retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/docCodeSetupCommons/{id}",
     *      summary="updateDocCodeSetupCommon",
     *      tags={"DocCodeSetupCommon"},
     *      description="Update DocCodeSetupCommon",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeSetupCommon",
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
     *                  ref="#/definitions/DocCodeSetupCommon"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocCodeSetupCommonAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocCodeSetupCommon $docCodeSetupCommon */
        $docCodeSetupCommon = $this->docCodeSetupCommonRepository->findWithoutFail($id);

        if (empty($docCodeSetupCommon)) {
            return $this->sendError('Doc Code Setup Common not found');
        }

        $docCodeSetupCommon = $this->docCodeSetupCommonRepository->update($input, $id);

        return $this->sendResponse($docCodeSetupCommon->toArray(), 'DocCodeSetupCommon updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/docCodeSetupCommons/{id}",
     *      summary="deleteDocCodeSetupCommon",
     *      tags={"DocCodeSetupCommon"},
     *      description="Delete DocCodeSetupCommon",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeSetupCommon",
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
        /** @var DocCodeSetupCommon $docCodeSetupCommon */
        $docCodeSetupCommon = $this->docCodeSetupCommonRepository->findWithoutFail($id);

        if (empty($docCodeSetupCommon)) {
            return $this->sendError('Doc Code Setup Common not found');
        }

        $docCodeSetupCommon->delete();

        return $this->sendSuccess('Doc Code Setup Common deleted successfully');
    }

    public function getDocumentCodeSetupCommon(Request $request)
    {
        $input = $request->all();
        $master_id = $input['master_id'];

        $company_id = $input['company_id'];


        $docCodeSetupCommon = DocCodeSetupCommon::with([
                                                        'document_code_transactions' => function ($query) use ($input) {
                                                            $query->where('company_id', $input['company_id']);
                                                        }])
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

        if($docCodeSetupCommon){
            foreach ($docCodeSetupCommon as $key => $value) {
                // Get the formats array from the service function
                $formats = $this->documentCodeConfigurationService->getDocumentCodeSetupValues($company_id, 'SEG', $master_id, $isPreview = 1, $documentSystemID);

                $formatsArray = [];

                for ($i = 1; $i <= 12; $i++) {
                    $format = 'format' . $i;
                    if ($value->$format == 5) {
                        $documentCodePrefix = DocumentCodePrefix::where('common_id', $value->id)
                            ->where('format', $format)
                            ->first();

                            if ($documentCodePrefix) {
                            $formats[$value->$format] = $documentCodePrefix->description;
                        } else {
                            $formats[$value->$format] = $value->document_code_transactions->master_prefix;
                        }
                    }
            
                    $formatsArray[] = $formats[$value->$format] ?? '';
                }
            
                // Generate codePreview
                $docCodeSetupCommon[$key]->codePreview = implode('', $formatsArray) . $documentSerial;
            }
        }

        return $this->sendResponse($docCodeSetupCommon->toArray(), 'Doc Code Setup Common retrieved successfully');
    }

    public function updateCommonFormat(Request $request)
    {
        $input = $request->all();
        unset($input['document_code_transactions']);
        $input = $this->convertArrayToValue($input);
        $id = $input['id'];

        /** @var DocCodeSetupCommon $DocCodeSetupCommon */
        $docCodeSetupCommon = $this->docCodeSetupCommonRepository->findWithoutFail($id);

        if (empty($docCodeSetupCommon)) {
            return $this->sendError('Document Code Master not found');
        }

        $docCodeSetupCommon = $this->docCodeSetupCommonRepository->update($input, $id);

        return $this->sendResponse($docCodeSetupCommon->toArray(), 'Doc Code Setup Common updated successfully');
    }

}
