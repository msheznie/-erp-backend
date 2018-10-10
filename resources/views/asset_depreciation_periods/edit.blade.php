@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Asset Depreciation Period
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($assetDepreciationPeriod, ['route' => ['assetDepreciationPeriods.update', $assetDepreciationPeriod->id], 'method' => 'patch']) !!}

                        @include('asset_depreciation_periods.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection