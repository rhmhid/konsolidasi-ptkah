<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>Form Input Masterdata Group Akses</h2>

    <div class="btn btn-sm btn-icon btn-active-color-dark" data-bs-dismiss="modal">
        <i class="fas fa-times"></i>
    </div>
    <!--end::Modal title-->
</div>
<!--end::Modal header-->

<!--begin::Modal body-->
<div class="modal-body py-6 px-lg-7">
    <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">                    
        <form method="post" id="form-edit-group-akses" novalidate>
            <input type="hidden" name="rgid" id="rgid" value="{{ $data_group->rgid }}" />

            <!--begin::Compact form-->
            <div class="row mb-5">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Group Akses</label>
                    <input type="text" name="role_kode" id="role_kode" value="{{ $data_group->role_kode }}" class="form-control form-control-sm rounded-1 w-100" required="" readonly="" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Group Akses</label>
                    <input type="text" name="role_name" id="role_name" value="{{ $data_group->role_name }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>

                <div class="col-lg-4">
                    <label class="form-label form-label-sm text-dark">Status Group</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                        <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <label class="fs-3 fw-bold text-dark d-block required">Modul</label>

                    <!--begin::Accordion-->
                    <div class="accordion" id="kt_accordion_menu">
                        {!! $list_module !!}
                    </div>
                    <!--end::Accordion-->
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
    $("#role_kode").click(function ()
    {
        var rgid = $('#rgid').val()

        Swal.fire({
            title: "Masukkan Kode",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Kode",
            showLoaderOnConfirm: true,

            preConfirm: async (kode) => {
                try {
                    if (!kode)
                    {
                        const message = 'Kode Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.user_management.akses_aplikasi.group_akses.cek_kode', ['kode' => ':kode']) }}"
                        link = link.replace(':kode', kode)

                    const response = await $.ajax({
                        url         : link,
                        data        : { jenis: 'gakses', id: rgid },
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

    $('.moduls').on('click', '.icon', function (e)
    {
        const $el = $(this)
        const $icon = $el.find('i')
        const $mid = $el.attr('mid')
        const $dropdown = $icon.closest('.list-modul')
        const $menuTreeBody = $dropdown.find('.submodul.mid-' + $mid)

        if (!$menuTreeBody.hasClass('collapse'))
        {
            $icon.removeClass('fa-rotate-180')
            $menuTreeBody.addClass('collapse')
        }
        else
        {
            $icon.addClass('fa-rotate-180')
            $menuTreeBody.removeClass('collapse')
        }
    })

    $('.m_module').change(function ()
    {
        const $el = $(this)

        $("[data-group_mid=" + $(this).val() + "]:checkbox").prop('checked', $(this).prop("checked"))
    })

    $('.m_submodule').change(function ()
    {
        const $el = $(this)

        var is_header = $el.attr("data-is_header")

        if (is_header == 't') $("[data-parent_mid=" + $el.val() + "]:checkbox").prop('checked', $el.prop("checked"))

        var parent_mid = $el.attr("data-parent_mid")

        if (typeof parent_mid !== "undefined")
        {
            var mdid_max = $("[data-parent_mid=" + parent_mid + "]:checked").length
            var chk_parent_mid

            if (mdid_max > 0) chk_parent_mid = true
            else chk_parent_mid = false

            $("[data-is_header][value=" + parent_mid + "]:checkbox").prop('checked', chk_parent_mid)
        }

        var mid = $el.attr("data-group_mid")
        var mid_max = $("[data-group_mid=" + mid + "]:checked").length
        var chk_mid

        if (mid_max > 0) chk_mid = true
        else chk_mid = false

        $("[id='mid[" + mid + "]']").prop('checked', chk_mid)
    })

    $('#is_aktif').change(function ()
    {
        const $el = $(this)
        const $label = $(`label[for="${$el.attr('id')}"]`)

        if ($el.is(':checked')) $label.text("Aktif")
        else $label.text("Tidak Aktif")
    })

    // aksi submit edit / update
    $('#form-edit-group-akses').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const $mid = $('.m_module:checked').length

            if ($mid == 0)
                return swalShowMessage('Warning!', "Wajib memilih minimal satu modul.", 'warning')

            const payload = new FormData(this)

            payload.append('_method', 'patch') // ganti ajax method post menjadi patch
            formSubmitUrl = "{{ route('api.user_management.akses_aplikasi.group_akses.save') }}"

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
                                $("#popup_edit_group_akses").modal('hide')

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