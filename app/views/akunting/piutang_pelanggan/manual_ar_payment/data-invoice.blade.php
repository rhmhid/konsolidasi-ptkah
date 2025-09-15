<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-file-invoice text-dark me-4"></span>
        Data Invoice
    </h2>

    <div class="btn btn-sm btn-icon btn-active-color-dark" data-bs-dismiss="modal">
        <i class="fas fa-times"></i>
    </div>
    <!--end::Modal title-->
</div>
<!--end::Modal header-->
                <form method="post" id="form-mar-inv" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="input-group">
                            <div class="col-lg-4">
                                <input type="text" id="sNoInv" class="form-control form-control-sm rounded-1" placeholder="Masukan No. Invoice / Keterangan" />
                            </div>
                                <div class="input-group-append">
                                <button type="submit" class="btn btn-sm btn-dark rounded-1 me-4 w-100 ms-2" id="btncariInv">
                                    <i class="la la-search"></i>
                                    Cari
                                </button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--end::Compact form-->
                </form>
                <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                    <table id="myTableInv" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                        <thead class="bg-dark text-uppercase fs-7 text-center">
                            <tr class="fw-bold text-white">
                                <th class="border-start py-5">Tanggal A/R</th>
                                <th class="border-start py-5">No. A/R</th>
                                <th class="border-start py-5">No. Inv</th>
                                <th class="border-start py-5">Nama Customer</th>
                                <th class="border-start py-5">Nominal Inv</th>
                                <th class="border-start py-5">Sisa Inv</th>
                                <th class="border-start py-5">Keterangan</th>
                                <th class="border-start py-5 w-100px">Pilih</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

<script language="javascript">
    const ajaxoptionsInv{{ $rand }} = {
        url     : "{{ route('api.akunting.piutang_pelanggan.manual_ar_payment.list_inv') }}",
        data    : function (params)
                {
                    params.custid       = '{{ $custid }}'
                    params.bank_ar      = '{{ $bank_ar }}'
                    params.pegawai_id   = '{{ $pegawai_id }}'
                    params.no_inv       = $("#sNoInv").val()
                },
    }

    const optionsInv{{ $rand }} = {
        columns:[
                    {
                        data: 'ardate',
                        name: 'ardate',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'arcode',
                        name: 'arcode',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'no_inv',
                        name: 'no_inv',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_customer',
                        name: 'nama_customer',

                        render: function (data, type, row, meta)
                        {
                            let $ret = row.nama_customer
                            if (row.custid == -1)
                                $ret += `<br /><I class="text-danger fw-semibold">[ ${row.nama_pegawai} ]</I>`

                            if (row.custid == -2)
                                $ret += `<br /><I class="text-danger fw-semibold">[ ${row.bank_nama} ]</I>`

                            return $ret
                        }
                    },
                    {
                        data: 'nominal_inv',
                        name: 'nominal_inv',
                        className: 'dt-body-right',
                    },
                    {
                        data: 'sisa_inv',
                        name: 'sisa_inv',
                        className: 'dt-body-right',
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                    },
                    {
                        data: 'maid',
                        name: 'maid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" 
                                            data-id="${row.maid}" data-arcode="${row.arcode}" data-ardate="${row.ardate}" data-no_inv="${row.no_inv}" data-nominal_inv="${row.nominal_inv} data-sisa_inv="${row.sisa_inv}" 
                                            title="Pilih" class="btn btn-dark btn-sm btn-icon fs-8" onclick="pilih('${row.maid}','${row.arcode}','${row.ardate}','${row.no_inv}','${row.nominal_inv_noformat}','${row.sisa_inv_noformat}')">
                                            <i class="las la-download"></i>
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
                '#myTableInv',
                ajaxoptionsInv{{ $rand }},
                optionsInv{{ $rand }}
            )
    // aksi submit cari
    $('#form-mar-inv').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })
</script>
