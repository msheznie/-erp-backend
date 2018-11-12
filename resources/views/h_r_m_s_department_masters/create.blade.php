@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            H R M S Department Master
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'hRMSDepartmentMasters.store']) !!}

                        @include('h_r_m_s_department_masters.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
