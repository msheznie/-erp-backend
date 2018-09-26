@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Asset Category
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('fixed_asset_categories.show_fields')
                    <a href="{!! route('fixedAssetCategories.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
