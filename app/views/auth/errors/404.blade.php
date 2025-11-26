@extends('layouts.main')

@section('content')
<!--begin::Authentication - Signup Welcome Message -->
<div class="d-flex flex-column flex-root">
    <!--begin::Authentication - 404 Page-->
    <div class="d-flex flex-column flex-center flex-column-fluid p-10">
        <!--begin::Illustration-->
        <img src="{{ asset('assets/media/illustrations/sketchy-1/18.png') }}" alt="" class="mw-100 mb-5 h-lg-300px" />
        <!--end::Illustration-->

        <!--begin::Message-->
        <h1 class="fw-bold mb-10" style="color: #A3A3C7">Seems there is nothing here</h1>
        <!--end::Message-->

        <!--begin::Link-->
        <div class="text-center">
            <a href="{{ route('homepage') }}" class="btn btn-sm btn-dark">Return Home</a>
        </div>
        <!--end::Link-->
    </div>
    <!--end::Authentication - 404 Page-->
</div>
<!--end::Authentication - Signup Welcome Message-->
@endsection