<div class="table-responsive">
    <table class="table" id="iOUBookingMasters-table">
        <thead>
            <tr>
                <th>Documentid</th>
        <th>Serialno</th>
        <th>Iouvoucherautoid</th>
        <th>Bookingcode</th>
        <th>Bookingdate</th>
        <th>Pullfromfuelyn</th>
        <th>Empid</th>
        <th>Empname</th>
        <th>Usertype</th>
        <th>Comments</th>
        <th>Submittedyn</th>
        <th>Submitteddate</th>
        <th>Submittedempid</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approvedyn</th>
        <th>Approvedbyempid</th>
        <th>Approvedbyempname</th>
        <th>Approveddate</th>
        <th>Approvalcomments</th>
        <th>Transactioncurrencyid</th>
        <th>Transactioncurrency</th>
        <th>Transactionexchangerate</th>
        <th>Transactioncurrencydecimalplaces</th>
        <th>Transactionamount</th>
        <th>Companylocalcurrencyid</th>
        <th>Companylocalcurrency</th>
        <th>Companylocalexchangerate</th>
        <th>Companylocalamount</th>
        <th>Companylocalcurrencydecimalplaces</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportingcurrency</th>
        <th>Companyreportingexchangerate</th>
        <th>Companyreportingamount</th>
        <th>Companyreportingcurrencydecimalplaces</th>
        <th>Empcurrencyid</th>
        <th>Empcurrency</th>
        <th>Empcurrencyexchangerate</th>
        <th>Empcurrencyamount</th>
        <th>Empcurrencydecimalplaces</th>
        <th>Isdeleted</th>
        <th>Deletedempid</th>
        <th>Deleteddate</th>
        <th>Currentlevelno</th>
        <th>Companyfinanceyearid</th>
        <th>Companyfinanceyear</th>
        <th>Fybegin</th>
        <th>Fyend</th>
        <th>Fyperioddatefrom</th>
        <th>Fyperioddateto</th>
        <th>Companyfinanceperiodid</th>
        <th>Companyid</th>
        <th>Companycode</th>
        <th>Segmentid</th>
        <th>Segmentcode</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($iOUBookingMasters as $iOUBookingMaster)
            <tr>
                <td>{{ $iOUBookingMaster->documentID }}</td>
            <td>{{ $iOUBookingMaster->serialNo }}</td>
            <td>{{ $iOUBookingMaster->iouVoucherAutoID }}</td>
            <td>{{ $iOUBookingMaster->bookingCode }}</td>
            <td>{{ $iOUBookingMaster->bookingDate }}</td>
            <td>{{ $iOUBookingMaster->pullFromFuelYN }}</td>
            <td>{{ $iOUBookingMaster->empID }}</td>
            <td>{{ $iOUBookingMaster->empName }}</td>
            <td>{{ $iOUBookingMaster->userType }}</td>
            <td>{{ $iOUBookingMaster->comments }}</td>
            <td>{{ $iOUBookingMaster->submittedYN }}</td>
            <td>{{ $iOUBookingMaster->submittedDate }}</td>
            <td>{{ $iOUBookingMaster->submittedEmpID }}</td>
            <td>{{ $iOUBookingMaster->confirmedYN }}</td>
            <td>{{ $iOUBookingMaster->confirmedByEmpID }}</td>
            <td>{{ $iOUBookingMaster->confirmedByName }}</td>
            <td>{{ $iOUBookingMaster->confirmedDate }}</td>
            <td>{{ $iOUBookingMaster->approvedYN }}</td>
            <td>{{ $iOUBookingMaster->approvedByEmpID }}</td>
            <td>{{ $iOUBookingMaster->approvedByEmpName }}</td>
            <td>{{ $iOUBookingMaster->approvedDate }}</td>
            <td>{{ $iOUBookingMaster->approvalComments }}</td>
            <td>{{ $iOUBookingMaster->transactionCurrencyID }}</td>
            <td>{{ $iOUBookingMaster->transactionCurrency }}</td>
            <td>{{ $iOUBookingMaster->transactionExchangeRate }}</td>
            <td>{{ $iOUBookingMaster->transactionCurrencyDecimalPlaces }}</td>
            <td>{{ $iOUBookingMaster->transactionAmount }}</td>
            <td>{{ $iOUBookingMaster->companyLocalCurrencyID }}</td>
            <td>{{ $iOUBookingMaster->companyLocalCurrency }}</td>
            <td>{{ $iOUBookingMaster->companyLocalExchangeRate }}</td>
            <td>{{ $iOUBookingMaster->companyLocalAmount }}</td>
            <td>{{ $iOUBookingMaster->companyLocalCurrencyDecimalPlaces }}</td>
            <td>{{ $iOUBookingMaster->companyReportingCurrencyID }}</td>
            <td>{{ $iOUBookingMaster->companyReportingCurrency }}</td>
            <td>{{ $iOUBookingMaster->companyReportingExchangeRate }}</td>
            <td>{{ $iOUBookingMaster->companyReportingAmount }}</td>
            <td>{{ $iOUBookingMaster->companyReportingCurrencyDecimalPlaces }}</td>
            <td>{{ $iOUBookingMaster->empCurrencyID }}</td>
            <td>{{ $iOUBookingMaster->empCurrency }}</td>
            <td>{{ $iOUBookingMaster->empCurrencyExchangeRate }}</td>
            <td>{{ $iOUBookingMaster->empCurrencyAmount }}</td>
            <td>{{ $iOUBookingMaster->empCurrencyDecimalPlaces }}</td>
            <td>{{ $iOUBookingMaster->isDeleted }}</td>
            <td>{{ $iOUBookingMaster->deletedEmpID }}</td>
            <td>{{ $iOUBookingMaster->deletedDate }}</td>
            <td>{{ $iOUBookingMaster->currentLevelNo }}</td>
            <td>{{ $iOUBookingMaster->companyFinanceYearID }}</td>
            <td>{{ $iOUBookingMaster->companyFinanceYear }}</td>
            <td>{{ $iOUBookingMaster->FYBegin }}</td>
            <td>{{ $iOUBookingMaster->FYEnd }}</td>
            <td>{{ $iOUBookingMaster->FYPeriodDateFrom }}</td>
            <td>{{ $iOUBookingMaster->FYPeriodDateTo }}</td>
            <td>{{ $iOUBookingMaster->companyFinancePeriodID }}</td>
            <td>{{ $iOUBookingMaster->companyID }}</td>
            <td>{{ $iOUBookingMaster->companyCode }}</td>
            <td>{{ $iOUBookingMaster->segmentID }}</td>
            <td>{{ $iOUBookingMaster->segmentCode }}</td>
            <td>{{ $iOUBookingMaster->createdUserGroup }}</td>
            <td>{{ $iOUBookingMaster->createdPCID }}</td>
            <td>{{ $iOUBookingMaster->createdUserID }}</td>
            <td>{{ $iOUBookingMaster->createdDateTime }}</td>
            <td>{{ $iOUBookingMaster->createdUserName }}</td>
            <td>{{ $iOUBookingMaster->modifiedPCID }}</td>
            <td>{{ $iOUBookingMaster->modifiedUserID }}</td>
            <td>{{ $iOUBookingMaster->modifiedDateTime }}</td>
            <td>{{ $iOUBookingMaster->modifiedUserName }}</td>
            <td>{{ $iOUBookingMaster->timestamp }}</td>
                <td>
                    {!! Form::open(['route' => ['iOUBookingMasters.destroy', $iOUBookingMaster->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('iOUBookingMasters.show', [$iOUBookingMaster->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('iOUBookingMasters.edit', [$iOUBookingMaster->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
