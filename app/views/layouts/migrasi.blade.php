<!--begin::Navbar-->
<div class="card mb-3 px-6">
    <!--begin::Navs-->
    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-7 fw-bolder">
        <!--begin::Nav item-->
        <li class="nav-item">
            <a class="nav-link text-active-dark ms-0 py-5" href="{{ route('migrasi_data.reset_data') }}">Reset Data</a>
        </li>
        <!--end::Nav item-->

        <!--begin::Nav item-->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Upload Masterdata</a>

            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item text-active-dark ms-0 py-2" href="{{ route('migrasi_data.updol.pegawai') }}">Pegawai</a>
                </li>
                <li>
                    <a class="dropdown-item text-active-dark ms-0 py-2" href="{{ route('migrasi_data.updol.user') }}">User Akses</a>
                </li>
                <li>
                    <a class="dropdown-item text-active-dark ms-0 py-2" href="{{ route('migrasi_data.import_barang') }}">Barang</a>
                </li>
            </ul>
        </li>
        <!--end::Nav item-->

        <!--begin::Nav item-->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Data Akunting</a>

            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item text-active-dark ms-0 py-2" href="{{ route('migrasi_data.akunting.import_tb') }}">Import Trial Balance</a>
                </li>
                <li>
                    <a class="dropdown-item text-active-dark ms-0 py-2" href="{{ route('migrasi_data.akunting.balance_ledger_tb') }}">Balance Ledger TB</a>
                </li>
                <li>
                    <a class="dropdown-item text-active-dark ms-0 py-2" href="{{ route('migrasi_data.akunting.import_manual_ap') }}">Import Manual A/P</a>
                </li>
            </ul>
        </li>
        <!--end::Nav item-->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Inventory</a>

            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item text-active-dark ms-0 py-2" href="{{ route('migrasi_data.import_stok') }}">Import Stok</a>
                </li>
            </ul>
        </li>

        <!--begin::Nav item-->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Jasper Test</a>

            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item text-active-dark ms-0 py-2" href="{{ route('jasper.test_statis') }}" target="_blank">Show PDF Test</a>
                </li>
                <li>
                    <a class="dropdown-item text-active-dark ms-0 py-2" href="{{ route('jasper.test_statis_ori') }}" target="_blank">Show PDF Test Original</a>
                </li>
                <li>
                    <a class="dropdown-item text-active-dark ms-0 py-2" href="{{ route('jasper.test_json') }}" target="_blank">Show PDF Test Json</a>
                </li>
            </ul>
        </li>
        <!--end::Nav item-->
    </ul>
    <!--begin::Navs-->
</div>
<!--end::Navbar-->
