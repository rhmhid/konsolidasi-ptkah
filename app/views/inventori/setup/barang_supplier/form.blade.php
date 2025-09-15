<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-prescription-bottle text-dark me-4"></span>
        Form Input Barang - Supplier
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
        <form method="post" id="fi-BrgSupp" novalidate>
            <input type="text" name="bsid" id="bsid" value="{{ $data_db->bsid }}" />
            <input type="text" name="mbid" id="mbid" value="{{ $data_db->mbid }}" />

            <!--begin::Compact form-->
            <div class="row g-5 gx-4">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Supplier</label>
                    {!! $cmb_supp !!}
                </div>

                <div class="col-lg-6 {{ $hide_brg_cmb }}">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Barang</label>
                    <select name="mbid_mdl" id="mbid-modal" class="form-select form-select-sm rounded-1" data-control="select2" data-allow-clear="true"></select>
                </div>
            </div>

            <div class="row g-5 gx-4 mt-3">
                <div class="col-lg-9">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Barang</label>
                    <div class="text-dark fw-bold fs-7 div-barang" data-kode="{{ $data_db->kode_brg }}" data-nama="{{ $data_db->nama_brg }}">{{ $data_db->barang }}</div>
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Satuan</label>
                    <input type="text" name="kode_satuan" id="kode_satuan" value="{{ $data_db->kode_satuan }}" class="form-control form-control-sm rounded-1 w-100" required="" readonly="" />
                </div>
            </div>

            <div class="row g-5 gx-4 mt-3">
                <div class="col-lg-8">
                    <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                    <input type="text" name="keterangan" id="keterangan" value="{{ $data_db->keterangan }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-4">
                    <label class="form-label form-label-sm text-dark">Supplier Utama</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_supp_utama" type="checkbox" id="is_supp_utama" value="t" {{ $chk_supp }} />
                        <label class="form-check-label fw-bold" for="is_supp_utama">{{ $txt_supp }}</label>
                    </div>
                </div>
            </div>

            <div class="row g-5 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="d-block text-dark fs-7 fw-bold mb-2 required">
                        Harga
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Nominal Ini Akan Menjadi Acuan Untuk Pembelian"></i>
                    </label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control form-control-sm rounded-1 currency" name="harga" id="harga" value="{{ $data_db->harga }}" required="" />
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="d-block text-dark fs-7 fw-bold mb-2 required">
                        Diskon ( % )
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Nominal Ini Akan Menjadi Acuan Untuk Pembelian"></i>
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm rounded-1 currency" name="disc" id="disc" value="{{ $data_db->disc }}" maxlength="3" data-precision="2" />
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-danger btn-sm rounded-1" id="btn_batal_barang" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="btn_save_barang">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    FormatMoney()

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

    $('#mbid-modal').select2({
        dropdownParent  : $('#md-fi-BrgSupp'),
        placeholder     : 'Pilih Barang...',
        ajax            :
                        {
                            url             : "{{ route('api.inventori.setup.barang_supplier.cari_barang') }}",

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
                                                    results: $.map(json, function (items)
                                                    {
                                                        return {
                                                            id          : items.mbid,
                                                            text        : items.barang,
                                                            kode_brg    : items.kode_brg,
                                                            nama_brg    : items.nama_brg,
                                                            kode_satuan : items.kode_satuan,
                                                            hna         : items.hna
                                                        }
                                                    })
                                                }
                                            }
                        }
    }).on('select2:select', function (e)
    {
        var items = e.params.data

        if (items.id != '')
        {
            $('#mbid').val(items.id)

            $('.div-barang').html(items.text)

            $('.div-barang').attr('data-kode', items.kode_brg)

            $('.div-barang').attr('data-nama', items.nama_brg)

            $('#kode_satuan').val(items.kode_satuan)

            $('#harga').val(items.hna).trigger('blur')

            $(this).val(null).trigger("change")
        }
    })

    $('#is_supp_utama').change(function ()
    {
        const $el = $(this)
        const $label = $(`label[for="${$el.attr('id')}"]`)

        if ($el.is(':checked')) $label.text("Ya")
        else $label.text("Tidak")
    })

    // aksi submit edit / update
    $('#fi-BrgSupp').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            Swal.fire({
                html: 'Pastikan inputan sudah sesuai, lanjutkan untuk simpan ?',
                icon: "info",
                buttonsStyling: false,
                showCancelButton: true,
                confirmButtonText: "Ya, Simpan Data !",
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: "btn btn-dark",
                    cancelButton: 'btn btn-danger'
                }
            }).then((result) =>
            {
                if (result.isConfirmed)
                {
                    ResetMoney()

                    const payload = new FormData(this)
                        payload.append('_method', 'patch') // ganti ajax method post menjadi patch

                    formSubmitUrl = "{{ route('api.inventori.setup.barang_supplier.save') }}"

                    showLoading()

                    setTimeout((function ()
                    {
                        doAjax(
                            formSubmitUrl,
                            payload,
                            "POST"
                        )
                        .done( data => {
                            Swal.close()

                            if (data.success)
                            {
                                swalShowMessage('Sukses', data.message, 'success')
                                .then((result) =>
                                {
                                    if (result.isConfirmed)
                                    {
                                        $("#md-fi-BrgSupp").modal('hide')

                                        table.ajax.reload(null, false)
                                    }
                                })
                            }
                            else swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                        })
                        .fail( err => {
                            Swal.close()

                            const res = err?.responseJSON

                            swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                        })
                    }), 2e3)
                }
            })

            return false
        }
    })
</script>