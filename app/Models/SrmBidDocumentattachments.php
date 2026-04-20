<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="SrmBidDocumentattachments",
 *      required={""},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="companyID",
 *          description="companyID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentID",
 *          description="documentID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="attachmentDescription",
 *          description="attachmentDescription",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="originalFileName",
 *          description="originalFileName",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="myFileName",
 *          description="myFileName",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="path",
 *          description="path",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="sizeInKbs",
 *          description="sizeInKbs",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SrmBidDocumentattachments extends Model
{

    public $table = 'srm_bid_documentattachments';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'tender_id',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'negotiation_id',
        'negotiation_code',
        'round_no',
        'attachmentDescription',
        'originalFileName',
        'myFileName',
        'path',
        'sizeInKbs',
        'type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'integer',
        'documentSystemCode' => 'integer',
        'negotiation_id' => 'integer',
        'round_no' => 'integer',
        'attachmentDescription' => 'string',
        'originalFileName' => 'string',
        'myFileName' => 'string',
        'path' => 'string',
        'sizeInKbs' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'tender_id' => 'required',
        'companySystemID' => 'required',
        'companyID' => 'required',
        'documentSystemID' => 'required',
        'documentID' => 'required',
        'documentSystemCode' => 'required'
    ];

    public static function getOriginalByContext(int $companySystemId, int $documentSystemId, int $tenderId, int $type)
    {
        return self::where('companySystemID', $companySystemId)
            ->where('documentSystemID', $documentSystemId)
            ->where('documentSystemCode', $tenderId)
            ->where('type', $type)
            ->whereNull('round_no')
            ->orderByDesc('id')
            ->first();
    }

    public static function getOriginalByContextAll(int $companySystemId, int $documentSystemId, int $tenderId, int $type)
    {
        return self::where('companySystemID', $companySystemId)
            ->where('documentSystemID', $documentSystemId)
            ->where('documentSystemCode', $tenderId)
            ->where('type', $type)
            ->whereNull('round_no')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function getLatestAttachmentByRounds(int $companySystemId, int $documentSystemId, int $tenderId, int $type)
    {
        return self::where('companySystemID', $companySystemId)
            ->where('documentSystemID', $documentSystemId)
            ->where('documentSystemCode', $tenderId)
            ->where('type', $type)
            ->whereNotNull('round_no')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('round_no')
            ->map(function ($rows) {
                return $rows->first();
            });
    }

    public static function getAttachmentsByRounds(int $companySystemId, int $documentSystemId, int $tenderId, int $type)
    {
        return self::where('companySystemID', $companySystemId)
            ->where('documentSystemID', $documentSystemId)
            ->where('documentSystemCode', $tenderId)
            ->where('type', $type)
            ->whereNotNull('round_no')
            ->orderBy('round_no', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('round_no');
    }
}
