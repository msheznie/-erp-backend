<table class="table table-responsive" id="companies-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Companyname</th>
        <th>Companynamelocalized</th>
        <th>Localname</th>
        <th>Masterlevel</th>
        <th>Companylevel</th>
        <th>Mastercomapanyid</th>
        <th>Mastercomapanyidreporting</th>
        <th>Companyshortcode</th>
        <th>Orglistorder</th>
        <th>Orglistsordorder</th>
        <th>Sortorder</th>
        <th>Listorder</th>
        <th>Companyaddress</th>
        <th>Companycountry</th>
        <th>Companytelephone</th>
        <th>Companyfax</th>
        <th>Companyemail</th>
        <th>Companyurl</th>
        <th>Subscriptionstarted</th>
        <th>Subscriptionupto</th>
        <th>Contactperson</th>
        <th>Contactpersontelephone</th>
        <th>Contactpersonfax</th>
        <th>Contactpersonemail</th>
        <th>Registrationnumber</th>
        <th>Companylogo</th>
        <th>Reportingcurrency</th>
        <th>Localcurrencyid</th>
        <th>Mainformname</th>
        <th>Menuinitialimage</th>
        <th>Menuinitialselectedimage</th>
        <th>Policyitemissuetollerence</th>
        <th>Policyaddonpercentage</th>
        <th>Policypoappdaydiff</th>
        <th>Policystockadjwaccurrentyn</th>
        <th>Policydepreciationrundate</th>
        <th>Isgroup</th>
        <th>Isattachementyn</th>
        <th>Reportingcriteria</th>
        <th>Reportingcriteriaformquery</th>
        <th>Supplierreportingcriteria</th>
        <th>Supplierreportingcriteriaformquery</th>
        <th>Supplierposavreportingcriteria</th>
        <th>Supplierposavreportingcriteriaformquery</th>
        <th>Supplierpospentreportingcriteriaformquery</th>
        <th>Exchangegainlossglcode</th>
        <th>Exchangelossglcode</th>
        <th>Exchangegainglcode</th>
        <th>Exchangeprovisionglcode</th>
        <th>Exchangeprovisionglcodear</th>
        <th>Isapprovalbyserviceline</th>
        <th>Isapprovalbyservicelinefinance</th>
        <th>Istaxyn</th>
        <th>Isactive</th>
        <th>Isactivegroup</th>
        <th>Showincombo</th>
        <th>Allowbackdatedgrv</th>
        <th>Allowcustomerinvwithoutcontractid</th>
        <th>Checkmaxqty</th>
        <th>Itemcodemustinpr</th>
        <th>Op Onopenpopupyn</th>
        <th>Showinnewrilrqhse</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($companies as $company)
        <tr>
            <td>{!! $company->CompanyID !!}</td>
            <td>{!! $company->CompanyName !!}</td>
            <td>{!! $company->CompanyNameLocalized !!}</td>
            <td>{!! $company->LocalName !!}</td>
            <td>{!! $company->MasterLevel !!}</td>
            <td>{!! $company->CompanyLevel !!}</td>
            <td>{!! $company->masterComapanyID !!}</td>
            <td>{!! $company->masterComapanyIDReporting !!}</td>
            <td>{!! $company->companyShortCode !!}</td>
            <td>{!! $company->orgListOrder !!}</td>
            <td>{!! $company->orgListSordOrder !!}</td>
            <td>{!! $company->sortOrder !!}</td>
            <td>{!! $company->listOrder !!}</td>
            <td>{!! $company->CompanyAddress !!}</td>
            <td>{!! $company->companyCountry !!}</td>
            <td>{!! $company->CompanyTelephone !!}</td>
            <td>{!! $company->CompanyFax !!}</td>
            <td>{!! $company->CompanyEmail !!}</td>
            <td>{!! $company->CompanyURL !!}</td>
            <td>{!! $company->SubscriptionStarted !!}</td>
            <td>{!! $company->SubscriptionUpTo !!}</td>
            <td>{!! $company->ContactPerson !!}</td>
            <td>{!! $company->ContactPersonTelephone !!}</td>
            <td>{!! $company->ContactPersonFax !!}</td>
            <td>{!! $company->ContactPersonEmail !!}</td>
            <td>{!! $company->registrationNumber !!}</td>
            <td>{!! $company->companyLogo !!}</td>
            <td>{!! $company->reportingCurrency !!}</td>
            <td>{!! $company->localCurrencyID !!}</td>
            <td>{!! $company->mainFormName !!}</td>
            <td>{!! $company->menuInitialImage !!}</td>
            <td>{!! $company->menuInitialSelectedImage !!}</td>
            <td>{!! $company->policyItemIssueTollerence !!}</td>
            <td>{!! $company->policyAddonPercentage !!}</td>
            <td>{!! $company->policyPOAppDayDiff !!}</td>
            <td>{!! $company->policyStockAdjWacCurrentYN !!}</td>
            <td>{!! $company->policyDepreciationRunDate !!}</td>
            <td>{!! $company->isGroup !!}</td>
            <td>{!! $company->isAttachementYN !!}</td>
            <td>{!! $company->reportingCriteria !!}</td>
            <td>{!! $company->reportingCriteriaFormQuery !!}</td>
            <td>{!! $company->supplierReportingCriteria !!}</td>
            <td>{!! $company->supplierReportingCriteriaFormQuery !!}</td>
            <td>{!! $company->supplierPOSavReportingCriteria !!}</td>
            <td>{!! $company->supplierPOSavReportingCriteriaFormQuery !!}</td>
            <td>{!! $company->supplierPOSpentReportingCriteriaFormQuery !!}</td>
            <td>{!! $company->exchangeGainLossGLCode !!}</td>
            <td>{!! $company->exchangeLossGLCode !!}</td>
            <td>{!! $company->exchangeGainGLCode !!}</td>
            <td>{!! $company->exchangeProvisionGLCode !!}</td>
            <td>{!! $company->exchangeProvisionGLCodeAR !!}</td>
            <td>{!! $company->isApprovalByServiceLine !!}</td>
            <td>{!! $company->isApprovalByServiceLineFinance !!}</td>
            <td>{!! $company->isTaxYN !!}</td>
            <td>{!! $company->isActive !!}</td>
            <td>{!! $company->isActiveGroup !!}</td>
            <td>{!! $company->showInCombo !!}</td>
            <td>{!! $company->allowBackDatedGRV !!}</td>
            <td>{!! $company->allowCustomerInvWithoutContractID !!}</td>
            <td>{!! $company->checkMaxQty !!}</td>
            <td>{!! $company->itemCodeMustInPR !!}</td>
            <td>{!! $company->op_OnOpenPopUpYN !!}</td>
            <td>{!! $company->showInNewRILRQHSE !!}</td>
            <td>{!! $company->createdUserGroup !!}</td>
            <td>{!! $company->createdPcID !!}</td>
            <td>{!! $company->createdUserID !!}</td>
            <td>{!! $company->modifiedPc !!}</td>
            <td>{!! $company->modifiedUser !!}</td>
            <td>{!! $company->createdDateTime !!}</td>
            <td>{!! $company->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['companies.destroy', $company->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('companies.show', [$company->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('companies.edit', [$company->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>