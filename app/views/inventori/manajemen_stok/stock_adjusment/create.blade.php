<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-box text-dark me-4"></span>
        Form Input Penyesuaian Stok
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
        <form method="post" id="form-input-adj" novalidate>
            <input type="hidden" name="mbid" id="mbid" value="{{ $data_db->mbid }}" />
            <input type="hidden" name="gid" id="gid" value="{{ $data_db->kode_gk }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Barang</label>
                    <input type="text" id="kode_brg" class="form-control form-control-sm rounded-1" value="{{ $data_db->kode_brg }}" readonly="" />
                </div>

                <div class="col-lg-8">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Barang</label>
                    <input type="text" id="nama_brg" class="form-control form-control-sm rounded-1" value="{{ $data_db->nama_brg }}" readonly="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Satuan</label>
                    <input type="text" name="kode_satuan" id="kode_satuan" class="form-control form-control-sm rounded-1" value="{{ $data_db->kode_satuan }}" readonly="" />
                </div>

                <div class="col-lg-8">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Gudang</label>
                    <input type="text" id="nama_gudang" class="form-control form-control-sm rounded-1" value="{{ $data_db->nama_gk }}" readonly="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Stok Tersedia</label>
                    <input type="text" name="stok_available" id="stok_available" class="form-control form-control-sm rounded-1 number-only" value="{{ $data_db->stock }}" readonly="" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Stok</label>
                    <input type="text" id="stok" class="form-control form-control-sm rounded-1 number-only calc" value="{{ $data_db->stock }}" required="" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Jumlah Adjustment</label>
                    <input type="text" name="vol_adj" id="vol_adj" class="form-control form-control-sm rounded-1 number-only" required="" readonly="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <div class="d-flex flex-column h-100">
                        <label for="keterangan" class="text-dark fw-bold fs-7 pb-2 required">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0" required=""></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-danger btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_simpan">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    $('#form-input-adj').on('change', '.calc', function (e)
    {
        e.preventDefault()

        let stok_available  = $('#stok_available').val(),
            stok            = $('#stok').val(),
            adj             = 0

        adj = parseFloat(stok) - parseFloat(stok_available)

        $('#vol_adj').val(adj)
    })

    // aksi submit edit / update
    $('#form-input-adj').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            let $adj = $('#vol_adj').val()

            if (parseFloat($adj) == 0)
            {
                swalShowMessage('Information', 'Jumlah Adjustment Tidak Boleh 0 Atau Minus.', 'error')

                return false
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.inventori.manajemen_stok.stock_adjusment.save') }}"

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
                                $("#mdl-form-adj").modal('hide')

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
</script>