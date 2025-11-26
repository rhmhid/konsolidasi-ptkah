<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-puzzle-piece text-dark me-4"></span>
        Form Input Pos Laba Rugi
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
        <form method="post" id="form-input-pos-pl" novalidate>
            <input type="hidden" name="pplrid" id="pplrid" value="{{ $data_pos->pplrid }}" />
            <input type="hidden" name="jenis_pos" id="jenis_pos" value="{{ $data_pos->jenis_pos }}" />            

            <!--begin::Compact form-->
            <div class="row g-0 gx-4 mb-5">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Urutan</label>
                    <input type="text" name="urutan" id="urutan" value="{{ $data_pos->urutan }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tab Indent</label>
                    <input type="text" name="level" id="level" value="{{ $data_pos->level }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mb-5">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Kode Pos</label>
                    <input type="text" name="kode_pos" id="kode_pos" value="{{ $data_pos->kode_pos }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Pos</label>
                    <input type="text" name="nama_pos" id="nama_pos" value="{{ $data_pos->nama_pos }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mb-5">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Parent Pos</label>
                    {!! $cmb_parent_pos !!}
                </div>

                <div class="col-lg-6">
                    <label class="form-label form-label-sm text-dark">Menghitung Subtotal</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="sum_total" type="checkbox" id="sum_total" value="t" {{ $chk_manual_journal }} />
                        <label class="form-check-label fw-bold" for="sum_total">Digunakan Untuk Menghitung Subtotal</label>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4">
                <div class="col-lg-6">
                    <label class="form-label form-label-sm text-dark">Status Aktif</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                        <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Pendapatan ?</label>
                    {!! $cmb_jenis_pos !!}
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
    $('.modal .form-select').select2({
        width: '100%',
        dropdownParent: $('#popup-form-pos-pl')
    })

    $('#is_aktif').change(function ()
    {
        const $el = $(this)
        const $label = $(`label[for="${$el.attr('id')}"]`)

        if ($el.is(':checked')) $label.text("Aktif")
        else $label.text("Tidak Aktif")
    })

    // aksi submit edit / update
    $('#form-input-pos-pl').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.setup.posisi_laporan.pos_pl.rekap.save') }}"

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
                                $("#popup-form-pos-pl").modal('hide')

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