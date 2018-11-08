<table class="table table-responsive" id="salaryProcessMasters-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Salaryprocesscode</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Processperiod</th>
        <th>Startdate</th>
        <th>Enddate</th>
        <th>Currency</th>
        <th>Salarymonth</th>
        <th>Description</th>
        <th>Createdate</th>
        <th>Rolllevforapp Curr</th>
        <th>Isreferredback</th>
        <th>Confirmedyn</th>
        <th>Confirmedby</th>
        <th>Approvedyn</th>
        <th>Approvedby</th>
        <th>Approveddate</th>
        <th>Confirmeddate</th>
        <th>Isrglconfirm</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyexchangerate</th>
        <th>Rptcurrencyid</th>
        <th>Rptcurrencyexchangerate</th>
        <th>Updatenoofdaysbtnflag</th>
        <th>Updatesalarybtnflag</th>
        <th>Getemployeebtnflag</th>
        <th>Updatessobtnflag</th>
        <th>Updaterabenefitbtnflag</th>
        <th>Updatetaxstep1Btnflag</th>
        <th>Updatetaxstep2Btnflag</th>
        <th>Updatetaxstep3Btnflag</th>
        <th>Updatetaxstep4Btnflag</th>
        <th>Updateheldsalarybtnflag</th>
        <th>Isheldsalary</th>
        <th>Showpayslip</th>
        <th>Paymentgenerated</th>
        <th>Paymasterautoid</th>
        <th>Bankidforpayment</th>
        <th>Bankaccountidforpayment</th>
        <th>Salaryprocesstype</th>
        <th>Thirteenmonthjvid</th>
        <th>Gratuityjvid</th>
        <th>Gratuityreversaljvid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createdusergroup</th>
        <th>Createdpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($salaryProcessMasters as $salaryProcessMaster)
        <tr>
            <td>{!! $salaryProcessMaster->CompanyID !!}</td>
            <td>{!! $salaryProcessMaster->salaryProcessCode !!}</td>
            <td>{!! $salaryProcessMaster->documentID !!}</td>
            <td>{!! $salaryProcessMaster->serialNo !!}</td>
            <td>{!! $salaryProcessMaster->processPeriod !!}</td>
            <td>{!! $salaryProcessMaster->startDate !!}</td>
            <td>{!! $salaryProcessMaster->endDate !!}</td>
            <td>{!! $salaryProcessMaster->Currency !!}</td>
            <td>{!! $salaryProcessMaster->salaryMonth !!}</td>
            <td>{!! $salaryProcessMaster->description !!}</td>
            <td>{!! $salaryProcessMaster->createDate !!}</td>
            <td>{!! $salaryProcessMaster->RollLevForApp_curr !!}</td>
            <td>{!! $salaryProcessMaster->isReferredBack !!}</td>
            <td>{!! $salaryProcessMaster->confirmedYN !!}</td>
            <td>{!! $salaryProcessMaster->confirmedby !!}</td>
            <td>{!! $salaryProcessMaster->approvedYN !!}</td>
            <td>{!! $salaryProcessMaster->approvedby !!}</td>
            <td>{!! $salaryProcessMaster->approvedDate !!}</td>
            <td>{!! $salaryProcessMaster->confirmedDate !!}</td>
            <td>{!! $salaryProcessMaster->isRGLConfirm !!}</td>
            <td>{!! $salaryProcessMaster->localCurrencyID !!}</td>
            <td>{!! $salaryProcessMaster->localCurrencyExchangeRate !!}</td>
            <td>{!! $salaryProcessMaster->rptCurrencyID !!}</td>
            <td>{!! $salaryProcessMaster->rptCurrencyExchangeRate !!}</td>
            <td>{!! $salaryProcessMaster->updateNoOfDaysBtnFlag !!}</td>
            <td>{!! $salaryProcessMaster->updateSalaryBtnFlag !!}</td>
            <td>{!! $salaryProcessMaster->getEmployeeBtnFlag !!}</td>
            <td>{!! $salaryProcessMaster->updateSSOBtnFlag !!}</td>
            <td>{!! $salaryProcessMaster->updateRABenefitBtnFlag !!}</td>
            <td>{!! $salaryProcessMaster->updateTaxStep1BtnFlag !!}</td>
            <td>{!! $salaryProcessMaster->updateTaxStep2BtnFlag !!}</td>
            <td>{!! $salaryProcessMaster->updateTaxStep3BtnFlag !!}</td>
            <td>{!! $salaryProcessMaster->updateTaxStep4BtnFlag !!}</td>
            <td>{!! $salaryProcessMaster->updateHeldSalaryBtnFlag !!}</td>
            <td>{!! $salaryProcessMaster->isHeldSalary !!}</td>
            <td>{!! $salaryProcessMaster->showpaySlip !!}</td>
            <td>{!! $salaryProcessMaster->paymentGenerated !!}</td>
            <td>{!! $salaryProcessMaster->PayMasterAutoId !!}</td>
            <td>{!! $salaryProcessMaster->bankIDForPayment !!}</td>
            <td>{!! $salaryProcessMaster->bankAccountIDForPayment !!}</td>
            <td>{!! $salaryProcessMaster->salaryProcessType !!}</td>
            <td>{!! $salaryProcessMaster->thirteenMonthJVID !!}</td>
            <td>{!! $salaryProcessMaster->gratuityJVID !!}</td>
            <td>{!! $salaryProcessMaster->gratuityReversalJVID !!}</td>
            <td>{!! $salaryProcessMaster->modifieduser !!}</td>
            <td>{!! $salaryProcessMaster->modifiedpc !!}</td>
            <td>{!! $salaryProcessMaster->createduserGroup !!}</td>
            <td>{!! $salaryProcessMaster->createdpc !!}</td>
            <td>{!! $salaryProcessMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['salaryProcessMasters.destroy', $salaryProcessMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('salaryProcessMasters.show', [$salaryProcessMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('salaryProcessMasters.edit', [$salaryProcessMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>