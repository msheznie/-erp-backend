<div class="table-responsive">
    <table class="table" id="deliveryOrders-table">
        <thead>
            <tr>
                <th>Ordertype</th>
        <th>Deliveryordercode</th>
        <th>Companysystemid</th>
        <th>Documentsystemid</th>
        <th>Companyfinanceyearid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Companyfinanceperiodid</th>
        <th>Fyperioddatefrom</th>
        <th>Fyperioddateto</th>
        <th>Deliveryorderdate</th>
        <th>Warehousesystemcode</th>
        <th>Servicelinesystemid</th>
        <th>Referenceno</th>
        <th>Customerid</th>
        <th>Salespersonid</th>
        <th>Narration</th>
        <th>Notes</th>
        <th>Contactpersonnumber</th>
        <th>Contactpersonname</th>
        <th>Transactioncurrencyid</th>
        <th>Transactioncurrencyer</th>
        <th>Transactionamount</th>
        <th>Companylocalcurrencyid</th>
        <th>Companylocalcurrencyer</th>
        <th>Companylocalamount</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportingcurrencyer</th>
        <th>Companyreportingamount</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approvedyn</th>
        <th>Approveddate</th>
        <th>Approvedempsystemid</th>
        <th>Approvedbyempid</th>
        <th>Approvedbyempname</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
        <th>Closedyn</th>
        <th>Closeddate</th>
        <th>Closedreason</th>
        <th>Createdusersystemid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedusersystemid</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($deliveryOrders as $deliveryOrder)
            <tr>
                <td>{{ $deliveryOrder->orderType }}</td>
            <td>{{ $deliveryOrder->deliveryOrderCode }}</td>
            <td>{{ $deliveryOrder->companySystemId }}</td>
            <td>{{ $deliveryOrder->documentSystemId }}</td>
            <td>{{ $deliveryOrder->companyFinanceYearID }}</td>
            <td>{{ $deliveryOrder->FYBiggin }}</td>
            <td>{{ $deliveryOrder->FYEnd }}</td>
            <td>{{ $deliveryOrder->companyFinancePeriodID }}</td>
            <td>{{ $deliveryOrder->FYPeriodDateFrom }}</td>
            <td>{{ $deliveryOrder->FYPeriodDateTo }}</td>
            <td>{{ $deliveryOrder->deliveryOrderDate }}</td>
            <td>{{ $deliveryOrder->wareHouseSystemCode }}</td>
            <td>{{ $deliveryOrder->serviceLineSystemID }}</td>
            <td>{{ $deliveryOrder->referenceNo }}</td>
            <td>{{ $deliveryOrder->customerID }}</td>
            <td>{{ $deliveryOrder->salesPersonID }}</td>
            <td>{{ $deliveryOrder->narration }}</td>
            <td>{{ $deliveryOrder->notes }}</td>
            <td>{{ $deliveryOrder->contactPersonNumber }}</td>
            <td>{{ $deliveryOrder->contactPersonName }}</td>
            <td>{{ $deliveryOrder->transactionCurrencyID }}</td>
            <td>{{ $deliveryOrder->transactionCurrencyER }}</td>
            <td>{{ $deliveryOrder->transactionAmount }}</td>
            <td>{{ $deliveryOrder->companyLocalCurrencyID }}</td>
            <td>{{ $deliveryOrder->companyLocalCurrencyER }}</td>
            <td>{{ $deliveryOrder->companyLocalAmount }}</td>
            <td>{{ $deliveryOrder->companyReportingCurrencyID }}</td>
            <td>{{ $deliveryOrder->companyReportingCurrencyER }}</td>
            <td>{{ $deliveryOrder->companyReportingAmount }}</td>
            <td>{{ $deliveryOrder->confirmedYN }}</td>
            <td>{{ $deliveryOrder->confirmedByEmpSystemID }}</td>
            <td>{{ $deliveryOrder->confirmedByEmpID }}</td>
            <td>{{ $deliveryOrder->confirmedByName }}</td>
            <td>{{ $deliveryOrder->confirmedDate }}</td>
            <td>{{ $deliveryOrder->approvedYN }}</td>
            <td>{{ $deliveryOrder->approvedDate }}</td>
            <td>{{ $deliveryOrder->approvedEmpSystemID }}</td>
            <td>{{ $deliveryOrder->approvedbyEmpID }}</td>
            <td>{{ $deliveryOrder->approvedbyEmpName }}</td>
            <td>{{ $deliveryOrder->refferedBackYN }}</td>
            <td>{{ $deliveryOrder->timesReferred }}</td>
            <td>{{ $deliveryOrder->RollLevForApp_curr }}</td>
            <td>{{ $deliveryOrder->closedYN }}</td>
            <td>{{ $deliveryOrder->closedDate }}</td>
            <td>{{ $deliveryOrder->closedReason }}</td>
            <td>{{ $deliveryOrder->createdUserSystemID }}</td>
            <td>{{ $deliveryOrder->createdUserGroup }}</td>
            <td>{{ $deliveryOrder->createdPCID }}</td>
            <td>{{ $deliveryOrder->createdUserID }}</td>
            <td>{{ $deliveryOrder->createdDateTime }}</td>
            <td>{{ $deliveryOrder->createdUserName }}</td>
            <td>{{ $deliveryOrder->modifiedUserSystemID }}</td>
            <td>{{ $deliveryOrder->modifiedPCID }}</td>
            <td>{{ $deliveryOrder->modifiedUserID }}</td>
            <td>{{ $deliveryOrder->modifiedDateTime }}</td>
            <td>{{ $deliveryOrder->modifiedUserName }}</td>
            <td>{{ $deliveryOrder->timestamp }}</td>
                <td>
                    {!! Form::open(['route' => ['deliveryOrders.destroy', $deliveryOrder->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('deliveryOrders.show', [$deliveryOrder->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('deliveryOrders.edit', [$deliveryOrder->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
