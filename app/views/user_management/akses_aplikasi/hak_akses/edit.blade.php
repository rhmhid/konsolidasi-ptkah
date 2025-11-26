<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <i class="las la-user fs-2 me-2"></i>
        Edit Akses User Login
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
                <div class="col-lg-4">
                    <label class="text-dark fs-7 fw-bold mb-2">Nama Pegawai</label>
                    <div class="text-dark fw-bold fs-7">{{ $data_akses->nama_lengkap }}</div>
                </div>

                <div class="col-lg-5">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Username</label>
                    <input type="text" name="login_user" id="login_user" class="form-control form-control-sm rounded-1 w-100" value="{{ $data_akses->username }}" required="" readonly="" maxlength="16" />
                </div>

                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Status User</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                      <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-6">
                    <label class="text-dark fs-7 fw-bold pb-2 required">Akses Administrator</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" name="is_admin" class="btn-check is-admin" id="user-admin" value="t" {{ $chk_admin_t}} />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="user-admin">User Administrator</label>

                        <input type="radio" name="is_admin" class="btn-check is-admin" id="user-non-admin" value="f" {{ $chk_admin_f}} />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="user-non-admin">Bukan Administrator</label>
                    </div>
                </div>

                <div class="col-lg-6 only-non-admin d-none">
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
                                    <input class="form-check-input" type="checkbox" name="rgid[]" value="{{ $r->rgid }}" id="rgid-{{ $r->rgid }}" {{ $r->check }} />
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

    HideNonAdminLoad()

    $('.modal .form-select').select2({
        width: '100%',
        dropdownParent: $('#popup_form_hak_akses')
    })

    $('.is-admin').on('click', HideNonAdmin)

    function HideNonAdmin ()
    {
        var is_admin = $('[name="is_admin"]:checked').val()

        if (is_admin == 'f')
            $('.only-non-admin').fadeIn(400)
        else
            $('.only-non-admin').fadeOut(400)
    }

    function HideNonAdminLoad ()
    {
        var is_admin = $('[name="is_admin"]:checked').val()

        if (is_admin == 'f')
            $('.only-non-admin').fadeIn(100)
        else
            $('.only-non-admin').fadeOut(100)
    }

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

    $('#login_user').click(function ()
    {
        var pid = $('[name="user_pid"]').val()

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
            user_pid: $form.find('[name="user_pid"]').val(),
            login_user: $form.find('[name="login_user"]').val(),
            is_aktif: $form.find('[name="is_aktif"]:checked').val(),
            is_admin: $form.find('[name="is_admin"]:checked').val()
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
                url         : "{{ route('api.user_management.akses_aplikasi.hak_akses.update') }}",
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