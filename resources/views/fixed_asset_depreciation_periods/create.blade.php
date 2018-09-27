@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Asset Depreciation Period
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'fixedAssetDepreciationPeriods.store']) !!}

                        @include('fixed_asset_depreciation_periods.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
