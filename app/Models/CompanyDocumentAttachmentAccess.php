<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="CompanyDocumentAttachmentAccess",
 *      required={""},
 *      @OA\Property(
 *          property="companydocumentattachmentAutoId",
 *          description="companydocumentattachmentAutoId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="iAmountYN",
 *          description="iAmountYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isAttachmentApprovalYN",
 *          description="isAttachmentApprovalYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="isAttachmentYN",
 *          description="isAttachmentYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="isBlockYN",
 *          description="isBlockYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="isCategoryYN",
 *          description="isCategoryYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="isEmailYN",
 *          description="isEmailYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="isSegmentAccessYN",
 *          description="isSegmentAccessYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="isSegmentYN",
 *          description="isSegmentYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CompanyDocumentAttachmentAccess extends Model
{

    public $table = 'companydocumentattachmentaccess';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'documentSystemID',
        'createdDateTime',
        'createdPcID',
        'createdUserID',
        'createdUserSystemID',
        'iAmountYN',
        'isAttachmentApprovalYN',
        'isAttachmentYN',
        'isBlockYN',
        'isCategoryYN',
        'isEmailYN',
        'isSegmentAccessYN',
        'isSegmentYN',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'documentSystemID' => 'integer',
        'createdDateTime' => 'datetime',
        'createdPcID' => 'string',
        'createdUserID' => 'integer',
        'createdUserSystemID' => 'integer',
        'iAmountYN' => 'boolean',
        'id' => 'integer',
        'isAttachmentApprovalYN' => 'boolean',
        'isAttachmentYN' => 'boolean',
        'isBlockYN' => 'boolean',
        'isCategoryYN' => 'boolean',
        'isEmailYN' => 'boolean',
        'isSegmentAccessYN' => 'boolean',
        'isSegmentYN' => 'boolean',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'documentSystemID' => 'required',
        'createdDateTime' => 'required',
        'createdPcID' => 'required',
        'createdUserID' => 'required',
        'createdUserSystemID' => 'required',
        'iAmountYN' => 'required',
        'isAttachmentApprovalYN' => 'required',
        'isAttachmentYN' => 'required',
        'isBlockYN' => 'required',
        'isCategoryYN' => 'required',
        'isEmailYN' => 'required',
        'isSegmentAccessYN' => 'required',
        'isSegmentYN' => 'required',
        'timestamp' => 'required'
    ];

    
}
