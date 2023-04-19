@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Srm Tender Bid Employee Details Edit Log
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($srmTenderBidEmployeeDetailsEditLog, ['route' => ['srmTenderBidEmployeeDetailsEditLogs.update', $srmTenderBidEmployeeDetailsEditLog->id], 'method' => 'patch']) !!}

                        @include('srm_tender_bid_employee_details_edit_logs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection