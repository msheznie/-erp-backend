<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\SRMPublicLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;
use Exception;
/**
 * Class SRMPublicLinkRepository
 * @package App\Repositories
 * @version October 2, 2024, 9:23 am +04
 *
 * @method SRMPublicLink findWithoutFail($id, $columns = ['*'])
 * @method SRMPublicLink find($id, $columns = ['*'])
 * @method SRMPublicLink first($columns = ['*'])
*/
class SRMPublicLinkRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'link',
        'api_key',
        'link_description',
        'expire_date',
        'expired',
        'current',
        'company_id',
        'created_user_group',
        'created_pc_id',
        'created_user_id',
        'created_date_time',
        'created_user_name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SRMPublicLink::class;
    }

    public function getPublicLinkSupplierData($request)
    {
        try
        {
            $input = $request->all();
            $companyId = $input['companyId'];
            $publicLinkData =  $this->model->getPublicSupplierLinks($companyId);

            foreach ($publicLinkData as $link)
            {
                $currentDate = now()->startOfDay();
                $expireDate = Carbon::parse($link->expire_date)->startOfDay();

                if ($expireDate->lessThan($currentDate) && !$link->expired)
                {
                    $link->expired = 1;
                    $link->save();
                }
            }

            return $publicLinkData;
        }
        catch (Exception $e)
        {
            return null;
        }
    }

    public function saveSupplierPublicLink($request)
    {
        $input = $request->all();
        try
        {
            return DB::transaction(function () use ($input)
            {
                $apiKey = $input['api_key'];
                $employee = Helper::getEmployeeInfo();
                $companyId = $input['companyId'];
                $encodedString = base64_encode('External');
                $existingLink = $this->model->where('link_description', $input['description'])
                    ->first();

                if ($existingLink)
                {
                    throw new \Exception('The link description already exists.');
                }


                $this->model->where('company_id', $input['companyId'])
                    ->update(['current' => 0]);


                $publicLink = $this->model->newInstance();


                $uuid = bin2hex(random_bytes(16));
                $link = env('SRM_LINK').$uuid.'/'.$apiKey.'/'.$encodedString;

                $publicLink->link = $link;
                $publicLink->uuid = $uuid;
                $publicLink->api_key = $apiKey;
                $publicLink->link_description = $input['description'];
                $publicLink->expire_date = Carbon::parse($input['expireDate'])->format('Y-m-d');
                $publicLink->expired = 0;
                $publicLink->current = 1;
                $publicLink->company_id = $companyId;
                $publicLink->created_user_group = $employee->userGroupID;
                $publicLink->created_pc_id = gethostname();
                $publicLink->created_user_id = $employee->employeeSystemID;
                $publicLink->save();
                return $publicLink;
            });
        }
        catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    public function getPublicLinkDataByUuid($request)
    {
        try
        {
            $input = $request->all();
            $inputData = $input['extra']['params'];
            $uuid = $inputData['token'];
            $type = $decodedString = base64_decode($inputData['type']);

            $publicLinkData = $this->model->getPublicLinkDataByUuid($uuid);

            if(empty($publicLinkData))
            {
                return [
                    'success'   => false,
                    'message'   => 'Invalid Token',
                    'data'      => 'Invalid',
                ];

            }

            if ($this->isLinkExpired($publicLinkData->expire_date))
            {
                $publicLinkData->expired = 1;
                $publicLinkData->save();

                return [
                    'success' => false,
                    'message' => 'Link has expired',
                    'data' => 'Expired',
                ];
            }

            if ($publicLinkData->current == 0 || $type != 'External')
            {
                return [
                    'success' => false,
                    'message' => 'Invalid Link',
                    'data' => 'Expired',
                ];
            }


            return [
                'success'   => true,
                'message'   => 'success',
                'data'      => $publicLinkData,
            ];




        }
        catch (Exception $e)
        {
            return null;
        }
    }

    private function isLinkExpired($expireDate)
    {
        $now = Carbon::now()->startOfDay();
        $expire = Carbon::parse($expireDate)->startOfDay();
        return $now->isAfter($expire);
    }
}
