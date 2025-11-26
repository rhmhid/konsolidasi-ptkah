<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-balance-scale text-dark me-4"></span>
        Form Input Mutasi Saldo
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
        <form method="post" id="form-input-ms" novalidate>
            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Mutasi</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="mutasi_date" id="mutasi_date" value="{{ $mutasi_date }}" class="form-control form-control-sm rounded-1 mydate-time" required="" />

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
                    <label class="text-dark fw-bold fs-7 pb-2">Nominal</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="amount" id="amount" required="" />
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kas / Bank Asal</label>
                    {!! $cmb_bank_from !!}
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2"> Saldo Kas / Bank Asal </label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="saldo_from" id="saldo_from" readonly="" />
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kas / Bank Tujuan</label>
                    {!! $cmb_bank_to !!}
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2"> Saldo Kas / Bank Tujuan</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="saldo_to" id="saldo_to" readonly="" />
                    </div>
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

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-danger btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_simpan">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<!--begin::template - Ubah-->
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

    $(".mydate-time").flatpickr({
        enableTime: true,
        dateFormat: "d-m-Y H:i",
        time_24hr: true,
        minuteIncrement: 1
    })

    $('.bank-select').change(function ()
    {
        var id      = $(this).val(),
            type    = $(this).attr('data-type'),
            tanggal = $('#mutasi_date').val()

        if (id != '')
        {
            let link = "{{ route('api.akunting.mutasi_saldo.cek_saldo', ['mybank' => ':mybank']) }}"
                link = link.replace(':mybank', id)

            $.ajax({
                url         : link,
                data        : { mydate: tanggal },
                dataType    : 'JSON',
                type        : 'POST',

                success     : function (data)
                            {
                                $('#saldo_' + type).val(data)

                                FormatMoney()
                            },

                error       : function (req, stat, err)
                            {
                            },

                async       : false,
                cache       : false
            })
        }

        return false
    })

    // aksi submit edit / update
    $('#form-input-ms').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            ResetMoney()

            let amount = $(this).find('[name="amount"]').val()

            if (parseFloat(amount) == 0)
            {
                FormatMoney()

                swalShowMessage("Information", 'Harap Input Nominal Mutasi !', 'warning')

                return
            }

            let bank_from = $(this).find('[name="bank_from"]').val()

            let bank_to = $(this).find('[name="bank_to"]').val()

            if (bank_from == bank_to)
            {
                FormatMoney()

                swalShowMessage("Information", 'Bank Asal & Tujuan Tidak Boleh Sama !', 'warning')

                return
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.mutasi_saldo.save') }}"

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
                                $("#mdl-form-mutasi").modal('hide')

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