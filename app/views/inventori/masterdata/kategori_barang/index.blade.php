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
                                <span class="las la-sitemap text-dark me-4"></span>
                                Data Kategori Barang
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm rounded-1 btn-light-success coa-inv" href="javascript:void(0)">
                                <i class="bi bi-list fs-3"></i>
                                Setup C.O.A Inventory
                            </a>
                            <a class="btn btn-sm rounded-1 btn-light-dark kategori-barang-add" data-kbid="0" href="javascript:void(0)">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-kategori-barang" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Tipe Kategori</label>
                                <div class="nav-group nav-group-sm nav-group-fluid rounded-1 border border-gray-300 bg-white p-1">
                                    <input type="radio" name="s_type_kate" class="btn-check" id="s-all" value="" checked="" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="s-all">Semua</label>

                                    <input type="radio" name="s_type_kate" class="btn-check" id="s-non-medis" value="t" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="s-non-medis">Medis</label>

                                    <input type="radio" name="s_type_kate" class="btn-check" id="s-medis" value="f" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="s-medis">Non Medis</label>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Kategori</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_kode_nama_kate" placeholder="Kode / Nama Kategori Barang" />
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
                <table id="myTableKategoriBarang" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kode Kategori</th>
                            <th class="border-start py-5">Nama Kategori</th>
                            <th class="border-start py-5">C.O.A Inventory</th>
                            <th class="border-start py-5">C.O.A COGS</th>
                            <th class="border-start py-5">Medis</th>
                            <th class="border-start py-5">Stock Take</th>
                            <th class="border-start py-5">Sales</th>
                            <th class="border-start py-5">Fixed Asset</th>
                            <th class="border-start py-5">Konsinyasi</th>
                            <th class="border-start py-5">Jasa</th>
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
<div class="modal fade" id="popup_form_kategori_barang" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_kategori_barang" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>

<div class="modal fade" id="popup_form_setup_coa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_setup_coa" aria-hidden="true">
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
    // DATATABLE myTableKategoriBarang
    const ajaxOptions = {
        url     : "{{ route('api.inventori.master_data.kategori_barang') }}",
        data    : function (params)
                {
                    params.s_type_kate      = $('[name="s_type_kate"]:checked').val()
                    params.s_kode_nama_kate = $("#s_kode_nama_kate").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'kode_kategori',
                        name: 'kode_kategori',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_kategori',
                        name: 'nama_kategori',
                    },
                    {
                        data: 'coa_inv',
                        name: 'coa_inv',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'coa_cogs',
                        name: 'coa_cogs',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'is_medis',
                        name: 'is_medis',
                        className: 'dt-body-center',

                        render: function (data)
                        {
                            let txt = 'Non Medis'

                            if (data == 't') txt = 'Medis'

                            let $icon = `<span class="badge badge-light-success">
                                            ${txt}
                                        </span>`

                            return $icon
                        }
                    },
                    {
                        data: 'is_freeze',
                        name: 'is_freeze',
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
                        data: 'is_fixed_asset',
                        name: 'is_fixed_asset',
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
                        data: 'is_konsinyasi',
                        name: 'is_konsinyasi',
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
                        data: 'is_service',
                        name: 'is_service',
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
                        data: 'is_aktif',
                        name: 'is_aktif',
                        className: 'dt-body-center',

                        render: function (data)
                        {
                            let status = 'danger'
                            let txt = 'Non Aktif'

                            if (data == 't')
                            {
                                status = 'success'
                                txt = 'Akitf'
                            }

                            let $icon = `<span class="badge badge-light-${status}">
                                            ${txt}
                                        </span>`

                            return $icon
                        }
                    },
                    {
                        data: 'kbid',
                        name: 'kbid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-kbid="${row.kbid}" title="Ubah" class="btn btn-dark btn-sm fs-8 kategori-barang-update">
                                            <i class="las la-edit"></i> Ubah
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
                '#myTableKategoriBarang',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-kategori-barang').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.coa-inv').click(function ()
    {
        myUrl = "{{ route('api.akunting.setup.master_coa.coa_default', ['sctype' => ':sctype']) }}";
        myUrl = myUrl.replace(':sctype', 2);

        $.ajax({
            url         : myUrl,
            data        : { },
            type        : 'GET',

            error       : function (req, stat, err)
                        {
                            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
                        },

            success     : function (response)
                        {
                            openModal('popup_form_setup_coa', response, null, true)
                        },

            async       : false,
            cache       : false
        })
    })

    $('.kategori-barang-add').click(function (e)
    {
        const kbid = $(this).data('kbid')

        openKategoriBarangModal(kbid)
    })

    $('#myTableKategoriBarang').on('click', '.kategori-barang-update', function ()
    {
        const kbid = $(this).data('kbid')

        openKategoriBarangModal(kbid)
    })

    function openKategoriBarangModal (kbid = 0)
    {
        showLoading()

        getForm(kbid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_kategori_barang', res, null, true)
        })
    }

    async function getForm (kbid)
    {
        let result
        let link = kbid == 0 ? "{{ route('api.inventori.master_data.kategori_barang.create') }}" : "{{ route('api.inventori.master_data.kategori_barang.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { kbid: kbid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush