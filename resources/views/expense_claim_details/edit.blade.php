@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Expense Claim Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($expenseClaimDetails, ['route' => ['expenseClaimDetails.update', $expenseClaimDetails->id], 'method' => 'patch']) !!}

                        @include('expense_claim_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection