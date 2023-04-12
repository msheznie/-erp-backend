@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Pricing Schedule Detail Edit Log
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($pricingScheduleDetailEditLog, ['route' => ['pricingScheduleDetailEditLogs.update', $pricingScheduleDetailEditLog->id], 'method' => 'patch']) !!}

                        @include('pricing_schedule_detail_edit_logs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection