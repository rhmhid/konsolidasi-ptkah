<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-prescription-bottle-alt text-dark me-4"></span>
        Form Input Masterdata Gudang & Depo
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
        <form method="post" id="form-input-gudang" novalidate>
            <input type="hidden" name="gid" id="gid" value="{{ $data_gudang->gid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Gudang</label>
                    <input type="text" name="kode_gudang" id="kode_gudang" value="{{ $data_gudang->kode_gudang }}" class="form-control form-control-sm rounded-1 w-100" required="" maxlength="10" />
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Gudang</label>
                    <input type="text" name="nama_gudang" id="nama_gudang" value="{{ $data_gudang->nama_gudang }}" class="form-control form-control-sm rounded-1 w-100" required="" />

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Cost Center</label>
                    {!! $cmb_cost_center !!}

                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        Harap diisi
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="mb-3">
                        <label class="text-dark fw-bold fs-7 pb-2">
                            <span>Lokasi</span>
                            <i class="fas fa-question-circle ms-2 text-hover-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Informasi lokasi gudang fisik berada (Gedung / Lantai / Ruang / Area)"></i>
                        </label>
                        <input type="text" name="lokasi" id="lokasi" value="{{ $data_gudang->lokasi }}" class="form-control form-control-sm rounded-1 w-100" />
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Status Gudang</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                        <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-flex align-items-center fs-7 text-dark fw-bolder mb-2">
                        <span>Jenis Gudang</span>
                        <i class="fas ms-2 fs-7"></i>
                    </label>

                    <span>
                        <div class="parent-check">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input parent-check-input" type="checkbox" name="is_gudang_besar" id="is_gudang_besar" value="t" {{ $chk_gudang }} />
                                <label class="form-check-label fs-7" for="is_gudang_besar">
                                    Gudang Besar (Pembelian dan Penerimaan Barang)
                                    <i class="fas fa-question-circle ms-2 text-hover-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Gudang untuk transaksi Pembelian dan Penerimaan barang dari Supplier"></i>
                                </label>
                            </div>
                        </div>

                        <div class="parent-check mt-3">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input parent-check-input" name="is_sales" type="checkbox" id="is_sales" value="t" {{ $chk_sales }} />
                                <label class="form-check-label fs-7" for="is_sales">
                                    Gudang Penjualan (Penjualan Barang)
                                    <i class="fas fa-question-circle ms-2 text-hover-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Gudang untuk penjualan barang di Halaman Kasir"></i>
                                </label>
                            </div>
                        </div>

                        <div class="parent-check mt-3">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input parent-check-input" type="checkbox" name="is_depo" id="is_depo" value="t" {{ $chk_depo }} />
                                <label class="form-check-label fs-7" for="is_depo">
                                    Depo (Gudang Kecil / Unit)
                                    <i class="fas fa-question-circle ms-2 text-hover-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Gudang kecil hanya digunakan untuk keperluan distribusi barang antar departemen"></i>
                                </label>
                            </div>
                        </div>
                    </span>
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

    $('#kode_gudang').change(function ()
    {
        var kode = $(this).val()
        var msid = $('#msid').val()

        $.ajax({
            url         : "{{ route('api.inventori.master_data.gudang.cek_kode') }}",
            data        : { jenis: 'gudang', id: msid, kode: kode },
            type        : 'POST',
            dataType    : 'JSON',

            error       : function (req, stat, err)
                        {

                        },

            success     : function (data)
                        {
                            if (data.message != '')
                            {
                                $('#kode_gudang').val('{{ $data_sat->kode_gudang }}')
                                $('#kode_gudang').focus()

                                Swal.fire('Peringatan', 'Kode Gudang Sudah Tersedia', 'error')
                            }

                            return false
                        },

            async       : false,
            cache       : false
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
    $('#form-input-gudang').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (checkRequired(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.inventori.master_data.gudang.save') }}"

            showLoading ()

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
                                $("#popup_form_gudang").modal('hide')

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