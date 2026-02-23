<?php

namespace App\Repositories;

use App\helper\FileSecurityValidator;
use App\helper\Helper;
use App\Http\Requests\SRM\SRMScenarioRequest;
use App\Models\Company;
use App\Models\SRMScenarioAttachments;
use App\Models\SRMScenarioDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SRMScenarioDetailsRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'scenario_master_id',
        'email_subject',
        'email_body',
        'company_system_id',
        'created_by',
    ];

    public function model()
    {
        return SRMScenarioDetails::class;
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function saveEmailData(SRMScenarioRequest $request): array
    {
        $data = $request->all();

        try {
            return DB::transaction(function () use ($data) {

                $isNew = false;

                if (empty($data['id'])) {
                    // CREATE
                    $detail = new SRMScenarioDetails();
                    $detail->scenario_master_id = $data['scenarioId'] ?? null;
                    $detail->company_system_id = $data['companySystemID'] ?? null;
                    $detail->created_by = $data['created_by'] ?? null;
                    $isNew = true;
                } else {
                    // UPDATE
                    $detail = SRMScenarioDetails::find($data['id']);

                    if (!$detail) {
                        throw new \Exception('Scenario details not found.');
                    }
                }
                $detail->email_subject = $data['email_subject'] ?? null;
                $detail->email_body = $data['email_body'] ?? null;

                // cc_emails comes from FE as array of strings
                $detail->cc_emails = !empty($data['cc_emails'])
                    ? $data['cc_emails']
                    : null;

                $detail->save();

                if (!empty($data['removed_attachment_ids']) && is_array($data['removed_attachment_ids'])) {

                    $deleteResult = $this->deleteScenarioAttachments(
                        $data['removed_attachment_ids'],
                        $data['companySystemID']
                    );

                    if (!$deleteResult['success']) {
                        return $deleteResult;
                    }
                }

                if (!empty($data['attachments']) && is_array($data['attachments'])) {
                    $attachment = $this->saveScenarioAttachments(
                        $data['attachments'],
                        $detail->id,
                        $data['companySystemID']
                    );
                    if (!$attachment['success']) {
                        return [
                            'success' => false,
                            'message' => $attachment['message'],
                        ];
                    }

                }

                return [
                    'success' => true,
                    'message' => $isNew
                        ? 'Email scenario details created successfully.'
                        : 'Email scenario details updated successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error saving email scenario details: ' . $e->getMessage(),
            ];
        }
    }

    public function getEmailDetailsData(Request $request)
    {
        $scenarioId = $request->input('id');
        $companyId = $request->input('companyId');

        $emailData = SRMScenarioDetails::where('scenario_master_id', $scenarioId)
            ->with(['attachments' => function ($q) {
                $q->select('my_file_name','original_file_name','path','size_in_kbs','scenario_detail_id');
            }])
            ->where('company_system_id', $companyId)
            ->first();

        // If record exists → return it
        if ($emailData) {
            return $emailData;
        }

        // Otherwise create default template
        $defaultBody = $this->getDefaultScenarioTemplate($scenarioId);

        $emailData = SRMScenarioDetails::create([
            'scenario_master_id' => $scenarioId,
            'company_system_id' => $companyId,
            'email_body' => $defaultBody,
            'created_by' => Auth::user()->employee_id ?? null,
        ]);

        return $emailData;
    }

    private function saveScenarioAttachments($attachments, $scenarioDetailId, $companySystemId)
    {
        $storedPaths = [];
        try {
            $companyData = Company::find($companySystemId);
            foreach ($attachments as $attachment) {
                if (!isset($attachment['id'])) {
                    $attachmentValidation = self::getAttachDocument($attachment);
                    if (!$attachmentValidation['success']) {
                        return $attachmentValidation;
                    }

                    if (empty($attachment['file'])) {
                        return [
                            'success' => false,
                            'message' => 'Attachment file is missing.'
                        ];
                    }

                    $decodedFile = base64_decode($attachment['file'], true);

                    if ($decodedFile === false) {
                        return [
                            'success' => false,
                            'message' => 'Invalid base64 attachment.'
                        ];
                    }

                    $extension = $attachment['fileType']
                        ?? pathinfo($attachment['originalFileName'], PATHINFO_EXTENSION);

                    $storedFileName =
                        $companyData['CompanyID'] . '_' .
                        Str::slug(pathinfo($attachment['originalFileName'], PATHINFO_FILENAME)) .
                        '_' . now()->timestamp . '.' . $extension;

                    $path = $companyData['CompanyID']
                        . '/SRM/Masters/EmailScenarioAttachments/'
                        . $storedFileName;

                    $disk = Helper::policyWiseDisk($companySystemId, 'public');

                    $test = Storage::disk($disk)->put($path, $decodedFile);
                    if (!$test) {
                        return [
                            'success' => false,
                            'message' => $test
                        ];
                    }

                    $document = new SRMScenarioAttachments();

                    $document->scenario_detail_id = $scenarioDetailId;
                    $document->company_system_id = $companySystemId;
                    $document->created_by = Auth::user()->employee_id;
                    $document->original_file_name = $attachment['originalFileName'] ?? null;
                    $document->size_in_kbs = $attachment['sizeInKbs'] ?? null;
                    $document->my_file_name = $storedFileName;
                    $document->path = $path;
                    if (!$document->save()) {
                        return [
                            'success' => false,
                            'message' => 'Failed to save attachment record'
                        ];
                    }

                    $storedPaths[] = [$disk, $path];
                }

            }
        } catch (\Exception $e) {
            foreach ($storedPaths as [$disk, $path]) {
                Storage::disk($disk)->delete($path);
            }
            return [
                'success' => false,
                'message' => 'Error saving email attachment details: ' . $e->getMessage(),
            ];
        }
        return ['success' => true];
    }

    public function getAttachDocument($attachment)
    {
        $extension = strtolower($attachment['fileType'] ?? '');
        $blockExtensionValidation = $this->validateBlockedExtensions($extension);
        if (!$blockExtensionValidation['success']) {
            return $blockExtensionValidation;
        }

        if (env('FILE_SECURITY_VALIDATION_ENABLED')) {
            $validateFileSecurity = $this->validateFileSecurity($attachment, $extension);
            if (!$validateFileSecurity['success']) {
                return $validateFileSecurity;
            }
        }

        $validateFileSize = $this->validateFileSize($attachment);
        if (!$validateFileSize['success']) {
            return $validateFileSize;
        }

        return ['success' => true];
    }

    protected function validateBlockedExtensions(string $extension): array
    {
        $blocked = config('srm.blocked_extensions', []);

        if (in_array($extension, $blocked, true)) {
            return [
                'success' => false,
                'message' => "The file type '$extension' is not allowed. Please upload a different file format."
            ];
        }

        return ['success' => true];
    }

    protected function validateFileSecurity(Request $request, string $extension)
    {
        if (!FileSecurityValidator::isExtensionAllowed($extension)) {
            return [
                'success' => false,
                'message' => "File type '{$extension}' is not allowed for security reasons",
            ];
        }

        $content = base64_decode($request->file);
        $mimeType = $request->get('mimeType');

        $result = FileSecurityValidator::validateFileContent(
            $content,
            $extension,
            $mimeType
        );

        if (!$result['isValid']) {
            return [
                'success' => false,
                'message' => $result['message']
            ];
        }

        return ['success' => true];
    }

    protected function validateFileSize(array $input)
    {
        if (!empty($input['size']) && $input['size'] > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
            return [
                'success' => false,
                'message' => trans('custom.maximum_allowed_file_size') . ' ' . Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT'))
            ];
        }
        return ['success' => true];
    }

    private function deleteScenarioAttachments(array $attachmentIds, $companySystemId)
    {
        try {

            $disk = Helper::policyWiseDisk($companySystemId, 'public');

            $attachments = SRMScenarioAttachments::whereIn('id', $attachmentIds)->get();

            foreach ($attachments as $attachment) {
                if (!empty($attachment->path)) {
                    Storage::disk($disk)->delete($attachment->path);
                }
                $attachment->delete();
            }

            return ['success' => true];

        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Error deleting attachment: ' . $e->getMessage(),
            ];
        }
    }

    private function getDefaultScenarioTemplate(int $scenarioId): string
    {
        $templates = [

            1 => 'Hi {supplierName},<br><br>
        <div>Based on your final revised proposal submitted on {bidSubmisionDate}, 
        we would like to inform you that we intend to award your company the 
        {tenderCode} | {tenderTitle} {documentType} for 
        <b>{finalCommercialPrice}</b> {currency} with all agreed conditions.<br><br></div>
        <div>We are looking forward to complete the tasks within the time frame that mentioned in the latest proposal</div>',

            2 => 'Hi {supplierName}
        <div>Thank you for your participation in our tender process. 
        We appreciate the effort and time you invested in your proposal. 
        After careful consideration, we regret to inform you that your bid has not been selected for award.</div>
        <div>We received several competitive proposals, making our decision a challenging one. 
        We hope for future opportunities to collaborate.</div>
        <div>Thank you</div>',

            3 => 'Hi {supplierName},<br><br>
        <div>Based on your final revised proposal submitted on {bidSubmisionDate}, 
        we would like to inform you that we intend to award your company the 
        {rfxCode} | {rfxTitle} {documentType} for 
        <b>{finalCommercialPrice}</b> {currency} with all agreed conditions.<br><br></div>
        <div>We are looking forward to complete the tasks within the time frame that mentioned in the latest proposal</div>',

            4 => 'Hi {supplierName},<br><br>
        <div>Based on your final revised proposal submitted on {bidSubmisionDate}, 
        we would like to inform you that we intend to award your company the 
        {tenderCode} | {tenderTitle} {documentType} for 
        <span style="font-weight: bolder;">{finalCommercialPrice}</span> 
        {currency} with all agreed conditions.<br><br></div>
        <div>We are looking forward to complete the tasks within the time frame that mentioned in the latest proposal</div>',

            5 => 'Hi {supplierName}
        <div>Thank you for your participation in our RFX process. 
        We appreciate the effort and time you invested in your proposal. 
        After careful consideration, we regret to inform you that your bid has not been selected for award.</div>
        <div>We received several competitive proposals, making our decision a challenging one. 
        We hope for future opportunities to collaborate.</div>
        <div>Thank you</div>',

            6 => 'Hi {supplierName},<br><br>
        <div>Based on your final revised proposal submitted on {bidSubmisionDate}, 
        we would like to inform you that we intend to award your company the 
        {rfxCode} | {rfxTitle} {documentType} for 
        <b>{finalCommercialPrice}</b> {currency} with all agreed conditions.<br><br></div>
        <div>We are looking forward to complete the tasks within the time frame that mentioned in the latest proposal</div>',
        ];

        return $templates[$scenarioId] ?? '';
    }
}
