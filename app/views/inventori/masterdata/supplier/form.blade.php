<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-box text-dark me-4"></span>
        Form Input Masterdata Supplier
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
        <form method="post" id="form-input-supplier" novalidate>
            <input type="hidden" name="suppid" id="suppid" value="{{ $data_supp->suppid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Supplier</label>
                    <input type="text" name="kode_supp" id="kode_supp" value="{{ $data_supp->kode_supp }}" class="form-control form-control-sm rounded-1 w-100" required="" readonly="" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Supplier</label>
                    <input type="text" name="nama_supp" id="nama_supp" value="{{ $data_supp->nama_supp }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tipe Supplier</label>
                    <div class="nav-group nav-group-sm nav-group-fluid rounded-1 border border-gray-300 bg-white p-1">
                        <input type="radio" name="type_supp" class="btn-check" id="non-medis" value="1" required="" {{ $chk_non_medis }} >
                        <label class="btn btn-sm btn-light-dark rounded-1" for="non-medis">Non Medis</label>

                        <input type="radio" name="type_supp" class="btn-check" id="medis" value="2" required="" {{ $chk_medis }} />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="medis">Medis</label>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Alamat</label>
                    <input type="text" name="addr_supp" id="addr_supp" value="{{ $data_supp->addr_supp }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>


                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">Kota</label>
                    <input type="text" name="kota_supp" id="kota_supp" value="{{ $data_supp->kota_supp }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">Kode Pos</label>
                    <input type="text" name="kode_pos" id="kode_pos" value="{{ $data_supp->kode_pos }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">Kontak Person</label>
                    <input type="text" name="kontak_supp" id="kontak_supp" value="{{ $data_supp->kontak_supp }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">Telp</label>
                    <input type="text" name="telp" id="telp" value="{{ $data_supp->telp }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">Fax</label>
                    <input type="text" name="fax" id="fax" value="{{ $data_supp->fax }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">E-Mail</label>
                    <input type="text" name="email_supp" id="email_supp" value="{{ $data_supp->email_supp }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">Bank</label>
                    <input type="text" name="bank" id="bank" value="{{ $data_supp->bank }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">Atas Nama</label>
                    <input type="text" name="atas_nama" id="atas_nama" value="{{ $data_supp->atas_nama }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">No Rekening</label>
                    <input type="text" name="no_rek" id="no_rek" value="{{ $data_supp->no_rek }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">NPWP</label>
                    <input type="text" name="npwp" id="npwp" value="{{ $data_supp->npwp }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-5">
                    <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                    <input type="text" name="keterangan" id="keterangan" value="{{ $data_supp->keterangan }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">C.O.A A/P</label>
                    {!! $cmb_coa_ap !!}
                </div>

                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Status Suplier</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                        <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
                    </div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" name="me_btn_simpan" id="me_btn_simpan">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    $('.modal .form-select').select2({
        width: '100%',
        dropdownParent: $('#popup_form_supplier')
    })

    $('#kode_supp').click(function ()
    {
        var suppid = $('#suppid').val()

        Swal.fire({
            title: "Masukkan Kode",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Kode",
            showLoaderOnConfirm: true,

            preConfirm: async (kode_supp) => {
                try {
                    if (!kode_supp)
                    {
                        const message = 'Kode Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.inventori.master_data.supplier.cek_kode', ['kode' => ':kode_supp']) }}"
                        link = link.replace(':kode_supp', kode_supp)

                    const response = await $.ajax({
                        url         : link,
                        data        : { jenis: 'supp', id: suppid },
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
    $('#form-input-supplier').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        var type_supp = $(this).find('[name="type_supp"]:checked')

        if (type_supp.length == 0)
        {
            Swal.fire("Information", 'Tipe Supplier Belum Dipilih', 'warning')
            return
        }

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.inventori.master_data.supplier.save') }}"

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
                        Swal.fire('Sukses', data.message, 'success')
                        .then((result) =>
                        {
                            if (result.isConfirmed)
                            {
                                $("#popup_form_supplier").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else Swal.fire('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    const res = err?.responseJSON
                    Swal.fire('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>