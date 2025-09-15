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
                                <span class="las la-prescription-bottle-alt text-dark me-4"></span>
                                Data Gudang & Depo
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm rounded-1 btn-light-dark gudang-add" data-gid="0" href="javascript:void(0)">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-gudang" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-5">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Gudang</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_kode_nama_gudang" placeholder="Kode / Nama Gudang" />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Lokasi</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_lokasi" placeholder="Lokasi Gudang" />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Jenis Gudang</label>
                                {!! $cmb_jenis !!}
                            </div>

                            <div class="col-lg-1">
                                <label class="text-dark fw-bold fs-8 pb-2 d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-dark rounded-1 me-4 w-100" id="btncari">
                                    <i class="la la-search"></i>Cari
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Compact form-->
                </form>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTableGudang" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kode Gudang</th>
                            <th class="border-start py-5">Nama Gudang</th>
                            <th class="border-start py-5">Lokasi</th>
                            <th class="border-start py-5">Gudang Besar</th>
                            <th class="border-start py-5">Gudang Penjualan</th>
                            <th class="border-start py-5">Depo</th>
                            <th class="border-start py-5">Cost Center</th>
                            <th class="border-start py-5">Status</th>
                            <th class="border-start py-5 w-100px">Fungsi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Ubah-->
<div class="modal fade" id="popup_form_gudang" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_gudang" aria-hidden="true">
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
    // DATATABLE myTableGudang
    const ajaxOptions = {
        url     : "{{ route('api.inventori.master_data.gudang') }}",
        data    : function (params)
                {
                    params.s_kode_nama_gudang   = $("#s_kode_nama_gudang").val()
                    params.s_lokasi             = $("#s_lokasi").val()
                    params.s_jenis_gudang       = $("#s_jenis_gudang").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'kode_gudang',
                        name: 'kode_gudang',
                    },
                    {
                        data: 'nama_gudang',
                        name: 'nama_gudang',
                    },
                    {
                        data: 'lokasi',
                        name: 'lokasi',
                    },
                    {
                        data: 'is_gudang_besar',
                        name: 'is_gudang_besar',
                        className: 'dt-body-center',

                        render: function (data)
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
                        }
                    },
                    {
                        data: 'is_sales',
                        name: 'is_sales',
                        className: 'dt-body-center',

                        render: function (data)
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
                        }
                    },
                    {
                        data: 'is_depo',
                        name: 'is_depo',
                        className: 'dt-body-center',

                        render: function (data)
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
                        }
                    },
                    {
                        data: 'cost_center',
                        name: 'cost_center',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'is_aktif',
                        name: 'is_aktif',
                        className: 'dt-body-center',

                        render: function (data)
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
                        }
                    },
                    {
                        data: 'gid',
                        name: 'gid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-gid="${row.gid}" title="Ubah" class="btn btn-dark btn-sm fs-8 gudang-update">
                                            <i class="las la-edit"></i> Ubah
                                        </a>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: "text-center",
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableGudang',
                ajaxOptions,
                options
            )

    // // aksi submit cari
    $('#form-gudang').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.gudang-add').click(function (e)
    {
        const gid = $(this).data('gid')

        openGudangModal(gid)
    })

    $('#myTableGudang').on('click', '.gudang-update', function ()
    {
        const gid = $(this).data('gid')

        openGudangModal(gid)
    })

    function openGudangModal (gid = 0)
    {
        showLoading()

        getForm(gid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_gudang', res, null, true)
        })
    }

    async function getForm (gid)
    {
        let result
        let link = gid == 0 ? "{{ route('api.inventori.master_data.gudang.create') }}" : "{{ route('api.inventori.master_data.gudang.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { gid: gid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush