<?php

namespace App\Repositories;

use App\Models\ItemSerial;
use App\Models\GRVDetails;
use App\Models\DocumentSubProduct;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemSerialRepository
 * @package App\Repositories
 * @version December 23, 2021, 11:01 am +04
 *
 * @method ItemSerial findWithoutFail($id, $columns = ['*'])
 * @method ItemSerial find($id, $columns = ['*'])
 * @method ItemSerial first($columns = ['*'])
*/
class ItemSerialRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemSystemCode',
        'productBatchID',
        'serialCode',
        'expireDate',
        'wareHouseSystemID',
        'binLocation',
        'soldFlag'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemSerial::class;
    }


    public function mapSubProducts($productSerial, $documentSystemID, $documentDetailID)
    {
        $subProduct = [
            'documentSystemID' => $documentSystemID,
            'documentDetailID' => $documentDetailID,
            'productSerialID' => $productSerial->id,
            'quantity' => 1,
            'sold' => 0,
            'soldQty' => 0
        ];
        switch ($documentSystemID) {
            case 3:
                $grvDetail = GRVDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($grvDetail) ? $grvDetail->grvAutoID : null;

                break;
            
            default:
                # code...
                break;
        }

        $res = DocumentSubProduct::create($subProduct);

        return ['status' => true];
    }
}
