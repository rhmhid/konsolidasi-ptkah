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
                                <span class="las la-city text-dark me-4"></span>
                                Kategori Asset
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <button type="button" class="btn btn-sm rounded-1 btn-light-success" data-facid="0" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Setup C.O.A
                            </button>

                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-dark fw-bold fs-7 w-200px py-4 setup-coa" data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a class="menu-link px-3 coa-list" href="javascript:void(0)" data-sctype="4">
                                        C.O.A Fixed Asset
                                    </a>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a class="menu-link px-3 coa-list" href="javascript:void(0)" data-sctype="5">
                                        C.O.A Depresiasi F/A
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>

                            <a class="btn btn-sm rounded-1 btn-light-dark kategori-fa-add" data-facid="0" href="javascript:void(0)">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-kategori-fa" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                           <div class="col-lg-11">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Kategori</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" name="kode_nama_kate" id="kode_nama_kate" placeholder="Kode / Nama Kategori F/A" />
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
                <table id="myTableKategoriFA" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kode Kategori</th>
                            <th class="border-start py-5">Nama Kategori</th>
                            <th class="border-start py-5">C.O.A F/A</th>
                            <th class="border-start py-5">C.O.A Depresiasi F/A</th>
                            <th class="border-start py-5">C.O.A Beban DFA</th>
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
<div class="modal fade" id="popup_form_kategori_fa" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_kategori_fa" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>

<div class="modal fade" id="popup_form_setup_coa" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_setup_coa" aria-hidden="true">
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
    // DATATABLE myTableKategoriFA
    const ajaxOptions = {
        url     : "{{ route('api.akunting.fixed_asset.kategori') }}",
        data    : function (params)
        {
                params.kode_nama_kate   = $("#kode_nama_kate").val()
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
                        data: 'coa_fa',
                        name: 'coa_fa',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'coa_accumulated',
                        name: 'coa_accumulated',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'coa_depreciation',
                        name: 'coa_depreciation',
                        className: 'dt-body-center',
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
                        data: 'facid',
                        name: 'facid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-facid="${row.facid}" title="Ubah" class="btn btn-dark btn-sm fs-8 kategori-fa-update">
                                            <i class="las la-edit"></i> Ubah
                                        </a>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableKategoriFA',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-kategori-fa').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.setup-coa').on('click', '.coa-list', function ()
    {
        const sctype = $(this).data('sctype')

        showLoading()

        getForm2(sctype).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_setup_coa', res, null, true)
        })
    })

    $('.kategori-fa-add').click(function (e)
    {
        const facid = $(this).data('facid')

        openKategoriFAModal(facid)
    })

    $('#myTableKategoriFA').on('click', '.kategori-fa-update', function ()
    {
        const facid = $(this).data('facid')

        openKategoriFAModal(facid)
    })

    function openKategoriFAModal (facid = 0)
    {
        showLoading()

        getForm(facid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_kategori_fa', res, null, true)
        })
    }

    async function getForm (facid)
    {
        let result
        let link = facid == 0 ? "{{ route('api.akunting.fixed_asset.kategori.create') }}" : "{{ route('api.akunting.fixed_asset.kategori.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { facid: facid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }

    async function getForm2 (sctype)
    {
        let result
        let link = "{{ route('api.akunting.setup.master_coa.coa_default', ['sctype' => ':sctype']) }}";
            link = link.replace(':sctype', sctype);

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
</script>
@endpush