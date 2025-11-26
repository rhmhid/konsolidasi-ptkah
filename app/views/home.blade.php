@php
    $_isMultiTenants = isMultiTenants();

    if ($_isMultiTenants == 't')
        $company_logo = Auth::user()->branch->branch_logo;
    else
        $company_logo = dataConfigs('company_logo');
@endphp

@extends('layouts.main')

@section('content')
<div class="card border border-gray-300 rounded-1">
    <div class="card-body p-0">
        <div class="d-flex justify-content-between flex-column p-4 px-6 pt-6">
            <div class="d-flex align-items-center">
                <div class="d-flex flex-column flex-grow-1">
                    <h3 >{{ $now }}</h3>

                    <h3 class="mt-3 mb-4">Hi, {{ Auth::user()->nama_lengkap }} Selamat datang di {{ dataConfigs('app_name_long') }}</h3>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column align-items-center justify-content-center h-100 w-100 mb-20 mt-5">
            <img src="{{ asset($company_logo) }}" class="text-center h-150px logo img-fluid mb-8" style="opacity: 0.6;" />

            <div class="text--local text--description text-center fw-bold fs-5 mt-5 text-dark">
                <div>Sebelum memulai penggunaan aplikasi, kami menyarankan anda untuk terlebih dahulu</div>
                <div>mempelajari penggunaan aplikasi.</div>
            </div>
        </div>
    </div>
</div>
@endsection