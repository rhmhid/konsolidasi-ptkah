<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-calendar text-dark me-4"></span>
        Form Input Periode Akunting
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
        <form method="post" id="form-add-periode-akunting" novalidate>
            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label class="text-dark fw-bold fs-7 pb-2 required">Begin Period (New)</label>
                        <div class="input-group">
                            <input type="text" name="pbegin" id="pbegin" class="form-control form-control-sm rounded-1" readonly="" required="" />
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-dark fw-bold fs-7 pb-2 required">End Period (New)</label>
                        <div class="input-group">
                            <input type="text" name="pend" id="pend" class="form-control form-control-sm rounded-1" readonly="" required="" />
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="d-flex flex-column h-100">
                        <label for="description" class="text-dark fw-bold fs-7 pb-2">Description</label>
                        <textarea id="description" name="description" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0"></textarea>
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
    $('#pbegin, #pend').flatpickr({
        defaultDate: null,
        dateFormat: "d-m-Y",
    })

    // aksi submit edit / update
    $('#form-add-periode-akunting').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)

            payload.append('_method', 'patch') // ganti ajax method post menjadi patch
            formSubmitUrl = "{{ route('api.akunting.setup.periode_akunting.save') }}"

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
                                $("#popup-form-periode-akunting").modal('hide')

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