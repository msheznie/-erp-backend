<?php
/**
 * =============================================
 * -- File Name : GRVTypes.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Types
 * -- Author : Mohamed Nazir
 * -- Create date : 12 - June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="GRVTypes",
 *      required={""},
 *      @SWG\Property(
 *          property="grvTypeID",
 *          description="grvTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="idERP_GrvTpes",
 *          description="idERP_GrvTpes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="des",
 *          description="des",
 *          type="string"
 *      )
 * )
 */
class GRVTypes extends Model
{

    public $table = 'erp_grvtpes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'idERP_GrvTpes',
        'des'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'grvTypeID' => 'integer',
        'idERP_GrvTpes' => 'string',
        'des' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
