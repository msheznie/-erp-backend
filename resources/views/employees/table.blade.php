<table class="table table-responsive" id="employees-table">
    <thead>
        <tr>
            <th>Empid</th>
        <th>Serial</th>
        <th>Empleadingtext</th>
        <th>Emppassword</th>
        <th>Empusername</th>
        <th>Emptitle</th>
        <th>Empinitial</th>
        <th>Empname</th>
        <th>Empname O</th>
        <th>Empfullname</th>
        <th>Empsurname</th>
        <th>Empsurname O</th>
        <th>Empfirstname</th>
        <th>Empfirstname O</th>
        <th>Empfamilyname</th>
        <th>Empfamilyname O</th>
        <th>Empfathername</th>
        <th>Empfathername O</th>
        <th>Empmanagerattached</th>
        <th>Empdateregistered</th>
        <th>Empteloffice</th>
        <th>Emptelmobile</th>
        <th>Emplandlineno</th>
        <th>Extno</th>
        <th>Empfax</th>
        <th>Empemail</th>
        <th>Emplocation</th>
        <th>Empdateterminated</th>
        <th>Emploginactive</th>
        <th>Empactive</th>
        <th>Usergroupid</th>
        <th>Empcompanyid</th>
        <th>Religion</th>
        <th>Isloggedin</th>
        <th>Isloggedoutfailyn</th>
        <th>Logingflag</th>
        <th>Issuperadmin</th>
        <th>Discharegedyn</th>
        <th>Hrusergroupid</th>
        <th>Isconsultant</th>
        <th>Istrainee</th>
        <th>Is3Rdparty</th>
        <th>3Rdpartycompanyname</th>
        <th>Gender</th>
        <th>Designation</th>
        <th>Nationality</th>
        <th>Ismanager</th>
        <th>Isapproval</th>
        <th>Isdashboard</th>
        <th>Isadmin</th>
        <th>Isbasicuser</th>
        <th>Activationcode</th>
        <th>Activationflag</th>
        <th>Ishr Admin</th>
        <th>Islock</th>
        <th>Oprptmanageraccess</th>
        <th>Issupportadmin</th>
        <th>Ishseadmin</th>
        <th>Excludeobjectivesyn</th>
        <th>Machineid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($employees as $employee)
        <tr>
            <td>{!! $employee->empID !!}</td>
            <td>{!! $employee->serial !!}</td>
            <td>{!! $employee->empLeadingText !!}</td>
            <td>{!! $employee->empPassword !!}</td>
            <td>{!! $employee->empUserName !!}</td>
            <td>{!! $employee->empTitle !!}</td>
            <td>{!! $employee->empInitial !!}</td>
            <td>{!! $employee->empName !!}</td>
            <td>{!! $employee->empName_O !!}</td>
            <td>{!! $employee->empFullName !!}</td>
            <td>{!! $employee->empSurname !!}</td>
            <td>{!! $employee->empSurname_O !!}</td>
            <td>{!! $employee->empFirstName !!}</td>
            <td>{!! $employee->empFirstName_O !!}</td>
            <td>{!! $employee->empFamilyName !!}</td>
            <td>{!! $employee->empFamilyName_O !!}</td>
            <td>{!! $employee->empFatherName !!}</td>
            <td>{!! $employee->empFatherName_O !!}</td>
            <td>{!! $employee->empManagerAttached !!}</td>
            <td>{!! $employee->empDateRegistered !!}</td>
            <td>{!! $employee->empTelOffice !!}</td>
            <td>{!! $employee->empTelMobile !!}</td>
            <td>{!! $employee->empLandLineNo !!}</td>
            <td>{!! $employee->extNo !!}</td>
            <td>{!! $employee->empFax !!}</td>
            <td>{!! $employee->empEmail !!}</td>
            <td>{!! $employee->empLocation !!}</td>
            <td>{!! $employee->empDateTerminated !!}</td>
            <td>{!! $employee->empLoginActive !!}</td>
            <td>{!! $employee->empActive !!}</td>
            <td>{!! $employee->userGroupID !!}</td>
            <td>{!! $employee->empCompanyID !!}</td>
            <td>{!! $employee->religion !!}</td>
            <td>{!! $employee->isLoggedIn !!}</td>
            <td>{!! $employee->isLoggedOutFailYN !!}</td>
            <td>{!! $employee->logingFlag !!}</td>
            <td>{!! $employee->isSuperAdmin !!}</td>
            <td>{!! $employee->discharegedYN !!}</td>
            <td>{!! $employee->hrusergroupID !!}</td>
            <td>{!! $employee->isConsultant !!}</td>
            <td>{!! $employee->isTrainee !!}</td>
            <td>{!! $employee->is3rdParty !!}</td>
            <td>{!! $employee->3rdPartyCompanyName !!}</td>
            <td>{!! $employee->gender !!}</td>
            <td>{!! $employee->designation !!}</td>
            <td>{!! $employee->nationality !!}</td>
            <td>{!! $employee->isManager !!}</td>
            <td>{!! $employee->isApproval !!}</td>
            <td>{!! $employee->isDashBoard !!}</td>
            <td>{!! $employee->isAdmin !!}</td>
            <td>{!! $employee->isBasicUser !!}</td>
            <td>{!! $employee->ActivationCode !!}</td>
            <td>{!! $employee->ActivationFlag !!}</td>
            <td>{!! $employee->isHR_admin !!}</td>
            <td>{!! $employee->isLock !!}</td>
            <td>{!! $employee->opRptManagerAccess !!}</td>
            <td>{!! $employee->isSupportAdmin !!}</td>
            <td>{!! $employee->isHSEadmin !!}</td>
            <td>{!! $employee->excludeObjectivesYN !!}</td>
            <td>{!! $employee->machineID !!}</td>
            <td>{!! $employee->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['employees.destroy', $employee->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('employees.show', [$employee->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('employees.edit', [$employee->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>