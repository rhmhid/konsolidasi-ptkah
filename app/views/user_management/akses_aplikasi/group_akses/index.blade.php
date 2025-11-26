
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
                                <span class="la la-gear text-dark me-4"></span>
                                Group Akses
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm w-100 rounded-1 btn-light-dark group-akses-add" data-rgid="" href="javascript:void(0)" modal-trigger="1">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-group-akses" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-11">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Group Akses</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="kode_nama_group" placeholder="Kode / Nama Group Akses" />
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
                <table id="myTableGroupAkses" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5 text-center">Kode Group Akses</th>
                            <th class="border-start py-5 text-center">Nama Group Akses</th>
                            <th class="border-start py-5 text-center">Status</th>
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
<div class="modal fade" id="popup_edit_group_akses" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false" tabindex="-1" role="dialog" aria-labelledby="popup_edit_group_akses" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Ubah-->
@endsection

@push('script')
<script type="text/javascript">
    // DATATABLE myTableGroupAkses
    const ajaxOptions = {
        url: "{{ route('api.user_management.akses_aplikasi.group_akses') }}",
        data: function(params) {
                params.kode_nama_group = $("#kode_nama_group").val()
            },
    }

    const options = {
        columns:[
                    {
                        data: 'role_kode', 
                        name: 'role_kode',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'role_name', 
                        name: 'role_name',
                    },
                    {
                        data: 'is_aktif',
                        name: 'is_aktif',

                        render: function (data, type, row, meta)
                        {
                            $btnstatus = `<span class="badge badge-${row.status_css}">
                                            <i class="bi bi-${row.status_icon} text-light fs-3"></i>
                                            &nbsp;${row.status_txt}
                                        </span>`

                            return $btnstatus
                        },

                        className: 'dt-body-center',
                    },
                    {
                        data: 'rgid',
                        name: 'rgid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-rgid="${row.rgid}" title="Ubah" class="btn btn-dark btn-sm btn-icon fs-8 group-akses-update">
                                            <i class="las la-edit fs-4"></i>
                                        </a>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: 'dt-body-center',
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableGroupAkses',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-group-akses').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.group-akses-add').on('click', function ()
    {
        const rgid = $(this).data('rgid')

        openGroupAksesEditModal(rgid)
    })

    $('#myTableGroupAkses').on('click', '.group-akses-update', function ()
    {
        const rgid = $(this).data('rgid')

        openGroupAksesEditModal(rgid)
    })

    function openGroupAksesEditModal (rgid = 0)
    {
        showLoading()

        getForm(rgid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_edit_group_akses', res, null, true)
        })
    }

    async function getForm (rgid)
    {
        let result
        let link = rgid == '' ? "{{ route('api.user_management.akses_aplikasi.group_akses.create') }}" : "{{ route('api.user_management.akses_aplikasi.group_akses.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { rgid: rgid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush