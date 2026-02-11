<?php

namespace App\Repositories;

use App\Models\SRMScenarioMaster;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;

/**
 * Class SRMScenarioMasterRepository
 * @package App\Repositories
 * @version February 6, 2026, 12:22 pm +04
 *
 * @method SRMScenarioMaster findWithoutFail($id, $columns = ['*'])
 * @method SRMScenarioMaster find($id, $columns = ['*'])
 * @method SRMScenarioMaster first($columns = ['*'])
 */
class SRMScenarioMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'document_id',
        'email_scenario_code',
        'email_scenario_name',
        'company_id',
        'is_active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SRMScenarioMaster::class;
    }

    public function getAllEmailMaster(Request $request)
    {
        $input = $request->all();

        $EmailMasterData = SRMScenarioMaster::getAllEmailMaster();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        return \DataTables::of($EmailMasterData)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->filter(function ($query) use ($input) {
                if (request()->has('search') && !empty(request('search')['value'])) {
                    $search = request('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('document_name', 'LIKE', "%{$search}%");
                    });
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }
}
