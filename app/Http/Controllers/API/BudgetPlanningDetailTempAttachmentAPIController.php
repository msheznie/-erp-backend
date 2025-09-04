<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateBudgetPlanningDetailTempAttachmentAPIRequest;
use App\Http\Requests\API\UpdateBudgetPlanningDetailTempAttachmentAPIRequest;
use App\Models\BudgetPlanningDetailTempAttachment;
use App\Models\DepartmentBudgetPlanningDetail;
use App\Repositories\BudgetPlanningDetailTempAttachmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Criteria\FilterBudgetTemplateAttachmentCriteria;
use Response;

/**
 * Class BudgetPlanningDetailTempAttachmentController
 * @package App\Http\Controllers\API
 */

class BudgetPlanningDetailTempAttachmentAPIController extends AppBaseController
{
    /** @var  BudgetPlanningDetailTempAttachmentRepository */
    private $budgetPlanningDetailTempAttachmentRepository;

    public function __construct(BudgetPlanningDetailTempAttachmentRepository $budgetPlanningDetailTempAttachmentRepo)
    {
        $this->budgetPlanningDetailTempAttachmentRepository = $budgetPlanningDetailTempAttachmentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/budgetPlanningDetailTempAttachments",
     *      summary="getBudgetPlanningDetailTempAttachmentList",
     *      tags={"BudgetPlanningDetailTempAttachment"},
     *      description="Get all BudgetPlanningDetailTempAttachments",
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
     *                  @OA\Items(ref="#/definitions/BudgetPlanningDetailTempAttachment")
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
        $this->budgetPlanningDetailTempAttachmentRepository->with([
            'uploaded_user' => function ($q) {
                $q->select('employeeSystemID', 'empName');
            }
        ]);
        $this->budgetPlanningDetailTempAttachmentRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetPlanningDetailTempAttachmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->budgetPlanningDetailTempAttachmentRepository->pushCriteria(new FilterBudgetTemplateAttachmentCriteria($request));

        $budgetPlanningDetailTempAttachments = $this->budgetPlanningDetailTempAttachmentRepository->all();

        return $this->sendResponse($budgetPlanningDetailTempAttachments->toArray(), 'Budget Planning Detail Temp Attachments retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/budgetPlanningDetailTempAttachments",
     *      summary="createBudgetPlanningDetailTempAttachment",
     *      tags={"BudgetPlanningDetailTempAttachment"},
     *      description="Create BudgetPlanningDetailTempAttachment",
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
     *                  ref="#/definitions/BudgetPlanningDetailTempAttachment"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetPlanningDetailTempAttachmentAPIRequest $request)
    {
        $input = $request->all();
        $messages = [
            'entry_id.required'           => trans('custom.entry_id_required'),
            'entry_id.integer'            => trans('custom.entry_id_integer'),
            'entry_id.exists'             => trans('custom.entry_id_exists'),
            'original_file_name.required' => trans('custom.original_file_name_required'),
            'original_file_name.string'   => trans('custom.original_file_name_string'),
            'file_type.required'          => trans('custom.file_type_required'),
            'file_type.string'            => trans('custom.file_type_string'),
            'file_size.required'          => trans('custom.file_size_required'),
            'file_size.integer'           => trans('custom.file_size_integer'),
            'file_size.max'               => trans('custom.file_size_max'),
            'description.string'         => trans('custom.description_string'),
            'file_data.required'          => trans('custom.file_data_required'),
            'file_data.string'            => trans('custom.file_data_string'),
        ];
        $validator = \Validator::make($input, [
            'entry_id'            => 'required|integer|exists:budget_det_template_entries,entryID',
            'original_file_name'  => 'required|string',
            'file_type'           => 'required|string',
            'file_size'           => 'required|integer|max:' . env('ATTACH_UPLOAD_SIZE_LIMIT', 10485760),
            'description'         => 'nullable|string',
            'file_data'           => 'required|string'
        ], $messages);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 422);
        }
        $entryData = BudgetPlanningDetailTempAttachment::getBudgetTempEntryData($input['entry_id']);
        if(empty($entryData)){
            return $this->sendError('Budget planning detail template entry not found', 404);
        }

        $departmentBudget = DepartmentBudgetPlanningDetail::getBudgetPlaningCompany($entryData->budget_detail_id);
        if(!$departmentBudget){
            return $this->sendError('Budget department not found');
        }

        $companySystemID = $departmentBudget->departmentBudgetPlanning->masterBudgetPlannings->companySystemID ?? 1;
        $companyId = $departmentBudget->departmentBudgetPlanning->masterBudgetPlannings->company->CompanyID ?? 'DEFAULT';
        $planningCode = $departmentBudget->departmentBudgetPlanning->masterBudgetPlannings->planningCode ?? 'DEFAULT';

        try{
            return DB::transaction(function () use ($input, $companySystemID, $companyId, $planningCode) {
                $extension = pathinfo($input['original_file_name'], PATHINFO_EXTENSION);
                if (empty($extension) && isset($input['file_type'])) {
                    $extension = str_replace(['image/', 'application/', 'text/'], '', $input['file_type']);
                }

                $blockedExtensions = [
                    'ace', 'ade', 'adp', 'ani', 'app', 'asp', 'aspx', 'asx', 'bas', 'bat', 'cla', 'cer', 'chm', 'cmd', 'cnt', 'com',
                    'cpl', 'crt', 'csh', 'class', 'der', 'docm', 'exe', 'fxp', 'gadget', 'hlp', 'hpj', 'hta', 'htc', 'inf', 'ins', 'isp', 'its', 'jar',
                    'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt',
                    'mdw', 'mdz', 'mht', 'mhtml', 'msc', 'msh', 'msh1', 'msh1xml', 'msh2', 'msh2xml', 'mshxml', 'msi', 'msp', 'mst', 'ops', 'osd',
                    'ocx', 'pl', 'pcd', 'pif', 'plg', 'prf', 'prg', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'pst', 'reg', 'scf', 'scr',
                    'sct', 'shb', 'shs', 'tmp', 'url', 'vb', 'vbe', 'vbp', 'vbs', 'vsmacros', 'vss', 'vst', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xml',
                    'xbap', 'xnk', 'php'
                ];

                if (in_array(strtolower($extension), $blockedExtensions)) {
                    return $this->sendError("This file type is not allowed: {$extension}", 400);
                }

                $fileData = $input['file_data'];
                if (strpos($fileData, 'data:') === 0) {
                    $fileData = substr($fileData, strpos($fileData, ',') + 1);
                }

                $decodedFile = base64_decode($fileData);
                if ($decodedFile === false) {
                    return $this->sendError("Invalid file data provided", 400);
                }

                $attachmentRecord = BudgetPlanningDetailTempAttachment::create([
                    'entry_id'           => $input['entry_id'],
                    'original_file_name' => $input['original_file_name'],
                    'file_type'          => $input['file_type'],
                    'file_size'          => $input['file_size'],
                    'description'        => $input['description'] ?? null,
                    'uploaded_by'        => Helper::getEmployeeSystemID(),
                ]);
                $fileName = 'BUDGET_TEMP_' . $input['entry_id'] . '_' . $attachmentRecord->id . '.' . $extension;
                if (Helper::checkPolicy($companySystemID, 50)) {
                    $filePath = $companyId . 'G_ERP/BUDGET_TEMP/' . $planningCode . '/' . $input['entry_id'] . '/' . $fileName;
                } else {
                    $filePath = 'G_ERP/BUDGET_TEMP/' . $planningCode . '/' . $input['entry_id'] . '/' . $fileName;
                }

                $disk = Helper::policyWiseDisk($companySystemID, 'public');
                Storage::disk($disk)->put($filePath, $decodedFile);

                $attachmentRecord->update([
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                ]);

                return $this->sendResponse($attachmentRecord, 'Attachment uploaded successfully');

            });
        } catch (\Exception $exception){
            return $this->sendError('Unexpected Error: ' . $exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/budgetPlanningDetailTempAttachments/{id}",
     *      summary="getBudgetPlanningDetailTempAttachmentItem",
     *      tags={"BudgetPlanningDetailTempAttachment"},
     *      description="Get BudgetPlanningDetailTempAttachment",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetPlanningDetailTempAttachment",
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
     *                  ref="#/definitions/BudgetPlanningDetailTempAttachment"
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
        /** @var BudgetPlanningDetailTempAttachment $budgetPlanningDetailTempAttachment */
        $budgetPlanningDetailTempAttachment = $this->budgetPlanningDetailTempAttachmentRepository->findWithoutFail($id);

        if (empty($budgetPlanningDetailTempAttachment)) {
            return $this->sendError('Budget Planning Detail Temp Attachment not found');
        }

        return $this->sendResponse($budgetPlanningDetailTempAttachment->toArray(), 'Budget Planning Detail Temp Attachment retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/budgetPlanningDetailTempAttachments/{id}",
     *      summary="updateBudgetPlanningDetailTempAttachment",
     *      tags={"BudgetPlanningDetailTempAttachment"},
     *      description="Update BudgetPlanningDetailTempAttachment",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetPlanningDetailTempAttachment",
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
     *                  ref="#/definitions/BudgetPlanningDetailTempAttachment"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetPlanningDetailTempAttachmentAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetPlanningDetailTempAttachment $budgetPlanningDetailTempAttachment */
        $budgetPlanningDetailTempAttachment = $this->budgetPlanningDetailTempAttachmentRepository->findWithoutFail($id);

        if (empty($budgetPlanningDetailTempAttachment)) {
            return $this->sendError('Budget Planning Detail Temp Attachment not found');
        }

        $budgetPlanningDetailTempAttachment = $this->budgetPlanningDetailTempAttachmentRepository->update($input, $id);

        return $this->sendResponse($budgetPlanningDetailTempAttachment->toArray(), 'BudgetPlanningDetailTempAttachment updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/budgetPlanningDetailTempAttachments/{id}",
     *      summary="deleteBudgetPlanningDetailTempAttachment",
     *      tags={"BudgetPlanningDetailTempAttachment"},
     *      description="Delete BudgetPlanningDetailTempAttachment",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetPlanningDetailTempAttachment",
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
        /** @var BudgetPlanningDetailTempAttachment $budgetPlanningDetailTempAttachment */
        $budgetPlDetTempAttachment = $this->budgetPlanningDetailTempAttachmentRepository->findWithoutFail($id);

        if (empty($budgetPlDetTempAttachment)) {
            return $this->sendError(trans('custom.budget_planning_detail_template_attachment_not_found'));
        }

        /*try{
            $deleteRecord = $this->budgetPlanningDetailTempAttachmentRepository->deleteAttachment($id, $budgetPlDetTempAttachment);
            if(!$deleteRecord['success']){
                return $this->sendError($deleteRecord['message'] ?? 'Failed to delete attachment');
            }
            return $this->sendResponse([], trans('custom.Budget_planning_detail_template_deleted_successfully'));
        } catch (\Exception $ex){
            return $this->sendError('Unexpected Error: ' . $ex->getMessage());
        }*/
    }
    public function downloadBudgetTempAttachment(Request $request){
        try{
            $attachmentId = $request->input('id');

            $attachment = $this->budgetPlanningDetailTempAttachmentRepository->findWithoutFail($attachmentId);
            if (empty($attachment)) {
                return $this->sendError(trans('custom.attachment_not_found'), 404);
            }

            $entryData = BudgetPlanningDetailTempAttachment::getBudgetTempEntryData($attachment->entry_id);
            if(empty($entryData)){
                return $this->sendError('Budget planning detail template entry not found', 404);
            }

            $departmentBudget = DepartmentBudgetPlanningDetail::getBudgetPlaningCompany($entryData->budget_detail_id);
            if(!$departmentBudget){
                return $this->sendError('Budget department not found');
            }

            $companySystemID = $departmentBudget->departmentBudgetPlanning->masterBudgetPlannings->companySystemID ?? 1;

            // Determine disk based on company policy
            $disk = Helper::policyWiseDisk($companySystemID, 'public');

            if (!Storage::disk($disk)->exists($attachment->file_path)) {
                return $this->sendError(trans('custom.file_not_found_on_storage'));
            }

            // Get file contents
            $fileContents = Storage::disk($disk)->get($attachment->file_path);

            return response($fileContents, 200)
                ->header('Content-Type', $attachment->file_type)
                ->header('Content-Disposition', 'attachment; filename="' . $attachment->original_file_name . '"');

        } catch (\Exception $exception){
            return $this->sendError('Error downloading attachment - ' . $exception->getMessage(), 500);
        }
    }

    public function deleteTemplateDetailAttachment(Request $request)
    {
        $input = $request->all();

        $budgetPlDetTempAttachment = $this->budgetPlanningDetailTempAttachmentRepository->findWithoutFail($input['id']);

        if (empty($budgetPlDetTempAttachment)) {
            return $this->sendError(trans('custom.budget_planning_detail_template_attachment_not_found'));
        }

        try{
            $deleteRecord = $this->budgetPlanningDetailTempAttachmentRepository->deleteAttachment($input['id'], $budgetPlDetTempAttachment);
            if(!$deleteRecord['success']){
                return $this->sendError($deleteRecord['message'] ?? 'Failed to delete attachment');
            }
            return $this->sendResponse([], trans('custom.Budget_planning_detail_template_deleted_successfully'));
        } catch (\Exception $ex){
            return $this->sendError('Unexpected Error: ' . $ex->getMessage());
        }
    }
}
