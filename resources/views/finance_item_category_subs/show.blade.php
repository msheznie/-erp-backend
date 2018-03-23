@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Finance Item Category Sub
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('finance_item_category_subs.show_fields')
                    <a href="{!! route('financeItemCategorySubs.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
