<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-file-invoice text-dark me-4"></span>
        Form Input Manual A/R
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
        <form method="post" id="fi-mar" novalidate>
            <input type="hidden" name="maid" id="maid" value="{{ $data_head->maid }}" readonly="" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4 mb-5">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Metode ?</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" name="mytype" class="btn-check" id="mytype1" value="1" checked="" />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="mytype1">By Form</label>

                        <input type="radio" name="mytype" class="btn-check" id="mytype2" value="2" />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="mytype2">By Upload</label>
                    </div>
                </div>
            </div>
            <div class="row g-0 gx-4">
                <div class="col-lg-2">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Invoice</label>
                    <div class="input-group">
                        <input type="text" name="ardate" id="ardate" value="{{ $data_head->ardate }}" class="form-control form-control-sm rounded-1 mydate-time" readonly="" required="" />
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>

                <div class="col-lg-4 div-upload d-none">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Pilih COA</label>
                    {!! $cmb_coa_main !!}
                </div>

                <div class="col-lg-4 div-upload d-none">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Upload File</label>
                    <input type="file" name="chooseFile" id="chooseFile" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-2 div-form">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Duedate</label>
                    <div class="input-group">
                        <input type="text" name="duedate" id="duedate" value="{{ $data_head->duedate }}" class="form-control form-control-sm rounded-1 mydate" readonly="" required="" />
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>

                <div class="col-lg-4 div-form">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Customer</label>
                    {!! $cmb_cust !!}
                </div>

                <div class="col-lg-4 div-bank div-form">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Pilih Bank</label>
                    {!! $cmb_bank !!}
                </div>
                <div class="col-lg-4 div-karyawan div-form">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Pilih Karyawan</label>
                    {!! $cmb_karyawan !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-3 div-form">
                    <label class="text-dark fw-bold fs-7 pb-2 required">No. Invoice</label>
                    <input type="text" class="form-control form-control-sm rounded-1" name="no_inv" id="no_inv" value="{{ $data_head->no_inv }}" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                    <input type="text" class="form-control form-control-sm rounded-1" name="keterangan" id="keterangan" value="{{ $data_head->keterangan }}" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3 div-form">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold required">
                        Detail Transaksi By C.O.A
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu Keuangan & Akuntansi -> Setup -> Master COA -> Chart Of Account"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-coa">
                        <thead class="bg-dark text-uppercase fs-7 text-center">
                            <tr class="fw-bold text-white">
                                <th class="w-80px">Fungsi</th>
                                <th>C.O.A</th>
                                <th>Notes</th>
                                <th class="w-300px">Amount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td class="fw-bold text-end" colspan="3">SUBTOTAL</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="subtotal" id="subtotal" value="{{ $data_head->subtotal }}" readonly="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="3">PPN</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm w-10px fs-8 rounded-1 currency sum-amount" mytype="persen" name="ppn" id="ppn" value="{{ $data_head->ppn }}" />
                                        <span class="input-group-text">%</span>
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-80px fs-8 rounded-1 currency sum-amount" name="ppn_rp" id="ppn_rp" value="{{ $data_head->ppn_rp }}" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="3">TOTAL</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="totalall" id="totalall" value="{{ $data_head->totalall }}" readonly="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end">
                                    <button type="button" class="btn btn-sm btn-dark rounded-1 add-coa me-2">
                                        <i class="fa fa-plus"></i>
                                        Tambah
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
                <button type="button" class="btn btn-danger btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info btn-sm rounded-1 div-upload d-none" id="btn-down">
                    <i class="las la-file-excel"></i> Download Template
                </button>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto ms-md-auto">
                    <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_simpan">Simpan</button>
                </div>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<!--begin::template - Ubah-->
<script type="text/javascript">
    {!! $AddCoa !!}

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

    $('input[name="mytype"]').on('change', function ()
    {
        var mytype = $(this).val()

        if (mytype == 2)
        {
            $('.div-upload').removeClass('d-none')
            $('.div-form').addClass('d-none')
        }
        else
        {
            $('.div-upload').addClass('d-none')
            $('.div-form').removeClass('d-none')
        }
        $('#coa_main').val(null).trigger('change');
    })

    $(".mydate-time").flatpickr({
        enableTime: true,
        dateFormat: "d-m-Y H:i",
        time_24hr: true,
        defaultDate : new Date(),
        minuteIncrement: 1
    })

    $(".mydate").flatpickr({
        defaultDate: null,
        dateFormat: "d-m-Y",
    })

    $('#btn-down').on('click', function (e)
    {
        e.preventDefault()

        showLoading()

        setTimeout((function ()
        {
            let link = "{{ route('api.akunting.piutang_pelanggan.manual_ar.tpl') }}"

            const name = 'File Upload Manual AR - ' + moment().format('DD-MM-YYYY') + '.xls'

            exportExcel({
                name,
                url: link,
                params: { }
            }).finally(() => {
                Swal.close()
            })
        }), 2e3)
    })


    hideDetailCust()

    $("#custid").change(function ()
    {
        $('#tbl-coa tbody').empty();
        hideDetailCust()
    })


    function hideDetailCust ()
    {
        let custid = $('#custid').val()
        $('.div-bank').hide()
        $('.div-karyawan').hide()

        $('#bank_id').val(null).trigger('change');
        $('#pegawai_id').val(null).trigger('change');

        if (custid == -1){
            $('.div-bank').hide()
            $('.div-karyawan').show()
            $('#bank_id').val(null).trigger('change');
            $('#pegawai_id').val('{{ $data_head->pegawai_id }}').trigger('change');
        }else if (custid == -2){
            $('.div-bank').show()
            $('.div-karyawan').hide()
            $('#bank_id').val('{{ $data_head->bank_id }}').trigger('change');
            $('#pegawai_id').val(null).trigger('change');
        }else{
            $('.div-bank').hide()
            $('.div-karyawan').hide()
        }


    }

    $('.add-coa').on('click', function (e)
    {
        e.preventDefault()

        AddCoa()
    })

    function AddCoa (coaid = 0, notes = '', amount = 0, madid = 0)
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

        let data_coa = `<tr class="align-middle">
                            <td class="text-center">
                                <i class="las la-trash fs-2 text-danger text-center hapus-coa" role="button"></i>
                            </td>
                            <td class="text-nowrap">
                                ${cmb_coa}
                                <input type="hidden" name="madid[]" value="${madid}" readonly="" />
                            </td>
                            <td class="text-nowrap">
                                <textarea id="notes[]" name="notes[]" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0">${notes}</textarea>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-amount" name="amount[]" value="${amount}" />
                                </div>
                            </td>
                        </tr>`

        $('#tbl-coa > tbody:last').append(data_coa)

        $('.select2-combo').select2({
            width: '100%',
            dropdownParent: $('#mdl-form-mar')
        })
    }

    $('#tbl-coa').on('click', '.hapus-coa', function ()
    {
        $(this).closest("tr").remove()

        subAmount()
    })

    $('#tbl-coa').on('change', '.calc-amount', function (e)
    {
        e.preventDefault()

        subAmount()
    })

    function subAmount ()
    {
        ResetMoney()

        let $listAmount = $('#tbl-coa .calc-amount')
        let $totAmount = 0

        $listAmount.each(function ()
        {
            const $field = $(this)
            const val = isNaN(parseFloat($field.val())) ? 0 : parseFloat($field.val())

            $totAmount = parseFloat($totAmount) + parseFloat(val)
        })

        $('#subtotal').val($totAmount)

        FormatMoney()

        summaryAmount()
    }

    $('#tbl-coa').on('change', '.sum-amount', function (e)
    {
        e.preventDefault()

        ResetMoney()

        const $field = $(this)
        const type = $field.attr('mytype')
        const val = isNaN(parseFloat($field.val())) ? 0 : parseFloat($field.val())
        const subtotal = $('#subtotal')
        const subval = isNaN(parseFloat(subtotal.val())) ? 0 : parseFloat(subtotal.val())

        if (type == 'persen')
        {
            var ppn_rp = (parseFloat(subval) * parseFloat(val)) / 100

            $('#ppn_rp').val(parseFloat(ppn_rp))
        }
        else
        {
            var ppn_rp_val = $('#ppn_rp')
            var ppn_rp = isNaN(parseFloat(ppn_rp_val.val())) ? 0 : parseFloat(ppn_rp_val.val())

            var ppn = (parseFloat(ppn_rp) / parseFloat(subval)) * 100

            $('#ppn').val(parseFloat(ppn))
        }

        FormatMoney()

        summaryAmount()
    })

    function summaryAmount (from = '')
    {
        ResetMoney()

        var subtotal = $('#subtotal')
        var subval = isNaN(parseFloat(subtotal.val())) ? 0 : parseFloat(subtotal.val())

        var ppn_rp_val = $('#ppn_rp')
        var ppn_rp = isNaN(parseFloat(ppn_rp_val.val())) ? 0 : parseFloat(ppn_rp_val.val())

        var totalall = parseFloat(subval) + parseFloat(ppn_rp)

        $('#totalall').val(parseFloat(totalall))

        FormatMoney()
    }

    // aksi submit edit / update
    $('#fi-mar').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {


            var mytype = $(this).find('[name="mytype"]:checked').val()

            if (mytype == 2)
            {

                let coa_main = $('#coa_main').val();
                if(coa_main == ''){
                    swalShowMessage('Peringatan', 'COA Belum di Pilih.', 'warning')
                    return false
                }


                const $chooseFile = $(this).find('[name="chooseFile"]')

                if (!$chooseFile.val())
                    return swalShowMessage('Warning!', "Template (.xls) Belum Dipilih.", 'warning')
            }
            else
            {


                let no_inv = $('#no_inv').val();
                if(no_inv == ''){
                    swalShowMessage('Peringatan', 'No. Invoice Belum di Isi.', 'warning')
                    return false
                }

                let custid = $('#custid').val();
                let bank_id = $('#bank_id').val();
                let pegawai_id = $('#pegawai_id').val();

                if(custid == ''){
                    swalShowMessage('Peringatan', 'Customer Belum di Isi.', 'warning')
                    return false
                }

                if(custid == '-1' &&  pegawai_id == ''){
                    swalShowMessage('Peringatan', 'Karyawan Belum di Pilih.', 'warning')
                    return false
                }


                if(custid == '-2' &&  bank_id == ''){
                    swalShowMessage('Peringatan', 'Bank EDC Belum di Pilih.', 'warning')
                    return false
                }



                    let jml_coa = $('#tbl-coa > tbody').find("tr").length

                    if (jml_coa < 1)
                    {
                        swalShowMessage("Information", 'Harap Pilih COA Terlebih Dahulu.', 'warning')
                        return
                    }

                    ResetMoney()

                    const list_coaid = document.getElementsByName('coaid[]')
                    const amount = document.getElementsByName('amount[]')

                    for (var i = 0; i < list_coaid.length; i++)
                    { 
                        amount[i].value = isNaN(parseFloat(amount[i].value)) ? 0 : parseFloat(amount[i].value)

                        if (parseFloat(amount[i].value) == 0 && list_coaid[i].value == '')
                        {
                            FormatMoney()

                            swalShowMessage('Peringatan', 'C.O.A belum dipilih & Amount belum diisi.', 'warning')

                            return false
                        }

                        if (parseFloat(amount[i].value) != 0 && list_coaid[i].value == '')
                        {
                            FormatMoney()

                            swalShowMessage('Peringatan', 'C.O.A belum dipilih.', 'warning')

                            return false
                        }

                        if (parseFloat(amount[i].value) == 0 && list_coaid[i].value != '')
                        {
                            FormatMoney()

                            swalShowMessage('Peringatan', 'Amount belum diisi.', 'warning')

                            return false
                        }
                    }

            }
                    const payload = new FormData(this)
                        payload.append('_method', 'patch') // ganti ajax method post menjadi patch

                    formSubmitUrl = "{{ route('api.akunting.piutang_pelanggan.manual_ar.save', ['mytype' => ':mytype']) }}"
                    formSubmitUrl = formSubmitUrl.replace(':mytype', mytype)

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
                                        $("#mdl-form-mar").modal('hide')

                                        table.ajax.reload(null, false)
                                    }
                                })
                            }
                            else swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                        })
                        .fail( err => {
                            const res = err?.responseJSON
                            swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                        })
                    }), 2e3)
        }
    })
</script>