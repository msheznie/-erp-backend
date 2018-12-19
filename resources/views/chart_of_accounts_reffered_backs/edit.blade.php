@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Chart Of Accounts Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($chartOfAccountsRefferedBack, ['route' => ['chartOfAccountsRefferedBacks.update', $chartOfAccountsRefferedBack->id], 'method' => 'patch']) !!}

                        @include('chart_of_accounts_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection