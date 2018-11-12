@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Asset Master Referred History
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($fixedAssetMasterReferredHistory, ['route' => ['fixedAssetMasterReferredHistories.update', $fixedAssetMasterReferredHistory->id], 'method' => 'patch']) !!}

                        @include('fixed_asset_master_referred_histories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection