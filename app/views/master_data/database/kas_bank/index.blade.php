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
                                <span class="las la-university text-dark me-4"></span>
                                Kas / Bank
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm w-100 rounded-1 btn-light-dark kas-bank-add" data-bank-id="0" href="javascript:void(0)" modal-trigger="1">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-bank" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Kas / Bank Type</label>
                                {!! $cmb_bank_type !!}
                            </div>

                            <div class="col-lg-8">
                                <label class="text-dark fw-bold fs-7 pb-2">Kas / Bank</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_kas_bank" placeholder="Kas / Bank" />
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
                <table id="myTableBank" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kas / Bank Type</th>
                            <th class="border-start py-5">Kas / Bank Nama</th>
                            <th class="border-start py-5">No. Rekening</th>
                            <th class="border-start py-5">Atas Nama</th>
                            <th class="border-start py-5">Cabang</th>
                            <th class="border-start py-5">Default C.O.A</th>
                            <th class="border-start py-5">C.O.A Ctrl. Acc</th>
                            <th class="border-start py-5">Status</th>
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
<div class="modal fade" id="popup_form_bank" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_bank" aria-hidden="true">
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
    // DATATABLE myTableBank
    const ajaxOptions = {
        url: "{{ route('api.master_data.database.kas_bank') }}",
        data: function(params) {
                params.s_bank_type  = $("#s_bank_type").val()
                params.s_kas_bank   = $("#s_kas_bank").val()
            },
    }

    const options = {
        columns:[
                    {
                        data: 'bank_type',
                        name: 'bank_type',
                    },
                    {
                        data: 'bank_nama',
                        name: 'bank_nama',
                    },
                    {
                        data: 'bank_no_rek',
                        name: 'bank_no_rek',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'bank_atas_nama',
                        name: 'bank_atas_nama',
                    },
                    {
                        data: 'bank_cabang',
                        name: 'bank_cabang',
                    },
                    {
                        data: 'default_coa',
                        name: 'default_coa',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'ctrl_acc_coaid',
                        name: 'ctrl_acc_coaid',
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
                        data: 'bank_id',
                        name: 'bank_id',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-bank-id="${row.bank_id}" title="Ubah" class="btn btn-dark btn-sm fs-8 kas-bank-update">
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

        /*rowGroup:   {
                        dataSrc: [ 'bank_type']
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
                        "<tr class=\"group fs-5 fw-bolder\"><td colspan=\"9\">" + group + "</td></tr>"
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
                '#myTableBank',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-bank').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.kas-bank-add').on('click', function ()
    {
        const bank_id = $(this).data('bank-id')

        openBankModal(bank_id)
    })

    $('#myTableBank').on('click', '.kas-bank-update', function ()
    {
        const bank_id = $(this).data('bank-id')

        openBankModal(bank_id)
    })

    function openBankModal (bank_id = 0)
    {
        showLoading()

        getForm(bank_id).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_bank', res, null, true)
        })
    }

    async function getForm (bank_id)
    {
        let result
        let link = bank_id == 0 ? "{{ route('api.master_data.database.kas_bank.create') }}" : "{{ route('api.master_data.database.kas_bank.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { bank_id: bank_id }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush