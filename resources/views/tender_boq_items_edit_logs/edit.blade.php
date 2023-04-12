@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Tender Boq Items Edit Log
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($tenderBoqItemsEditLog, ['route' => ['tenderBoqItemsEditLogs.update', $tenderBoqItemsEditLog->id], 'method' => 'patch']) !!}

                        @include('tender_boq_items_edit_logs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection