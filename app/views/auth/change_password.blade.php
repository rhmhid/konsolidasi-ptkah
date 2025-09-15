<form id="konfigurasi-akun-form-modal" method="post" novalidate>
    <div class="modal-header border-0">
        <h3 class="modal-title text-dark">
            <span class="las la-users text-dark me-4"></span>
            Konfigurasi Akun
        </h3>

        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body pt-0">
        <input type="hidden" name="_method" value="PATCH" />

        <div class="row g-5 mb-2">
            <div class="col-lg-12">
                <label class="d-block text-dark fs-7 fw-bold mb-2">Nama User</label>
                <div class="text-dark fw-bold fs-7">{{ Auth::user()->nama_lengkap }}</div>
            </div>
        </div>

        <div class="row g-5 mb-2">
            <div class="col-lg-6">
                <label class="d-block text-dark fs-7 fw-bold mb-2 required">Username</label>
                <input type="text" name="username" id="username" class="form-control form-control-sm rounded-1" value="{{ Auth::user()->username }}" required="" />
            </div>

            <div class="col-lg-6 konfigurasi-akun-password-container">
                <label class="d-block text-dark fs-7 fw-bold mb-2 required">Password Lama</label>
                <div class="position-relative">
                    <input type="password" name="userpass_lama" class="form-control form-control-sm rounded-1" autocomplete="off" readonly="" onfocus="this.removeAttribute('readonly');" required="" />
                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-lg-6 konfigurasi-akun-password-container">
                <label class="d-block text-dark fs-7 fw-bold mb-2 required">Password Baru</label>
                <div class="position-relative">
                    <input type="password" name="userpass_baru" class="form-control form-control-sm rounded-1" autocomplete="off" readonly="" onfocus="this.removeAttribute('readonly');" required="" />
                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                </div>

                <div class="fs-7 fw-bold text-danger mt-2 konfigurasi-akun password-invalid-message d-none"></div>
            </div>

            <div class="col-lg-6 konfigurasi-akun-password-container">
                <label class="d-block text-dark fs-7 fw-bold mb-2 required">Konfirmasi Password Baru</label>
                <div class="position-relative">
                    <input type="password" name="userpass_confirm" class="form-control form-control-sm rounded-1" autocomplete="off" readonly="" onfocus="this.removeAttribute('readonly');" required="" />
                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="fs-7 mt-2">
            <span class="text-muted">Minimal harus 6 karakter dengan kombinasi Huruf (besar/kecil) dan Angka.</span>
        </div>
    </div>

    <div class="modal-footer d-flex justify-content-between bg-light">
        <button type="button" class="btn btn-sm btn-danger rounded-1" data-bs-dismiss="modal" aria-label="Close">Tutup</button>
        <button type="submit" class="btn btn-sm btn-dark rounded-1" id="kt_simpan_pass">Simpan</button>
    </div>
</form>

<script type="text/javascript">
    // Show / Hide Password
    $('.konfigurasi-akun-password-container i').click(function ()
    {
        const $icon = $(this)
        const $container = $icon.closest('.konfigurasi-akun-password-container')
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

    $('#username').click(function ()
    {
        var pid = {{ Auth::user()->pid }}

        Swal.fire({
            title: "Masukkan Username",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Username",
            showLoaderOnConfirm: true,

            preConfirm: async (kode) => {
                try {
                    if (!kode)
                    {
                        const message = 'Username Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.user_management.akses_aplikasi.hak_akses.cek_user', ['kode' => ':kode']) }}"
                        link = link.replace(':kode', kode)

                    const response = await $.ajax({
                        url         : link,
                        data        : { jenis: 'akses_user', id: pid },
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

    $('form#konfigurasi-akun-form-modal').submit(function (e)
    {
        e.preventDefault()

        const $form = $(this)
        const url = $form.attr('action')
        const data = $form.serialize()

        if (!validasiForm(this)) return

        const {
            ok,
            message
        } = validatePassword($form.find('[name="userpass_baru"]').val())

        if (!ok && message)
        {
            $('.konfigurasi-akun.password-invalid-message').removeClass('d-none').text(message)

            swalShowMessage('Format Password Salah', message, 'warning')

            return
        }
        else $('.konfigurasi-akun.password-invalid-message').addClass('d-none').text('')

        if (!$form.find('[name=username]').val() && !$form.find('[name=userpass_baru]').val() && !$form.find('[name=userpass_confirm]').val())
        {
            swalShowMessage('Perhatian', 'Field belum di isi apapun!', 'warning')

            return
        }

        if ($form.find('[name=userpass_baru]').val() && !$form.find('[name=userpass_lama]').val())
        {
            swalShowMessage('Perhatian', 'Password lama harap diisi!', 'warning')

            return
        }

        if ($form.find('[name=userpass_baru]').val() != $form.find('[name=userpass_confirm]').val())
        {
            swalShowMessage('Perhatian', 'Konfirmasi password tidak sama!', 'warning')

            return
        }

        const payload = new FormData(this)

        payload.append('_method', 'patch') // ganti ajax method post menjadi patch
        formSubmitUrl = "{{ route('api.auth.change_password.save') }}"

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
                            $('#form-logout').submit()
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
    })
</script>