<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-sitemap text-dark me-4"></span>
        Form Input Masterdata Kategori Barang
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
            <input type="hidden" name="kbid" id="kbid" value="{{ $data_kel_brg->kbid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Kategori</label>
                    <input type="text" name="kode_kategori" id="kode_kategori" value="{{ $data_kel_brg->kode_kategori }}" class="form-control form-control-sm rounded-1 w-100" required="" readonly="" />
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" value="{{ $data_kel_brg->nama_kategori }}" class="form-control form-control-sm rounded-1 w-100" required="" />

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Default Kode Barang</label>
                    <input type="text" name="format_kode_brg" id="format_kode_brg" value="{{ $data_kel_brg->format_kode_brg }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Panjang Kode Barang</label>
                    <select class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." name="length_format_kode_brg" id="length_format_kode_brg" required="">
                        <option value="" disabled="" selected="">Pilih Panjang Kode Barang</option>
                        {!! $opt_length_kode_brg !!}
                    </select>
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">C.O.A Inventory</label>
                    {!! $cmb_coa_inv !!}
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">C.O.A COGS A/P Konsinyasi</label>
                    {!! $cmb_coa_cogs_ap_kons !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">C.O.A Pendapatan</label>
                    {!! $cmb_coa_sales !!}
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">C.O.A COGS</label>
                    {!! $cmb_coa_cogs !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">C.O.A Adjusment</label>
                    {!! $cmb_coa_adj !!}
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">C.O.A Stock Opaname</label>
                    {!! $cmb_coa_so !!}
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">C.O.A Cost Item Usage</label>
                    {!! $cmb_coa_ciu !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Jenis Barang</label>
                    <div class="nav-group nav-group-sm nav-group-fluid rounded-1 border border-gray-300 bg-white p-1">
                        <input type="radio" name="is_medis" class="btn-check" id="medis" value="t" required="" {{ $chk_medis }} />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="medis">Medis</label>

                        <input type="radio" name="is_medis" class="btn-check" id="non-medis" value="f" required="" {{ $chk_non_medis }} >
                        <label class="btn btn-sm btn-light-dark rounded-1" for="non-medis">Non Medis</label>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="form-label form-label-sm text-dark">Enabled Stock Take</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_freeze" type="checkbox" id="is_freeze" value="t" {{ $chk_freeze }} />
                        <label class="form-check-label fw-bold" for="is_freeze">Digunakan untuk stock take</label>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="form-label form-label-sm text-dark">Barang Sales</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_sales" type="checkbox" id="is_sales" value="t" {{ $chk_sales }} />
                        <label class="form-check-label fw-bold" for="is_sales">Digunakan untuk penjualan</label>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="form-label form-label-sm text-dark">Barang Fixed Asset</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_fixed_asset" type="checkbox" id="is_fixed_asset" value="t" {{ $chk_fixed_asset }} />
                        <label class="form-check-label fw-bold" for="is_fixed_asset">Digunakan untuk barang asset</label>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="form-label form-label-sm text-dark">Barang Konsinyasi</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_konsinyasi" type="checkbox" id="is_konsinyasi" value="t" {{ $chk_konsinyasi }} />
                        <label class="form-check-label fw-bold" for="is_konsinyasi">Digunakan untuk barang Konsinyasi</label>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="form-label form-label-sm text-dark">Status Aktif</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                        <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="form-label form-label-sm text-dark">Barang Jasa</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_service" type="checkbox" id="is_service" value="t" {{ $chk_service }} />
                        <label class="form-check-label fw-bold" for="is_service">Digunakan untuk jenis barang berupa jasa / pelayanan</label>
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
        var kbid = $('#kbid').val()

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

                    let link = "{{ route('api.inventori.master_data.kategori_barang.cek_kode', ['kode' => ':kode_kategori']) }}"
                        link = link.replace(':kode_kategori', kode_kategori)

                    const response = await $.ajax({
                        url         : link,
                        data        : { jenis: 'kel_brg', id: kbid },
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

        var is_medis = $(this).find('[name="is_medis"]:checked')

        if (is_medis.length == 0)
        {
            swalShowMessage("Information", 'Jenis Barang Belum Dipilih', 'warning')
            return
        }

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (checkRequired(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.inventori.master_data.kategori_barang.save') }}"
            
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
                                $("#popup_form_kategori_barang").modal('hide')

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