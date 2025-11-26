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
                                <th class="border-start py-5">Tanggal A/P</th>
                                <th class="border-start py-5">No. A/P</th>
                                <th class="border-start py-5">No. Inv</th>
                                <th class="border-start py-5">Nama Supplier</th>
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
        url     : "{{ route('api.akunting.hutang_supplier.manual_ap_payment.list_inv') }}",
        data    : function (params)
                {
                    params.suppid       = '{{ $suppid }}'
                    params.doctor_id    = '{{ $doctor_id }}'
                    params.no_inv       = $("#sNoInv").val()
                },
    }

    const optionsInv{{ $rand }} = {
        columns:[
                    {
                        data: 'apdate',
                        name: 'apdate',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'apcode',
                        name: 'apcode',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'no_inv',
                        name: 'no_inv',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_supp',
                        name: 'nama_supp',

                        render: function (data, type, row, meta)
                        {
                            let $ret = row.nama_supp
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
                                            data-id="${row.maid}" data-apcode="${row.apcode}" data-apdate="${row.apdate}" data-no_inv="${row.no_inv}" data-nominal_inv="${row.nominal_inv} data-sisa_inv="${row.sisa_inv}" 
                                            title="Pilih" class="btn btn-dark btn-sm btn-icon fs-8" onclick="pilih('${row.maid}','${row.apcode}','${row.apdate}','${row.no_inv}','${row.nominal_inv_noformat}','${row.sisa_inv_noformat}')">
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
