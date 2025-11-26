<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <i class="las la-user fs-2 me-2"></i>
        Tambah Akses User Login
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

            <!--begin::Compact form-->
            <div class="row mb-3">
                <div class="col-lg-4">
                    <label class="text-dark fs-7 fw-bold mb-2 required">Nama</label>
                    <select name="i_pid" id="i_pid" required="" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true"></select>
                </div>

                <div class="col-lg-5">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Username</label>
                    <input type="text" name="login_user" id="login_user" class="form-control form-control-sm rounded-1 w-100" required="" readonly="" maxlength="16" />
                </div>

                <div class="col-lg-3">
                    <label class="fs-6 fw-bold form-label text-dark">&nbsp;</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="reset_method" id="reset-password-method" value="t" checked="" />
                        <span class="form-check-label fw-bold text-muted">Random Password</span>
                    </div>
                </div>
            </div>

            <div class="row mb-3 d-none" id="input-password-manual">
                <div class="col-lg-6 password-field-container">
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

                <div class="col-lg-6 password-field-container">
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

            <div class="row mb-3">
                <div class="col-lg-6">
                    <label class="text-dark fs-7 fw-bold pb-2 required">Akses Administrator</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" name="is_admin" class="btn-check is-admin" id="user-admin" value="t" />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="user-admin">User Administrator</label>

                        <input type="radio" name="is_admin" class="btn-check is-admin" id="user-non-admin" value="f" />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="user-non-admin">Bukan Administrator</label>
                    </div>
                </div>

                <div class="col-lg-2">
                    <label class="form-label form-label-sm text-dark">Status User</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                      <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
                    </div>
                </div>

                <div class="col-lg-4 only-non-admin d-none">
                    <label class="text-dark fs-7 fw-bold pb-2">Approval Akses</label>
                    <select name="user_approval[]" id="user-approval" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" multiple="" data-placeholder="Pilih Approval Akses">
                        <option id="select-all-approval" value="-1">-- Pilih Semua --</option>
                        {!! $opt_approval_akses !!}
                    </select>
                </div>
            </div>

            <div class="row mb-3 only-non-admin">
                <div class="col-lg-6">
                    <label class="text-dark fs-7 fw-bold pb-2">Gudang Akses</label>
                    <select name="user_gudang[]" id="user-gudang" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" multiple="" data-placeholder="Pilih Gudang Akses">
                        <option id="select-all-gudang" value="-1">-- Pilih Semua --</option>
                        {!! $opt_gudang_akses !!}
                    </select>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fs-7 fw-bold pb-2">Otorisasi Akses</label>
                    <select name="user_otorisasi[]" id="user-otorisasi" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" multiple="" data-placeholder="Pilih Otorisasi Akses">
                        <option id="select-all-otorisasi" value="-1">-- Pilih Semua --</option>
                        {!! $opt_otorisasi_akses !!}
                    </select>
                </div>
            </div>

            <div class="row only-non-admin">
                <div class="col-12">
                    <label class="form-label form-label-sm text-dark">
                        <span>Akses Menu berdasarkan Group Akses</span>
                    </label>

                    <div class="row gy-3">
                        @foreach ($data_group_akses as $k => $r)
                            <div class="col-6 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rgid[]" value="{{ $r->rgid }}" id="rgid-{{ $r->rgid }}" />
                                    <label class="form-check-label fw-bold" for="rgid-{{ $r->rgid }}">
                                        {{ $r->role_name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
    var selectAllGudang = false
    var selectAllOtorisasi = false
    var selectAllApproval = false

    $('#reset-password-method').change(function ()
    {
        const $el = $(this)
        const $label = $(`#input-password-manual`)

        if ($el.is(':checked')) $label.addClass("d-none")
        else $label.removeClass("d-none")

        $label.find('input').val('')
    })

    $('.only-non-admin').hide()

    $('.modal .form-select').select2({
        width: '100%',
        dropdownParent: $('#popup_form_hak_akses')
    })

    $('.is-admin').on('click', function ()
    {
        var is_admin = $('[name="is_admin"]:checked').val()

        if (is_admin == 'f')
            $('.only-non-admin').fadeIn(500)
        else
            $('.only-non-admin').fadeOut(500)
    })

    $('#i_pid').select2({
        dropdownParent      : $('#popup_form_hak_akses'),
        placeholder         : 'Pilih...',
        minimumInputLength  : 3,
        ajax                :
                            {
                                url             : "{{ route('api.user_management.akses_aplikasi.hak_akses.cari_pegawai') }}",

                                data            : function (params)
                                                {
                                                    var param = {
                                                        q   : params.term,
                                                    }

                                                    // Query parameters will be ?search=[term]&type=public
                                                    return param
                                                },

                                processResults  : function (json)
                                                {
                                                    return {
                                                        results: $.map(json, function(items)
                                                        {
                                                            return {
                                                                id              : items.pid,
                                                                text            : items.nrp + ' - ' + items.nama_lengkap,
                                                                nrp             : items.nrp,
                                                                nama_lengkap    : items.nama_lengkap,
                                                                ptype           : items.ptype,
                                                            }
                                                        })
                                                    }
                                                }
                            }
    }).on('select2:select', function (e)
    {
        var items = e.params.data;

        if (items.id != '' && items.ptype == 3)
        {
            $('#login_user').val(items.nrp)
        }
    })

    $("select#user-gudang").on('select2:select', function (e)
    {
        const data = e.params.data
        const $select = $(this)

        if (data.id == -1)
        {
            if (selectAllGudang === true)
                selectAllGudang = false
            else
            {
                selectAllGudang = true

                $select.find('option').prop('selected', 'selected').trigger('change')
            }

            $select.find('option#select-all-gudang').prop('selected', false).trigger('change')
        }
    })

    $("select#user-otorisasi").on('select2:select', function (e)
    {
        const data = e.params.data
        const $select = $(this)

        if (data.id == -1)
        {
            if (selectAllOtorisasi === true)
                selectAllOtorisasi = false
            else
            {
                selectAllOtorisasi = true

                $select.find('option').prop('selected', 'selected').trigger('change')
            }

            $select.find('option#select-all-otorisasi').prop('selected', false).trigger('change')
        }
    })

    $("select#user-approval").on('select2:select', function (e)
    {
        const data = e.params.data
        const $select = $(this)

        if (data.id == -1)
        {
            if (selectAllApproval === true)
                selectAllApproval = false
            else
            {
                selectAllApproval = true

                $select.find('option').prop('selected', 'selected').trigger('change')
            }

            $select.find('option#select-all-approval').prop('selected', false).trigger('change')
        }
    })

    $('#is_aktif').change(function ()
    {
        const $el = $(this)
        const $label = $(`label[for="${$el.attr('id')}"]`)

        if ($el.is(':checked')) $label.text("Aktif")
        else $label.text("Tidak Aktif")
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

    $('#login_user').click(function ()
    {
        var pid = $('#i_pid').val()

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

    // aksi submit edit / update
    $('#form-input-user').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        if (!validasiForm(this)) return

        const $form = $(this)
        const url = $form.attr('action')
        const $rgid = $('[name="rgid[]"]:checked')

        const payload = {
            _method: $form.find('[name="_method"]').val(),
            user_pid: $form.find('[name="i_pid"]').val(),
            login_user: $form.find('[name="login_user"]').val(),
            reset_method: $form.find('[name="reset_method"]:checked').val(),
            is_aktif: $form.find('[name="is_aktif"]:checked').val(),
            login_pass: $form.find('[name="login_pass"]').val(),
            login_pass_confirm: $form.find('[name="login_pass_confirm"]').val(),
            is_admin: $form.find('[name="is_admin"]:checked').val()
        }

        // Check format password.
        if (payload.reset_method != 't')
        {
            // Check format password.
            if (payload.login_pass == '')
                return swalShowMessage('Perhatian!', "Password Belum Diisi.", 'warning')

            if (payload.login_pass_confirm == '')
                return swalShowMessage('Perhatian!', "Konfirmasi Password Belum Diisi.", 'warning')

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

        if ($('[name="is_admin"]').length)
            payload.is_admin = $('[name="is_admin"]:checked').val()

        if (!payload.is_admin)
            return swalShowMessage('Perhatian!', "Status Akses Administrator Belum Ditentukan.", 'warning')

        if (payload.is_admin == 'f')
        {
            payload.user_gudang = $form.find('[id="user-gudang"]').val()
            payload.user_otorisasi = $form.find('[id="user-otorisasi"]').val()
            payload.user_approval = $form.find('[id="user-approval"]').val()

            if (!$rgid.length)
                return swalShowMessage('Warning!', "Wajib memilih minimal satu group akses jika bukan user Administrator.", 'warning')
            else
            {
                payload.rgid = []

                $rgid.each(function ()
                {
                    payload.rgid.push($(this).val())
                })
            }
        }

        showLoading()

        setTimeout((function ()
        {
            $.ajax({
                url         : "{{ route('api.user_management.akses_aplikasi.hak_akses.save') }}",
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
                                            $("#popup_form_hak_akses").modal('hide')

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