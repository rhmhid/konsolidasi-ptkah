@extends('layouts.main')

@section('content')
<div class="d-flex flex-column flex-lg-row">
    <!--begin::Content-->
    <div class="flex-lg-row-fluid">
        <!--begin::Contents-->
        <div class="card border border-gray-300 rounded-1">
            <!--begin::Card header-->
            <div class="card-body p-0" id="kt_content_header">
                <div class="d-flex justify-content-between flex-column border-bottom border-gray-300 p-4 px-6">
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column flex-grow-1">
                            <h2 class="pt-2 text-dark">
                                <span class="la la-users text-dark me-4"></span>
                                Data User
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm w-100 rounded-1 btn-light-dark hak-akses-add" href="javascript:void(0)" modal-trigger="1">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-hak-akses" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-11">
                                <label class="text-dark fw-bold fs-7 pb-2">Username / NRP / Nama Pegawai</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="kode_nama_user" placeholder="Cari Bedasarkan User / NRP / Nama Pegawai" />
                                </div>
                            </div>

                            <div class="col-lg-1">
                                <label class="text-dark fw-bold fs-8 pb-2 d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-dark rounded-1 me-4 w-100" id="btncari">
                                    <i class="la la-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Compact form-->
                </form>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTableHakAkses" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5 text-center">NRP</th>
                            <th class="border-start py-5 text-center">Nama Lengkap</th>
                            <th class="border-start py-5 text-center">Username</th>
                            <th class="border-start py-5 text-center">Status Akses</th>
                            <th class="border-start py-5 text-center w-100px">Fungsi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Ubah-->
<div class="modal fade" id="popup_form_hak_akses" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_hak_akses" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>

<div class="modal fade" id="popup_form_password" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_password" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Ubah-->
@endsection

@push('script')
<script type="text/javascript">
    // DATATABLE myTableHakAkses
    const ajaxOptions = {
        url: "{{ route('api.user_management.akses_aplikasi.hak_akses') }}",
        data: function(params) {
                params.kode_nama_user   = $("#kode_nama_user").val()
            },
    }

    const options = {
        columns:[
                    {
                        data: 'nrp',
                        name: 'nrp',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap',
                    },
                    {
                        data: 'username',
                        name: 'username',
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            let status = 'danger'
                            let icon = 'times'

                            if (data == 't')
                            {
                                status = 'success'
                                icon = 'check'
                            }

                            let $icon = `<div class="symbol symbol-20px symbol-circle">
                                            <div class="symbol-label fs-8 fw-bold bg-light-${status} text-${status}">
                                                <span class="fas fa-${icon}"></span>  
                                            </div>
                                        </div>`

                            return $icon
                        },
                    },
                    {
                        data: 'pid',
                        name: 'pid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<button class="btn btn-sm btn-icon btn-dark" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="las la-ellipsis-h fs-4"></i>
                                        </button>

                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item py-2 hak-akses-update" data-fields="all" data-pid="${row.pid}">
                                                    <i class="las la-user-lock fs-3 text-dark me-2"></i> Ubah Data
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item py-2 hak-akses-update" data-fields="password" data-pid="${row.pid}">
                                                    <i class="las la-key fs-3 text-dark me-2"></i> Ganti Password
                                                </a>
                                            </li>
                                        </ul>`

                            return $btnFungsi
                        },

                        sortable: false,
                        className: 'dt-body-center',
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableHakAkses',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-hak-akses').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.hak-akses-add').on('click', function ()
    {
        showLoading()

        let getForm = async function ()
        {
            let result

            try {
                result = await $.ajax({
                    url     : "{{ route('api.user_management.akses_aplikasi.hak_akses.create') }}",
                    type    : 'GET',
                    data    : { }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
            }
        }

        getForm().then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_hak_akses', res, null, true)
        })
    })

    $('#myTableHakAkses').on('click', '.hak-akses-update', function ()
    {
        const fields = $(this).data('fields')
        const pid = $(this).data('pid')

        showLoading()

        let getEdit = async function (fields, pid)
        {
            let result
            let myurl = fields == 'password' ? "{{ route('api.user_management.akses_aplikasi.hak_akses.edit_pass') }}" : "{{ route('api.user_management.akses_aplikasi.hak_akses.edit') }}"

            try {
                result = await $.ajax({
                    url     : myurl,
                    type    : 'GET',
                    data    : { fields: fields, pid: pid }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
            }
        }

        getEdit(fields, pid).then((res) => {
            Swal.close()

            let modals_name = fields == 'password' ? 'popup_form_password' : 'popup_form_hak_akses'

            // Show modal
            openModal(modals_name, res, null, true)
        })
    })
</script>
@endpush