<?php

namespace App\Services\API;

use App\helper\Helper;
use App\Models\Company;
use App\Models\CustomerInvoice;
use App\Models\CustomerMaster;
use App\Repositories\CustomerInvoiceRepository;
use App\Utils\ServiceResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerInvoiceBalanceAPIService
{
    public function __construct(private CustomerInvoiceRepository $customerInvoiceRepository) {}

    public function getApprovedBalances(array $input): ServiceResponse
    {
        $companyId = (int) ($input['company_id'] ?? 0);
        $companyMaster = Company::where('companySystemID', $companyId)->first();
        if (! $companyMaster) {
            return ServiceResponse::failure(trans('custom.the_company_system_ID_not_matching_with_system', ['companySystemID' => $companyId]));
        }

        $subCompanies = Helper::checkIsCompanyGroup($companyId)
            ? Helper::getGroupCompany($companyId)
            : [$companyId];

        $invoiceCodes = $this->normalizeStringArray($input['invoice_code'] ?? null);
        $customerCodes = $this->normalizeStringArray($input['customer_code'] ?? null);

        if (! empty($invoiceCodes)) {
            foreach ($invoiceCodes as $code) {
                $invoice = CustomerInvoice::where('bookingInvCode', $code)->whereIn('companySystemID', $subCompanies)->first();
                if (! $invoice) {
                    return ServiceResponse::failure(trans('custom.the_invoice_code_not_matching_with_system', ['invoiceCode' => $code]));
                }
            }
        }

        if (! empty($customerCodes)) {
            foreach ($customerCodes as $code) {
                $customer = CustomerMaster::where('CutomerCode', $code)->whereIn('primaryCompanySystemID', $subCompanies)->first();
                if (! $customer) {
                    return ServiceResponse::failure(trans('custom.the_customer_code_not_matching_with_system', ['customerCode' => $code]));
                }
            }
        }

        $invoiceTypeLabelToDocumentType = [
            'direct invoice' => 0,
            'proforma invoice' => 1,
            'item sales invoice' => 2,
            'from delivery note' => 3,
            'from delivery order' => 3,
            'from sales order' => 4,
            'from quotation' => 5,
        ];

        $type = null;
        $rawInvoiceType = $input['invoice_type'] ?? null;
        if ($rawInvoiceType !== null && $rawInvoiceType !== '') {
            $key = strtolower(trim((string) $rawInvoiceType));
            if (! array_key_exists($key, $invoiceTypeLabelToDocumentType)) {
                return ServiceResponse::failure('Customer invoice Type not match with system');
            }
            $type = $invoiceTypeLabelToDocumentType[$key];
        }

        $generatedFromList = $input['generated_from'] ?? null;

        $documentTypeLabels = [
            0 => 'Direct Invoice',
            1 => 'Proforma Invoice',
            2 => 'Item Sales Invoice',
            3 => 'From Delivery Note',
            4 => 'From Sales Order',
            5 => 'From Quotation',
        ];

        $query = $this->customerInvoiceRepository->approvedBalancesQuery($subCompanies, $generatedFromList);

        if (! empty($invoiceCodes)) {
            $query->whereIn($query->getModel()->getTable().'.bookingInvCode', $invoiceCodes);
        }
        if (! empty($customerCodes)) {
            $query->whereHas('customer', function ($q) use ($customerCodes) {
                $q->whereIn('CutomerCode', $customerCodes);
            });
        }
        if ($type !== null) {
            $query->where($query->getModel()->getTable().'.isPerforma', $type);
        }

        $query->orderBy($query->getModel()->getTable().'.custInvoiceDirectAutoID', 'desc');

        $usePagination = array_key_exists('page', $input) && $input['page'] !== null;
        if ($usePagination) {
            $page = (int) ($input['page'] ?? 1);
            $perPage = (int) ($input['per_page'] ?? 10);
            $perPage = min(max($perPage, 1), 500);

            $paginator = (clone $query)->paginate($perPage, ['*'], 'page', $page);
            $invoiceIds = $paginator->getCollection()->pluck('custInvoiceDirectAutoID')->filter()->values()->all();
            $docsByInvoice = $this->customerInvoiceRepository->loadStatusDetailsByInvoiceIds($invoiceIds, $subCompanies);

            $paginator->setCollection(
                $paginator->getCollection()->map(function ($invoice) use ($docsByInvoice, $documentTypeLabels) {
                    return $this->customerInvoiceRepository->mapBalanceItem($invoice, $docsByInvoice, $documentTypeLabels);
                })
            );

            return ServiceResponse::success($paginator, 'Customer invoice balances retrieved successfully');
        }

        $rows = $query->get();
        $invoiceIds = $rows->pluck('custInvoiceDirectAutoID')->filter()->values()->all();
        $docsByInvoice = $this->customerInvoiceRepository->loadStatusDetailsByInvoiceIds($invoiceIds, $subCompanies);

        $data = $rows->map(function ($invoice) use ($docsByInvoice, $documentTypeLabels) {
            return $this->customerInvoiceRepository->mapBalanceItem($invoice, $docsByInvoice, $documentTypeLabels);
        })->values();

        return ServiceResponse::success($data, 'Customer invoice balances retrieved successfully');
    }

    private function normalizeStringArray($value): array
    {
        if (! is_array($value)) {
            return [];
        }
        return array_values(array_filter(array_map(function ($v) {
            $s = trim((string) $v);
            return $s === '' ? null : $s;
        }, $value)));
    }
}

