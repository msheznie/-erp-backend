@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Pricing Schedule Master Edit Log
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($pricingScheduleMasterEditLog, ['route' => ['pricingScheduleMasterEditLogs.update', $pricingScheduleMasterEditLog->id], 'method' => 'patch']) !!}

                        @include('pricing_schedule_master_edit_logs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection