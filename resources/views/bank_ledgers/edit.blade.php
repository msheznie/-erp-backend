@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bank Ledger
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bankLedger, ['route' => ['bankLedgers.update', $bankLedger->id], 'method' => 'patch']) !!}

                        @include('bank_ledgers.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection