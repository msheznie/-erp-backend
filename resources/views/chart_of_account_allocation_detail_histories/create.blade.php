@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Chart Of Account Allocation Detail History
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'chartOfAccountAllocationDetailHistories.store']) !!}

                        @include('chart_of_account_allocation_detail_histories.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
