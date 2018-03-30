@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Chart Of Accounts Assigned
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($chartOfAccountsAssigned, ['route' => ['chartOfAccountsAssigneds.update', $chartOfAccountsAssigned->id], 'method' => 'patch']) !!}

                        @include('chart_of_accounts_assigneds.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection