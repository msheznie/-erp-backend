@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Asset Master Referred History
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'fixedAssetMasterReferredHistories.store']) !!}

                        @include('fixed_asset_master_referred_histories.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
