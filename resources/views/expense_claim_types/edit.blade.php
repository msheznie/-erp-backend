@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Expense Claim Type
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($expenseClaimType, ['route' => ['expenseClaimTypes.update', $expenseClaimType->id], 'method' => 'patch']) !!}

                        @include('expense_claim_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection