@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Employees Department
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'employeesDepartments.store']) !!}

                        @include('employees_departments.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
