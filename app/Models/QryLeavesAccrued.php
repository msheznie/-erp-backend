<?php
/**
 * =============================================
 * -- File Name : QryLeavesAccrued.php
 * -- Project Name : ERP
 * -- Module Name : Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 01 - September 2019
 * -- Description : this is a model for mysqlview
 * -- REVISION HISTORY
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QryLeavesAccrued extends Model
{
    public $table = 'hrms_qry_leavesaccrued';

    public function leaveMaster()
    {
        return $this->belongsTo('App\Models\LeaveMaster','leaveType',  'leavemasterID');
    }
}
