@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Tender Final Bids
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($tenderFinalBids, ['route' => ['tenderFinalBids.update', $tenderFinalBids->id], 'method' => 'patch']) !!}

                        @include('tender_final_bids.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection