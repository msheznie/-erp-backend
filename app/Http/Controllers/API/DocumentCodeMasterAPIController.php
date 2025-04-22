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
use App\Models\DocumentCodePrefix;
use App\Models\DocumentCodeTransaction;
use App\Models\ProcumentOrder;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\DB;
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
        $company_id = $input['companyId'];

        $documentCodeMasters = DocumentCodeMaster::with(['document_code_transactions' => function ($query) use ($company_id) {
                                                        $query->where('company_id', $company_id);
                                                    }, 'doc_code_numbering_sequences'])
                                                    ->where('module_id', $module_id)
                                                    ->where('company_id', $company_id)
                                                    ->get();

        
        if ($documentCodeMasters->count() > 0) {
            foreach ($documentCodeMasters as $documentCodeMaster) {
                switch ($documentCodeMaster->document_code_transactions->document_system_id) {
                    case 1:
                            $lastSerial = PurchaseRequest::where('documentSystemID', $documentCodeMaster->document_code_transactions->document_system_id)
                            ->where('companySystemID', $company_id)
                            ->latest('serialNumber')
                            ->first()
                            ->serialNumber;

                            $documentCodeMaster->last_serial = $lastSerial;
                            $documentCodeMaster->save();

                        break;
                    case 2:
                        $lastSerial = ProcumentOrder::where('documentSystemID', $documentCodeMaster->document_code_transactions->document_system_id)
                        ->where('companySystemID', $company_id)
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
        $company_id = $input['company_id'];

        $documentCodeMasters = DocumentCodeMaster::with([
                                                    'document_code_transactions' => function ($query) use ($company_id) {
                                                        $query->where('company_id', $company_id);
                                                    }])->where('id', $id)
                                                    ->where('company_id', $company_id)
                                                    ->first();
        $data = [
            'isGettingEdited' => 1,
            'isGettingEditedTime' => now()
        ];
        if($documentCodeMasters){
            $documentCodeMasters->document_code_transactions->update($data);
        }

        return $this->sendResponse($documentCodeMasters->toArray(), 'Document Code Masters retrieved successfully');

    }

    public function updateDocumentCodeTransaction(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $documentCodeMasters = DocumentCodeMaster::with([
                                                        'document_code_transactions' => function ($query) use ($input) {
                                                            $query->where('company_id', $input['company_id']);
                                                        },
                                                        'doc_code_numbering_sequences'
                                                    ])
                                                    ->where('id', $id)
                                                    ->first();
        if($documentCodeMasters){
            $documentCodeMasters->document_code_transactions->update(['isGettingEdited' => 0]);
        }

        return $this->sendResponse($documentCodeMasters->toArray(), 'Document Code Transaction updated successfully');

    }

    public function isGettingCodeConfigured(Request $request)
    {
        $input = $request->all();
        $documentSystemID = $input['documentSystemID'];
        $company_id = $input['company_id'];

        $isGettingEdited = DocumentCodeTransaction::where('document_system_id', $documentSystemID)
                                                    ->where('company_id', $company_id)
                                                    ->first();

        if (!$isGettingEdited) {
            return $this->sendError('Document Code Transaction not found', 404);
        }

        if ($isGettingEdited && $isGettingEdited->isGettingEdited == 1) {
            return $this->sendError('Document code configuration in progress', 500);
        }

        return $this->sendResponse($isGettingEdited->toArray(), 'Document Code Transaction isGettingEdited retrieved successfully');

    }

    public function updateDocumentCodeMaster(Request $request)
    {
        $input = $request->all();
        unset($input['doc_code_numbering_sequences']);
        unset($input['document_code_transactions']);
        unset($input['numbering_sequence_id']);
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
            if($docCodeSetupCommon && $documentCodeMaster->serialization == 0){
                foreach ($docCodeSetupCommon as $codeSetupCommon) {
                    $formatCount = $input['formatCount'];
                    $hasYYYYFormat = false;
                    for ($i = 1; $i <= $formatCount; $i++) {
                        $formatKey = 'format' . $i;
                        $formatValue = $codeSetupCommon->{'format' . $i};
                        if ($formatValue == 6 || $formatValue == 7) {
                            $hasYYYYFormat = true;
                            break;
                        }
                    }
                    if($documentCodeMaster->numbering_sequence_id == 2){
                        if (!$hasYYYYFormat) {
                            return $this->sendError('Please select a valid financial year in either YYYY or YY format for Finance Year Based serialization.',400);
                        }
                    }
                    $deletedFormatNumber = $input['formatCount'] + 1;
                    $nullFormat = 'format' . $deletedFormatNumber;
                    $codeSetupCommon->update([$nullFormat => null]);
                }
            }

            $docCodeSetupTypeBased = DocCodeSetupTypeBased::with('type')->where('master_id', $id)->get();
            if($docCodeSetupTypeBased && $documentCodeMaster->serialization == 1){
                foreach ($docCodeSetupTypeBased as $codeSetupTypeBased) {
                    $formatCount = $input['formatCount'];
                    $hasYYYYFormat = false;
                    for ($i = 1; $i <= $formatCount; $i++) {
                        $formatKey = 'format' . $i;
                        $formatValue = $codeSetupTypeBased->{'format' . $i};
                        if ($formatValue == 6 || $formatValue == 7) {
                            $hasYYYYFormat = true;
                            break;
                        }
                    }
                    if($documentCodeMaster->numbering_sequence_id == 2){
                        if (!$hasYYYYFormat) {
                            return $this->sendError('Please select a valid financial year in either YYYY or YY format for Finance Year Based serialization.',400);
                        }
                    }
                    $deletedFormatNumber = $input['formatCount'] + 1;
                    $nullFormat = 'format' . $deletedFormatNumber;

                    $codeSetupTypeBased->update([$nullFormat => null]);
                }
            }

        }
        




        $documentCodeMaster = $this->documentCodeMasterRepository->update($input, $id);

        return $this->sendResponse($documentCodeMaster->toArray(), 'DocumentCodeMaster updated successfully');
    }

    public function updateDocumentCode(Request $request)
    {
        $input = $request->all();

        DB::beginTransaction();
        try {
    
            if (isset($input['documentCodeMaster'])) {
                $input['documentCodeMaster'] = json_decode($input['documentCodeMaster'], true);
                $documentCodeMaster =$input['documentCodeMaster'];
                unset($documentCodeMaster['doc_code_numbering_sequences']);
                unset($documentCodeMaster['document_code_transactions']);
                $documentCodeMaster = $this->convertArrayToSelectedValue($documentCodeMaster, array('numbering_sequence_id'));
                if($documentCodeMaster['serial_length'] > 12){
                    return $this->sendError('Serial length should not be greater than 12.',400);
                }
                if($documentCodeMaster['serial_length'] < 4){
                    return $this->sendError('Serial length should not be less than 4.',400);
                }

                DocumentCodeMaster::where('id', $documentCodeMaster['id'])->update($documentCodeMaster);
            }

            if (isset($input['typeBased'])) {
                $input['typeBased'] = json_decode($input['typeBased'], true);
            }

            if (isset($input['common'])) {
                $input['common'] = json_decode($input['common'], true);
            }


            if(isset($input['documentCodeMaster'])){
                if($documentCodeMaster['numbering_sequence_id'] == 2){
                    if (isset($input['typeBased'])) {
                        foreach ($input['typeBased'] as $typeBased) {
                            unset($typeBased['codePreview']);
                            unset($typeBased['type']);
                            $typeBased = $this->convertArrayToSelectedValue($typeBased, array(  'format1',
                                                                                                'format2',
                                                                                                'format3',
                                                                                                'format4',
                                                                                                'format5',
                                                                                                'format6',
                                                                                                'format7',
                                                                                                'format8',
                                                                                                'format9',
                                                                                                'format10',
                                                                                                'format11',
                                                                                                'format12',));

                            // Extract format values (format1 to format12)
                            $formats = [];
                            for ($i = 1; $i <= 12; $i++) {
                                $key = "format$i";
                                if (isset($typeBased[$key])) {
                                    $formats[] = (array) $typeBased[$key]; 
                                }
                            }

                            $flattenedFormats = !empty($formats) ? array_merge(...$formats) : [];

                            //check if any format have selected YYYY or YY
                            if (!in_array(6, $flattenedFormats) && !in_array(7, $flattenedFormats)) {
                                return $this->sendError('Please select a valid financial year in either YYYY or YY format for Finance Year Based serialization.',400);
                            }
                            $this->updateDocumentCodePrefix($typeBased, $formats,1);
                            DocCodeSetupTypeBased::where('id', $typeBased['id'])->update($typeBased);
                            
                        }
                    }
                    
                    if (isset($input['common'])) {
                        foreach ($input['common'] as $common) {
                            unset($common['codePreview']);
                            unset($common['document_code_transactions']);
                            $common = $this->convertArrayToSelectedValue($common, array(  'format1',
                                                                                        'format2',
                                                                                        'format3',
                                                                                        'format4',
                                                                                        'format5',
                                                                                        'format6',
                                                                                        'format7',
                                                                                        'format8',
                                                                                        'format9',
                                                                                        'format10',
                                                                                        'format11',
                                                                                        'format12',));

                            // Extract format values (format1 to format12)
                            $formats = [];
                            for ($i = 1; $i <= 12; $i++) {
                                $key = "format$i";
                                if (isset($common[$key])) {
                                    $formats[] = (array) $common[$key]; 
                                }
                            }

                            $flattenedFormats = !empty($formats) ? array_merge(...$formats) : [];

                            //check if any format have selected YYYY or YY
                            if (!in_array(6, $flattenedFormats) && !in_array(7, $flattenedFormats)) {
                                return $this->sendError('Please select a valid financial year in either YYYY or YY format for Finance Year Based serialization.',400);
                            }
                            $this->updateDocumentCodePrefix($common, $formats,0);
                            DocCodeSetupCommon::where('id', $common['id'])->update($common);
                            
                        }

                    }
                } else {
                    if (isset($input['common'])) {
                        foreach ($input['common'] as $common) {
                            unset($common['codePreview']);
                            unset($common['document_code_transactions']);
                            $common = $this->convertArrayToSelectedValue($common, array(  'format1',
                                                                                        'format2',
                                                                                        'format3',
                                                                                        'format4',
                                                                                        'format5',
                                                                                        'format6',
                                                                                        'format7',
                                                                                        'format8',
                                                                                        'format9',
                                                                                        'format10',
                                                                                        'format11',
                                                                                        'format12',));
                            
                            // Extract format values (format1 to format12)
                            $formats = [];
                            for ($i = 1; $i <= 12; $i++) {
                                $key = "format$i";
                                if (isset($common[$key])) {
                                    $formats[] = (array) $common[$key];
                                }
                            }

                            $this->updateDocumentCodePrefix($common, $formats,0);
                            DocCodeSetupCommon::where('id', $common['id'])->update($common);
                        }
                    }

                    if (isset($input['typeBased'])) {
                        foreach ($input['typeBased'] as $typeBased) {
                            unset($typeBased['codePreview']);
                            unset($typeBased['type']);
                            $typeBased = $this->convertArrayToSelectedValue($typeBased, array(  'format1',
                                                                                                'format2',
                                                                                                'format3',
                                                                                                'format4',
                                                                                                'format5',
                                                                                                'format6',
                                                                                                'format7',
                                                                                                'format8',
                                                                                                'format9',
                                                                                                'format10',
                                                                                                'format11',
                                                                                                'format12',));

                            // Extract format values (format1 to format12)
                            $formats = [];
                            for ($i = 1; $i <= 12; $i++) {
                                $key = "format$i";
                                if (isset($typeBased[$key])) {
                                    $formats[] = (array) $typeBased[$key];
                                }
                            }

                            $this->updateDocumentCodePrefix($typeBased, $formats,1);

                            DocCodeSetupTypeBased::where('id', $typeBased['id'])->update($typeBased);
                        }
                    }
                }
            }

            DB::commit();
            return $this->sendResponse($documentCodeMaster, 'Document code configured successfully');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Error occurred in document code configuration',500);
        }

    }

    private function updateDocumentCodePrefix($setup, $formats, $setupBased)
    {
        foreach ($formats as $key => $format) {
            if (in_array(5, $format)) {
                // Get the setup ID and format number

                $setupId = $setup['id'];
                $company_id = $setup['company_id'];
                $formatNumber = $key + 1; 
                $formatColumn = 'format' . $formatNumber;

                // Check if DocumentCodePrefix already exists for this setup and format
                if($setupBased == 1){
                    $documentCodePrefix = DocumentCodePrefix::where('type_based_id', $setupId)
                    ->where('format', $formatColumn)
                    ->first();

                    // If it doesn't exist, create a new one
                    if (!$documentCodePrefix) {
                        $description = $setup['type_id'] == 1 ? 'D-PO' : 'R-PO';
                        $documentCodePrefix = DocumentCodePrefix::create([
                            'type_based_id' => $setupId,
                            'format' => $formatColumn,
                            'company_id' => $company_id,
                            'description' => $description,
                        ]);
                    }

                } else {

                    $documentCodePrefix = DocumentCodePrefix::where('common_id', $setupId)
                    ->where('format', $formatColumn)
                    ->first();

                    // If it doesn't exist, create a new one
                    if (!$documentCodePrefix) {
                        $document_transaction_id =$setup['document_transaction_id'];
                        $docCodeTransaction = DocumentCodeTransaction::where('id', $document_transaction_id)->first();
                        $description = $docCodeTransaction->master_prefix;
                        $documentCodePrefix = DocumentCodePrefix::create([
                            'common_id' => $setupId,
                            'format' => $formatColumn,
                            'company_id' => $company_id,
                            'description' => $description,
                        ]);
                    }
                }



            } else {
                $formatNumber = $key + 1; 
                $formatColumn = 'format' . $formatNumber;
                // Check if DocumentCodePrefix exists for this setup but not for this format
                if($setupBased == 1){
                    $documentCodePrefix = DocumentCodePrefix::where('type_based_id', $setup['id'])
                    ->where('format', $formatColumn)
                    ->first();

                    // If it exists, delete it
                    if ($documentCodePrefix) {
                        $documentCodePrefix->delete();
                    }
                } else {
                    $documentCodePrefix = DocumentCodePrefix::where('common_id', $setup['id'])
                    ->where('format', $formatColumn)
                    ->first();
                    // If it exists, delete it
                    if ($documentCodePrefix) {
                        $documentCodePrefix->delete();
                    }
                }

            }
        }
    }

}
