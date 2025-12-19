<?php

namespace App\Repositories;

use App\Models\CalendarDatesDetailEditLog;
use Illuminate\Contracts\Foundation\Application;
use InfyOm\Generator\Common\BaseRepository;
use App\Repositories\CalendarDatesDetailRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class CalendarDatesDetailEditLogRepository
 * @package App\Repositories
 * @version April 21, 2023, 1:19 pm +04
 *
 * @method CalendarDatesDetailEditLog findWithoutFail($id, $columns = ['*'])
 * @method CalendarDatesDetailEditLog find($id, $columns = ['*'])
 * @method CalendarDatesDetailEditLog first($columns = ['*'])
*/
class CalendarDatesDetailEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'calendar_date_id',
        'company_id',
        'from_date',
        'master_id',
        'modify_type',
        'ref_log_id',
        'tender_id',
        'to_date',
        'version_id'
    ];
    protected $calendarDateDetailRepo;
    public function __construct(CalendarDatesDetailRepository $calendarDatesDetailsRepository, Application $app)
    {
        parent::__construct($app);
        $this->calendarDateDetailRepo = $calendarDatesDetailsRepository;
    }
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CalendarDatesDetailEditLog::class;
    }

    public function saveCalendarDateDetailHistory($tender_id, $version_id = null){
        try {
            return DB::transaction(function () use ($tender_id, $version_id) {
                $calendarDates = $this->calendarDateDetailRepo->getCalendarDateDetailForAmd($tender_id);
                if(!empty($calendarDates)){
                    foreach($calendarDates as $record){
                        $level_no = $this->model->getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['id'] = $record['id'];
                        $recordData['level_no'] = $level_no;
                        $recordData['version_id'] = $version_id;
                        $this->model->create($recordData);
                    }
                }
                return ['success' => false, 'message' => trans('srm_tender_rfx.success')];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
}
