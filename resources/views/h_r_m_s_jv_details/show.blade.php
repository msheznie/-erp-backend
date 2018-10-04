@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            H R M S Jv Details
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('h_r_m_s_jv_details.show_fields')
                    <a href="{!! route('hRMSJvDetails.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
