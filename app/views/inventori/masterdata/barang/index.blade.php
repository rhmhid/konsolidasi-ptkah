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
                                <span class="las la-prescription-bottle text-dark me-4"></span>
                                Data Barang
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm rounded-1 btn-light-dark barang-add" data-mbid="0" href="javascript:void(0)">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-brang" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Kategori Barang</label>
                                {!! $cmb_kel_brg !!}
                            </div>

                            <div class="col-lg-5">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Barang</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_kode_nama_brg" placeholder="Kode / Nama Barang" />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Status Barang</label>
                                <div class="nav-group nav-group-sm nav-group-fluid rounded-1 border border-gray-300 bg-white p-1">
                                    <label>
                                        <input type="radio" class="btn-check" name="s_is_aktif" id="s_is_aktif_all" value="" checked="" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Semua</span>
                                    </label>
                                    <label>
                                        <input type="radio" class="btn-check" name="s_is_aktif" id="s_is_aktif_t" value="t" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Aktif</span>
                                    </label>
                                    <label>
                                        <input type="radio" class="btn-check" name="s_is_aktif" id="s_is_aktif_f" value="f" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Non Aktif</span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-1">
                                <label class="text-dark fw-bold fs-8 pb-2 d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-dark rounded-1 me-4 w-100" id="btncari">
                                    <i class="la la-search"></i>
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Compact form-->
                </form>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTableBarang" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kode Barang</th>
                            <th class="border-start py-5">Nama Barang</th>
                            <th class="border-start py-5">Kategori Barang</th>
                            <th class="border-start py-5">Satuan Kecil</th>
                            <th class="border-start py-5">Satuan Besar</th>
                            <th class="border-start py-5">HNA</th>
                            <th class="border-start py-5">HNA + PPN</th>
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
<div class="modal fade" id="popup_form_barang" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_barang" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>

<div class="modal fade" id="popup_satuan_barang" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_satuan_barang" aria-hidden="true">
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
    // DATATABLE myTableBarang
    const ajaxOptions = {
        url     : "{{ route('api.inventori.master_data.barang') }}",
        data    : function (params)
                {
                    params.s_kbid           = $("#s_kbid").val()
                    params.s_kode_nama_brg  = $("#s_kode_nama_brg").val()
                    params.s_is_aktif       = $('input[name="s_is_aktif"]:checked').val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'kode_brg',
                        name: 'kode_brg',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_brg',
                        name: 'nama_brg',
                    },
                    {
                        data: 'kel_brg',
                        name: 'kel_brg',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'satuan_kecil',
                        name: 'satuan_kecil',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'satuan_besar',
                        name: 'satuan_besar',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'hna',
                        name: 'hna',
                        className: 'dt-body-right',
                    },
                    {
                        data: 'hna_ppn',
                        name: 'hna_ppn',
                        className: 'dt-body-right',
                    },
                    {
                        data: 'is_aktif',
                        name: 'is_aktif',
                        className: 'text-center',

                        render: function (data, type, row, meta)
                        {
                            $icon = `<span class="badge badge-${row.status_css}">
                                        <i class="bi bi-${row.status_icon} text-light fs-3"></i>
                                        &nbsp;${row.status_txt}
                                    </span>`

                            return $icon
                        }
                    },
                    {
                        data: 'mbid',
                        name: 'mbid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<button class="btn btn-sm btn-icon btn-dark barang-update" data-mbid="${row.mbid}" >
                                            <i class="las la-edit fs-4"></i>
                                        </button>`

                            $btnFungsi += `&nbsp;<button class="btn btn-sm btn-icon btn-info branch-assign" data-item_type="4" data-base_id="${row.mbidenc}">
                                            <i class="las la-server fs-4"></i>
                                        </button>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: "dt-body-center",
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableBarang',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-brang').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.barang-add').click(function (e)
    {
        const mbid = $(this).data('mbid')

        openBarangModal(mbid)
    })

    $('#myTableBarang').on('click', '.barang-update', function ()
    {
        const mbid = $(this).data('mbid')

        openBarangModal(mbid)
    })

    function openBarangModal (mbid = 0)
    {
        showLoading()

        getForm(mbid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_barang', res, null, true)
        })
    }

    async function getForm (mbid)
    {
        let result
        let link = mbid == 0 ? "{{ route('api.inventori.master_data.barang.create') }}" : "{{ route('api.inventori.master_data.barang.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { mbid: mbid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }

    $('#myTableBarang').on('click', '.branch-assign', function ()
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
</script>
@endpush