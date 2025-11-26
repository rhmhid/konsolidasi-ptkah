<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-puzzle-piece text-dark me-4"></span>
        Form Input Masterdata Chart Of Account
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
        <form method="post" id="form-input-coa" novalidate>
            <input type="hidden" name="coaid" id="coaid" value="{{ $data_coa->coaid }}" />
            <input type="hidden" name="coatid" id="coatid" value="{{ $data_coa->coatid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4 mb-5">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Group C.O.A</label>
                    {!! $cmb_group_coa !!}
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Default C.O.A</label>
                    <select name="default_debet" id="default_debet" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Default Posistion C.O.A" required="">
                        <option></option>
                        <option value="t" {{ $sel_def_dr }}>Debet</option>
                        <option value="f" {{ $sel_def_cr }}>Credit</option>
                    </select>
                </div>
            </div>

            <div class="row g-0 gx-4 mb-5">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Post Type C.O.A</label>
                    <select name="allow_post" id="allow_post" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Post Type C.O.A" required="">
                        <option></option>
                        <option value="t" {{ $sel_allow_t }}>Detail (Postable)</option>
                        <option value="f" {{ $sel_allow_f }}>Header (Non-Postable)</option>
                    </select>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Parent C.O.A</label>
                    {!! $cmb_parent_coa !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mb-5">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode C.O.A</label>
                    <input type="text" name="coacode" id="coacode" value="{{ $data_coa->coacode }}" class="form-control form-control-sm rounded-1 w-100" required="" maxlength="16" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama C.O.A</label>
                    <input type="text" name="coaname" id="coaname" value="{{ $data_coa->coaname }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mb-5">
                @if ($show_pos_na)
                    <div class="col-lg-6">
                        <label class="text-dark fw-bold fs-7 pb-2">POS Neraca</label>
                        {!! $cmb_pos_na !!}
                    </div>
                @endif

                @if ($show_pos_pl)
                    <div class="col-lg-6">
                        <label class="text-dark fw-bold fs-7 pb-2">POS Laba Rugi</label>
                        {!! $cmb_pos_pl !!}
                    </div>
                @endif

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">POS Arus Kas</label>
                    {!! $cmb_pos_cf !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mb-5">
                <!--div class="col-lg-6">
                    <label class="form-label form-label-sm text-dark">Profit & Lost (P/L) Account</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="period_reset" type="checkbox" id="period_reset" value="t" {{ $chk_reset }} />
                        <label class="form-check-label fw-bold" for="period_reset">Profit & Lost (P/L) Account ?</label>
                    </div>
                </div-->

                <div class="col-lg-6">
                    <label class="form-label form-label-sm text-dark">Use Manual Jurnal</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_manual_journal" type="checkbox" id="is_manual_journal" value="t" {{ $chk_manual_journal }} />
                        <label class="form-check-label fw-bold" for="is_manual_journal">Digunakan Untuk Manual Jurnal</label>
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="form-label form-label-sm text-dark">Use Petty Cash</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_petty_cash" type="checkbox" id="is_petty_cash" value="t" {{ $chk_petty_cash }} />
                        <label class="form-check-label fw-bold" for="is_petty_cash">Digunakan Untuk Petty Cash</label>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mb-5">
                <div class="col-lg-6">
                    <label class="form-label form-label-sm text-dark">Status Aktif</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_valid" type="checkbox" id="is_valid" value="t" {{ $chk_valid }} />
                        <label class="form-check-label fw-bold" for="is_valid">{{ $txt_aktif }}</label>
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
        dropdownParent: $('#popup-form-coa')
    })

    $('#is_valid').change(function ()
    {
        const $el = $(this)
        const $label = $(`label[for="${$el.attr('id')}"]`)

        if ($el.is(':checked')) $label.text("Aktif")
        else $label.text("Tidak Aktif")
    })

    // aksi submit edit / update
    $('#form-input-coa').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)

            payload.append('_method', 'patch') // ganti ajax method post menjadi patch
            formSubmitUrl = "{{ route('api.akunting.setup.master_coa.coa.save') }}"

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
                                $("#popup-form-coa").modal('hide')

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
