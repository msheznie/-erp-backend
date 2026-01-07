<?php

namespace App\Models;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

class ContractDocument extends Model
{
    use Compoships;
    public $table = 'cm_contract_document';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $hidden = ['id'];

    public $fillable = [
        'uuid',
        'contractID',
        'documentMasterID',
        'documentType',
        'documentName',
        'documentDescription',
        'attachedDate',
        'followingRequest',
        'status',
        'receivedBy',
        'receivedDate',
        'receivedFormat',
        'documentVersionNumber',
        'documentResponsiblePerson',
        'documentExpiryDate',
        'returnedBy',
        'returnedDate',
        'returnedTo',
        'companySystemID',
        'created_by',
        'updated_by',
        'attach_after_approval',
        'is_editable'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'contractID' => 'integer',
        'documentMasterID' => 'integer',
        'documentType' => 'integer',
        'documentName' => 'string',
        'documentDescription' => 'string',
        'attachedDate' => 'string',
        'followingRequest' => 'integer',
        'status' => 'integer',
        'receivedBy' => 'string',
        'receivedDate' => 'string',
        'receivedFormat' => 'integer',
        'documentVersionNumber' => 'string',
        'documentResponsiblePerson' => 'string',
        'documentExpiryDate' => 'string',
        'returnedBy' => 'string',
        'returnedDate' => 'string',
        'returnedTo' => 'string',
        'companySystemID' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'attach_after_approval' => 'integer',
        'is_editable' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
    public function documentMaster()
    {
        return $this->belongsTo(ContractDocumentMaster::class, 'documentType', 'id');
    }

    public function attachment()
    {
        return $this->belongsTo('App\Models\DocumentAttachments', ['documentMasterID', 'id'],
            ['documentSystemID', 'documentSystemCode']);
    }

    public static function contractDocuments($selectedCompanyID, $contractID)
    {
        $contractDocuments = ContractDocument::select('id', 'uuid', 'documentType', 'documentName',
            'documentDescription', 'followingRequest', 'attachedDate', 'status','documentExpiryDate',
            'attach_after_approval', 'is_editable', 'documentMasterID')
            ->with(['documentMaster' => function ($query)
            {
                $query->select('id', 'uuid', 'documentType');
            },
                'attachment' => function ($query)
                {
                    $query->select('attachmentID', 'myFileName', 'documentSystemID',
                        'documentSystemCode','originalFileName','path');
                }
            ])
            ->where([
                'contractID' => $contractID,
                'companySystemID' => $selectedCompanyID
            ])
            ->orderBy('id', 'desc');

        return $contractDocuments
            ->get()
            ->map(function ($doc) {
                $doc->attachment = [
                    'attachmentID' => $doc->attachmentID,
                    'myFileName' => $doc->myFileName,
                    'originalFileName' => $doc->originalFileName,
                    'path' => $doc->path,
                    'documentSystemID' => $doc->documentSystemID,
                    'documentSystemCode' => $doc->documentSystemCode,
                ];

                unset($doc->attachmentID, $doc->myFileName, $doc->originalFileName, $doc->path, $doc->documentSystemID, $doc->documentSystemCode);

                return $doc;
            });
    }
}
