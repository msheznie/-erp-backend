@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Report Template Cash Bank
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'reportTemplateCashBanks.store']) !!}

                        @include('report_template_cash_banks.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
