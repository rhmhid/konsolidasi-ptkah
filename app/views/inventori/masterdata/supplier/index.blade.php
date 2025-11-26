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
                                <span class="las la-user-friends text-dark me-4"></span>
                                Data Supplier
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm rounded-1 btn-light-success coa-supp" href="javascript:void(0)">
                                <i class="bi bi-list fs-3"></i>
                                Setup C.O.A Supplier
                            </a>
                            <a class="btn btn-sm rounded-1 btn-light-dark supplier-add" data-suppid="0" href="javascript:void(0)">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-supplier" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Tipe Supplier</label>
                                <div class="nav-group nav-group-sm nav-group-fluid rounded-1 border border-gray-300 bg-white p-1">
                                    <input type="radio" name="s_type_supp" class="btn-check" id="s-all" value="" checked="" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="s-all">Semua</label>

                                    <input type="radio" name="s_type_supp" class="btn-check" id="s-non-medis" value="2" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="s-non-medis">Medis</label>

                                    <input type="radio" name="s_type_supp" class="btn-check" id="s-medis" value="1" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="s-medis">Non Medis</label>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Supplier</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" name="kode_nama_supp" id="kode_nama_supp" value="" placeholder="Kode / Nama Supplier">
                                </div>
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
                <table id="myTableSupplier" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kode Supplier</th>
                            <th class="border-start py-5">Nama Supplier</th>
                            <th class="border-start py-5">Alamat</th>
                            <th class="border-start py-5">E-Mail</th>
                            <th class="border-start py-5">C.O.A A/P</th>
                            <th class="border-start py-5">Tipe Supplier</th>
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
<div class="modal fade" id="popup_form_supplier" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_supplier" aria-hidden="true">
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
    // DATATABLE myTableSupplier
    const ajaxOptions = {
        url     : "{{ route('api.inventori.master_data.supplier') }}",
        data    : function (params)
                {
                    params.s_type_supp      = $('[name="s_type_supp"]:checked').val()
                    params.kode_nama_supp   = $("#kode_nama_supp").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'kode_supp',
                        name: 'kode_supp',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_supp',
                        name: 'nama_supp',
                    },
                    {
                        data: 'addr_supp',
                        name: 'addr_supp',
                    },
                    {
                        data: 'email_supp',
                        name: 'email_supp',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'coa_ap',
                        name: 'coa_ap',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'type_supp',
                        name: 'type_supp',
                        className: 'dt-body-center',
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
                        data: 'suppid',
                        name: 'suppid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<button class="btn btn-sm btn-icon btn-dark supplier-update" data-suppid="${row.suppid}" >
                                            <i class="las la-edit fs-4"></i>
                                        </button>`

                            $btnFungsi += `&nbsp;<button class="btn btn-sm btn-icon btn-info branch-assign" data-item_type="3" data-base_id="${row.suppidenc}">
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
                '#myTableSupplier',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-supplier').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.coa-supp').click(function ()
    {
        showLoading()

        getForm2(1).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_setup_coa', res, null, true)
        })
    })

    $('.supplier-add').click(function (e)
    {
        const suppid = $(this).data('suppid')

        openSupplierModal(suppid)
    })

    $('#myTableSupplier').on('click', '.supplier-update', function ()
    {
        const suppid = $(this).data('suppid')

        openSupplierModal(suppid)
    })

    function openSupplierModal (suppid = 0)
    {
        showLoading()

        getForm(suppid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_supplier', res, null, true)
        })
    }

    async function getForm (suppid)
    {
        let result
        let link = suppid == 0 ? "{{ route('api.inventori.master_data.supplier.create') }}" : "{{ route('api.inventori.master_data.supplier.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { suppid: suppid }
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


    $('#myTableSupplier').on('click', '.branch-assign', function ()
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
