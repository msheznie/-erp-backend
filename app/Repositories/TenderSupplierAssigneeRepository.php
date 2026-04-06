<?php

namespace App\Repositories;

use App\helper\Helper;
use Carbon\Carbon;
use App\Models\TenderMaster;
use App\Models\SRMScenarioDetails;
use App\Models\SRMScenarioMaster;
use App\Models\SRMScenarioAttachments;
use App\Models\SupplierRegistrationLink;
use App\Models\TenderSupplierAssignee;
use App\Models\TenderSupplierAssigneeEditLog;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use App\Services\SrmDocumentModifyService;
use mysql_xdevapi\Exception;

/**
 * Class TenderSupplierAssigneeRepository
 * @package App\Repositories
 * @version June 2, 2022, 12:07 pm +04
 *
 * @method TenderSupplierAssignee findWithoutFail($id, $columns = ['*'])
 * @method TenderSupplierAssignee find($id, $columns = ['*'])
 * @method TenderSupplierAssignee first($columns = ['*'])
*/
class TenderSupplierAssigneeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'registration_link_id',
        'supplier_assigned_id',
        'supplier_email',
        'supplier_name',
        'tender_master_id',
        'updated_by',
        'mail_sent'
    ];
    protected $srmDocumentModifyService;
    public function __construct(Application $app, SrmDocumentModifyService $srmDocumentModifyService)
    {
        parent::__construct($app);
        $this->srmDocumentModifyService = $srmDocumentModifyService;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderSupplierAssignee::class;
    }

    public function deleteAllAssignedSuppliers($input) {
        try{
            return DB::transaction(function () use ($input) {
                $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($input['tenderId']);
                if($requestData['enableRequestChange']){
                    TenderSupplierAssigneeEditLog::where('tender_master_id',$input['tenderId'])
                        ->where('company_id',$input['companySystemId'])
                        ->where('mail_sent',0)
                        ->whereNotIn('supplier_assigned_id', $input['removedSupplierAssignedIds'])
                        ->where('version_id', $requestData['versionID'])
                        ->where('is_deleted', 0)
                        ->update(['is_deleted' => 1]);
                } else {
                    TenderSupplierAssignee::where('tender_master_id',$input['tenderId'])
                        ->where('company_id',$input['companySystemId'])
                        ->where('mail_sent',0)
                        ->whereNotIn('supplier_assigned_id', $input['removedSupplierAssignedIds'])
                        ->delete();
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.deleted_successfully')];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }

    
    public function deleteAllSelectedSuppliers($input) {
        try{
            return DB::transaction(function () use ($input) {
                $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($input['tenderId']);
                if($requestData['enableRequestChange']){
                    TenderSupplierAssigneeEditLog::where('tender_master_id', $input['tenderId'])
                        ->where('company_id', $input['companySystemId'])
                        ->where('version_id', $requestData['versionID'])
                        ->where('is_deleted', 0)
                        ->whereIn('amd_id', $input['deleteList'])
                        ->update(['is_deleted' => 1, 'updated_at' => now()]);
                } else {
                    TenderSupplierAssignee::where('tender_master_id',$input['tenderId'])->where('company_id',$input['companySystemId'])->whereIn('id',$input['deleteList'])->delete();
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.deleted_successfully')];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public function supplierAssignCRUD($input)
    {
        try{
            return DB::transaction(function () use ($input) {
                $name = $input['name'];
                $email = $input['email'];
                $regNo = $input['regNo'];
                $tenderId = $input['tenderId'];
                $companySystemID = $input['companySystemID'];
                $employee = Helper::getEmployeeInfo();
                $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderId);
                $validateFields = $this->validateFileds($input);

                if(!$validateFields['status']){
                    return ['success' => false, 'message' => $validateFields['message'], 'code' => $validateFields['code']];
                }

                $data = [
                    'tender_master_id' => $tenderId,
                    'supplier_name' => $name,
                    'supplier_email' => $email,
                    'registration_number' => $regNo,
                    'created_by' => $employee->employeeSystemID,
                    'company_id' => $companySystemID,
                    'created_at' => now()
                ];
                if($requestData['enableRequestChange']){
                    $data['id'] = null;
                    $data['level_no'] = 0;
                    $data['version_id'] = $requestData['versionID'];

                    $result = TenderSupplierAssigneeEditLog::create($data);
                } else {
                    $result = TenderSupplierAssignee::create($data);
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_saved'), 'data' => $result];
            });
        } catch(\Exception $ex){
            return ['success' => false, 'message' => trans('srm_tender_rfx.unexpected_error', ['message' => $ex->getMessage()])];
        }
    }
    public function validateFileds($input){
        $validator = \Validator::make($input, [
            'email' => 'required|email|max:255',
            'name' => 'required|max:255',
            'regNo' => 'required|max:255',
        ],[
            'email.required' => trans('srm_tender_rfx.email_required'),
            'email.email'    => trans('srm_tender_rfx.email_invalid'),
            'email.max'      => trans('srm_tender_rfx.email_max'),
            'email.unique'   => trans('srm_tender_rfx.email_exists'),
            'name.required'  => trans('srm_tender_rfx.name_required'),
            'name.max'       => trans('srm_tender_rfx.name_max'),
            'regNo.required' => trans('srm_tender_rfx.regNo_required'),
            'regNo.max'      => trans('srm_tender_rfx.regNo_max'),
        ]);
        if ($validator->fails()) {
            return ['status' => false, 'message' => $validator->messages(), 'code' => 422];
        }


        $email = $input['email'];
        $regNo = $input['regNo'];
        $companyId =$input['companySystemID'];

        $supplierRegLink = SupplierRegistrationLink::select('id','email','registration_number')
            ->where('company_id',$companyId)
            ->where('STATUS',1)
            ->get();

        $emails = $supplierRegLink->pluck('email')->toArray();
        $registrationNumbers = $supplierRegLink->pluck('registration_number')->toArray();

        if (in_array($email, $emails)) {
            return ['status' => false, 'message' => trans('srm_tender_rfx.email_exists'),'code' => 402];
        }

        if (in_array($regNo, $registrationNumbers)) {
            return ['status' => false, 'message' => trans('srm_tender_rfx.regNo_exists'),'code' => 402];
        }


        return ['status' => true, 'message' => trans('srm_tender_rfx.success')];

    }
    public static function deleteAssignedUsers($id, $versionID, $editOrAmend){
        try{
            return DB::transaction(function () use ($id, $versionID, $editOrAmend) {
                $supplier = $editOrAmend ?
                    TenderSupplierAssigneeEditLog::find($id) :
                    TenderSupplierAssignee::find($id);

                if(empty($supplier)){
                    return ['success' => false, 'message' => trans('srm_tender_rfx.assigned_supplier_not_found')];
                }

                if($editOrAmend){
                    TenderSupplierAssigneeEditLog::where('amd_id', $id)->where('version_id', $versionID)->update(['is_deleted' => 1, 'updated_at' => now()]);
                } else {
                    TenderSupplierAssignee::where('id', $id)->delete();
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_deleted')];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => trans('srm_tender_rfx.unexpected_error', ['message' => $ex->getMessage()])];
        }
    }

    public function getInvitationEmailData(array $input): array
    {
        $tenderId = isset($input['tenderId']) ? (int) $input['tenderId'] : null;
        $uuid = $input['uuid'] ?? null;
        $companySystemId = (int) ($input['companySystemId'] ?? $input['companyId'] ?? 0);
        $rfx = !empty($input['rfx']);

        $tenderQuery = TenderMaster::with('currency')
            ->select('id', 'title', 'description', 'document_type', 'tender_code', 'bid_submission_closing_date', 'currency_id', 'document_system_id', 'company_id');
        if ($tenderId) {
            $tenderQuery->where('id', $tenderId);
        } elseif (!empty($uuid)) {
            $tenderQuery->where('uuid', $uuid);
        } else {
            return ['success' => false, 'code' => 422, 'message' => 'Tender is required'];
        }
        if ($companySystemId > 0) {
            $tenderQuery->where('company_id', $companySystemId);
        }
        $tenderMaster = $tenderQuery->first();
        if (!$tenderMaster) {
            return ['success' => false, 'code' => 404, 'message' => trans('srm_tender_rfx.tender_not_found')];
        }
        if ($companySystemId <= 0) {
            $companySystemId = (int) $tenderMaster->company_id;
        }

        $loginUrl = env('SRM_LINK');
        $docType = 'Tender';
        if ($rfx) {
            switch ($tenderMaster->document_type) {
                case 1:
                    $docType = 'RFQ';
                    break;
                case 2:
                    $docType = 'RFI';
                    break;
                case 3:
                    $docType = 'RFP';
                    break;
                default:
                    $docType = 'RFX';
                    break;
            }
        }

        $documentId = $rfx ? 113 : 108;
        $scenarioCode = $rfx ? 'RFX_INVITATION' : 'TENDER_INVITATION';
        $scenarioMasterId = SRMScenarioMaster::getScenarioMasterIdByDocumentAndCode($documentId, $scenarioCode, null);
        $details = null;
        if ($scenarioMasterId) {
            $details = SRMScenarioDetails::getScenarioDetailsById([
                'scenarioId' => $scenarioMasterId,
                'companyId' => $companySystemId,
            ]);
        }

        $subjectDefault = $rfx ? 'Invitation for RFX' : 'Invitation for Tender';
        $bodyDefault = $this->getInvitationDefaultBodyForPreview($rfx);
        $subject = $details && !empty($details->email_subject) ? $details->email_subject : $subjectDefault;
        $body = $details && !empty($details->email_body) ? $details->email_body : $bodyDefault;
        $ccEmails = $details && !empty($details->cc_emails) && is_array($details->cc_emails) ? $details->cc_emails : [];
        $attachments = [];
        if ($details && $details->relationLoaded('attachments') && $details->attachments) {
            foreach ($details->attachments as $attachment) {
                $attachments[] = [
                    'attachmentID' => $attachment->id,
                    'originalFileName' => $attachment->original_file_name ?? $attachment->my_file_name ?? '',
                    'path' => $attachment->path ?? '',
                ];
            }
        }

        $context = $this->buildInvitationContext($tenderMaster, $loginUrl, $docType, '{supplierName}');
        $subject = $this->replaceInvitationPlaceholders($subject, $context, false);
        $body = $this->replaceInvitationPlaceholders($body, $context, false);

        return [
            'success' => true,
            'data' => [
                'email_subject' => $subject,
                'email_body' => $body,
                'cc_emails' => $ccEmails,
                'attachments' => $attachments,
                'scenario_code' => $scenarioCode,
            ]
        ];
    }

    public function getResolvedInvitationEmailContent($tenderMaster, string $loginUrl, string $docType, string $supplierName, ?array $customInvitationEmail, bool $rfx, int $type): array
    {
        $alertMessageDefault = "Invitation for " . $docType . " ";
        $bodyDefault = '';
        if ($type == 1) {
            if ($rfx) {
                $alertMessageDefault = "Invitation for RFX ";
                $bodyDefault = "Dear Supplier," . "<br /><br />" . "
            You are invited to participate in a new ".$docType.", " . $tenderMaster['title'] . ".
            Please find the link below to login to the supplier portal. " . "<br /><br />" . "Click Here: " . "</b><a href='" . $loginUrl . "'>" . $loginUrl . "</a><br /><br />" . " Thank You" . "<br />";
            } else {
                $bodyDefault = "Dear Supplier," . "<br /><br />" . "
            We trust this message finds you well." . "<br /><br />" . "
            We are in the process of inviting reputable suppliers to participate in a ".$docType." for an upcoming project. Your company's outstanding reputation and capabilities have led us to extend this invitation to you." . "<br /><br />" . "
            If your company is interested in participating in the ".$docType." process, please click on the link below." . "<br /><br />" . "
            " . "<b>" . " ".$docType." Title :" . "</b> " . $tenderMaster['title'] . "<br /><br />" . "
            " . "<b>" . " ".$docType." Description :" . "</b> " . $tenderMaster['description'] . "<br /><br />" . "
            " . "<b>" . "Link :" . "</b> " . "<a href='" . $loginUrl . "'>" . $loginUrl . "</a><br /><br />" . "
            If you have any initial inquiries or require further information, feel free to reach out to us." . "<br /><br />" . "
            Thank you for considering this invitation. We look forward to the possibility of collaborating with your esteemed company." . "<br /><br />";
            }
        } else {
            $bodyDefault = "Dear Supplier," . "<br /><br />" . "
            You are invited to participate in a new ".$docType.", " . $tenderMaster['title'] . ".
            Please find the below link to register at supplier portal. It will expire in 96 hours. " . "<br /><br />" . "Click Here: " . "</b><a href='" . $loginUrl . "'>" . $loginUrl . "</a><br /><br />" . " Thank You" . "<br />";
        }

        $context = $this->buildInvitationContext($tenderMaster, $loginUrl, $docType, $supplierName);
        $subject = !empty($customInvitationEmail['email_subject'])
            ? $this->replaceInvitationPlaceholders($customInvitationEmail['email_subject'], $context, true)
            : $alertMessageDefault;
        $bodyTemplate = !empty($customInvitationEmail['email_body']) ? $customInvitationEmail['email_body'] : $bodyDefault;
        $body = $this->replaceInvitationPlaceholders($bodyTemplate, $context, true);
        $ccEmail = isset($customInvitationEmail['cc_emails']) && is_array($customInvitationEmail['cc_emails']) ? $customInvitationEmail['cc_emails'] : [];

        return [
            'subject' => $subject,
            'body' => $body,
            'cc' => $ccEmail
        ];
    }

    public function resolveInvitationAttachmentUrls($customInvitationEmail, int $companySystemId): array
    {
        $attachmentIds = [];
        if (isset($customInvitationEmail['attachment_ids']) && is_array($customInvitationEmail['attachment_ids'])) {
            $attachmentIds = $customInvitationEmail['attachment_ids'];
        }
        if (empty($attachmentIds)) {
            return [];
        }
        $attachments = SRMScenarioAttachments::whereIn('id', $attachmentIds)
            ->where('company_system_id', $companySystemId)
            ->get(['path']);
        $urls = [];
        foreach ($attachments as $attachment) {
            if (!empty($attachment->path)) {
                $url = Helper::getFileUrlFromS3($attachment->path);
                if ($url) {
                    $urls[] = $url;
                }
            }
        }
        return $urls;
    }

    private function buildInvitationContext($tenderMaster, $loginUrl, $docType, $supplierName = ''): array
    {
        $currency = optional($tenderMaster->currency)->CurrencyCode ?? '';
        $bidSubmissionDate = '';
        if (!empty($tenderMaster->bid_submission_closing_date)) {
            $bidSubmissionDate = Carbon::parse($tenderMaster->bid_submission_closing_date)->format('d/m/Y');
        }
        return [
            'supplierName' => $supplierName,
            'SupplierName' => $supplierName,
            'tenderCode' => $tenderMaster->tender_code ?? '',
            'tenderTitle' => $tenderMaster->title ?? '',
            'rfxCode' => $tenderMaster->tender_code ?? '',
            'rfxTitle' => $tenderMaster->title ?? '',
            'bidSubmissionDate' => $bidSubmissionDate,
            'bidSubmisionDate' => $bidSubmissionDate,
            'currency' => $currency,
            'srmLink' => $loginUrl,
            'documentType' => $docType,
        ];
    }

    private function replaceInvitationPlaceholders(string $content, array $context, bool $replaceSupplierName = true): string
    {
        $replace = [
            '{tenderCode}' => $context['tenderCode'] ?? '',
            '{tenderTitle}' => $context['tenderTitle'] ?? '',
            '{rfxCode}' => $context['rfxCode'] ?? '',
            '{rfxTitle}' => $context['rfxTitle'] ?? '',
            '{bidSubmissionDate}' => $context['bidSubmissionDate'] ?? '',
            '{bidSubmisionDate}' => $context['bidSubmisionDate'] ?? '',
            '{currency}' => $context['currency'] ?? '',
            '{srmLink}' => $context['srmLink'] ?? '',
            '{documentType}' => $context['documentType'] ?? '',
        ];
        if ($replaceSupplierName) {
            $replace['{supplierName}'] = $context['supplierName'] ?? '';
            $replace['{SupplierName}'] = $context['SupplierName'] ?? '';
        }
        return str_replace(array_keys($replace), array_values($replace), $content);
    }

    private function getInvitationDefaultBodyForPreview(bool $rfx): string
    {
        if ($rfx) {
            return 'Dear {supplierName},<br /><br />
            You are invited to participate in a new RFX, {rfxTitle}.<br />
            <b>RFX Code :</b> {rfxCode}<br /><br />
            <b>Bid Submission Date :</b> {bidSubmissionDate}<br /><br />
            <b>Currency :</b> {currency}<br /><br />
            Please find the link below to login to the supplier portal.<br /><br />
            Click Here: <a href=\'{srmLink}\'>{srmLink}</a><br /><br />
            Thank You<br />';
        }

        return 'Dear {supplierName},<br /><br />
        We trust this message finds you well.<br /><br />
        We are in the process of inviting reputable suppliers to participate in a Tender for an upcoming project. Your company\'s outstanding reputation and capabilities have led us to extend this invitation to you.<br /><br />
        If your company is interested in participating in the Tender process, please click on the link below.<br /><br />
        <b>Tender Code :</b> {tenderCode}<br /><br />
        <b>Tender Title :</b> {tenderTitle}<br /><br />
        <b>Bid Submission Date :</b> {bidSubmissionDate}<br /><br />
        <b>Currency :</b> {currency}<br /><br />
        <b>Link :</b> <a href=\'{srmLink}\'>{srmLink}</a><br /><br />
        Thank you for considering this invitation. We look forward to the possibility of collaborating with your esteemed company.<br /><br />';
    }
}
