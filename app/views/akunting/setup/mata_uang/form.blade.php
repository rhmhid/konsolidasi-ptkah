<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-dollar-sign text-dark me-4"></span>
        Form Input Masterdata Currency
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
        <form method="post" id="form-input-currency" novalidate>
            <input type="hidden" name="cid" id="cid" value="{{ $data_curr->cid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label class="text-dark fw-bold fs-7 pb-2 required">Kode Currency</label>
                        <input type="text" name="curr_code" id="curr_code" value="{{ $data_curr->curr_code }}" class="form-control form-control-sm rounded-1 w-100" readonly="" required="" />
                    </div>

                    <div class="mb-3">
                        <label class="text-dark fw-bold fs-7 pb-2 required">Nama Currency</label>
                        <input type="text" name="curr_name" id="curr_name" value="{{ $data_curr->curr_name }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label form-label-sm text-dark">Status Currency</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                            <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="d-flex flex-column h-100">
                        <label for="curr_desc" class="text-dark fw-bold fs-7 pb-2">Description</label>
                        <textarea id="curr_desc" name="curr_desc" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0">{{ $data_curr->curr_desc }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_simpan">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    $('#curr_code').click(function ()
    {
        var cid = $('#cid').val()

        Swal.fire({
            title: "Masukkan Kode",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Kode",
            showLoaderOnConfirm: true,

            preConfirm: async (curr_code) => {
                try {
                    if (!curr_code)
                    {
                        const message = 'Kode Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.akunting.setup.mata_uang.cek_kode', ['kode' => ':curr_code']) }}"
                        link = link.replace(':curr_code', curr_code)

                    const response = await $.ajax({
                        url         : link,
                        data        : { jenis: 'curr', id: cid },
                        type        : 'POST',
                        dataType    : 'JSON'
                    })

                    if (response.success === false)
                        return Swal.showValidationMessage(`${(await response.message)}`)

                    return response
                } catch (error) {
                    Swal.showValidationMessage(`Request failed: ${error}`)
                }
            },

            allowOutsideClick: () => !Swal.isLoading(),
            backdrop: true
        }).then((result) => {
            if (result.isConfirmed)
                $(this).val(result.value.kode)
        })
    })

    $('#is_aktif').change(function ()
    {
        const $el = $(this)
        const $label = $(`label[for="${$el.attr('id')}"]`)

        if ($el.is(':checked')) $label.text("Aktif")
        else $label.text("Tidak Aktif")
    })

    // aksi submit edit / update
    $('#form-input-currency').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.setup.mata_uang.save') }}"
            
            showLoading()

            setTimeout((function ()
            {
                doAjax(
                    formSubmitUrl,
                    payload,
                    "POST"
                )
                .done( data => {
                    swal.close()

                    if (data.success)
                    {
                        swalShowMessage('Sukses', data.message, 'success')
                        .then((result) =>
                        {
                            if (result.isConfirmed)
                            {
                                $("#popup_form_currency").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    const res = err?.responseJSON

                    swal.close()

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>