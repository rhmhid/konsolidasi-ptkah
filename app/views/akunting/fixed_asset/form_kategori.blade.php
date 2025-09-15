<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-city text-dark me-4"></span>
        Form Input Kategori Asset
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
        <form method="post" id="form-input-kategori-barang" novalidate>
            <input type="hidden" name="facid" id="facid" value="{{ $data_kate->facid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Kategori</label>
                    <input type="text" name="kode_kategori" id="kode_kategori" value="{{ $data_kate->kode_kategori }}" class="form-control form-control-sm rounded-1 w-100" required="" readonly="" />

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-5">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" value="{{ $data_kate->nama_kategori }}" class="form-control form-control-sm rounded-1 w-100" required="" />

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Periode</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" name="is_monthly" class="btn-check" id="is_monthly_f" value="f" required="" {{ $chk_monthly_f }} />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="is_monthly_f">Tahunan</label>

                        <input type="radio" name="is_monthly" class="btn-check" id="is_monthly_t" value="t" required="" {{ $chk_monthly_t }} />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="is_monthly_t">Bulanan</label>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">C.O.A Fixed Asset</label>
                    {!! $cmb_coa_fa !!}

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">C.O.A Depresiasi F/A</label>
                    {!! $cmb_dep_fa !!}

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">C.O.A Beban DFA</label>
                    {!! $cmb_cogs_dfa !!}

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">
                        <span>Kategori Barang</span>
                        <i class="fas fa-question-circle ms-2 text-hover-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Informasi untuk menghubungkan data asset dengan kategori barang (keperluan untuk auto insert data penerimaan)"></i>
                    </label>

                    {!! $cmb_kel_brg !!}

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Default Format Kode F/A</label>
                    <input type="text" name="format_kode_fa" id="format_kode_fa" value="{{ $data_kate->format_kode_fa }}" class="form-control form-control-sm rounded-1 w-100" maxlength="32" required="" />

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Panjang Kode F/A</label>
                    <select class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." name="length_format_kode_fa" id="length_format_kode_fa" required="">
                        <option value="" disabled="" selected="">Pilih Panjang Kode F/A</option>
                        {!! $opt_length_kode_fa !!}
                    </select>

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
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
    $('.modal [data-bs-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    })

    $('.modal .form-select').each(function ()
    {
        var obj, parent

        // you can set your default select2 options in obj
        obj = {
            // default options
            width: '100%',
        }

        // if there is a modal that select is inside it
        parent = $(this).closest('.modal')

        if (parent.length) obj['dropdownParent'] = parent

        $(this).select2(obj)
    })

    $('#kode_kategori').click(function ()
    {
        var facid = $('#facid').val()

        Swal.fire({
            title: "Masukkan Kode",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Kode",
            showLoaderOnConfirm: true,

            preConfirm: async (kode_kategori) => {
                try {
                    if (!kode_kategori)
                    {
                        const message = 'Kode Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.akunting.fixed_asset.cek_kode', ['mytype' => 'kategori', 'kode' => ':kode_kategori']) }}"
                        link = link.replace(':kode_kategori', kode_kategori)

                    const response = await $.ajax({
                        url         : link,
                        data        : { id: facid },
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
    $('#form-input-kategori-barang').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (checkRequired(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.fixed_asset.kategori.save') }}"

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
                                $("#popup_form_kategori_fa").modal('hide')

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