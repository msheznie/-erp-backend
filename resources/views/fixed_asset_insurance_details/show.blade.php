@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Asset Insurance Detail
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('fixed_asset_insurance_details.show_fields')
                    <a href="{!! route('fixedAssetInsuranceDetails.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
