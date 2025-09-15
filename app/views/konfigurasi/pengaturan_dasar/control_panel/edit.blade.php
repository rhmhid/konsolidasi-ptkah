<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="la la-gear text-dark me-4"></span>
        Ubah Control Panel
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
        <form method="post" id="form-edit-control-panel" novalidate>
            <input type="hidden" name="e_cid" id="e_cid" value="{{ $data_configs->cid }}" />

            <!--begin::Compact form-->
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <label class="text-dark fw-bold fs-7 pb-2 required">Deskripsi</label>
                        <input type="text" name="e_keterangan" id="e_keterangan" value="{{ $data_configs->keterangan }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                    </div>
                </div>

                <div class="mt-3 row">
                    <div class="col-lg-12">
                        <label class="text-dark fw-bold fs-7 pb-2">Data</label>
                        <input type="text" name="e_data" id="e_data" value="{{ $data_configs->data }}" class="form-control form-control-sm rounded-1 w-100" />
                    </div>
                </div>

                <div class="mt-3 row" id="e_data_type_box">
                    <div class="col-lg-12">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-grow-1">
                                <!--begin::Content-->
                                <div class="fw-bold">
                                    <h4 class="text-gray-800 fw-bolder fs-7">Tipe Data :</h4>
                                    <div class="text-gray-600 fs-6" id="e_data_type">{{ $data_configs->data_type }}</div>
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                    </div>
                </div>

                <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm w-90px fs-7" name="me_btn_batal" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark btn-sm rounded-1" name="me_btn_simpan" id="me_btn_simpan">Simpan</button>
                </div>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    // aksi submit edit / update
    $('#form-edit-control-panel').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)

            payload.append('_method', 'patch') // ganti ajax method post menjadi patch
            formSubmitUrl = "{{ route('api.pengaturan_dasar.control_panel.save') }}"

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
                                $("#popup_edit_control_panel").modal('hide')

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