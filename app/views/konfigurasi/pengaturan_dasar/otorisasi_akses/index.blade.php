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
                                Otorisasi Akses
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm w-100 rounded-1 btn-light-dark otorisasi-akses-add" href="javascript:void(0)" modal-trigger="1">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-otorisasi-akses" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-6">
                                <label class="text-dark fw-bold fs-7 pb-2">Group Otorisasi</label>
                                {!! $cmb_group !!}
                            </div>

                            <div class="col-lg-5">
                                <label class="text-dark fw-bold fs-7 pb-2">Nama User</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_nama_user" placeholder="Nama User" />
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
                <table id="myTableOtorisasi" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="py-5">Group Otorisasi</th>
                            <th class="border-start py-5">Nama User</th>
                            <th class="border-start py-5">User Input</th>
                            <th class="border-start py-5">Waktu Input</th>
                            <th class="border-start py-5 w-100px">Fungsi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Ubah-->
<div class="modal fade" id="popup_create_otorisasi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_create_otorisasi" aria-hidden="true">
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
    // DATATABLE myTableOtorisasi
    const ajaxOptions = {
        url     : "{{ route('api.pengaturan_dasar.otorisasi_akses') }}",
        data    : function (params)
                {
                    params.s_otogid     = $("#s_otogid").val()
                    params.s_nama_user  = $("#s_nama_user").val()
                },
    }

    const options = {
        columns: [
                    {
                        data: 'os_group', 
                        name: 'os_group',
                    },
                    {
                        data: 'os_user', 
                        name: 'os_user',
                    },
                    {
                        data: 'user', 
                        name: 'user',
                    },
                    {
                        data: 'waktu', 
                        name: 'wa ktu',
                    },
                    {
                        data: 'otoid',
                        name: 'otoid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-otoid="${row.otoid}" title="Hapus" class="btn btn-danger btn-sm fs-8 otorisasi-akses-cancel">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: "text-center",
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        /*rowGroup:   {
                        dataSrc: [ 'os_group']
                    },*/

        "drawCallback": function (settings) {
            var api = this.api()
            var rows = api.rows({
                page: "current"
            }).nodes()
            var last = null

            api.column(0, {
                page: "current"
            }).data().each(function(group, i) {
                if (last !== group) {
                    $(rows).eq(i).before(
                        "<tr class=\"group fs-5 fw-bolder\"><td colspan=\"5\">" + group + "</td></tr>"
                    )

                    last = group
                }
            })
        },

        columnDefs: [ {
                        targets: [ 0 ],
                        visible: false
                    } ],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableOtorisasi',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-otorisasi-akses').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.otorisasi-akses-add').click(function ()
    {
        // api.pengaturan_dasar.otorisasi_akses.create
        showLoading()

        getForm().then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_create_otorisasi', res, null, true)
        })
    })

    async function getForm ()
    {
        let result
        let link = "{{ route('api.pengaturan_dasar.otorisasi_akses.create') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }

    $('#myTableOtorisasi').on('click', '.otorisasi-akses-cancel', function ()
    {
        const otoid = $(this).data('otoid')

        Swal.fire({
            html: 'Apakah Anda Yakin <span class="badge badge-danger">akan menghapus data ini</span> ?',
            icon: "info",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Ya, Hapus !",
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: "btn btn-dark",
                cancelButton: 'btn btn-danger'
            }
        }).then((result) =>
        {
            if (result.isConfirmed)
            {
                const payload = new FormData()
                    payload.append('otoid', otoid)

                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

                formSubmitUrl = "{{ route('api.pengaturan_dasar.otorisasi_akses.delete') }}"

                showLoading()

                setTimeout((function ()
                {
                    doAjax(
                        formSubmitUrl,
                        payload,
                        "POST"
                    )
                    .done( data => {
                        if (data.success)
                        {
                            swalShowMessage('Sukses', data.message, 'success')
                            .then((result) =>
                            {
                                if (result.isConfirmed) table.ajax.reload(null, false)
                            })
                        }
                        else swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                    })
                    .fail( err => {
                        const res = err?.responseJSON
                        swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                    })
                }), 2e3)
            }
        })
    })
</script>
@endpush