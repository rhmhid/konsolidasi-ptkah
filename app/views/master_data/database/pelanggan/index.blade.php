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
                                <span class="la la-user-check text-dark me-4"></span>
                                Pelanggan
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline text-end">
                            <a class="btn btn-sm rounded-1 btn-light-danger cust-coa" href="javascript:void(0)" modal-trigger="1">
                                <i class="las la-list fs-3"></i>
                                Setup C.O.A
                            </a>
                            <a class="btn btn-sm rounded-1 btn-light-dark cust-add" data-custid="0" href="javascript:void(0)" modal-trigger="1">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-cust" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-8">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Pelanggan</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="kode_nama_cust" placeholder="Kode / Nama Pelanggan" />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Tipe</label>
                                <select name="s_gcustid" id="s_gcustid" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih...">
                                    <option value=""></option>
                                    @foreach ($data_group as $row)
                                        {{ $row = FieldsToObject($row) }}

                                        <option value="{{ $row->gcustid }}">{{ $row->nama_group }}</option>
                                    @endforeach 
                                </select>
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
                <table id="myTableCust" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Tipe Pelanggan</th>
                            <th class="border-start py-5">Kode Pelanggan</th>
                            <th class="border-start py-5">Nama Pelanggan</th>
                            <th class="border-start py-5">COA Piutang</th>
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
<div class="modal fade" id="popup-form-cust" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup-form-cust" aria-hidden="true">
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
    // DATATABLE myTableCust
    const ajaxOptions = {
        url: "{{ route('api.master_data.database.pelanggan') }}",
        data: function(params)
            {
                params.kode_nama_cust   = $("#kode_nama_cust").val()
                params.s_gcustid        = $("#s_gcustid").val()
            },
    }

    const options = {
        columns:[
                    {
                        data: 'nama_group', 
                        name: 'nama_group',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'kode_customer', 
                        name: 'kode_customer',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_customer', 
                        name: 'nama_customer',
                    },
                    {
                        data: 'coa_ar', 
                        name: 'coaid_ar',
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
                        data: 'custid',
                        name: 'custid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<button class="btn btn-light-success btn-icon btn-sm rounded-1 cust-update" data-bs-toggle="tooltip" title="Ubah Data" data-custid="${row.custid}">
                                            <i class="la la-edit fs-4"></i>
                                        </button>`;

                                $btnFungsi += `&nbsp;&nbsp;<button class="btn btn-sm btn-icon btn-info branch-assign" data-item_type="2" title="Assign Branch" data-base_id="${row.custid_enc}">
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

        /*rowGroup:   {
                        dataSrc: [ 'ftname']
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
                        "<tr class=\"group fs-5 fw-bolder\"><td colspan=\"6\">" + group + "</td></tr>"
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
                '#myTableCust',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-cust').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.cust-coa').click(function ()
    {
        showLoading()

        getForm2(3).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_setup_coa', res, null, true)
        })
    })

    $('.cust-add').on('click', function ()
    {
        const custid = $(this).data('custid')

        openCustEditModal(custid)
    })

    $('#myTableCust').on('click', '.cust-update', function ()
    {
        const custid = $(this).data('custid')

        openCustEditModal(custid)
    })

        $('#myTableCust').on('click', '.branch-assign', function ()
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

    function openCustEditModal (custid = 0)
    {
        showLoading()

        getForm(custid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup-form-cust', res, null, true)
        })
    }

    async function getForm (custid)
    {
        let result
        let link = custid == 0 ? "{{ route('api.master_data.database.pelanggan.create') }}" : "{{ route('api.master_data.database.pelanggan.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { custid: custid }
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