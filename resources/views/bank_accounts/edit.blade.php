@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bank Account
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bankAccount, ['route' => ['bankAccounts.update', $bankAccount->id], 'method' => 'patch']) !!}

                        @include('bank_accounts.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection