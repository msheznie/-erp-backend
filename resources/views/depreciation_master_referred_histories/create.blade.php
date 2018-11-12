@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Depreciation Master Referred History
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'depreciationMasterReferredHistories.store']) !!}

                        @include('depreciation_master_referred_histories.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
