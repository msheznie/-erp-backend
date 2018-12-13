@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bank Reconciliation Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bankReconciliationRefferedBack, ['route' => ['bankReconciliationRefferedBacks.update', $bankReconciliationRefferedBack->id], 'method' => 'patch']) !!}

                        @include('bank_reconciliation_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection