@php
    $_isMultiTenants = isMultiTenants();

    if ($_isMultiTenants == 't')
    {
        $company_name = Auth::user()->branch->branch_name;
        $company_logo = Auth::user()->branch->branch_logo;
    }
    else
    {
        $company_name = dataConfigs('company_name');
        $company_logo = dataConfigs('app_logo_small');
    }
@endphp

<div id="kt_app_header" class="app-header" data-kt-sticky="true" data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize" data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between bg-dark" id="kt_app_header_container">
        <!--begin::Sidebar mobile toggle-->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-white w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-outline ki-abstract-14 fs-2 fs-md-1"></i>
            </div>
        </div>
        <!--end::Sidebar mobile toggle-->

        <!--begin::Mobile logo-->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="{{ route('homepage') }}" class="d-lg-none">
                <img alt="Logo" src="{{ asset($company_logo) }}" class="h-30px" />
            </a>
        </div>
        <!--end::Mobile logo-->

        <!--begin::Header wrapper-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-white fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ $company_name }}
                </h1>
                <!--end::Title-->

                <!--begin::Separator-->
                <span class="h-20px border-gray-300 border-start mx-4 text-white"></span>
                <h1 class="d-flex align-items-center text-white fw-bold fs-6 my-1">{{ dataConfigs('company_moto') }}</h1>
                <!--end::Separator-->
            </div>
            <!--end::Page title-->

            <!--begin::Navbar-->
            <div class="app-navbar flex-shrink-0">
                <!--begin::Chat-->
                <div class="app-navbar-item ms-1 ms-md-4">
                    <!--begin::Menu wrapper-->
                    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-dark w-35px h-35px position-relative" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" id="kt_menu_item_wow">
                        <i class="ki-duotone ki-notification-status fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>

                        <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                    </div>

                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true" id="kt_menu_notifications">
                        <!--begin::Heading-->
                        <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image: url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}')">
                            <!--begin::Title-->
                            <div class="d-flex align-items-center px-9 mt-10 mb-6">
                                <i class="fas fa-bell text-white me-3"></i>
                                <!--h3 class="text-white fw-bold px-9 mt-10 mb-6"-->
                                <h3 class="text-white fw-bold m-0">
                                    Pemberitahuan
                                    <span class="fs-8 opacity-75 ps-3 d-none" id="notif-count">0 Pesan</span>
                                </h3>
                            </div>
                            <!--end::Title-->

                            <!--begin::Tabs-->
                            <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9">
                                <li class="nav-item">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab" href="#kt_topbar_notifications_1">Pesan</a>
                                </li>
                                <li class="nav-item d-none">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_2">Notif</a>
                                </li>
                            </ul>
                            <!--end::Tabs-->
                        </div>
                        <!--end::Heading-->

                        <!--begin::Tab content-->
                        <div class="tab-content">
                            <!--begin::Tab panel-->
                            <div class="tab-pane fade show active" id="kt_topbar_notifications_1" role="tabpanel">
                                <div class="d-flex flex-column mh-350px" style="overflow-y: auto;">
                                    <div class="d-flex flex-column mt-2 mb-4 list-mymail-notif"></div>
                                </div>
                            </div>
                            <!--end::Tab panel-->

                            <!--begin::Tab panel-->
                            <div class="tab-pane fade" id="kt_topbar_notifications_2" role="tabpanel">
                                <div class="d-flex flex-column mh-350px" style="overflow-y: auto;">
                                    <div class="d-flex flex-column mt-2 mb-4 mymail-notif"></div>
                                </div>
                            </div>
                            <!--end::Tab panel-->
                        </div>
                        <!--end::Tab content-->
                    </div>
                    <!--end::Menu-->
                    <!--end::Menu wrapper-->
                </div>
                <!--end::Chat-->

                <!--begin::User menu-->
                <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
                    <!--begin::Menu wrapper-->
                    <div class="cursor-pointer symbol symbol-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <img src="{{ asset('assets/media/avatars/blank.png') }}" class="rounded-3" alt="user" />
                    </div>

                    <!--begin::User account menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="{{ asset('assets/media/avatars/blank.png') }}" />
                                </div>
                                <!--end::Avatar-->

                                <!--begin::Username-->
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">
                                        {{ Auth::user()->nama_lengkap }}

                                        @if (Auth::user()->is_admin == 't')
                                        <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">{{ Auth::user()->getAdminCaption() }}</span>
                                        @endif
                                    </div>

                                    @if ($_isMultiTenants == 't')
                                        <a href="javascript:void(0)" class="fw-semibold text-muted text-hover-dark fs-7">{{ Auth::user()->branch->branch_name }}</a>
                                    @else
                                        <a href="javascript:void(0)" class="fw-semibold text-muted text-hover-dark fs-7">{{ Auth::user()->email }}</a>
                                    @endif
                                </div>
                                <!--end::Username-->
                            </div>
                        </div>
                        <!--end::Menu item-->

                        <!--begin::Menu separator-->
                        <div class="separator my-2"></div>
                        <!--end::Menu separator-->

                        @if ($_isMultiTenants == 't')
                            <div class="menu-item px-5 mb-2">
                                <button class="btn btn-dark w-100 btn-sm ShowModalBranch">Pilih Cabang</button>
                            </div>
                        @endif

                        <!--begin::Menu item-->
                        <div class="menu-item px-5 my-1">
                            <a href="javascript:void(0)" class="menu-link px-5 konfigurasi-akun-modal">
                                <span class="las la-key text-dark me-4"></span>
                                Konfigurasi Akun
                            </a>
                        </div>
                        <!--end::Menu item-->

                        <!--begin::Menu item-->
                        <div class="menu-item px-5">
                            <a href="javascript:void(0)" class="menu-link px-5 proses-logout">
                                <span class="las la-sign-out-alt text-danger me-4"></span>
                                Keluar
                            </a>
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::User account menu-->
                    <!--end::Menu wrapper-->
                </div>
                <!--end::User menu-->
            </div>
            <!--end::Navbar-->
        </div>
        <!--end::Header wrapper-->
    </div>
    <!--end::Header container-->
</div>