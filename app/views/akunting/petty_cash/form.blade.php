<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-money-bill text-dark me-4"></span>
        Form Input Kas & Bank
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
        <form method="post" id="form-input-pc" novalidate>
            <input type="hidden" name="pcid" id="pcid" value="{{ $data_head->pcid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Dokumen</label>
                    <div class="input-group input-group-sm">
                        <!--input type="text" name="pcdate" id="pcdate" value="{{ $data_head->pcdate }}" class="form-control form-control-sm rounded-1 mydate-time" required="" /-->
                        <input type="text" name="pcdate" id="pcdate" value="{{ $data_head->pcdate }}" class="form-control form-control-sm rounded-1 mydate" required="" />

                        <span class="input-group-text">
                            <i class="las la-calendar-alt fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                    </div>

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-8">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Cash Book</label>
                    {!! $cmb_cash_book !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-8">
                    <div class="d-flex flex-column h-100">
                        <label for="keterangan" class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0">{{ $data_head->keterangan }}</textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">&nbsp;</label>
                    <select id="sTrans-Tipe" class="form-select form-select-sm rounded-1 w-100" data-control="select2"></select>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold required">
                        Detail Transaksi
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu Keuangan & Akuntansi -> Kas & Bank -> Transaction Type"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-trans">
                        <thead class="bg-dark text-uppercase fs-7 text-center">
                            <tr class="fw-bold text-white">
                                <th>Fungsi</th>
                                <th>Transaksi</th>
                                <th>Notes</th>
                                <th>Debet</th>
                                <th>Credit</th>
                                <th>Cost Center</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" id="tot-deb" value="0" readonly="" />
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" id="tot-cre" value="0" readonly="" />
                                    </div>
                                </td>
                                <td class="fw-bold">
                                    Saldo : <font id="txt-balanced"></font>
                                    <input type="hidden" id="saldo" value="0" readonly="" />
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-danger btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_simpan">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    {!! $AddTrans !!}

    $('.modal [data-bs-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    })

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

    /*$(".mydate-time").flatpickr({
        enableTime: true,
        dateFormat: "d-m-Y H:i",
        time_24hr: true,
        minuteIncrement: 1
    })*/

    $('.mydate').flatpickr({
        defaultDate: null,
        dateFormat: "d-m-Y",
    })

    $("#bank_id").change(function()
    {
        get_saldo()
    })

    function get_saldo ()
    {
        let pcdate  = $('#pcdate').val(),
            bank_id = $('#bank_id option:selected').val()

        if (bank_id != '')
        {
            let link = "{{ route('api.akunting.petty_cash.transaction.check_saldo', ['mybank' => ':mybank']) }}"
                link = link.replace(':mybank', bank_id)

            $.ajax({
                url         : link,
                data        : { pcdate : pcdate, bank_id : bank_id },
                dataType    : 'JSON',
                type        : 'GET',

                success     : function (data)
                            {
                                $('#saldo').val(data.saldo)

                                FormatMoney()

                                hitungBalance()
                            },

                error       : function (req, stat, err)
                            {
                            },

                async       : false,
                cache       : false
            })
        }

        return false
    }

    function hitungBalance ()
    {
        ResetMoney()

        let $saldo  = $('#saldo').val(),
            $totDeb = $('#tot-deb').val(),
            $totCre = $('#tot-cre').val()

        let $balance = parseFloat($saldo) - parseFloat($totDeb) + parseFloat($totCre)

        $('#txt-balanced').html('Rp. ' + MoneyFormat($balance))

        if (parseFloat($balance) != 0)
            $('#txt-balanced').css('color', 'red')
        else
            $('#txt-balanced').css('color', '#000')

        FormatMoney()
    }

    $('#sTrans-Tipe').select2({
        dropdownParent      : $('#mdl-form-pc'),
        placeholder         : '-- Pilih Transaksi',
        minimumInputLength  : 3,
        ajax                :
                            {
                                url             : "{{ route('api.akunting.petty_cash.transaction.cari_trans') }}",

                                data            : function (params)
                                                {
                                                    var param = {
                                                        q   : params.term,
                                                    }

                                                    // Query parameters will be ?search=[term]&type=public
                                                    return param
                                                },

                                processResults  : function (json)
                                                {
                                                    return {
                                                        results: $.map(json, function (items)
                                                        {
                                                            return {
                                                                id          : items.pctid,
                                                                text        : items.keterangan,
                                                                type_trans  : items.type_trans,
                                                                coatid      : items.coatid
                                                            }
                                                        })
                                                    }
                                                },

                                cache           : true,
                            }
    }).on('select2:select', function (e)
    {
        var items = e.params.data

        if (items.id != '')
        {
            /*var pctidList = window.document.getElementsByName('pctid[]')

            for (var j = 0; j < pctidList.length; j++)
            {
                if (items.id == pctidList[j].value)
                {
                    swalShowMessage("Information", 'Transaksi ' + items.text + ' Telah Berada Dalam List !', 'warning')

                    $(this).val(null).trigger("change")

                    return false
                }
            }*/

            AddTrans(items.id, items.text, items.type_trans, items.coatid)

            $(this).val(null).trigger("change")
        }
    })

    function AddTrans (pctid, ket_trans, type_trans, coatid, notes = '', debet = 0, credit = 0, pccid = 0, pcdid = 0)
    {
        let attr_debet, attr_credit = type_trans_txt = ""
        let row_cost_center = `{!! $row_cost_center !!}`
        let cost_center = row_cost_center.split(';')
        let cmb_cost = `<select name="pccid[]" id="pccid-${pctid}" data-pctid="${pctid}" class="form-select form-select-sm rounded-1 w-100" data-control="select2">`

        $.each(cost_center, function (idx, val)
        {
            let data_cost = val.split(':')
            let sel_cost = ''

            if (data_cost[0] == pccid) sel_cost = 'selected=""'

            cmb_cost += `<option value="${data_cost[0]}" ${sel_cost}>${data_cost[1]}</option>`
        })

        cmb_cost += `</select>`

        if (type_trans == 1)
        {
            attr_debet = 'readonly=""'
            attr_credit = ''
            type_trans_txt = 'CASH IN'
        }
        else
        {
            attr_debet = ''
            attr_credit = 'readonly=""'
            type_trans_txt = 'CASH OUT'
        }

        let data_coa = `<tr class="align-middle">
                            <td class="text-center">
                                <i class="las la-trash fs-2 text-danger text-center hapus-trans" role="button"></i>
                            </td>
                            <td class="text-nowrap">
                                ${ket_trans}<br /><I class="text-danger fw-semibold">[ ${type_trans_txt} ]</I>
                                <input type="hidden" name="pcdid[]" id="pcdid[]" value="${pcdid}" />
                                <input type="hidden" name="pctid[]" id="pctid[]" value="${pctid}" />
                                <input type="hidden" name="ket_trans[]" id="ket_trans[]" value="${ket_trans}" />
                                <input type="hidden" name="type_trans[]" id="type_trans[]" value="${type_trans}" />
                                <input type="hidden" name="coatid[]" id="coatid[]" value="${coatid}" />
                            </td>
                            <td class="text-nowrap">
                                <textarea id="notes[]" name="notes[]" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0" required="">${notes}</textarea>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-amount" mytype="debet" name="debet[]" value="${debet}" ${attr_debet} />
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-amount" mytype="credit" name="credit[]" value="${credit}" ${attr_credit} />
                                </div>
                            </td>
                            <td class="text-nowrap">
                                ${cmb_cost}
                            </td>
                        </tr>`

        $('#tbl-trans > tbody:last').append(data_coa)

        $('#pccid-' + pctid).select2()
    }

    $('#tbl-trans').on('click', '.hapus-trans', function ()
    {
        $(this).closest("tr").remove()

        summaryAmount()
    })

    $('#tbl-trans').on('change', '.calc-amount', function (e)
    {
        e.preventDefault()

        summaryAmount()
    })

    function summaryAmount ()
    {
        ResetMoney()

        let $listAmount = $('#tbl-trans .calc-amount')
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

        $('#tot-deb').val($totDeb)
        $('#tot-cre').val($totCre)

        FormatMoney()

        hitungBalance()
    }

    // aksi submit edit / update
    $('#form-input-pc').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            let jml_trans = $('#tbl-trans > tbody').find("tr").length

            if (jml_trans == 0)
            {
                swalShowMessage("Information", 'Harap Pilih Transaksi. Minimal 1 Data/Baris.', 'warning')
                return
            }

            ResetMoney()

            const list_pctid = document.getElementsByName('pctid[]')
            const ket_trans = document.getElementsByName('ket_trans[]')
            const type_trans = document.getElementsByName('type_trans[]')
            const coatid = document.getElementsByName('coatid[]')
            const debet = document.getElementsByName('debet[]')
            const credit = document.getElementsByName('credit[]')
            const pccid = document.getElementsByName('pccid[]')

            for (var i = 0; i < list_pctid.length; i++)
            {
                if (type_trans[i].value == 1 && parseFloat(credit[i].value) == 0)
                {
                    FormatMoney()

                    Swal.fire('Peringatan', 'Credit ' + ket_trans[i].value + ' belum diinput', 'warning')

                    return false
                }
                else if (type_trans[i].value == 2 && parseFloat(debet[i].value) == 0)
                {
                    FormatMoney()

                    Swal.fire('Peringatan', 'Debet ' + ket_trans[i].value + ' belum diinput', 'warning')

                    return false
                }

                if (coatid[i].value > 3 && pccid[i].value == '')
                {
                    FormatMoney()

                    Swal.fire('Peringatan', ket_trans[i].value + ' termasuk akun laba rugi, harap pilih cost center.', 'warning')

                    return false
                }
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.petty_cash.transaction.save') }}"

            showLoading()

            setTimeout((function ()
            {
                doAjax(
                    formSubmitUrl,
                    payload,
                    "POST"
                )
                .done( data => {
                    Swal.close()

                    if (data.success)
                    {
                        swalShowMessage('Sukses', data.message, 'success')
                        .then((result) =>
                        {
                            if (result.isConfirmed)
                            {
                                $("#mdl-form-pc").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    Swal.close()

                    const res = err?.responseJSON

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>