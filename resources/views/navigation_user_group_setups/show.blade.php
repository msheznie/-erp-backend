@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Navigation User Group Setup
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('navigation_user_group_setups.show_fields')
                    <a href="{!! route('navigationUserGroupSetups.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
