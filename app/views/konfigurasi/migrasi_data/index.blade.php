@extends('layouts.main')

@section('content')

@include('layouts.migrasi')

<div class="mt-20 py-20" style="margin-top: 150px !important;">
    <!--begin::Wrapper-->
    <div class="mb-10">
        <!--begin::Top-->
        <div class="text-center mb-15">
            <!--begin::Title-->
            <h1 class="fs-2hx text-dark mb-5">Modul Migrasi Data</h1>
            <!--end::Title-->

            <!--begin::Text-->
            <div class="fs-5 text-muted fw-bold">
                <h3><em>{{ dataConfigs('app_name_long') }}</em></h3>
                @if (isMultiTenants() == 't')
                Site : {{ Auth::user()->branch->branch_name }}
                @else
                Site : {{ dataConfigs('company_name') }}
                @endif
            </div>
            <!--end::Text-->
        </div>
        <!--end::Top-->
    </div>
    <!--end::Wrapper-->
</div>
@endsection