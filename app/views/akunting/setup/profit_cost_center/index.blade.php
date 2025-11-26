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
                                <span class="las la-balance-scale text-dark me-4"></span>
                                Profit & Cost Center
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm w-100 rounded-1 btn-light-dark profit-cost-add" data-pccid="0" href="javascript:void(0)" modal-trigger="1">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-proft-cost" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-8">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Profit Cost</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_kode_nama" placeholder="Kode / Nama Profit Cost">
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Type Profit Cost</label>
                                <div class="position-relative me-md-2">
                                    <select id="s_pcctype" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Type">
                                        <option></option>
                                        <option value="1">Profit Center</option>
                                        <option value="2">Cost Center</option>
                                    </select>
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
                <table id="myTableProfitCost" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="text-center border-start py-5">Code</th>
                            <th class="text-center border-start py-5">Profit / Cost Center</th>
                            <th class="text-center border-start py-5">Type</th>
                            <th class="text-center border-start py-5">Status</th>
                            <th class="text-center border-start py-5 w-100px">Fungsi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Ubah-->
<div class="modal fade" id="popup-form-profit-center" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup-form-profit-center" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Ubah-->
@endsection

@push('script')
<script type="text/javascript">
    // DATATABLE myTableProfitCost
    const ajaxOptions = {
        url: "{{ route('api.akunting.setup.profit_cost_center') }}",
        data: function(params) {
                params.s_kode_nama  = $("#s_kode_nama").val()
                params.s_pcctype    = $("#s_pcctype").val()
            },
    }

    const options = {
        columns:[
                    {
                        data: 'pcccode',
                        name: 'pcccode',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'pccname',
                        name: 'pccname',
                    },
                    {
                        data: 'pcctype_txt',
                        name: 'pcctype_txt',
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
                        data: 'pccid',
                        name: 'pccid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<button class="btn btn-light-success btn-icon btn-sm rounded-1 profit-cost-update" data-bs-toggle="tooltip" title="Ubah Data" data-pccid="${row.pccid}">
                                            <i class="la la-edit fs-4"></i>
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
                '#myTableProfitCost',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-proft-cost').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.profit-cost-add').on('click', function ()
    {
        const pccid = $(this).data('pccid')

        openProfitCostModal(pccid)
    })

    $('#myTableProfitCost').on('click', '.profit-cost-update', function ()
    {
        const pccid = $(this).data('pccid')

        openProfitCostModal(pccid)
    })

    function openProfitCostModal (pccid = 0)
    {
        showLoading()

        getForm(pccid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup-form-profit-center', res, null, true)
        })
    }

    async function getForm (pccid)
    {
        let result
        let link = pccid == 0 ? "{{ route('api.akunting.setup.profit_cost_center.create') }}" : "{{ route('api.akunting.setup.profit_cost_center.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { pccid: pccid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush