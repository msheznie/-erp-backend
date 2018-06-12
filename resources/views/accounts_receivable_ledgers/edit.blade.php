@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Accounts Receivable Ledger
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($accountsReceivableLedger, ['route' => ['accountsReceivableLedgers.update', $accountsReceivableLedger->id], 'method' => 'patch']) !!}

                        @include('accounts_receivable_ledgers.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection