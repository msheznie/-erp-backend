@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Commercial Bid Ranking Items
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'commercialBidRankingItems.store']) !!}

                        @include('commercial_bid_ranking_items.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
