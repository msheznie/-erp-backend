<?php

namespace App\Models;

use Eloquent as Model;


class CashFlowSubCategoryGLCode extends Model
{

    public $table = 'cash_flow_subcategory_gl_code';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'cashFlowReportID',
        'pvID',
        'brvID',
        'grvID',
        'deoID',
        'invID',
        'custInvID',
        'pvDetailID',
        'brvDetailID',
        'payDetailAutoID',
        'chartOfAccountID',
        'subCategoryID',
        'localAmount',
        'rptAmount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'chartOfAccountID' => 'integer',
        'subCategoryID' => 'integer',
        'localAmount' => 'double',
        'rptAmount' => 'double'
    ];

}
