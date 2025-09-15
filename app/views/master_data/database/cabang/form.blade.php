<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-code-branch text-dark me-4"></span>
        Form Input Cabang
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
        <form method="post" id="form-input-branch" novalidate>
            <input type="hidden" name="bid" id="bid" value="{{ $data_rs->bid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Cabang</label>
                    <input type="text" name="branch_code" id="branch_code" value="{{ $data_rs->branch_code }}" class="form-control form-control-sm rounded-1 w-100" required="" readonly="" />
                </div>

                <div class="col-lg-5">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Cabang</label>
                    <input type="text" name="branch_name" id="branch_name" value="{{ $data_rs->branch_name }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Company Group</label>
                    <input type="text" name="branch_sub_corp" id="branch_sub_corp" value="{{ $data_rs->branch_sub_corp }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tipe Cabang</label>
                    {!! $cmb_tipe !!}
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Wilayah Cabang</label>
                    {!! $cmb_wilayah !!}
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                    <input type="text" name="branch_desc" id="branch_desc" value="{{ $data_rs->branch_desc }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="text-dark fw-bold fs-7 pb-2">Alamat</label>
                    <input type="text" name="branch_addr" id="branch_addr" value="{{ $data_rs->branch_addr }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Status Cabang</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_primary" type="checkbox" id="is_primary" value="t" {{ $chk_primary }} />
                        <label class="form-check-label fw-bold" for="is_primary">Utama</label>
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Status Aktif</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                        <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
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
    $('.modal .form-select').select2({
        width: '100%',
        dropdownParent: $('#popup_form_branch')
    })

    $('#branch_code').click(function ()
    {
        var bid = $('#bid').val()

        Swal.fire({
            title: "Masukkan Kode",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Kode",
            showLoaderOnConfirm: true,

            preConfirm: async (branch_code) => {
                try {
                    if (!branch_code)
                    {
                        const message = 'Kode Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.master_data.database.cabang.cek_kode', ['type' => 'branch', 'kode' => ':branch_code']) }}"
                        link = link.replace(':branch_code', branch_code)

                    const response = await $.ajax({
                        url         : link,
                        data        : { id: bid },
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
    $('#form-input-branch').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            var bid = $('#bid').val()

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.master_data.database.cabang.save', ['type' => 'mybid']) }}"
            formSubmitUrl = formSubmitUrl.replace(':mybid', bid)

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
                                $("#popup_form_branch").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    const res = err?.responseJSON
                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>