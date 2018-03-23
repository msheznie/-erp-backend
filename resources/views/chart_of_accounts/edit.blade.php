@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Chart Of Account
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($chartOfAccount, ['route' => ['chartOfAccounts.update', $chartOfAccount->id], 'method' => 'patch']) !!}

                        @include('chart_of_accounts.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection