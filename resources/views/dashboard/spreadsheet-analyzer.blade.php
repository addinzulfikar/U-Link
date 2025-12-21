@extends('layouts.admin')

@section('title', 'Analisis Spreadsheet - U-LINK')

@section('page-title', 'Analisis Spreadsheet')

@section('sidebar')
    @include('partials.admin.sidebar-admin-toko')
@endsection

@section('content')
    @livewire('spreadsheet-analyzer')
@endsection
