<?php

namespace App\Repositories;

use App\Http\Requests\SRM\DocumentMasterRemoveRequest;
use App\Http\Requests\SRM\DocumentMasterRequest;
use App\Models\Company;
use App\Models\SRMDocumentMaster;
use App\Models\TenderDocumentTypes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use App\helper\FileSecurityValidator;
use Illuminate\Support\Facades\Storage;
use App\helper\Helper;

/**
 * Class SRMDocumentMasterRepository
 * @package App\Repositories
 * @version January 19, 2026, 8:40 am +04
 *
 * @method SRMDocumentMaster findWithoutFail($id, $columns = ['*'])
 * @method SRMDocumentMaster find($id, $columns = ['*'])
 * @method SRMDocumentMaster first($columns = ['*'])
 */
class SRMDocumentMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'document_name',
        'document_area',
        'envelope_type',
        'default_to_tender',
        'default_to_rfx',
        'path',
        'original_file_name',
        'size_in_kbs',
        'company_system_id',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SRMDocumentMaster::class;
    }

    public function getAllDocumentMaster(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $params = [
            'data' => $input,
            'isDataTable' => true
        ];

        $documentMasterData = SRMDocumentMaster::getAllDocumentMaster($params);
        return \DataTables::of($documentMasterData)
            ->removeColumn('id')
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->filter(function ($query) use ($input) {
                if (request()->has('search') && !empty(request('search')['value'])) {
                    $search = request('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('document_name', 'LIKE', "%{$search}%");
                    });
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getDocumentFormData()
    {
        $documentAreaIds = [54, 1, 2];

        $documentArea = TenderDocumentTypes::getDocumentAreaByIds($documentAreaIds);
        $envelopeDrop = [
            ['id' => 1, 'description' => 'Commercial'],
            ['id' => 2, 'description' => 'Technical'],
            ['id' => 3, 'description' => 'Common'],
        ];
        return [
            'docArea' => $documentArea,
            'envelope' => $envelopeDrop
        ];
    }

    public function getDocumentMaster($uuid)
    {
        return SRMDocumentMaster::getDocumentMasterByUuid($uuid);
    }

    public function documentMasterCrud(DocumentMasterRequest $request)
    {
        $data = $request->all();

        try {
            $attachDocument = self::getAttachDocument($data['attachment']);
            if (!$attachDocument['success']) {
                return $attachDocument;
            }

            return DB::transaction(function () use ($data) {

                $companyData = Company::find($data['company_system_id']);

                if (isset($data['uuid']) && !empty($data['uuid'])) {
                    // UPDATE existing document
                    $document = SRMDocumentMaster::where('uuid', $data['uuid'])->firstOrFail();
                    $message = 'Document master updated successfully';
                } else {
                    // CREATE new document
                    $document = new SRMDocumentMaster();
                    $document->uuid = Str::random(36);
                    $message = 'Document master created successfully';
                }

                // Update all fields
                $document->document_name = $data['document_name'];
                $document->document_area = $data['document_area'];
                $document->envelope_type = $data['envelope_type'] ?? null;
                $document->default_to_tender = $data['default_to_tender'];
                $document->default_to_rfx = $data['default_to_rfx'];
                $document->company_system_id = $data['company_system_id'];


                if (isset($data['original_file_name']) && empty($data['original_file_name'])) {
                    if (!empty($document->path) && Storage::disk(Helper::policyWiseDisk($data['company_system_id'], 'public'))->exists($document->path)) {
                        Storage::disk(Helper::policyWiseDisk($data['company_system_id'], 'public'))->delete($document->path);
                    }

                    $document->original_file_name = null;
                    $document->size_in_kbs = null;
                    $document->my_file_name = null;
                    $document->path = null;
                }

                if (!empty($data['attachment'])) {
                    $document->original_file_name = $data['attachment']['originalFileName'];
                    $document->size_in_kbs = $data['attachment']['sizeInKbs'];
                    $document->my_file_name = $companyData['CompanyID'] . '_' . Str::snake($document->document_name) . '_' . now()->timestamp;

                    $path = $companyData['CompanyID'] . '/SRM/Masters/DocumentMaster/' . $document->my_file_name;
                    $decodeFile = base64_decode($data['attachment']['file']);

                    Storage::disk(Helper::policyWiseDisk($data['company_system_id'], 'public'))
                        ->put($path, $decodeFile);

                    $document->path = $path;
                }

                $document->save();

                return ['success' => true, 'message' => $message];
            });

        } catch (\Exception $exception) {
            return [
                'success' => false,
                'message' => 'Error saving document: ' . $exception->getMessage()
            ];
        }
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

        $this->validateFileSize($attachment);
        if (!$blockExtensionValidation['success']) {
            return $blockExtensionValidation;
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

    public function getTenderDocumentMaster(Request $request)
    {
        $input = $request->all();
        $params = [
            'uuid' => $input['uuid'],
            'recordType' => 'single'
        ];

        $documentMasterData = SRMDocumentMaster::getAllDocumentMaster($params);
        if (empty($documentMasterData)) {
            return [
                'success' => false,
                'message' => 'Records not found'
            ];
        }
        return [
            'success' => true,
            'message' => 'Records retrieved successfully',
            'data' => $documentMasterData
        ];
    }

    public function removeDocMasterDelete(DocumentMasterRemoveRequest $request)
    {
        $input = $request->all();

        try {
            return DB::transaction(function () use ($input) {

                $deleted = SRMDocumentMaster::where('uuid', $input['uuid'])->delete();

                if ($deleted) {
                    return [
                        'success' => true,
                        'message' => 'Document record deleted successfully.'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Document record not found.'
                ];
            });
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ];
        }
    }
}
