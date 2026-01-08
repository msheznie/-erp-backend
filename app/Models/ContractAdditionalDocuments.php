<?php

namespace App\Models;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

class ContractAdditionalDocuments extends Model
{
    use Compoships;
    public $table = 'cm_contract_additional_document';

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
        'expiryDate',
        'companySystemID',
        'created_by',
        'updated_by',
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
        'expiryDate' => 'string',
        'companySystemID' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'is_editable' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
    public function documentMaster() {
        return $this->belongsTo(ContractDocumentMaster::class, 'documentType', 'id');
    }
    public function attachment(){
        return $this->belongsTo('App\Models\DocumentAttachments', ['documentMasterID', 'id'],
            ['documentSystemID', 'documentSystemCode']);
    }

    public static function additionalDocumentList($selectedCompanyID, $contractID)
    {
        $additionalDocumentsQuery = ContractAdditionalDocuments::select(
            'id', 'documentMasterID', 'uuid', 'documentType', 'documentName',
            'documentDescription', 'expiryDate', 'is_editable')
            ->with([
                'documentMaster' => function ($query)
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
            ->orderBy('id', 'desc')
            ->get();

        $contractDocumentsQuery = ContractDocument::select(
            'id','documentMasterID','uuid', 'documentType', 'documentName',
            'documentDescription','documentExpiryDate as expiryDate', 'is_editable')
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
                'companySystemID' => $selectedCompanyID,
                'followingRequest' => 0
            ])
            ->orderBy('id', 'desc')
            ->get();

        return $additionalDocumentsQuery
            ->merge($contractDocumentsQuery)
            ->map(function ($doc) {
                $doc->attachment = [
                    'attachmentID' => $doc->attachmentID,
                    'myFileName' => $doc->myFileName,
                    'originalFileName' => $doc->originalFileName,
                    'path' => $doc->path,
                    'documentSystemID' => $doc->documentSystemID,
                    'documentSystemCode' => $doc->documentSystemCode,
                ];

                unset(
                    $doc->attachmentID,
                    $doc->myFileName,
                    $doc->originalFileName,
                    $doc->path,
                    $doc->documentSystemID,
                    $doc->documentSystemCode
                );

                return $doc;
            })
            ->sortByDesc('id')
            ->values();
    }

}
