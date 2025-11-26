<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-file-alt text-dark me-4"></span>
        Form Input Jurnal Manual
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
        <form method="post" id="form-input-jm" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="jmid" id="jmid" value="{{ $data_jm->jmid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Metode ?</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" name="mytype" class="btn-check" id="mytype1" value="1" checked="" />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="mytype1">By Form</label>

                        <input type="radio" name="mytype" class="btn-check" id="mytype2" value="2" />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="mytype2">By Upload</label>
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Dokumen</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="trans_date" id="trans_date" value="{{ $data_period->trans_date }}" class="form-control form-control-sm rounded-1 mydate-time" required="" />

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

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Posting ?</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" name="is_posted" class="btn-check" id="is_posted_f" value="f" />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="is_posted_f">Not Posted</label>

                        <input type="radio" name="is_posted" class="btn-check" id="is_posted_t" value="t" checked="" />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="is_posted_t">Posted</label>
                    </div>
                </div>

                <div class="col-lg-3 div-upload d-none">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Upload File</label>
                    <input type="file" name="chooseFile" id="chooseFile" class="form-control form-control-sm rounded-1 w-100" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <div class="d-flex flex-column h-100">
                        <label for="keterangan" class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0">{{ $data_jm->keterangan }}</textarea>
                    </div>
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
                                <th>Fungsi</th>
                                <th>C.O.A</th>
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
                                    BALANCED : <font id="txt-balanced"></font>
                                    <input type="hidden" id="balanced" value="0" readonly="" />
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
                <button type="button" class="btn btn-danger btn-sm rounded-1 w-100 w-md-auto" id="btn-batal" data-bs-dismiss="modal">
                    <i class="las la-undo"></i> Batal
                </button>

                <button type="button" class="btn btn-info btn-sm rounded-1 div-upload d-none" id="btn-down">
                    <i class="las la-file-excel"></i> Download Template
                </button>

                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto ms-md-auto">
                    <button type="button" class="btn btn-success btn-sm rounded-1 w-100 w-md-auto div-upload d-none" id="btn-preview">
                        <i class="las la-eye"></i> Preview Data
                    </button>

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

<script type="text/javascript">
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
    })

    $(".mydate-time").flatpickr({
        enableTime: false,
        dateFormat: "d-m-Y",
        time_24hr: true,
        minuteIncrement: 1
    })

    $('#btn-down').on('click', function (e)
    {
        e.preventDefault()

        showLoading()

        setTimeout((function ()
        {
            let link = "{{ route('api.akunting.jurnal_manual.tpl') }}"

            const name = 'File Upload Jurnal Manual - ' + moment().format('DD-MM-YYYY') + '.xls'

            exportExcel({
                name,
                url: link,
                params: { }
            }).finally(() => {
                Swal.close()
            })
        }), 2e3)
    })

    $('.add-coa').on('click', function (e)
    {
        e.preventDefault()

        AddCoa()
    })

    function AddCoa (coaid = '', notes = '', debet = 0, credit = 0, pccid = '')
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
                            </td>
                            <td class="text-nowrap">
                                ${cmb_coa}
                            </td>
                            <td class="text-nowrap">
                                <textarea id="notes[]" name="notes[]" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0" required="">${notes}</textarea>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-amount" mytype="debet" name="debet[]" value="${debet}" />
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-amount" mytype="credit" name="credit[]" value="${credit}" />
                                </div>
                            </td>
                            <td class="text-nowrap">
                                ${cmb_cost}
                            </td>
                        </tr>`

        $('#tbl-coa > tbody:last').append(data_coa)

        $('.select2-combo').select2({
            width: '100%',
            dropdownParent: $('#mdl-form-jm')
        })
    }

    $('#tbl-coa').on('click', '.hapus-coa', function ()
    {
        $(this).closest("tr").remove()

        summaryAmount()
    })

    $('#tbl-coa').on('change', '.calc-amount', function (e)
    {
        e.preventDefault()

        summaryAmount()
    })

    function summaryAmount ()
    {
        ResetMoney()

        let $listAmount = $('#tbl-coa .calc-amount')
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

        let $balanced = parseFloat($totDeb) - parseFloat($totCre)

        $('#balanced').val($balanced)

        $('#txt-balanced').html('Rp. ' + MoneyFormat($balanced))

        if (parseFloat($balanced) != 0)
            $('#txt-balanced').css('color', 'red')
        else
            $('#txt-balanced').css('color', '#000')

        FormatMoney()
    }

    // aksi submit edit / update
    $('#form-input-jm').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            var is_posted = $(this).find('[name="is_posted"]:checked')

            if (is_posted.length == 0)
            {
                swalShowMessage("Information", 'Harap Pilih Status Posting !', 'warning')
                return
            }

            var mytype = $(this).find('[name="mytype"]:checked').val()

            if (mytype == 2)
            {
                const $chooseFile = $(this).find('[name="chooseFile"]')

                if (!$chooseFile.val())
                    return swalShowMessage('Warning!', "Template (.xls) Belum Dipilih.", 'warning')
            }
            else
            {
                let jml_coa = $('#tbl-coa > tbody').find("tr").length

                if (jml_coa < 2)
                {
                    swalShowMessage("Information", 'Harap Pilih COA. Minimal 2 Data/Baris.', 'warning')
                    return
                }

                ResetMoney()

                let totDeb = $(this).find('#tot-deb').val()
                let totCre = $(this).find('#tot-cre').val()

                if (parseFloat(totDeb) == 0 && parseFloat(totDeb) == 0)
                {
                    FormatMoney()

                    swalShowMessage("Information", 'Nominal Belum Diinput.', 'warning')
                    return 
                }

                const list_coaid = document.getElementsByName('coaid[]')
                const debet = document.getElementsByName('debet[]')
                const credit = document.getElementsByName('credit[]')
                const pccid = document.getElementsByName('pccid[]')

                for (var i = 0; i < list_coaid.length; i++)
                { 
                    var subtotal = parseFloat(debet[i].value) + parseFloat(credit[i].value)

                    if (parseFloat(subtotal) != 0 && list_coaid[i].value == '')
                    {
                        FormatMoney()

                        Swal.fire('Peringatan', 'C.O.A belum dipilih.', 'warning')

                        return false
                    }

                    if (parseFloat(debet[i].value) != 0 && parseFloat(credit[i].value) != 0)
                    {
                        FormatMoney()

                        Swal.fire('Peringatan', 'Debet & Credit hanya boleh terisi salah satu per C.O.A', 'warning')

                        return false
                    }

                    var coatid = list_coaid[i].options[list_coaid[i].selectedIndex].getAttribute("data-coatid")

                    if (coatid > 3 && pccid[i].value == '')
                    {
                        FormatMoney()

                        Swal.fire('Peringatan', 'C.O.A termasuk akun laba rugi, harap pilih cost center.', 'warning')

                        return false
                    }
                }

                let balanced = $(this).find('#balanced').val()

                if (parseFloat(balanced) != 0)
                {
                    FormatMoney()

                    swalShowMessage("Information", 'Transaksi tidak balance sejumlah ' + MoneyFormat(balanced, 2) + '.', 'warning')
                    return 
                }
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.jurnal_manual.save', ['mytype' => ':mytype']) }}"
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
                                swal.close()

                                $("#mdl-form-jm").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else
                    {
                        swal.close()

                        swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                    }
                })
                .fail( err => {
                    swal.close()

                    const res = err?.responseJSON

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })

    // aksi submit edit / update
    $('#btn-preview').click(function ()
    {
        const $form = $('#form-input-jm')
        const $mytype = $form.find('[name="mytype"]:checked').val()
        const $chooseFile = $form.find('[name="chooseFile"]')[0]

        if (!$chooseFile.files.length)
            return swalShowMessage('Warning!', "Template (.xls) Belum Dipilih.", 'warning')

        const payload = new FormData()
            payload.append('chooseFile', $chooseFile.files[0]) // kirim file-nya

        formSubmitUrl = "{{ route('api.akunting.jurnal_manual.parsing_excel') }}"

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
                    swal.close()

                    $('#form-input-jm').find('[name="mytype"][value="1"]').prop('checked', true).trigger('change')

                    $('#tbl-coa tbody').empty()

                    // Contoh: tampilkan data dalam tabel
                    let html = ''
                    data.data.forEach((item, i) =>
                    {
                        AddCoa(item.coaid, item.notes, item.debet, item.credit, item.pccid)
                    })

                    FormatMoney()

                    summaryAmount()
                }
                else
                {
                    swal.close()

                    swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                }
            })
            .fail( err => {
                swal.close()

                const res = err?.responseJSON

                swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
            })
        }), 2e3)
    })
</script>
