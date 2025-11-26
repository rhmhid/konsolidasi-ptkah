<!--begin::Form-->
<form class="form" id="user_auth" method="post">
    <input type="hidden" name="otogid" id="otogid" value="{{ $otogid }}" />
    <input type="hidden" name="notes" id="notes" value="{{ $notes }}" />

    <!--begin::Modal header-->
    <div class="modal-header" id="kt_modal_auth">
        <!--begin::Modal title-->
        <h2 id="headerModal">
            <span class="las la-user-cog text-dark me-4"></span>
            {{ $subtitle }}
        </h2>
        <!--end::Modal title-->

        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-dark" data-bs-dismiss="modal">
            <i class="fas fa-times"></i>
        </div>
        <!--end::Close-->
    </div>
    <!--end::Modal header-->

    <!--begin::Modal body-->
    <div class="modal-body">
        <!--begin::Scroll-->
        <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_auth" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
            <!--begin::Input group-->
            <div class="row gx-4 mb-5">
                <div class="col-lg-6">
                    <label class="required text-dark fs-7 fw-bold">Nama User</label>
                    {!! $cmb_user_auth !!}
            	</div>

            	<div class="col-lg-6 password-field-container" data-kt-password-meter="true">
            		<label class="required text-dark fs-7 fw-bold">Password</label>
                    <div class="position-relative">
                        <input type="password" name="pass_user" name="pass_user" class="form-control form-control-sm rounded-1" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" placeholder="Masukkan Password" required="" />
                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                            <i class="bi bi-eye-slash fs-2"></i>
                            <i class="bi bi-eye fs-2 d-none"></i>
                        </span>
                    </div>

                    <div class="fs-7 fw-bold text-danger mt-2 peserta-pass password-invalid-message d-none"></div>

                    <!--begin::Meter-->
                    <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">

                    </div>
                    <!--end::Meter-->
            	</div>
            </div>

            <div class="row gx-4">
                <div class="col-lg-12">
                    <label class="required text-dark fs-7 fw-bold">Keterangan</label>
                    <textarea class="form-control form-control-sm rounded-1" name="keterangan_auth" id="keterangan_auth" cols="10" rows="5" placeholder="" required=""></textarea>
                </div>
            </div>
        </div>
        <!--end::Scroll-->
    </div>
    <!--end::Modal body-->

    <!--begin::Modal footer-->
    <div class="modal-footer bg-light">
        <div class="w-100 py-4 d-flex justify-content-between">
            <button type="button" class="btn btn-danger btn-sm rounded-1" id="btn_batal_auth" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-dark btn-sm rounded-1" id="btn_save_auth">Simpan</button>
        </div>
    </div>
    <!--end::Modal footer-->
</form>
<!--end::Form-->

<script type="text/javascript">
    $('.select-auth-user').select2({
        width: '100%',
        dropdownParent: $('#popup_auth_user')
    })

    // Show / Hide Password
    $('.password-field-container i').click(function ()
    {
        const $icon = $(this)
        const $container = $icon.closest('.password-field-container')
        const $password = $container.find('input')

        $container.find('i.d-none').removeClass('d-none')

        if ($icon.hasClass('bi-eye-slash'))
        {
            $password.attr('type', 'text')
            $icon.addClass('d-none')
        }
        else {
            $password.attr('type', 'password')
            $container.find('.bi-eye').addClass('d-none')
        }
    })

    $('#user_auth').submit(function (e)
    {
        e.preventDefault()

        if (validasiForm(this))
        {
            const ket_auth = $('#popup_auth_user #keterangan_auth').val()

            const payloadAuth = new FormData(this)
                payloadAuth.append('_method', 'patch') // ganti ajax method post menjadi patch

            formAuthUrl = "{{ route('api.auth.otorisasi_akses.save') }}"

            showLoading()

            setTimeout((function ()
            {
                doAjax(
                    formAuthUrl,
                    payloadAuth,
                    "POST"
                )
                .done( data => {
                    Swal.close()

                    if (data.success)
                    {
                        $("#popup_auth_user").modal('hide')

                        NextStep($("#{{ $frm }}")[0], ket_auth)
                    }
                    else Swal.fire('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    Swal.close()

                    const res = err?.responseJSON

                    Swal.fire('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>