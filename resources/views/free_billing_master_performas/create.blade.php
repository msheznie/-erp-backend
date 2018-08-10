@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Free Billing Master Performa
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'freeBillingMasterPerformas.store']) !!}

                        @include('free_billing_master_performas.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
