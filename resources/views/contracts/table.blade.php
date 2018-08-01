<table class="table table-responsive" id="contracts-table">
    <thead>
        <tr>
            <th>Contractnumber</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Clientid</th>
        <th>Cutomercode</th>
        <th>Servicelinecode</th>
        <th>Contractdescription</th>
        <th>Contstartdate</th>
        <th>Contenddate</th>
        <th>Contcurrencyid</th>
        <th>Contvalue</th>
        <th>Isinitialextcont</th>
        <th>Contextupto</th>
        <th>Linetechnicalincharge</th>
        <th>Linefinanceincharge</th>
        <th>Lineothersincharge</th>
        <th>Contractcreatedon</th>
        <th>Createdpcid</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Allowmultiplebillingyn</th>
        <th>Iscontract</th>
        <th>Allowrentalwithoutmityn</th>
        <th>Alloweditrentaldes</th>
        <th>Defaultrateinrental</th>
        <th>Allowedituom</th>
        <th>Rentaltemplate</th>
        <th>Contracttype</th>
        <th>Contractsubtype</th>
        <th>Bankid</th>
        <th>Accountid</th>
        <th>Vendoncode</th>
        <th>Paymentindaysforjob</th>
        <th>Ticketclientserialstart</th>
        <th>Secondarylogocomp</th>
        <th>Secondarylogname</th>
        <th>Secondarylogoactive</th>
        <th>Estrevserviceglcode</th>
        <th>Estrevproductglcode</th>
        <th>Isformulaapplicable</th>
        <th>Ophrsrounding</th>
        <th>Formulaophrsfromfield</th>
        <th>Formulaophrstofield</th>
        <th>Formulastandbyfield</th>
        <th>Isstandbyapplicable</th>
        <th>Customerrepname</th>
        <th>Customerrepemail</th>
        <th>Showcontdetinmot</th>
        <th>Showcontdetinmit</th>
        <th>Performatempid</th>
        <th>Timestamp</th>
        <th>Continvtemplate</th>
        <th>Isallowgeneratetransrental</th>
        <th>Isallowserviceentryinperforma</th>
        <th>Isalloweddefauldusage</th>
        <th>Actiontrackerenabled</th>
        <th>Webtemplate</th>
        <th>Isrequiredstamp</th>
        <th>Showsystemno</th>
        <th>Isallowedtoolswithoutmot</th>
        <th>Isdispacthavailable</th>
        <th>Isrequireappnewwell</th>
        <th>Ismorningreportavailable</th>
        <th>Iscontractactive</th>
        <th>Allowmutipleticketsinproforma</th>
        <th>Isserviceentryapplicable</th>
        <th>Isticketkpiapplicable</th>
        <th>Istickettotalapplicable</th>
        <th>Ismotassetdesceditable</th>
        <th>Mottemplate</th>
        <th>Mittemplate</th>
        <th>Rentaldates</th>
        <th>Invoicetemplate</th>
        <th>Rentalsheettemplate</th>
        <th>Isrequirednetworkrefno</th>
        <th>Formulalochrsfromfield</th>
        <th>Formulalochrstofield</th>
        <th>Isserviceapplicable</th>
        <th>Isallowtoedithours</th>
        <th>Contractstatus</th>
        <th>Tickettemplates</th>
        <th>Allowopstdydaysinmit</th>
        <th>Motprinttemplate</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($contracts as $contract)
        <tr>
            <td>{!! $contract->ContractNumber !!}</td>
            <td>{!! $contract->companySystemID !!}</td>
            <td>{!! $contract->CompanyID !!}</td>
            <td>{!! $contract->clientID !!}</td>
            <td>{!! $contract->CutomerCode !!}</td>
            <td>{!! $contract->ServiceLineCode !!}</td>
            <td>{!! $contract->contractDescription !!}</td>
            <td>{!! $contract->ContStartDate !!}</td>
            <td>{!! $contract->ContEndDate !!}</td>
            <td>{!! $contract->ContCurrencyID !!}</td>
            <td>{!! $contract->contValue !!}</td>
            <td>{!! $contract->isInitialExtCont !!}</td>
            <td>{!! $contract->ContExtUpto !!}</td>
            <td>{!! $contract->LineTechnicalIncharge !!}</td>
            <td>{!! $contract->LineFinanceIncharge !!}</td>
            <td>{!! $contract->LineOthersIncharge !!}</td>
            <td>{!! $contract->ContractCreatedON !!}</td>
            <td>{!! $contract->createdPcID !!}</td>
            <td>{!! $contract->createdUserGroup !!}</td>
            <td>{!! $contract->createdUserID !!}</td>
            <td>{!! $contract->createdDateTime !!}</td>
            <td>{!! $contract->modifiedPc !!}</td>
            <td>{!! $contract->modifiedUser !!}</td>
            <td>{!! $contract->allowMultipleBillingYN !!}</td>
            <td>{!! $contract->isContract !!}</td>
            <td>{!! $contract->allowRentalWithoutMITyn !!}</td>
            <td>{!! $contract->allowEditRentalDes !!}</td>
            <td>{!! $contract->defaultRateInRental !!}</td>
            <td>{!! $contract->allowEditUOM !!}</td>
            <td>{!! $contract->rentalTemplate !!}</td>
            <td>{!! $contract->contractType !!}</td>
            <td>{!! $contract->contractSubType !!}</td>
            <td>{!! $contract->bankID !!}</td>
            <td>{!! $contract->accountID !!}</td>
            <td>{!! $contract->vendonCode !!}</td>
            <td>{!! $contract->paymentInDaysForJob !!}</td>
            <td>{!! $contract->ticketClientSerialStart !!}</td>
            <td>{!! $contract->secondaryLogoComp !!}</td>
            <td>{!! $contract->secondaryLogName !!}</td>
            <td>{!! $contract->secondaryLogoActive !!}</td>
            <td>{!! $contract->estRevServiceGLcode !!}</td>
            <td>{!! $contract->estRevProductGLcode !!}</td>
            <td>{!! $contract->isFormulaApplicable !!}</td>
            <td>{!! $contract->opHrsRounding !!}</td>
            <td>{!! $contract->formulaOphrsFromField !!}</td>
            <td>{!! $contract->formulaOphrsToField !!}</td>
            <td>{!! $contract->formulaStandbyField !!}</td>
            <td>{!! $contract->isStandByApplicable !!}</td>
            <td>{!! $contract->customerRepName !!}</td>
            <td>{!! $contract->customerRepEmail !!}</td>
            <td>{!! $contract->showContDetInMOT !!}</td>
            <td>{!! $contract->showContDetInMIT !!}</td>
            <td>{!! $contract->performaTempID !!}</td>
            <td>{!! $contract->timeStamp !!}</td>
            <td>{!! $contract->contInvTemplate !!}</td>
            <td>{!! $contract->isAllowGenerateTransRental !!}</td>
            <td>{!! $contract->isAllowServiceEntryInPerforma !!}</td>
            <td>{!! $contract->isAllowedDefauldUsage !!}</td>
            <td>{!! $contract->actionTrackerEnabled !!}</td>
            <td>{!! $contract->webTemplate !!}</td>
            <td>{!! $contract->isRequiredStamp !!}</td>
            <td>{!! $contract->showSystemNo !!}</td>
            <td>{!! $contract->isAllowedToolsWithoutMOT !!}</td>
            <td>{!! $contract->isDispacthAvailable !!}</td>
            <td>{!! $contract->isRequireAppNewWell !!}</td>
            <td>{!! $contract->isMorningReportAvailable !!}</td>
            <td>{!! $contract->isContractActive !!}</td>
            <td>{!! $contract->allowMutipleTicketsInProforma !!}</td>
            <td>{!! $contract->isServiceEntryApplicable !!}</td>
            <td>{!! $contract->isTicketKPIApplicable !!}</td>
            <td>{!! $contract->isTicketTotalApplicable !!}</td>
            <td>{!! $contract->isMotAssetDescEditable !!}</td>
            <td>{!! $contract->motTemplate !!}</td>
            <td>{!! $contract->mitTemplate !!}</td>
            <td>{!! $contract->rentalDates !!}</td>
            <td>{!! $contract->invoiceTemplate !!}</td>
            <td>{!! $contract->rentalSheetTemplate !!}</td>
            <td>{!! $contract->isRequiredNetworkRefNo !!}</td>
            <td>{!! $contract->formulaLocHrsFromField !!}</td>
            <td>{!! $contract->formulaLocHrsToField !!}</td>
            <td>{!! $contract->isServiceApplicable !!}</td>
            <td>{!! $contract->isAllowToEditHours !!}</td>
            <td>{!! $contract->contractStatus !!}</td>
            <td>{!! $contract->ticketTemplates !!}</td>
            <td>{!! $contract->allowOpStdyDaysinMIT !!}</td>
            <td>{!! $contract->motprintTemplate !!}</td>
            <td>
                {!! Form::open(['route' => ['contracts.destroy', $contract->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('contracts.show', [$contract->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('contracts.edit', [$contract->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>