<?php

namespace App\Models;

use Illuminate\Support\Str;
use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="SRMDocumentMaster",
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
 *          property="document_name",
 *          description="document_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="document_area",
 *          description="document_area",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="envelope_type",
 *          description="envelope_type",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="default_to_tender",
 *          description="default_to_tender",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="default_to_rfx",
 *          description="default_to_rfx",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="path",
 *          description="path",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="original_file_name",
 *          description="original_file_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="size_in_kbs",
 *          description="size_in_kbs",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="company_system_id",
 *          description="company_system_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="created_by",
 *          description="created_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
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
class SRMDocumentMaster extends Model
{

    public $table = 'srm_document_master';
    protected $guarded = ['uuid'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public $fillable = [
        'uuid',
        'document_name',
        'document_area',
        'envelope_type',
        'default_to_tender',
        'default_to_rfx',
        'path',
        'original_file_name',
        'my_file_name',
        'size_in_kbs',
        'company_system_id',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'document_name' => 'required',
    ];


    public static function getAllDocumentMaster($params)
    {
        // Base columns
        $columns = [
            'uuid',
            'document_name',
            'document_area',
            'envelope_type',
            'default_to_tender',
            'default_to_rfx',
            'original_file_name'
        ];

        if (!empty($params['isDataTable']) && $params['isDataTable'] === true) {
            $columns[] = 'id';
        }

        if (!empty($params['masterData']) && $params['masterData'] === true) {
            $columns = array_merge($columns, ['path', 'original_file_name', 'my_file_name', 'size_in_kbs','id']);
        }

        $query = self::select($columns);

        // DataTable request
        if (!empty($params['isDataTable']) && $params['isDataTable'] === true) {
            return $query;
        }

        // Single record request
        if (!empty($params['recordType']) && $params['recordType'] == 'single' && !empty($params['uuid'])) {
            return $query->where('uuid', $params['uuid'])->first();
        }

        if (!empty($params['masterData']) && $params['masterData'] === true) {
            $query->where(function($q) use ($params) {
                if ($params['docSystemId'] == 108) {
                    $q->where('default_to_tender', 1);
                } else {
                    $q->where('default_to_rfx', 1);
                }
            });

            if(isset($params['ids']))
            {
                $query->whereNotIn('id', $params['ids']);
            }

        }


        // Default: get all
        return $query->get();
    }

    public static function getDocumentMasterByUuid($id)
    {
        return self::where('uuid', $id)->first();
    }

    protected static function boot()
    {
        parent::boot(); // call the Eloquent Model boot

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::random(36);
            }
        });
    }
}
