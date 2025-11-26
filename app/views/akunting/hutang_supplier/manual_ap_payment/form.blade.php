<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-file-invoice text-dark me-4"></span>
        Form Input Manual A/P Payment
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
        <form method="post" id="fi-mapp" novalidate>
            <input type="hidden" name="mapid" id="mapid" value="{{ $data_head->mapid }}" readonly="" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-2">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Bayar</label>
                    <div class="input-group">
                        <input type="text" name="paydate" id="paydate" value="{{ $data_head->paydate }}" class="form-control form-control-sm rounded-1 mydate-time" readonly="" required="" />
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Supplier/Sub Dokter</label>
                    {!! $cmb_supp !!}
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kas / Bank</label>
                    {!! $cmb_bank !!}
                </div>

                <div class="col-lg-2">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Cara Bayar</label>
                    {!! $cmb_cara_bayar !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">No. Bayar</label>
                    <input type="text" class="form-control form-control-sm rounded-1" name="no_bayar" id="no_bayar" value="{{ $data_head->no_bayar }}" required="" />
                </div>

                <div class="col-lg-5">
                    <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                    <input type="text" class="form-control form-control-sm rounded-1" name="keterangan" id="keterangan" value="{{ $data_head->keterangan }}" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Cari No. Inv</label>
                    <select id="sInvAp" class="form-select form-select-sm rounded-1 w-100" data-control="select2"></select>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3 page-doctor">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Dokter</label>
                    {!! $cmb_doctor !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold required">
                        Detail Transaksi By Invoice
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu Keuangan & Akuntansi -> Hutang Supplier -> Manual A/P"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-inv">
                        <thead class="bg-dark text-uppercase fs-7 text-center">
                            <tr class="fw-bold text-white">
                                <th class="w-80px">Fungsi</th>
                                <th>No. A/P</th>
                                <th>Tanggal A/P</th>
                                <th>No. Invoice</th>
                                <th>Nominal Invoice</th>
                                <th>Sisa Pembayaran</th>
                                <th>Nominal Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td class="fw-bold text-end" colspan="4">SUBTOTAL</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" id="subtotal_inv" value="{{ $data_head->subtotal_inv }}" readonly="" />
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" id="subtotal_outs" value="{{ $data_head->subtotal_outs }}" readonly="" />
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="subtotal_pay" id="subtotal_pay" value="{{ $data_head->subtotal_pay }}" readonly="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="6">POTONGAN</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency sum-amount" name="potongan" id="potongan" value="{{ $data_head->potongan }}" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="6">PEMBULATAN</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency sum-amount" name="pembulatan" id="pembulatan" value="{{ $data_head->pembulatan }}" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="6">BIAYA LAINNYA</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency sum-amount" name="other_cost" id="other_cost" value="{{ $data_head->other_cost }}" />
                                    </div>
                                </td>
                            </tr>
                            <tr class="page-doctor">
                                <td class="fw-bold text-end" colspan="6">PAJAK DOKTER</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency sum-amount" name="tax_doctor" id="tax_doctor" value="{{ $data_head->tax_doctor }}" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="6">TOTAL PEMBAYARAN</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="totpay" id="totpay" value="{{ $data_head->totpay }}" readonly="" />
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-danger btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                    <a class="btn btn-sm rounded-1 btn-dark inv-search" data-mapid="0" href="javascript:void(0)">
                        <i class="bi bi-search"></i>
                        Cari Invoice
                    </a>
                </div>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_simpan">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->
<!--begin::Modal - Ubah-->
<div class="modal fade" id="mdl-form-inv" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-inv" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <!--begin::Modal content-->
        <div class="modal-content" id="modal2">asdamsdkm</div>
        <!--end::Modal content-->
    </div>
</div>



<!--begin::template - Ubah-->
<script type="text/javascript">
    {!! $AddInv !!}

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

    $(".mydate-time").flatpickr({
        enableTime: true,
        dateFormat: "d-m-Y H:i",
        time_24hr: true,
        minuteIncrement: 1
    })

    $(".mydate").flatpickr({
        defaultDate: null,
        dateFormat: "d-m-Y",
    })

    HideDoctor()

    $("#suppid").change(function ()
    {
        HideDoctor()

        ResetTable()
    })

    $("#doctor_id").change(function ()
    {
        ResetTable()
    })

    function HideDoctor ()
    {
        let suppid = $('#suppid').val()

        if (suppid == -1)
            $('.page-doctor').show()
        else
            $('.page-doctor').hide()
    }

    function ResetTable ()
    {
        $('#tbl-inv tbody').empty()

        subAmount()
    }

    $('#sInvAp').select2({
        dropdownParent      : $('#mdl-form-mapp'),
        placeholder         : '-- Cari Invoice --',
        minimumInputLength  : 3,
        ajax                :
                            {
                                url             : "{{ route('api.akunting.hutang_supplier.manual_ap_payment.cari_invoice') }}",

                                data            : function (params)
                                                {
                                                    var param = {
                                                        q           : params.term,
                                                        suppid      : $('#suppid option:selected').val(),
                                                        doctor_id   : $('#doctor_id option:selected').val()
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
                                                                id          : items.maid,
                                                                text        : 'Inv : ' + items.no_inv + ' [ Rp. ' + MoneyFormat(items.nominal_inv) + ' # No. AP : ' + items.apcode + ' ]',
                                                                apcode      : items.apcode,
                                                                apdate      : items.apdate,
                                                                no_inv      : items.no_inv,
                                                                nominal_inv : items.nominal_inv,
                                                                sisa_inv    : items.sisa
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
            var maidList = window.document.getElementsByName('maid[]')

            for (var j = 0; j < maidList.length; j++)
            {
                if (items.id == maidList[j].value)
                {
                    swalShowMessage("Information", 'Invoice ' + items.no_inv + ' Telah Berada Dalam List !', 'warning')

                    $(this).val(null).trigger("change")

                    return false
                }
            }

            AddInv(items.id, items.apcode, items.apdate, items.no_inv, items.nominal_inv, items.sisa_inv)

            $(this).val(null).trigger("change")
        }
    }).on('select2:opening', function (e)
    {
        var suppid = $('#suppid option:selected').val()
        var doctor_id = $('#doctor_id option:selected').val()

        if (suppid == '')
        {
            swalShowMessage('Information, ', 'Supplier Belum Dipilih !', 'warning')

            return false
        }
        else if (suppid == -1 && doctor_id == '')
        {
            swalShowMessage('Information, ', 'Dokter Belum Dipilih !', 'warning')

            return false
        }
    })

    function AddInv (maid, apcode, apdate, no_inv, nominal_inv, sisa_inv, nominal_terima = 0, mapdid = 0)
    {
        let data_coa = `<tr class="align-middle">
                            <td class="text-center">
                                <i class="las la-trash fs-2 text-danger text-center hapus-inv" role="button"></i>
                            </td>
                            <td class="text-nowrap">
                                ${apcode}
                                <input type="hidden" name="maid[]" value="${maid}" readonly="" />
                                <input type="hidden" name="mapdid[]" value="${mapdid}" readonly="" />
                                <input type="hidden" name="apcode[]" value="${apcode}" readonly="" />
                            </td>
                            <td class="text-nowrap">
                                ${apdate}
                            </td>
                            <td class="text-nowrap">
                                ${no_inv}
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency amount-inv" name="nominal_inv[]" value="${nominal_inv}" />
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency amount-outs" name="sisa_inv[]" value="${sisa_inv}" />
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency amount-pay" data-outs="${sisa_inv}" name="nominal_terima[]" value="${nominal_terima}" />
                                </div>
                            </td>
                        </tr>`

        $('#tbl-inv > tbody:last').append(data_coa)

        $('.select2-combo').select2({
            width: '100%',
            dropdownParent: $('#mdl-form-mapp')
        })

        FormatMoney()

        subAmount()
    }

    $('#tbl-inv').on('click', '.hapus-inv', function ()
    {
        $(this).closest("tr").remove()

        subAmount()
    })

    $('#tbl-inv').on(
    {
        change      : function (e)
                    {
                        // Handle change...
                        e.preventDefault()

                        const $field = $(this)
                        const amountOuts = $field.attr('data-outs')

                        if (parseFloat($field.val()) > parseFloat(amountOuts))
                        {
                            swalShowMessage('Information, ', 'Nominal Pembyaran Melebihi Outstanding !', 'warning')

                            $field.val(amountOuts)

                            return false
                        }

                        subAmount()
                    },

        dblclick    : function (e)
                    {
                        // Handle dblclick...
                        e.preventDefault()

                        ResetMoney()

                        const $field = $(this)
                        const amountOuts = $field.attr('data-outs')

                        $field.val(amountOuts)

                        FormatMoney()

                        subAmount()
                    },
    }, '.amount-pay');

    function subAmount ()
    {
        ResetMoney()

        let $listAmountInv = $('#tbl-inv .amount-inv')
        let $totAmountInv = 0

        $listAmountInv.each(function ()
        {
            const $field = $(this)
            const val = isNaN(parseFloat($field.val())) ? 0 : parseFloat($field.val())

            $totAmountInv = parseFloat($totAmountInv) + parseFloat(val)
        })

        $('#subtotal_inv').val($totAmountInv)

        let $listAmountOuts = $('#tbl-inv .amount-outs')
        let $totAmountOuts = 0

        $listAmountOuts.each(function ()
        {
            const $field = $(this)
            const val = isNaN(parseFloat($field.val())) ? 0 : parseFloat($field.val())

            $totAmountOuts = parseFloat($totAmountOuts) + parseFloat(val)
        })

        $('#subtotal_outs').val($totAmountOuts)

        let $listAmountPay = $('#tbl-inv .amount-pay')
        $totAmountPay = 0

        $listAmountPay.each(function ()
        {
            const $field = $(this)
            const val = isNaN(parseFloat($field.val())) ? 0 : parseFloat($field.val())

            $totAmountPay = parseFloat($totAmountPay) + parseFloat(val)
        })

        $('#subtotal_pay').val($totAmountPay)

        FormatMoney()

        summaryAmount()
    }

    $('#tbl-inv').on('change', '.sum-amount', function (e)
    {
        e.preventDefault()

        summaryAmount()
    })

    function summaryAmount ()
    {
        ResetMoney()

        var subtotal_pay = $('#subtotal_pay')
        var subtotal_pay = isNaN(parseFloat(subtotal_pay.val())) ? 0 : parseFloat(subtotal_pay.val())

        var potongan = $('#potongan')
        var potongan = isNaN(parseFloat(potongan.val())) ? 0 : parseFloat(potongan.val())

        var pembulatan = $('#pembulatan')
        var pembulatan = isNaN(parseFloat(pembulatan.val())) ? 0 : parseFloat(pembulatan.val())

        var other_cost = $('#other_cost')
        var other_cost = isNaN(parseFloat(other_cost.val())) ? 0 : parseFloat(other_cost.val())

        var tax_doctor = $('#tax_doctor')
        var tax_doctor = isNaN(parseFloat(tax_doctor.val())) ? 0 : parseFloat(tax_doctor.val())

        var totpay = parseFloat(subtotal_pay) - parseFloat(potongan) + parseFloat(pembulatan) + parseFloat(other_cost) - parseFloat(tax_doctor)

        $('#totpay').val(parseFloat(totpay))

        FormatMoney()
    }



    $('.inv-search').on('click', function (e)
    {
        var suppid = $('#suppid option:selected').val()
        var doctor_id = $('#doctor_id option:selected').val()

        if (suppid == '')
        {
            swalShowMessage('Information, ', 'Supplier Belum Dipilih !', 'warning')

            return false
        }
        else if (suppid == -1 && doctor_id == '')
        {
            swalShowMessage('Information, ', 'Dokter Belum Dipilih !', 'warning')

            return false
        }



        openModalInv(suppid,doctor_id)
    })

    function openModalInv (suppid,doctor_id)
    {
        showLoading()
        getDataInvoice(suppid,doctor_id).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-inv', res, null, true)
        })
    }

    async function getDataInvoice (suppid,doctor_id)
    {


        let result
        let link =  "{{ route('api.akunting.hutang_supplier.manual_ap_payment.data_invoice') }}"
        try {
            result = await $.ajax({
                url     : link,
                type    : 'POST',
                data    : { suppid :suppid,
                            doctor_id :doctor_id,
                        }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }




    // aksi submit edit / update
    $('#fi-mapp').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            let suppid = $('#suppid option:selected').val()
            let doctor_id = $('#doctor_id option:selected').val()

            if (suppid == -1 && doctor_id == '')
            {
                swalShowMessage('Information, ', 'Dokter Belum Dipilih !', 'warning')

                return false
            }

            let jml_inv = $('#tbl-inv > tbody').find("tr").length

            if (jml_inv < 1)
            {
                swalShowMessage("Information", 'Harap Pilih Invoice Terlebih Dahulu.', 'warning')
                return
            }

            ResetMoney()

            const list_maid = document.getElementsByName('maid[]')
            const nominal_terima = document.getElementsByName('nominal_terima[]')

            for (var i = 0; i < list_maid.length; i++)
            { 
                nominal_terima[i].value = isNaN(parseFloat(nominal_terima[i].value)) ? 0 : parseFloat(nominal_terima[i].value)

                if (parseFloat(nominal_terima[i].value) == 0)
                {
                    FormatMoney()

                    swalShowMessage('Peringatan', 'Nominal Terima belum diisi.', 'warning')

                    return false
                }
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.hutang_supplier.manual_ap_payment.save') }}"

            showLoading()

            setTimeout((function ()
            {
                doAjax(
                    formSubmitUrl,
                    payload,
                    "POST"
                )
                .done( data => {
                    if (data.success)
                    {
                        swalShowMessage('Sukses', data.message, 'success')
                        .then((result) =>
                        {
                            if (result.isConfirmed)
                            {
                                $("#mdl-form-mapp").modal('hide')

                                window.location.reload();
                //                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else
                    {
                        swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')

                        FormatMoney()
                    }
                })
                .fail( err => {
                    const res = err?.responseJSON

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')

                    FormatMoney()
                })
            }), 2e3)
        }
    })

    function pilih(id,apcode,apdate,no_inv,nominal_inv,sisa_inv){
                var maidList = window.document.getElementsByName('maid[]')

                for (var j = 0; j < maidList.length; j++)
                {
                    if (id == maidList[j].value)
                    {
                        swalShowMessage("Information", 'Invoice ' + no_inv + ' Telah Berada Dalam List !', 'warning')

                        $(this).val(null).trigger("change")

                        return false
                    }
                }

                AddInv(id, apcode, apdate, no_inv, nominal_inv, sisa_inv)
                $(this).val(null).trigger("change")
    }

</script>
