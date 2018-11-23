@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Direct Receipt Details Reffered History
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'directReceiptDetailsRefferedHistories.store']) !!}

                        @include('direct_receipt_details_reffered_histories.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
