<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-university text-dark me-4"></span>
        Form Input Masterdata Kas / Bank
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
        <form method="post" id="form-input-bank" novalidate>
            <input type="hidden" name="bank_id" id="bank_id" value="{{ $data_bank->bank_id }}" />

            <!--begin::Compact form-->
            <div class="row mb-3">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Bank Type</label>
                    {!! $cmb_bank_type !!}
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Bank</label>
                    <input type="text" name="kode_bank" id="kode_bank" value="{{ $data_bank->bank_kode }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Bank</label>
                    <input type="text" name="nama_bank" id="nama_bank" value="{{ $data_bank->bank_nama }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-12">
                    <label class="text-dark fw-bold fs-7 pb-2">Alamat</label>
                    <input type="text" name="alamat" id="alamat" value="{{ $data_bank->alamat }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Kota</label>
                    <input type="text" name="kota" id="kota" value="{{ $data_bank->kota }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Kontak</label>
                    <input type="text" name="kontak" id="kontak" value="{{ $data_bank->kontak }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">No. Telp</label>
                    <div class="input-group input-group-sm">
                        <span class="symbol symbol-20px input-group-text">
                            <img class="rounded-1" src="{{ asset('assets/media/flags/indonesia.svg') }}">
                        </span>

                        <input type="text" class="form-control form-control-sm rounded-1" name="no_telp" id="no_telp" value="{{ $data_bank->no_telp }}" inputmode="text" placeholder="____ __________">
                    </div>

                    <div class="invalid-feedback d-block">
                        Contoh : 022123456789
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">No. Rekening</label>
                    <input type="text" name="bank_no_rek" id="bank_no_rek" value="{{ $data_bank->bank_no_rek }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Atas Nama</label>
                    <input type="text" name="bank_atas_nama" id="bank_atas_nama" value="{{ $data_bank->bank_atas_nama }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Cabang</label>
                    <input type="text" name="bank_cabang" id="bank_cabang" value="{{ $data_bank->bank_cabang }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Persentase Adm ( Debet )</label>
                    <input type="text" name="persen_adm_deb" id="persen_adm_deb" value="{{ $data_bank->persen_adm_deb }}" class="form-control form-control-sm rounded-1 w-100 currency" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Persentase Adm ( Credit )</label>
                    <input type="text" name="persen_adm_cre" id="persen_adm_cre" value="{{ $data_bank->persen_adm_cre }}" class="form-control form-control-sm rounded-1 w-100 currency" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Bank Image</label>
                    {!! $cmb_bank_image !!}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Default C.O.A</label>
                    {!! $cmb_default_coa !!}
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">C.O.A Ctrl. Acc</label>
                    {!! $cmb_ctrl_coa !!}
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3" data-type="just_bank">
                    <label class="form-label form-label-sm text-dark">Status Credit Card</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_cc" type="checkbox" id="is_cc" value="t" {{ $chk_cc }} />
                        <label class="form-check-label fw-bold" for="is_cc">Sebagai Credit Card</label>
                    </div>
                </div>

                <div class="col-lg-3" data-type="just_bank">
                    <label class="form-label form-label-sm text-dark">Status Transfer</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_transfer" type="checkbox" id="is_transfer" value="t" {{ $chk_transfer }} />
                        <label class="form-check-label fw-bold" for="is_transfer">Sebagai Bank Transfer</label>
                    </div>
                </div>

                <div class="col-lg-3" data-type="just_bank">
                    <label class="form-label form-label-sm text-dark">Status Petty Cash</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_petty_cash" type="checkbox" id="is_petty_cash" value="t" {{ $chk_petty_cash }} />
                        <label class="form-check-label fw-bold" for="is_petty_cash">Sebagai Petty Cash</label>
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Status Bank</label>
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
    HideBankType()

    $('.modal .form-select').select2({
        width: '100%',
        dropdownParent: $('#popup_form_bank')
    })

    $('#bank_type').on('change', HideBankType)

    function HideBankType ()
    {
        var bank_type = $('#bank_type').val()

        if (bank_type == 1) $('[data-type="just_bank"]').fadeIn(500)
        else $('[data-type="just_bank"]').fadeOut(500)
    }

    $('#no_telp').val(parsePhone('{{ $data_bank->no_telp }}')).trigger('change')

    Inputmask({
        "mask" : "9999999999",
    }).mask("#no_telp")

    $("#kode_bank").click(function ()
    {
        var bank_id = $('#bank_id').val()

        Swal.fire({
            title: "Masukkan Kode",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Kode",
            showLoaderOnConfirm: true,

            preConfirm: async (kode_bank) => {
                try {
                    if (!kode_bank)
                    {
                        const message = 'Kode Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.master_data.database.kas_bank.cek_kode', ['kode' => ':kode_bank']) }}"
                        link = link.replace(':kode_bank', kode_bank)

                    const response = await $.ajax({
                        url         : link,
                        data        : { jenis: 'bank', id: bank_id },
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
    $('#form-input-bank').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const resetTelp = parsePhone($('#no_telp').val())
            const payload = new FormData(this)

                payload.append('_method', 'patch') // ganti ajax method post menjadi patch
                payload.set('no_telp', resetTelp)

            formSubmitUrl = "{{ route('api.master_data.database.kas_bank.save') }}"

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
                                $("#popup_form_bank").modal('hide')

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