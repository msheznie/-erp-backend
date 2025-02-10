<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentCodeMasterAPIRequest;
use App\Http\Requests\API\UpdateDocumentCodeMasterAPIRequest;
use App\Models\DocumentCodeMaster;
use App\Repositories\DocumentCodeMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\DocCodeSetupCommon;
use App\Models\DocCodeSetupTypeBased;
use App\Models\ProcumentOrder;
use App\Models\PurchaseRequest;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentCodeMasterController
 * @package App\Http\Controllers\API
 */

class DocumentCodeMasterAPIController extends AppBaseController
{
    /** @var  DocumentCodeMasterRepository */
    private $documentCodeMasterRepository;

    public function __construct(DocumentCodeMasterRepository $documentCodeMasterRepo)
    {
        $this->documentCodeMasterRepository = $documentCodeMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeMasters",
     *      summary="getDocumentCodeMasterList",
     *      tags={"DocumentCodeMaster"},
     *      description="Get all DocumentCodeMasters",
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
     *                  @OA\Items(ref="#/definitions/DocumentCodeMaster")
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
        $this->documentCodeMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->documentCodeMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentCodeMasters = $this->documentCodeMasterRepository->all();

        return $this->sendResponse($documentCodeMasters->toArray(), 'Document Code Masters retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentCodeMasters",
     *      summary="createDocumentCodeMaster",
     *      tags={"DocumentCodeMaster"},
     *      description="Create DocumentCodeMaster",
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
     *                  ref="#/definitions/DocumentCodeMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentCodeMasterAPIRequest $request)
    {
        $input = $request->all();

        $documentCodeMaster = $this->documentCodeMasterRepository->create($input);

        return $this->sendResponse($documentCodeMaster->toArray(), 'Document Code Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeMasters/{id}",
     *      summary="getDocumentCodeMasterItem",
     *      tags={"DocumentCodeMaster"},
     *      description="Get DocumentCodeMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeMaster",
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
     *                  ref="#/definitions/DocumentCodeMaster"
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
        /** @var DocumentCodeMaster $documentCodeMaster */
        $documentCodeMaster = $this->documentCodeMasterRepository->findWithoutFail($id);

        if (empty($documentCodeMaster)) {
            return $this->sendError('Document Code Master not found');
        }

        return $this->sendResponse($documentCodeMaster->toArray(), 'Document Code Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentCodeMasters/{id}",
     *      summary="updateDocumentCodeMaster",
     *      tags={"DocumentCodeMaster"},
     *      description="Update DocumentCodeMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeMaster",
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
     *                  ref="#/definitions/DocumentCodeMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentCodeMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentCodeMaster $documentCodeMaster */
        $documentCodeMaster = $this->documentCodeMasterRepository->findWithoutFail($id);

        if (empty($documentCodeMaster)) {
            return $this->sendError('Document Code Master not found');
        }

        $documentCodeMaster = $this->documentCodeMasterRepository->update($input, $id);

        return $this->sendResponse($documentCodeMaster->toArray(), 'DocumentCodeMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentCodeMasters/{id}",
     *      summary="deleteDocumentCodeMaster",
     *      tags={"DocumentCodeMaster"},
     *      description="Delete DocumentCodeMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeMaster",
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
        /** @var DocumentCodeMaster $documentCodeMaster */
        $documentCodeMaster = $this->documentCodeMasterRepository->findWithoutFail($id);

        if (empty($documentCodeMaster)) {
            return $this->sendError('Document Code Master not found');
        }

        $documentCodeMaster->delete();

        return $this->sendSuccess('Document Code Master deleted successfully');
    }


    public function getDocumentCodeMasters(Request $request)
    {
        $input = $request->all();
        $module_id = $input['module_id'];

        $documentCodeMasters = DocumentCodeMaster::with('document_code_transactions', 'doc_code_numbering_sequences')
                                                    ->where('module_id', $module_id)
                                                    ->get();

        
        if ($documentCodeMasters->count() > 0) {
            foreach ($documentCodeMasters as $documentCodeMaster) {
                switch ($documentCodeMaster->document_code_transactions->document_system_id) {
                    case 1:
                            $lastSerial = PurchaseRequest::where('documentSystemID', $documentCodeMaster->document_code_transactions->document_system_id)
                            ->latest('serialNumber')
                            ->first()
                            ->serialNumber;

                            $documentCodeMaster->last_serial = $lastSerial;
                            $documentCodeMaster->save();

                        break;
                    case 2:
                        $lastSerial = ProcumentOrder::where('documentSystemID', $documentCodeMaster->document_code_transactions->document_system_id)
                        ->latest('serialNumber')
                        ->first()
                        ->serialNumber;

                        $documentCodeMaster->last_serial = $lastSerial;
                        $documentCodeMaster->save();
                        break;
                    default:
                            $documentCodeMaster->last_serial = 0;
                            $documentCodeMaster->save();
                        break;
                }
            }
        }
        return $this->sendResponse($documentCodeMasters->toArray(), 'Document Code Masters retrieved successfully');

    }

    public function getDocumentCodeMaster(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $documentCodeMasters = DocumentCodeMaster::with('document_code_transactions', 'doc_code_numbering_sequences')
                                                    ->where('id', $id)
                                                    ->first();

        return $this->sendResponse($documentCodeMasters->toArray(), 'Document Code Masters retrieved successfully');

    }

    public function updateDocumentCodeMaster(Request $request)
    {
        $input = $request->all();
        unset($input['doc_code_numbering_sequences']);
        unset($input['document_code_transactions']);
        $input = $this->convertArrayToValue($input);

        $id = $input['id'];

        /** @var DocumentCodeMaster $documentCodeMaster */
        $documentCodeMaster = $this->documentCodeMasterRepository->findWithoutFail($id);

        if (empty($documentCodeMaster)) {
            return $this->sendError('Document Code Master not found');
        }

        if($input['formatCount'] < $documentCodeMaster->formatCount){
            $formatCount = 'format' . $documentCodeMaster->formatCount;


            $docCodeSetupCommon = DocCodeSetupCommon::with('document_code_transactions')->where('master_id', $id)->get();
            if($docCodeSetupCommon){
                foreach ($docCodeSetupCommon as $codeSetupCommon) {
                    $codeSetupCommon->update([$formatCount => null]);
                }
            }

            $docCodeSetupTypeBased = DocCodeSetupTypeBased::with('type')->where('master_id', $id)->get();
            if($docCodeSetupTypeBased){
                foreach ($docCodeSetupTypeBased as $codeSetupTypeBased) {
                    $codeSetupTypeBased->update([$formatCount => null]);
                }
            }

        }
        




        $documentCodeMaster = $this->documentCodeMasterRepository->update($input, $id);

        return $this->sendResponse($documentCodeMaster->toArray(), 'DocumentCodeMaster updated successfully');
    }

}
