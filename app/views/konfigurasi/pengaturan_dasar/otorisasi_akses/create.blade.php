<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="la la-users text-dark me-4"></span>
        Form Input Masterdata Otorisasi Akses
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
        <form method="post" id="form-add-otorisasi-akses" novalidate>
            <!--begin::Compact form-->
            <div class="row">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Group Otorisasi</label>
                    {!! $cmb_group !!}
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama User</label>
                    <select name="i_pid" id="i_pid" required="" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true"></select>
                </div>
            </div>

            <div class="mt-5 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_simpan">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    $('.modal .form-select').select2({
        width: '100%',
        dropdownParent: $('#popup_create_otorisasi')
    })

    $('#i_pid').select2({
        dropdownParent      : $('#popup_create_otorisasi'),
        placeholder         : 'Pilih Username - Nama Asli',
        minimumInputLength  : 3,
        ajax                :
                            {
                                url             : "{{ route('api.pengaturan_dasar.otorisasi_akses.cari_user') }}",

                                data            : function (params)
                                                {
                                                    var param = {
                                                        q       : params.term,
                                                        otogid  : $("#otogid_val option:selected").val()
                                                    }

                                                    // Query parameters will be ?search=[term]&type=public
                                                    return param
                                                },

                                processResults  : function (json)
                                                {
                                                    return {
                                                        results: json
                                                    }
                                                }
                            }
    })

    $("#otogid_val").change(function()
    {
        $('#i_pid').val(null).trigger("change")
    })

    // aksi submit edit / update
    $('#form-add-otorisasi-akses').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)

            payload.append('_method', 'patch') // ganti ajax method post menjadi patch
            formSubmitUrl = "{{ route('api.pengaturan_dasar.otorisasi_akses.save') }}"

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
                                $("#popup_create_otorisasi").modal('hide')

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