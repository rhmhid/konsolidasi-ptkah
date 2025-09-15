<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-clipboard-list text-dark me-4"></span>
        Form Data Revaluate Asset
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
        <form method="post" id="fi-fas-rev" novalidate>
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
                    <label class="text-dark fw-bold fs-7 pb-2">Nilai Perolehan Sebelumnya</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="nilai_perolehan_old" id="nilai_perolehan_old" value="{{ $data_db->nilai_perolehan }}" readonly="" />
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Nilai Buku Asset Sebelumnya</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="nilai_buku" id="nilai_buku" value="{{ $data_db->nilai_buku }}" readonly="" />
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nilai Perolehan</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="nilai_perolehan_baru" value="{{ $data_db->nilai_perolehan }}" id="nilai_perolehan_baru" required="" />
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Nilai Minimum</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="nilai_minimum_baru" value="{{ $data_db->nilai_minimum }}" id="nilai_minimum_baru" />
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Efektif Baru</label>
                    <div class="input-group">
                        <input type="text" name="fadate" id="fadate" value="{{ $data_db->fadate }}" class="form-control form-control-sm rounded-1 mydate" readonly="" required="" />
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Masa Manfaat</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 number-only" name="umur_thn" id="umur_thn" value="{{ $data_db->umur_thn }}" required="" maxlength="3" />
                        <span class="input-group-text">Tahun</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 number-only" name="umur_bln" id="umur_bln" value="{{ $data_db->umur_bln }}" required="" maxlength="2" />
                        <span class="input-group-text">Bulan</span>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Cost Center</label>
                    {!! $cmb_cost_center !!}
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

    $(".mydate").flatpickr({
        defaultDate: null,
        dateFormat: "d-m-Y",
    })

    $('#fi-fas-rev').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            ResetMoney()

            let $form = $(this)
            let nilai_perolehan = $form.find('[id="nilai_perolehan_baru"]').val()
            let umur_thn = $form.find('[id="umur_thn"]').val()
            let umur_bln = $form.find('[id="umur_bln"]').val()

            nilai_perolehan = isNaN(parseFloat(nilai_perolehan)) ? 0 : parseFloat(nilai_perolehan)

            if (parseFloat(nilai_perolehan) == 0)
            {
                FormatMoney()

                swalShowMessage('Peringatan', 'Nilai Perolehan belum diisi.', 'warning')

                return false
            }

            let masa_manfaat = (parseInt(umur_thn) * 12 ) + parseInt(umur_bln)

            if (parseFloat(masa_manfaat) == 0)
            {
                FormatMoney()

                swalShowMessage('Peringatan', 'Masa Manfaat belum diisi.', 'warning')

                return false
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.fixed_asset.save_revaluate') }}"

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