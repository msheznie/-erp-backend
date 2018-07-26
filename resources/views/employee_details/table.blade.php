<table class="table table-responsive" id="employeeDetails-table">
    <thead>
        <tr>
            <th>Employeesystemid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Empid</th>
        <th>Employeestatus</th>
        <th>Empimage</th>
        <th>Countrycode</th>
        <th>Expatorlocal</th>
        <th>Secondarynationality</th>
        <th>Dateassumed</th>
        <th>Dateassumed O</th>
        <th>Dob</th>
        <th>Dob O</th>
        <th>Placeofbirth</th>
        <th>Placeofbirth O</th>
        <th>Contactaddress1</th>
        <th>Contactaddress1 O</th>
        <th>Contactaddresscity</th>
        <th>Contactaddresscity O</th>
        <th>Contactaddresscountry</th>
        <th>Contactaddresscountry O</th>
        <th>Permenantaddress1</th>
        <th>Permenantaddress1 O</th>
        <th>Permenantaddresscity</th>
        <th>Permenantaddresscity O</th>
        <th>Permenantaddresscountry</th>
        <th>Permenantaddresscountry O</th>
        <th>Emplocation</th>
        <th>Locationtypeid</th>
        <th>Pasi Employercont</th>
        <th>Gender</th>
        <th>Pasiregno</th>
        <th>Pasi Employeecont</th>
        <th>Endofcontract</th>
        <th>Endofcontract O</th>
        <th>Manpower No</th>
        <th>Groupingid</th>
        <th>Holdsalary</th>
        <th>Categoryid</th>
        <th>Gradeid</th>
        <th>Schedulemasterid</th>
        <th>Departmentid</th>
        <th>Functionaldepartmentid</th>
        <th>Employeesgradingmasterid</th>
        <th>Designationid</th>
        <th>Maritialstatus</th>
        <th>Maritalstatusdate</th>
        <th>Noofkids</th>
        <th>Slbseniority</th>
        <th>Slbseniority O</th>
        <th>Wsiseniority</th>
        <th>Wsiseniority O</th>
        <th>Salarypaycurrency</th>
        <th>Iscontract</th>
        <th>Issso</th>
        <th>Emptax</th>
        <th>Gratuityid</th>
        <th>Ispermenant</th>
        <th>Isra</th>
        <th>Taxid</th>
        <th>Familystatus</th>
        <th>Groupraid</th>
        <th>Contractid</th>
        <th>Otcalculationhour</th>
        <th>Travelclaimcategoryid</th>
        <th>Newdepartmentid</th>
        <th>Medicalexaminationdate</th>
        <th>Medicalexamiiationexpirydate</th>
        <th>Isgeneral</th>
        <th>Rigassigned</th>
        <th>Employeecategoriesid</th>
        <th>Insurancecode</th>
        <th>Insurancetypeid</th>
        <th>Militaryservices</th>
        <th>Physicalstatus</th>
        <th>Isrehire</th>
        <th>Bloodtypeid</th>
        <th>Retiredate</th>
        <th>Workhour</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddate</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($employeeDetails as $employeeDetails)
        <tr>
            <td>{!! $employeeDetails->employeeSystemID !!}</td>
            <td>{!! $employeeDetails->companySystemID !!}</td>
            <td>{!! $employeeDetails->companyID !!}</td>
            <td>{!! $employeeDetails->empID !!}</td>
            <td>{!! $employeeDetails->employeestatus !!}</td>
            <td>{!! $employeeDetails->empimage !!}</td>
            <td>{!! $employeeDetails->countryCode !!}</td>
            <td>{!! $employeeDetails->expatOrLocal !!}</td>
            <td>{!! $employeeDetails->SecondaryNationality !!}</td>
            <td>{!! $employeeDetails->dateAssumed !!}</td>
            <td>{!! $employeeDetails->dateAssumed_O !!}</td>
            <td>{!! $employeeDetails->DOB !!}</td>
            <td>{!! $employeeDetails->DOB_O !!}</td>
            <td>{!! $employeeDetails->placeofBirth !!}</td>
            <td>{!! $employeeDetails->placeofBirth_O !!}</td>
            <td>{!! $employeeDetails->contactaddress1 !!}</td>
            <td>{!! $employeeDetails->contactaddress1_O !!}</td>
            <td>{!! $employeeDetails->contactaddresscity !!}</td>
            <td>{!! $employeeDetails->contactaddresscity_O !!}</td>
            <td>{!! $employeeDetails->contactaddresscountry !!}</td>
            <td>{!! $employeeDetails->contactaddresscountry_O !!}</td>
            <td>{!! $employeeDetails->permenantaddress1 !!}</td>
            <td>{!! $employeeDetails->permenantaddress1_O !!}</td>
            <td>{!! $employeeDetails->permenantaddresscity !!}</td>
            <td>{!! $employeeDetails->permenantaddresscity_O !!}</td>
            <td>{!! $employeeDetails->permenantaddresscountry !!}</td>
            <td>{!! $employeeDetails->permenantaddresscountry_O !!}</td>
            <td>{!! $employeeDetails->empLocation !!}</td>
            <td>{!! $employeeDetails->locationTypeID !!}</td>
            <td>{!! $employeeDetails->pasi_employercont !!}</td>
            <td>{!! $employeeDetails->gender !!}</td>
            <td>{!! $employeeDetails->pasiregno !!}</td>
            <td>{!! $employeeDetails->pasi_employeecont !!}</td>
            <td>{!! $employeeDetails->endOfContract !!}</td>
            <td>{!! $employeeDetails->endOfContract_O !!}</td>
            <td>{!! $employeeDetails->manpower_no !!}</td>
            <td>{!! $employeeDetails->groupingID !!}</td>
            <td>{!! $employeeDetails->holdSalary !!}</td>
            <td>{!! $employeeDetails->categoryID !!}</td>
            <td>{!! $employeeDetails->gradeID !!}</td>
            <td>{!! $employeeDetails->schedulemasterID !!}</td>
            <td>{!! $employeeDetails->departmentID !!}</td>
            <td>{!! $employeeDetails->functionalDepartmentID !!}</td>
            <td>{!! $employeeDetails->employeesgradingmasterID !!}</td>
            <td>{!! $employeeDetails->designationID !!}</td>
            <td>{!! $employeeDetails->maritialStatus !!}</td>
            <td>{!! $employeeDetails->maritalStatusDate !!}</td>
            <td>{!! $employeeDetails->noOfKids !!}</td>
            <td>{!! $employeeDetails->SLBSeniority !!}</td>
            <td>{!! $employeeDetails->SLBSeniority_O !!}</td>
            <td>{!! $employeeDetails->WSISeniority !!}</td>
            <td>{!! $employeeDetails->WSISeniority_O !!}</td>
            <td>{!! $employeeDetails->salaryPayCurrency !!}</td>
            <td>{!! $employeeDetails->isContract !!}</td>
            <td>{!! $employeeDetails->isSSO !!}</td>
            <td>{!! $employeeDetails->empTax !!}</td>
            <td>{!! $employeeDetails->gratuityID !!}</td>
            <td>{!! $employeeDetails->isPermenant !!}</td>
            <td>{!! $employeeDetails->isRA !!}</td>
            <td>{!! $employeeDetails->taxid !!}</td>
            <td>{!! $employeeDetails->familyStatus !!}</td>
            <td>{!! $employeeDetails->groupRAID !!}</td>
            <td>{!! $employeeDetails->contractID !!}</td>
            <td>{!! $employeeDetails->otcalculationHour !!}</td>
            <td>{!! $employeeDetails->travelclaimcategoryID !!}</td>
            <td>{!! $employeeDetails->newDepartmentID !!}</td>
            <td>{!! $employeeDetails->medicalExaminationDate !!}</td>
            <td>{!! $employeeDetails->medicalExamiiationExpirydate !!}</td>
            <td>{!! $employeeDetails->isGeneral !!}</td>
            <td>{!! $employeeDetails->rigAssigned !!}</td>
            <td>{!! $employeeDetails->employeeCategoriesID !!}</td>
            <td>{!! $employeeDetails->insuranceCode !!}</td>
            <td>{!! $employeeDetails->insuranceTypeID !!}</td>
            <td>{!! $employeeDetails->militaryServices !!}</td>
            <td>{!! $employeeDetails->physicalStatus !!}</td>
            <td>{!! $employeeDetails->isRehire !!}</td>
            <td>{!! $employeeDetails->bloodTypeID !!}</td>
            <td>{!! $employeeDetails->retireDate !!}</td>
            <td>{!! $employeeDetails->workHour !!}</td>
            <td>{!! $employeeDetails->createdUserGroup !!}</td>
            <td>{!! $employeeDetails->createdPCid !!}</td>
            <td>{!! $employeeDetails->createdUserID !!}</td>
            <td>{!! $employeeDetails->modifiedUser !!}</td>
            <td>{!! $employeeDetails->modifiedPc !!}</td>
            <td>{!! $employeeDetails->createdDate !!}</td>
            <td>{!! $employeeDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['employeeDetails.destroy', $employeeDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('employeeDetails.show', [$employeeDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('employeeDetails.edit', [$employeeDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>