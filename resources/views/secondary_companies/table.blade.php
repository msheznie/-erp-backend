<div class="table-responsive">
    <table class="table" id="secondaryCompanies-table">
        <thead>
            <tr>
                <th>Companysystemid</th>
        <th>Logo</th>
        <th>Name</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($secondaryCompanies as $secondaryCompany)
            <tr>
                <td>{!! $secondaryCompany->companySystemID !!}</td>
            <td>{!! $secondaryCompany->logo !!}</td>
            <td>{!! $secondaryCompany->name !!}</td>
                <td>
                    {!! Form::open(['route' => ['secondaryCompanies.destroy', $secondaryCompany->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('secondaryCompanies.show', [$secondaryCompany->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('secondaryCompanies.edit', [$secondaryCompany->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
