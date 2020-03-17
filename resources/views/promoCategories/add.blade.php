@extends('layouts.master')

@section('title', 'إضافة  قسم جديد ')

@section('styles')
    {!! Html::style('css/form.css') !!}
@stop

@section('content')
    @section('breadcrumbs')
        {!! Breadcrumbs::render('add.promoCategories') !!}
    @stop

    <div class="page-content">
        <div class="col-md-12">
            <div class="page-header">
                <h1><i class="menu-icon fa fa-magic"></i> إضافة  قسم جديد  </h1>
            </div>
        </div>

        <div class="col-md-12">
            {{ Form::open(['route' => 'admin.promoCategories.store', 'class' => 'form','files' => true]) }}
                @include('promoCategories.form', ['btn' => 'حفظ'])
            {{ Form::close() }}
        </div>
    </div>
@stop
