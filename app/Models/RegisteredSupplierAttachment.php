<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="RegisteredSupplierAttachment",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="resgisteredSupplierID",
 *          description="resgisteredSupplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="attachmentDescription",
 *          description="attachmentDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="originalFileName",
 *          description="originalFileName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="myFileName",
 *          description="myFileName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="sizeInKbs",
 *          description="sizeInKbs",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="path",
 *          description="path",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isUploaded",
 *          description="isUploaded",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class RegisteredSupplierAttachment extends Model
{

    public $table = 'registeredsupplierattachment';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';
    public $timestamps = false;


    public $fillable = [
        'resgisteredSupplierID',
        'attachmentDescription',
        'originalFileName',
        'myFileName',
        'sizeInKbs',
        'path',
        'isUploaded'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'resgisteredSupplierID' => 'integer',
        'attachmentDescription' => 'string',
        'originalFileName' => 'string',
        'myFileName' => 'string',
        'sizeInKbs' => 'float',
        'path' => 'string',
        'isUploaded' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
