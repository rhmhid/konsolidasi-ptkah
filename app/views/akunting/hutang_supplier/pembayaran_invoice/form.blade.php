<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-file-invoice text-dark me-4"></span>
        Form Input Pembayaran Invoice
    </h2>

    <div class="btn btn-sm btn-icon btn-active-color-dark" data-bs-dismiss="modal">
        <i class="fas fa-times"></i>
    </div>
    <!--end::Modal title-->
</div>
<!--end::Modal header-->

<!--begin::Modal body-->
<div class="modal-body py-6 px-lg-7">
    <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
        <form method="post" id="fi-app" novalidate>
            <input type="hidden" name="appid" id="appid" value="{{ $data_head->appid }}" readonly="" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-8">
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Pembuatan</label>
                            <div class="input-group">
                                <input type="text" name="paydate" id="paydate" value="{{ $data_head->paydate }}" class="form-control form-control-sm rounded-1 mydate-time" readonly="" required="" />
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Pembayaran</label>
                            <div class="input-group">
                                <input type="text" name="tgl_bayar" id="tgl_bayar" value="{{ $data_head->tgl_bayar }}" class="form-control form-control-sm rounded-1 mydate" readonly="" required="" />
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Nama Supplier</label>
                            {!! $cmb_supp !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                            <textarea class="form-control form-control-sm rounded-1" rows="4" name="keterangan" id="keterangan">{{ $data_head->keterangan }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Bank Supplier</label>
                            <div class="form-control-plaintext rounded px-3 py-2 fst-italic info-bank-supp">otomatis</div>
                        </div>

                        <div class="col-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">No. Rekening Supplier</label>
                            <div class="form-control-plaintext rounded px-3 py-2 fst-italic info-norek-supp">otomatis</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Kas / Bank</label>
                            {!! $cmb_bank !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Cara Bayar</label>
                            {!! $cmb_cara_bayar !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold required">
                        Detail Invoice
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu Invoice Pembelian"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-inv">
                        <thead class="bg-dark text-uppercase fs-7 text-center">
                            <tr class="fw-bold text-white">
                                <th class="align-middle border-start py-5">Kode / Tgl AP</th>
                                <th class="align-middle border-start py-5">Invoice / Duedate</th>
                                <th class="align-middle border-start py-5">Faktur Pajak / keterangan</th>
                                <th class="align-middle border-start py-5">Nominal</th>
                                <th class="align-middle border-start py-5">Sisa Pembayaran</th>
                                <th class="align-middle border-start py-5 w-250px">Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody id="listData"></tbody>
                    </table>

                    <div class="bg-light-success p-3 mt-n4">
                        <div class="row">
                            <div class="col-10 text-end fw-bold">Total Invoice</div>
                            <div class="col-2 text-end fw-bold">Rp. <font id="vtotal-inv">0</font></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold">
                        ADD/LESS
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu master C.O.A"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-coa">
                        <thead class="bg-dark text-uppercase fs-7 text-center">
                            <tr class="fw-bold text-white">
                                <th class="align-middle py-5 w-50px">&nbsp;</th>
                                <th class="align-middle border-start py-5">C.O.A</th>
                                <th class="align-middle border-start py-5">Keterangan</th>
                                <th class="align-middle border-start py-5">Debet</th>
                                <th class="align-middle border-start py-5">Credit</th>
                                <th class="align-middle border-start py-5">Cost Center</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <div class="bg-light-success p-3 mt-n4">
                        <div class="row">
                            <div class="col-2">
                                <button type="button" class="btn btn-sm btn-dark rounded-1 add-coa me-2">
                                    <i class="fa fa-plus"></i>
                                    Tambah C.O.A
                                </button>
                            </div>
                            <div class="col-8 text-end fw-bold">Total Add/Less ( dr )</div>
                            <div class="col-2 text-end fw-bold">Rp. <font id="vtotal-addless-dr">0</font></div>
                        </div>
                    </div>

                    <div class="bg-light-success p-3 mt-n4">
                        <div class="row">
                            <div class="col-10 text-end fw-bold">Total Add/Less ( cr )</div>
                            <div class="col-2 text-end fw-bold">Rp. <font id="vtotal-addless-cr">0</font></div>
                        </div>
                    </div>

                    <div class="bg-light-success p-3 mt-n4">
                        <div class="row">
                            <div class="col-10 text-end fw-bold">Total Invoice + Add/Less</div>
                            <div class="col-2 text-end fw-bold">Rp. <font id="vsubtotal">0</font></div>
                        </div>
                    </div>

                    <div class="bg-light-success p-3 mt-n2">
                        <div class="row align-items-center">
                            <div class="col-10 text-end fw-bold">Potongan</div>
                            <div class="col-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm text-end currency calc-amount" name="potongan" id="potongan" value="{{ $data_head->potongan }}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light-success p-3 mt-n4">
                        <div class="row align-items-center">
                            <div class="col-10 text-end fw-bold">Pembulatan</div>
                            <div class="col-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm text-end currency calc-amount" name="pembulatan" id="pembulatan" value="{{ $data_head->pembulatan }}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light-success p-3 mt-n4">
                        <div class="row">
                            <div class="col-10 text-end fw-bold">Other Cost</div>
                            <div class="col-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm text-end currency calc-amount" name="other_cost" id="other-cost" value="{{ $data_head->other_cost }}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light-success p-3 mt-n4">
                        <div class="row">
                            <div class="col-10 text-end fw-bold">Total Pembayaran</div>
                            <div class="col-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm text-end currency" name="totpay" id="totpay" value="0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex flex-wrap gap-2">
                <div class="d-flex flex-column flex-md-row gap-2">
                    <button type="button" class="btn btn-danger btn-sm rounded-1 w-100 w-md-auto" id="btn-batal" data-bs-dismiss="modal">
                        <i class="las la-undo"></i> Batal
                    </button>
                </div>

                <!-- Kanan: Simpan -->
                <div class="ms-auto">
                    <button type="submit" class="btn btn-dark btn-sm rounded-1 w-100 w-md-auto" id="btn-simpan">
                        <i class="las la-save"></i> Simpan
                    </button>
                </div>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<!--begin::template - Ubah-->
<script type="text/javascript">
    {!! $AddPay !!}

    initDatePicker()

    initToolTip()

    function initDatePicker ()
    {
        $('.mydate').flatpickr({
            defaultDate: null,
            dateFormat: "d-m-Y",
        })
    }

    function initToolTip ()
    {
        $('.modal [data-bs-toggle="tooltip"]').tooltip({
            trigger: 'hover'
        })
    }

    $('.modal .form-select').each(function ()
    {
        var obj, parent

        // you can set your default select2 options in obj
        obj = {
            // default options
            width: '100%',
        }

        // if there is a modal that select is inside it
        parent = $(this).closest('.modal')

        if (parent.length) obj['dropdownParent'] = parent

        $(this).select2(obj)
    })

    $(".mydate-time").flatpickr({
        enableTime: true,
        dateFormat: "d-m-Y H:i",
        time_24hr: true,
        minuteIncrement: 1
    })

    $('#suppid').on('change', function ()
    {
        getInfoSupp().then(data => {
            if (data)
            {
                $('.info-bank-supp').html(data.bank)
                $('.info-norek-supp').html(data.no_rek)
            }
            else
            {
                $('.info-bank-supp').html('Otomatis')
                $('.info-norek-supp').html('Otomatis')   
            }

            clearData()

            if (this.value != '') showListHutang()    
        })
    })

    var getInfoSupp = async function () 
    {
        let result
        let suppid = $('#suppid option:selected').val()

        let url = "{{ route('api.akunting.hutang_supplier.pembayaran_invoice.info_supplier', ['myid' => ':myid']) }}"
            url = url.replace(':myid', suppid)

        try {
            result = await $.ajax({
                url     : url,
                type    : 'GET',
                data    : { suppid: suppid }
            })

            return result
        } catch (error) {
            swalShowMessage('Gagal', 'Gagal mengambil data supplier.', 'error')
        }
    }

    var clearData = function ()
    {
        $('#listData').html('')

        $('#vtotal-inv').html(MoneyFormat(0))

        calcInvoiceAddless()
    }

    var showListHutang = function ()
    {
        $('#listData').html('<tr><td colspan="6" class="text-center"><span class="spinner spinner-border" id="loading-animation1"></span></td></tr>')

        getOutstandingAP().then(res => {
            if (res.length < 1)
            {
                listData = `<tr>
                                <td colspan="6" class="text-center">Data tidak ditemukan</td>
                            </tr>`

                $('#listData').html(listData)
            }
            else
            {
                $('#listData').html('')

                res.forEach(data => {
                    AddPay(data.apsid, data.apcode, data.apdate, data.no_invoice, data.duedate, data.no_faktur_pajak, data.keterangan, data.nominal_hutang, data.sisa_hutang)
                })
            }
        })
    }

    var getOutstandingAP = async function ()
    {
        let result
        let suppid = $('#suppid option:selected').val()
        let appid = $('#appid').val()

        let url = "{{ route('api.akunting.hutang_supplier.pembayaran_invoice.list_outstanding_ap', ['myid' => ':myid']) }}"
            url = url.replace(':myid', suppid)

        try {
            result = await $.ajax({
                url     : url,
                type    : 'GET',
                data    : { suppid: suppid, appid: appid }
            })

            return result
        } catch (error) {
            swalShowMessage('Gagal', 'Gagal mengambil data supplier.', 'error')
        }
    }


    function AddPay (apsid, apcode, apdate, no_invoice, duedate, no_faktur_pajak, keterangan, nominal_hutang, sisa_hutang, pay = 0, appdid = 0)
    {
        listData = `<tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="d-flex justify-content-start flex-column mx-3">
                                    <span class="text-dark mb-1 fs-8">${apcode}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-8">
                                        ${apdate}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="d-flex justify-content-start flex-column mx-3">
                                    <span class="text-dark mb-1 fs-8">${no_invoice}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-8">
                                        ${duedate}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <div class="d-flex justify-content-start flex-column mx-3">
                                    <span class="text-dark mb-1 fs-8">${no_faktur_pajak}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-8">
                                        ${keterangan}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="text-end mx-3">${MoneyFormat(nominal_hutang)}</td>
                        <td class="text-end mx-3">${MoneyFormat(sisa_hutang)}</td>
                        <td class="mx-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-inv" name="pay[${apsid}]" id="pay-${apsid}" value="${pay}"/>
                            </div>

                            <input type="hidden" class="apsid-row" name="apsid[${apsid}]" value="${apsid}" />
                            <input type="hidden" id="appdid-${apsid}" name="appdid[${apsid}]" value="${appdid}">
                            <input type="hidden" id="apcode-${apsid}" name="apcode[${apsid}]" value="${apcode}">
                            <input type="hidden" id="nominal-hutang-${apsid}" name="nominal_hutang[${apsid}]" value="${nominal_hutang}">
                            <input type="hidden" id="sisa-hutang-${apsid}" name="sisa_hutang[${apsid}]" value="${sisa_hutang}">
                        </td>
                    </tr>`

        $('#tbl-inv > tbody:last').append(listData)
    }

    $('.add-coa').on('click', function (e)
    {
        e.preventDefault()

        AddCoa()
    })

    function AddCoa (coaid = '', ket_addless = '', debet = 0, credit = 0, pccid = '', appaid = 0)
    {
        let row_coa = `{!! $row_coa !!}`
        let coa = row_coa.split(';')
        let cmb_coa = `<select name="coaid[]" class="form-select form-select-sm rounded-1 w-100 select2-combo" data-control="select2">`

        $.each(coa, function (idx, val)
        {
            let data_coa = val.split(':')
            let sel_coa = ''

            if (data_coa[0] == coaid) sel_coa = 'selected=""'

            cmb_coa += `<option value="${data_coa[0]}" data-coatid="${data_coa[1]}" ${sel_coa}>${data_coa[2]}</option>`
        })

        cmb_coa += `</select>`

        let row_cost_center = `{!! $row_cost_center !!}`
        let cost_center = row_cost_center.split(';')
        let cmb_cost = `<select name="pccid[]" class="form-select form-select-sm rounded-1 w-100 select2-combo" data-control="select2">`

        $.each(cost_center, function (idx, val)
        {
            let data_cost = val.split(':')
            let sel_cost = ''

            if (data_cost[0] == pccid) sel_cost = 'selected=""'

            cmb_cost += `<option value="${data_cost[0]}" ${sel_cost}>${data_cost[1]}</option>`
        })

        cmb_cost += `</select>`

        let data_coa = `<tr class="align-middle">
                            <td class="text-center">
                                <i class="las la-trash fs-2 text-danger text-center hapus-coa" role="button"></i>
                                <input type="hidden" class="form-control form-control-sm w-50px fs-8 rounded-1" name="appaid[]" value="${appaid}" />
                            </td>
                            <td class="text-nowrap">
                                ${cmb_coa}
                            </td>
                            <td class="text-nowrap">
                                <textarea id="ket_addless[]" name="ket_addless[]" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0" required="">${ket_addless}</textarea>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-addless" mytype="debet" name="debet[]" value="${debet}" />
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-addless" mytype="credit" name="credit[]" value="${credit}" />
                                </div>
                            </td>
                            <td class="text-nowrap">
                                ${cmb_cost}
                            </td>
                        </tr>`

        $('#tbl-coa > tbody:last').append(data_coa)

        $('.select2-combo').select2({
            width: '100%',
            dropdownParent: $('#mdl-form-app')
        })
    }

    $('#tbl-coa').on('click', '.hapus-coa', function ()
    {
        $(this).closest("tr").remove()

        calcAddless()
    })

    $('#tbl-inv').on('dblclick', '.calc-inv', function ()
    {
        ResetMoney()

        let $tr = $(this).closest("tr")
        let $apsid = $tr.find('.apsid-row').val()
        let $sisa_hutang = parseFloat($tr.find('#sisa-hutang-' + $apsid).val()) || 0

        $(this).val($sisa_hutang).trigger('change')

        FormatMoney()
    })

    $('#tbl-inv').on('change', '.calc-inv', function ()
    {
        ResetMoney()

        let $tr = $(this).closest("tr")
        let $apsid = $tr.find('.apsid-row').val()
        let $sisa_hutang = parseFloat($tr.find('#sisa-hutang-' + $apsid).val()) || 0
        let $pay = $(this).val()

        if (parseFloat($pay) > parseFloat($sisa_hutang))
        {
            swalShowMessage("Information", 'Pembayaran melebihi sisa tagihan.', 'warning')

            $(this).val($sisa_hutang).trigger('change')

            return false
        }

        let $listAmount = $('#tbl-inv .calc-inv')
        let $totAmount = 0

        $listAmount.each(function ()
        {
            const $field = $(this)
            const val = isNaN(parseFloat($field.val())) ? 0 : parseFloat($field.val())

            $totAmount = parseFloat($totAmount) + parseFloat(val)
        })

        $('#vtotal-inv').html(MoneyFormat($totAmount))

        FormatMoney()

        calcInvoiceAddless()
    })

    $('#tbl-coa').on('change', '.calc-addless', function ()
    {
        calcAddless
    })

    function calcAddless ()
    {
        ResetMoney()

        let $listAmount = $('#tbl-coa .calc-addless')
        let $totDeb = 0
        let $totCre = 0

        $listAmount.each(function ()
        {
            const $field = $(this)
            const type = $field.attr('mytype')
            const val = isNaN(parseFloat($field.val())) ? 0 : parseFloat($field.val())

            if (type == 'debet')
                $totDeb = parseFloat($totDeb) + parseFloat(val)

            if (type == 'credit')
                $totCre = parseFloat($totCre) + parseFloat(val)
        })

        $('#vtotal-addless-dr').html(MoneyFormat($totDeb))
        $('#vtotal-addless-cr').html(MoneyFormat($totCre))

        FormatMoney()

        calcInvoiceAddless()
    }

    function calcInvoiceAddless ()
    {
        let $totInv = ResetFormatVal($('#vtotal-inv').html())
        let $totAddLessDr = ResetFormatVal($('#vtotal-addless-dr').html())
        let $totAddLessCr = ResetFormatVal($('#vtotal-addless-cr').html())

        let $totAddLess = parseFloat($totAddLessDr) - parseFloat($totAddLessCr)

        let $totInvoiceAddless = parseFloat($totInv) + parseFloat($totAddLess)

        $('#vsubtotal').html(MoneyFormat($totInvoiceAddless))

        calcSummary()
    }

    function calcSummary ()
    {
        ResetMoney()

        let $subTotal = ResetFormatVal($('#vsubtotal').html())
        let $potongan = parseFloat($('#potongan').val()) || 0
        let $pembulatan = parseFloat($('#pembulatan').val()) || 0
        let $otherCost = parseFloat($('#other-cost').val()) || 0

        let $totPay = parseFloat($subTotal) - parseFloat($potongan) + parseFloat($pembulatan) + parseFloat($otherCost)

        $('#totpay').val($totPay)

        FormatMoney()
    }

    $('#fi-app').on('change', '.calc-amount', function ()
    {
        calcSummary()
    })

    // aksi submit edit / update
    $('#fi-app').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            let $totInv = ResetFormatVal($('#vtotal-inv').html())

            if (parseFloat($totInv) == 0)
            {
                swalShowMessage("Information", 'Belum ada invoice yang dibayarkan.', 'warning')

                return false
            }

            ResetMoney()

            let $totPay = $('#totpay').val()

            if (parseFloat($totPay) <= 0)
            {
                swalShowMessage("Information", 'Pembayaran mengakibatkan Minus / Masih 0.', 'warning')

                return false
            }

            let list_coaid = document.getElementsByName('coaid[]')
            let debet = document.getElementsByName('debet[]')
            let credit = document.getElementsByName('credit[]')
            let pccid = document.getElementsByName('pccid[]')

            for (var i = 0; i < list_coaid.length; i++)
            { 
                var subtotal = parseFloat(debet[i].value) + parseFloat(credit[i].value)

                if (parseFloat(subtotal) != 0 && list_coaid[i].value == '')
                {
                    FormatMoney()

                    swalShowMessage('Peringatan', 'C.O.A belum dipilih.', 'warning')

                    return false
                }

                if (parseFloat(debet[i].value) != 0 && parseFloat(credit[i].value) != 0)
                {
                    FormatMoney()

                    swalShowMessage('Peringatan', 'Debet & Credit hanya boleh terisi salah satu per C.O.A', 'warning')

                    return false
                }

                var coatid = list_coaid[i].options[list_coaid[i].selectedIndex].getAttribute("data-coatid")

                if (coatid > 3 && pccid[i].value == '')
                {
                    FormatMoney()

                    swalShowMessage('Peringatan', 'C.O.A termasuk akun laba rugi, harap pilih cost center.', 'warning')

                    return false
                }
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.hutang_supplier.pembayaran_invoice.save') }}"

            showLoading()

            setTimeout((function ()
            {
                doAjax(
                    formSubmitUrl,
                    payload,
                    "POST"
                )
                .done( data => {
                    FormatMoney()

                    Swal.close()

                    if (data.success)
                    {
                        swalShowMessage('Sukses', data.message, 'success')
                        .then((result) =>
                        {
                            if (result.isConfirmed)
                            {
                                $("#mdl-form-app").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    FormatMoney()

                    Swal.close()

                    const res = err?.responseJSON

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>