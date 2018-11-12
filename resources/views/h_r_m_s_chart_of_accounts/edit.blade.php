@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            H R M S Chart Of Accounts
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($hRMSChartOfAccounts, ['route' => ['hRMSChartOfAccounts.update', $hRMSChartOfAccounts->id], 'method' => 'patch']) !!}

                        @include('h_r_m_s_chart_of_accounts.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection