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
                                <span class="las la-users text-dark me-4"></span>
                                Data Pegawai
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm w-100 rounded-1 btn-light-dark pegawai-add" href="javascript:void(0)" data-pid="" modal-trigger="1">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-pegawai" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-11">
                                <label class="text-dark fw-bold fs-7 pb-2">NRP / Nama Pegawai</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="kode_nama_emp" placeholder="NRP / Nama Pegawai" />
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
                <table id="myTablePegawai" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5 text-center">NRP</th>
                            <th class="border-start py-5 text-center">Nama Lengkap</th>
                            <th class="border-start py-5 text-center">Alamat</th>
                            <th class="border-start py-5 text-center">Tempat, Tanggal Lahir</th>
                            <th class="border-start py-5 text-center">Status</th>
                            <th class="border-start py-5 text-center">Dokter</th>
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
<div class="modal fade" id="popup_form_pegawai" data-bs-backdrop="static" data-bs-focus="false" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_pegawai" aria-hidden="true">
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
    // DATATABLE myTablePegawai
    const ajaxOptions = {
        url     : "{{ route('api.master_data.pegawai') }}",
        data    : function (params)
                {
                    params.kode_nama_emp    = $("#kode_nama_emp").val()
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
                        data: 'alamat_lengkap',
                        name: 'alamat_lengkap',
                    },
                    {
                        data: 'ttl',
                        name: 'ttl',
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
                        data: 'is_dokter',
                        name: 'is_dokter',

                        render: function (data, type, row, meta)
                        {
                            $btnstatus = `<span class="badge badge-${row.dokter_css}">
                                            <i class="bi bi-${row.dokter_icon} text-light fs-3"></i>
                                            &nbsp;${row.dokter_txt}
                                        </span>`

                            return $btnstatus
                        },

                        className: 'dt-body-center',
                    },
                    {
                        data: 'pid',
                        name: 'pid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<button class="btn btn-sm btn-icon btn-dark pegawai-update" data-pid="${row.pid}">
                                            <i class="las la-edit fs-4"></i>
                                        </button>`

                            if ($_isMultiTenants == 't')
                                $btnFungsi += `&nbsp;<button class="btn btn-sm btn-icon btn-info branch-assign" data-item_type="1" data-base_id="${row.pid}">
                                                    <i class="las la-server fs-4"></i>
                                                </button>`

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
                '#myTablePegawai',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-pegawai').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.pegawai-add').on('click', function ()
    {
        const pid = $(this).data('pid')

        openPegawaiForm(pid)
    })

    $('#myTablePegawai').on('click', '.pegawai-update', function ()
    {
        const pid = $(this).data('pid')

        openPegawaiForm(pid)
    })

    function openPegawaiForm (pid)
    {
        showLoading()

        getForm(pid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_pegawai', res, null, true)
        })
    }

    async function getForm (pid)
    {
        let result
        let link = pid == '' ? "{{ route('api.master_data.pegawai.create') }}" : "{{ route('api.master_data.pegawai.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { pid: pid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }

    if ($_isMultiTenants == 't')
    {
        $('#myTablePegawai').on('click', '.branch-assign', function ()
        {
            const item_type = $(this).data('item_type')
            const base_id = $(this).data('base_id')

            showLoading()

            let getForm = async function (item_type, base_id)
            {
                let result
                let link = "{{ route('api.master_data.database.cabang.assign_branch') }}"

                try {
                    result = await $.ajax({
                        url     : link,
                        type    : 'GET',
                        data    : { item_type: item_type, base_id: base_id }
                    })

                    return result
                } catch (error) {
                    Swal.close()

                    swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
                }
            }

            getForm(item_type, base_id).then((res) => {
                Swal.close()

                // Show modal
                openModal('mdl-branch-assign', res, null, true)
            })
        })
    }
</script>
@endpush
