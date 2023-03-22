@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Commercial Bid Ranking Items
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('commercial_bid_ranking_items.show_fields')
                    <a href="{{ route('commercialBidRankingItems.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
