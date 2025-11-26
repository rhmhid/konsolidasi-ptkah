<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-clipboard-list text-dark me-4"></span>
        Form Write Off Data Asset
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
        <form method="post" id="fi-fas-wo" novalidate>
            <input type="hidden" name="faid" id="faid" value="{{ $data_db->faid }}" readonly="" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Asset</label>
                    <input type="text" name="facode" id="facode" value="{{ $data_db->facode }}" class="form-control form-control-sm rounded-1 w-100" readonly="" required="" />
                </div>

                <div class="col-lg-8">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Asset</label>
                    <input type="text" name="faname" id="faname" value="{{ $data_db->faname }}" class="form-control form-control-sm rounded-1 w-100" readonly="" required="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Nilai Perolehan</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="nilai_perolehan" id="nilai_perolehan" value="{{ $data_db->nilai_perolehan }}" readonly="" />
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Nilai Buku Asset</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="nilai_buku" id="nilai_buku" value="{{ $data_db->nilai_buku }}" readonly="" />
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Write Off</label>
                    <div class="input-group">
                        <input type="text" name="wo_date" id="wo_date" value="{{ $wo_date }}" class="form-control form-control-sm rounded-1 mydate-time" readonly="" required="" />
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Perlakuan Write Off</label>
                    {!! $cmb_write_off !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3 div-jual">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kas/Bank (bila Penjualan Asset)</label>
                    {!! $cmb_bank !!}
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nilai Jual (Rp.) (bila Penjualan Asset)</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency sum-amount" name="nilai_jual" id="nilai_jual" value="{{ $data_db->nilai_buku }}" />
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3 div-jual">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">PPn</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm w-10px fs-8 rounded-1 currency sum-amount" mytype="persen" name="ppn" id="ppn" value="{{ $data_head->ppn }}" />
                        <span class="input-group-text">%</span>
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-80px fs-8 rounded-1 currency sum-amount" name="ppn_rp" id="ppn_rp" value="{{ $data_head->ppn_rp }}" />
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Total Penjualan</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="total_jual" id="total_jual" value="{{ $data_db->nilai_buku }}" />
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">C.O.A Asset</label>
                    <input type="text" name="coa_asset" id="coa_asset" value="{{ $data_db->coa_fa }}" class="form-control form-control-sm rounded-1 w-100" readonly="" required="" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Write Off To C.O.A</label>
                    {!! $cmb_coa_write_off !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <div class="d-flex flex-column h-100">
                        <label for="notes" class="text-dark fw-bold fs-7 pb-2">Catatan</label>
                        <textarea id="notes" name="notes" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0"></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
                <button type="button" class="btn btn-danger btn-sm rounded-1 w-100 w-md-auto" id="btn-batal" data-bs-dismiss="modal">
                    <i class="las la-undo"></i> Batal
                </button>

                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto ms-md-auto">
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
    FormatMoney()

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

    HideJual()

    // aksi submit edit / update
    $('#wo_status').change(function (e)
    {
        e.preventDefault()

        HideJual()
    })

    function HideJual ()
    {
        let $wo_status = $("#wo_status option:selected").val()

        if ($wo_status == 2)
            $('.div-jual').show()
        else
            $('.div-jual').hide()
    }

    $('.sum-amount').on('change', function (e)
    {
        e.preventDefault()

        ResetMoney()

        const $field = $(this)
        const type = $field.attr('mytype')
        const val = isNaN(parseFloat($field.val())) ? 0 : parseFloat($field.val())
        const nilai_jual = $('#nilai_jual')
        const nilaival = isNaN(parseFloat(nilai_jual.val())) ? 0 : parseFloat(nilai_jual.val())

        if (type == 'persen')
        {
            var ppn_rp = (parseFloat(nilaival) * parseFloat(val)) / 100

            $('#ppn_rp').val(parseFloat(ppn_rp))
        }
        else
        {
            var ppn_rp_val = $('#ppn_rp')
            var ppn_rp = isNaN(parseFloat(ppn_rp_val.val())) ? 0 : parseFloat(ppn_rp_val.val())

            var ppn = (parseFloat(ppn_rp) / parseFloat(nilaival)) * 100

            $('#ppn').val(parseFloat(ppn))
        }

        var total_jual = parseFloat(nilaival) + parseFloat(ppn_rp)

        $('#total_jual').val(parseFloat(total_jual))

        FormatMoney()
    })

    $('#fi-fas-wo').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            ResetMoney()

            let $form = $(this)
            let wo_status = $form.find('[id="wo_status"] option:selected').val()

            if (wo_status == 2)
            {
                let bank_id = $form.find('[id="bank_id"] option:selected').val()

                if (bank_id == '')
                {
                    FormatMoney()

                    swalShowMessage('Peringatan', 'Kas/Bank belum dipilih.', 'warning')

                    return false
                }

                let nilai_jual = $form.find('[id="nilai_jual"]').val()
                    nilai_jual = isNaN(parseFloat(nilai_jual)) ? 0 : parseFloat(nilai_jual)

                if (parseFloat(nilai_jual) == 0)
                {
                    FormatMoney()

                    swalShowMessage('Peringatan', 'Nilai Jual belum diisi.', 'warning')

                    return false
                }
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.fixed_asset.save_write_off') }}"

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
                                FormatMoney()

                                $("#mdl-form-fa2").modal('hide')

                                table.ajax.reload(null, false)
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
</script>