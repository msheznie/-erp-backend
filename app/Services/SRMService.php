<?php

namespace App\Services;

use App\Models\ProcumentOrder;
use Illuminate\Http\Request;

class SRMService
{
    private $POService = null;
    public function __construct(POService $POService)
    {
        $this->POService = $POService;
    }

    /**
     * get currencies
     * @return array
     */
    public function getCurrencies(): array
    {
        $data = [
            'LKR',
            'USD',
            'ASD'
        ];

        return [
            'success'   => true,
            'message'   => 'currencies successfully get',
            'data'      => $data
        ];
    }
    public function getPoList(Request $request): array
    {
        $supplierID = $request->input('auth.id');
        $per_page = $request->input('extra.per_page');
        $page = $request->input('extra.page');
        $data = ProcumentOrder::where('approved', -1)
            ->where('supplierID', $supplierID)
            ->with(['currency', 'created_by'])
            ->paginate($per_page, ['*'], 'page', $page);
        return [
            'success'   => true,
            'message'   => 'Purchase order list successfully get',
            'data'      => $data
        ];
    }
    public function getPoPrintData(Request $request)
    {
        $purchaseOrderID = $request->input('extra.purchaseOrderID');
        $data =  $this->POService->getPoPrintData($purchaseOrderID);
        return [
            'success'   => true,
            'message'   => 'Purchase order print data successfully get',
            'data'      => $data
        ];
    }

    public function getPoAddons(Request $request)
    {
        $purchaseOrderID = $request->input('extra.purchaseOrderID');
        $data =  $this->POService->getPoAddons($purchaseOrderID);
        return [
            'success'   => true,
            'message'   => 'Purchase order addon successfully get',
            'data'      => $data
        ];
    }
}
