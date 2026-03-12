<?php

namespace App\Services\API;

use App\helper\Helper;
use App\Models\Company;
use App\Repositories\BankAccountRepository;
use App\Repositories\BankMasterRepository;
use App\Utils\ServiceResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class BankMasterAPIService
{
    public function __construct(
        private BankMasterRepository $bankMasterRepository,
        private BankAccountRepository $bankAccountRepository
    ) {}

    public function pullBankMaster(array $input): ServiceResponse
    {
        $companyId = $input['company_id'] ?? null;
        if (empty($companyId)) {
            return ServiceResponse::failure(trans('custom.companySystemID_is_required'));
        }

        $company = Company::find($companyId);
        if (!$company) {
            return ServiceResponse::failure(trans('custom.company_not_found'));
        }

        $companySystemIDs = Helper::checkIsCompanyGroup($companyId)
            ? Helper::getGroupCompany($companyId)
            : [$companyId];

        $bankShortCodes = $this->normalizeBankShortCodes($input);
        if (!empty($bankShortCodes)) {
            $existingCodes = $this->bankMasterRepository->findExistingBankShortCodes($bankShortCodes);
            $normalizedInputs = collect($bankShortCodes)->map(fn ($c) => strtolower((string) $c))->unique()->values();
            $normalizedExisting = $existingCodes->map(fn ($c) => strtolower((string) $c))->unique()->values();
            $missing = $normalizedInputs->diff($normalizedExisting)->values()->all();
            if (!empty($missing)) {
                return ServiceResponse::failure(trans('custom.input_value_not_matching'));
            }
            $bankShortCodes = $existingCodes->values()->all();
        }

        $query = $this->bankAccountRepository->getApprovedActiveForCompanies(
            $companySystemIDs,
            $bankShortCodes ?: null
        );

        $usePagination = !empty($input['page']);
        $page = (int) ($input['page'] ?? 1);
        $perPage = (int) ($input['per_page'] ?? 10);
        $perPage = min(max($perPage, 1), 500);

        $accounts = $query->get();
        $banks = $this->groupAccountsByBankAndMap($accounts);

        if ($usePagination) {
            $total = count($banks);
            $data = new LengthAwarePaginator(
                array_values(array_slice($banks, ($page - 1) * $perPage, $perPage)),
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $data = $banks;
        }

        return ServiceResponse::success($data, trans('custom.data_retrieved_successfully_3'));
    }

    private function normalizeBankShortCodes(array $input): ?array
    {
        if (!empty($input['bank_short_codes']) && is_array($input['bank_short_codes'])) {
            $codes = array_values(array_filter(array_map('trim', $input['bank_short_codes'])));
            return empty($codes) ? null : $codes;
        }
        if (!empty($input['bank_short_code']) && is_string($input['bank_short_code'])) {
            $code = trim($input['bank_short_code']);
            return $code === '' ? null : [$code];
        }
        return null;
    }

    private function groupAccountsByBankAndMap($accounts): array
    {
        $grouped = $accounts->groupBy('bankmasterAutoID');
        $result = [];

        foreach ($grouped as $bankmasterAutoID => $accountList) {
            $first = $accountList->first();
            $bank = $first->bank;
            $company = $first->company;

            $accountItems = [];
            foreach ($accountList as $account) {
                $accountItems[] = [
                    'accountNo' => $account->AccountNo,
                    'accountName' => $account->AccountName,
                    'currency' => $account->currency ? $account->currency->CurrencyCode : null,
                    'swiftCode' => $account->accountSwiftCode,
                    'accountIBAN#' => $account->getAttribute('accountIBAN#'),
                    'isSalaryBank' => (bool) $account->isManualActive,
                    'glCode' => $account->glCodeLinked,
                    'isActive' => (bool) $account->isAccountActive,
                    'isDefault' => (bool) $account->isDefault,
                ];
            }

            $result[] = [
                'company' => $company ? $company->CompanyName : null,
                'bankShortCode' => $bank ? $bank->bankShortCode : $first->bankShortCode,
                'bankName' => $bank ? $bank->bankName : $first->bankName,
                'accounts' => $accountItems,
            ];
        }

        return $result;
    }
}
