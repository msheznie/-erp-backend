@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Item Issue Master Reffered Back
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('item_issue_master_reffered_backs.show_fields')
                    <a href="{!! route('itemIssueMasterRefferedBacks.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
