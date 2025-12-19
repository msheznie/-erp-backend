<?php

namespace App\Repositories;

use App\Models\Alert;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AlertRepository
 * @package App\Repositories
 * @version April 26, 2018, 4:02 am UTC
 *
 * @method Alert findWithoutFail($id, $columns = ['*'])
 * @method Alert find($id, $columns = ['*'])
 * @method Alert first($columns = ['*'])
*/
class AlertRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'empID',
        'docID',
        'docApprovedYN',
        'docSystemCode',
        'docCode',
        'alertMessage',
        'alertDateTime',
        'alertViewedYN',
        'alertViewedDateTime',
        'empName',
        'empEmail',
        'ccEmailID',
        'emailAlertMessage',
        'isEmailSend',
        'attachmentFileName',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Alert::class;
    }
}
