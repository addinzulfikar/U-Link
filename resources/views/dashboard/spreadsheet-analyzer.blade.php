@extends('layouts.admin')

@section('title', 'Upload Data Keuangan - U-LINK')

@section('page-title', 'Upload Data Keuangan')

@section('sidebar')
    @include('partials.admin.sidebar-admin-toko')
@endsection

@section('content')
    @livewire('spreadsheet-analyzer')
@endsection
