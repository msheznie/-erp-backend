@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Po Advance Payment
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'poAdvancePayments.store']) !!}

                        @include('po_advance_payments.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
