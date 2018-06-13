@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Company Finance Year
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($companyFinanceYear, ['route' => ['companyFinanceYears.update', $companyFinanceYear->id], 'method' => 'patch']) !!}

                        @include('company_finance_years.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection