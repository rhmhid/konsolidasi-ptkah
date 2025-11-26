<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <i class="las la-key fs-2 me-2"></i>
        Update Password User Login
    </h2>

    <div class="btn btn-sm btn-icon btn-active-color-dark" data-bs-dismiss="modal">
        <i class="fas fa-times"></i>
    </div>
    <!--end::Modal title-->
</div>
<!--end::Modal header-->

<!--begin::Modal body-->
<div class="modal-body py-10 px-lg-17">
    <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
        <form method="post" id="form-input-user" novalidate>
            <input type="hidden" name="_method" value="patch" />
            <input type="hidden" name="user_pid" value="{{ $data_akses->pid }}" />

            <!--begin::Compact form-->
            <div class="row mb-3">
                <div class="col-lg-6">
                    <label class="text-dark fs-7 fw-bold mb-2">Nama Pegawai</label>
                    <div class="text-dark fw-bold fs-7">{{ $data_akses->nama_lengkap }}</div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Password</label>
                    <div class="text-dark fw-bold fs-6 password-field-clue cursor-pointer">
                        <font class="pass-clue">{{ $clue }}</font>&nbsp;
                        <i class="bi bi-eye-slash fs-2 text-dark"></i>
                        <i class="bi bi-eye fs-2 text-dark d-none"></i>
                    </div>
                </div>
            </div>

            <!--begin::Input group-->
            <div class="d-flex flex-stack">
                <!--begin::Label-->
                <div class="me-5">
                    <label class="fs-6 fw-bold form-label text-dark">Reset Password ?</label>
                </div>
                <!--end::Label-->

                <!--begin::Switch-->
                <label class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" name="reset_method" id="reset-password-method" value="t" checked="" />
                    <span class="form-check-label fw-bold text-muted">Random Password</span>
                </label>
                <!--end::Switch-->
            </div>
            <!--end::Input group-->

            <div class="row mb-3 d-none input-password-manual">
                <div class="col-lg-12 password-field-container">
                    <label class="text-dark fs-7 fw-bold pb-2 required">Password</label>
                    <div class="position-relative">
                        <input type="password" name="login_pass" class="form-control form-control-sm rounded-1" autocomplete="off" readonly="" onfocus="this.removeAttribute('readonly');" />
                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2">
                            <i class="bi bi-eye-slash fs-2"></i>
                            <i class="bi bi-eye fs-2 d-none"></i>
                        </span>
                    </div>

                    <div class="fs-7 fw-bold text-danger mt-2 hak-akses password-invalid-message d-none"></div>

                    <div class="fs-7 mt-2">
                        <span class="text-muted">Minimal harus 6 karakter dengan kombinasi Huruf (besar/kecil) dan Angka.</span>
                    </div>
                </div>
            </div>

            <div class="row d-none input-password-manual">
                <div class="col-lg-12 password-field-container">
                    <label class="text-dark fs-7 fw-bold pb-2 required">Konfirmasi Password</label>
                    <div class="position-relative">
                        <input type="password" name="login_pass_confirm" class="form-control form-control-sm rounded-1" autocomplete="off" readonly="" onfocus="this.removeAttribute('readonly');" />
                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2">
                            <i class="bi bi-eye-slash fs-2"></i>
                            <i class="bi bi-eye fs-2 d-none"></i>
                        </span>
                    </div>

                    <div class="fs-7 fw-bold text-danger mt-2 hak-akses password-invalid-message d-none"></div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm rounded-1" name="me_btn_batal" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" name="me_btn_simpan" id="me_btn_simpan">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    $('.pass-clue').text($('.pass-clue').text().replace(/./g, '*'))

    $('#reset-password-method').change(function ()
    {
        const $el = $(this)
        const $label = $(`.input-password-manual`)

        if ($el.is(':checked')) $label.addClass("d-none")
        else $label.removeClass("d-none")

        $label.find('input').val('')
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
        else
        {
            $password.attr('type', 'password')
            $container.find('.bi-eye').addClass('d-none')
        }
    })

    $('.password-field-clue i').click(function ()
    {
        const $icon = $(this)
        const $container = $icon.closest('.password-field-clue')
        const $password = $container.find('font')

        $container.find('i.d-none').removeClass('d-none')

        if ($icon.hasClass('bi-eye-slash'))
        {
            $password.text('{{ $clue }}')
            $icon.addClass('d-none')
        }
        else
        {
            $password.text($password.text().replace(/./g, '*'))
            $container.find('.bi-eye').addClass('d-none')
        }
    })

    // aksi submit edit / update
    $('#form-input-user').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        if (!validasiForm(this)) return

        const $form = $(this)
        const url = $form.attr('action')

        const payload = {
            _method: $form.find('[name="_method"]').val(),
            user_pid: $form.find('[name="user_pid"]').val(),
            reset_method: $form.find('[name="reset_method"]:checked').val(),
            login_pass: $form.find('[name="login_pass"]').val(),
            login_pass_confirm: $form.find('[name="login_pass_confirm"]').val()
        }

        // Check format password.
        if (payload.reset_method != 't')
        {
            // Check format password.
            if (payload.login_pass == '')
                return swalShowMessage('Perhatian!', "Password Belum Diisi.", 'warning')

            if (payload.login_pass_confirm == '')
                return swalShowMessage('Perhatian!', "Konfirmasi Password Belum Diisi.", 'warning')

            // Check format password.
            if (payload.login_pass.length)
            {
                const {
                    ok,
                    message
                } = validatePassword(payload.login_pass)

                if (!ok && message)
                {
                    $('.hak-akses.password-invalid-message').removeClass('d-none').text(message)

                    swalShowMessage("Format Password Salah", message, 'warning')
                    return
                }
                else $('.hak-akses.password-invalid-message').addClass('d-none').text('')
            }

            if (payload.login_pass && (payload.login_pass !== payload.login_pass_confirm))
                return swalShowMessage('Perhatian!', "Konfirmasi password salah.", 'warning')
        }

        showLoading()

        setTimeout((function ()
        {
            $.ajax({
                url         : "{{ route('api.user_management.akses_aplikasi.hak_akses.update_pass') }}",
                data        : payload,
                type        : 'POST',

                error       : function (err)
                            {
                                swal.close()

                                swalShowMessage('Error', err?.responseJSON?.message || 'An Error Occured.', 'error')
                            },

                success     : function (data)
                            {
                                swal.close()

                                if (data.success)
                                {
                                    swalShowMessage('Sukses', data.message, 'success')
                                    .then((result) =>
                                    {
                                        if (result.isConfirmed)
                                        {
                                            $("#popup_form_password").modal('hide')

                                            table.ajax.reload(null, false)
                                        }
                                    })
                                }
                                else swalShowMessage('Error', data.message || 'An Error Occured.', 'error')
                            },

                async       : false,
                cache       : false
            })
        }), 2e3)
    })
</script>