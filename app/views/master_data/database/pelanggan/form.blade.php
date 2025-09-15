<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-user-friends text-dark me-4"></span>
        Form Input Masterdata Customer
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
        <form method="post" id="form-input-customer" novalidate>
            <input type="hidden" name="custid" id="custid" value="{{ $data_cust->custid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Group Customer</label>
                    {!! $cmb_branch !!}
                </div>
            </div>
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Group Customer</label>
                    {!! $cmb_group !!}
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Customer</label>
                    <input type="text" name="kode_customer" id="kode_customer" value="{{ $data_cust->kode_customer }}" class="form-control form-control-sm rounded-1 w-100" readonly="" readonly="" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Customer</label>
                    <input type="text" name="nama_customer" id="nama_customer" value="{{ $data_cust->nama_customer }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Alamat Customer</label>
                    <input type="text" name="alamat_customer" id="alamat_customer" value="{{ $data_cust->alamat_customer }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">COA Piutang</label>
                    {!! $cmb_coa_ar !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Kota Customer</label>
                    <input type="text" name="kota_customer" id="kota_customer" value="{{ $data_cust->kota_customer }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">Telp Customer</label>
                    <input type="text" name="telp_customer" id="telp_customer" value="{{ $data_cust->telp_customer }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">E-Mail Customer</label>
                    <input type="text" name="email_customer" id="email_customer" value="{{ $data_cust->email_customer }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-9">
                    <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                    <input type="text" name="keterangan" id="keterangan" value="{{ $data_cust->keterangan }}" class="form-control form-control-sm rounded-1 w-100" />
                </div>

                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Status Customer</label>
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
        dropdownParent: $('#popup-form-cust')
    })

    $('#kode_customer').click(function ()
    {
        var custid = $('#custid').val()

        Swal.fire({
            title: "Masukkan Kode",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Kode",
            showLoaderOnConfirm: true,

            preConfirm: async (kode_customer) => {
                try {
                    if (!kode_customer)
                    {
                        const message = 'Kode Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.master_data.database.pelanggan.cek_kode', ['kode' => ':kode_customer']) }}"
                        link = link.replace(':kode_customer', kode_customer)

                    const response = await $.ajax({
                        url         : link,
                        data        : { jenis: 'cust', id: custid },
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
    $('#form-input-customer').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.master_data.database.pelanggan.save') }}"

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
                                $("#popup-form-cust").modal('hide')

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