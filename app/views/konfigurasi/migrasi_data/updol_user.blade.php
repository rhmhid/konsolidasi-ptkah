@extends('layouts.main')

@section('content')

@include('layouts.migrasi')

<div class="d-flex flex-column flex-lg-row">
    <!--begin::Content-->
    <div class="flex-lg-row-fluid">
        <!--begin::Contents-->
        <div class="card rounded-1 border border-gray-300">
            <!--begin::Card header-->
            <div class="card-body p-0" id="kt_content_header">
                <div class="d-flex justify-content-between flex-column border-bottom border-gray-300 p-4 px-6">
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column flex-grow-1">
                            <h2 class="pt-2 text-dark">
                                <span class="las la-users text-dark me-4"></span>
                                Form Upload Data User Akses
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="border-bottom border-gray-300">
                    <form method="post" id="form-upload-user-akses" novalidate="">
                        <!--begin::Compact form-->
                        <div class="p-6 pb-0">
                            <div class="row g-0 gx-4">
                                <div class="col-lg-12">
                                    <label class="text-dark fw-bold fs-7 pb-2 required">Upload File</label>
                                    <input type="file" name="chooseFile" id="chooseFile" class="form-control form-control-sm rounded-1 w-100" required="" />
                                </div>
                            </div>

                            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                                <!--begin::Trigger-->
                                <button type="button" class="btn btn-danger btn-sm rounded-1 me-4 w-100" id="me_btn_download" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start">
                                    <i class="la la-file-csv"></i> Download Template (.csv)
                                </button>
                                <!--end::Trigger-->

                                <!--begin::Menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-dark fw-bold fs-7 w-200px py-4" data-kt-menu="true">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a class="menu-link px-3" href="javascript:void(0)" id="a_down" data-status-akses="0">
                                            Semua Akses
                                        </a>
                                    </div>
                                    <!--end::Menu item-->

                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a class="menu-link px-3" href="javascript:void(0)" id="a_down" data-status-akses="1">
                                            Belum Setting
                                        </a>
                                    </div>
                                    <!--end::Menu item-->

                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a class="menu-link px-3" href="javascript:void(0)" id="a_down" data-status-akses="2">
                                            Sudah Setting
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu-->

                                <button type="submit" class="btn btn-dark btn-sm rounded-1 w-100" id="me_btn_upload">
                                    <i class="la la-file-csv"></i> Upload Template (.csv)
                                </button>
                            </div>
                        </div>
                        <!--end::Compact form-->
                    </form>
                </div>

                <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                    <table id="myTableGroupAkses" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                        <thead class="bg-dark text-uppercase fs-7">
                            <tr class="fw-bold text-white">
                                <th class="border-start py-5">ID</th>
                                <th class="border-start py-5">Kode Group Akses</th>
                                <th class="border-start py-5">Nama Group Akses</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    // DATATABLE myTableGroupAkses
    const ajaxOptions = {
        url: "{{ route('api.migrasi_data.updol.user.list') }}",
    }

    const options = {
        columns:[
                    {
                        data: 'rgid', 
                        name: 'rgid',
                        className: 'text-center',
                    },
                    {
                        data: 'role_kode', 
                        name: 'role_kode',
                        className: 'text-center',
                    },
                    {
                        data: 'role_name', 
                        name: 'role_name',
                    },
                ],

        order: [[0, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableGroupAkses',
                ajaxOptions,
                options
            )

    $(document).off('click', '#a_down').on('click', '#a_down', function ()
    {
        const $btn_down = $(this)
        const status_akses = $btn_down.attr('data-status-akses')

        showLoading()

        setTimeout((function ()
        {
            swal.close()

            var myUrl = "{{ route('migrasi_data.download.user', ['type' => ':type']) }}"
                myUrl = myUrl.replace(':type', status_akses)

            window.location.replace(myUrl)
        }), 2e3)
    })

    $('#form-upload-user-akses').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const $form = $(this)
        const url = $form.attr('action')
        const $chooseFile = $('[name="chooseFile"]')

        if (!$chooseFile.val())
            return swalShowMessage('Warning!', "Template (.csv) Belum Dipilih.", 'warning')

        const payload = new FormData(this)
            payload.append('_method', 'patch') // ganti ajax method post menjadi patch

        formSubmitUrl = "{{ route('api.migrasi_data.updol.user.save') }}"

        showLoading()

        setTimeout((function ()
        {
            e.removeAttribute("data-kt-indicator"), e.disabled = !1, doAjax(
                formSubmitUrl,
                payload,
                "POST"
            )
            .done( data => {
                swal.close()

                if (data.success) swalShowMessage('Sukses', data.message, 'success')
                else swalShowMessage('Error', data.message || 'An Error Occured.', 'error')
            })
            .fail( err => {
                swal.close()

                swalShowMessage('Error', err?.responseJSON?.message || 'An Error Occured.', 'error')
            })
        }), 2e3)
    })
</script>
@endpush