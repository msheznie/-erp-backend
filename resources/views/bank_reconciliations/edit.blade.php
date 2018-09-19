@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bank Reconciliation
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bankReconciliation, ['route' => ['bankReconciliations.update', $bankReconciliation->id], 'method' => 'patch']) !!}

                        @include('bank_reconciliations.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection