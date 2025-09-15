<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-users text-dark me-4"></span>
        Form Input Masterdata Pegawai
    </h2>

    <div class="btn btn-sm btn-icon btn-active-color-dark" data-bs-dismiss="modal">
        <i class="fas fa-times"></i>
    </div>
    <!--end::Modal title-->
</div>
<!--end::Modal header-->

<!--begin::Modal body-->
<div class="modal-body py-6 px-lg-17">
    <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
        <form method="post" id="form-input-pegawai" novalidate>
            <input type="hidden" name="pid" id="pid" value="{{ $data_emp->pid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">NRP</label>
                    <input type="text" name="nrp" id="nrp" value="{{ $data_emp->nrp }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ $data_emp->nama_lengkap }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-5">
                <div class="col-lg-6">
                    <div class="d-flex flex-column h-100">
                        <label for="alamat_lengkap" class="text-dark fw-bold fs-7 pb-2">Alamat</label>
                        <textarea id="alamat_lengkap" name="alamat_lengkap" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0">{{ $data_emp->alamat_lengkap }}</textarea>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="mb-3">
                        <label class="text-dark fw-bold fs-7 pb-2 required">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ $data_emp->tempat_lahir }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                    </div>

                </div>

                <div class="col-lg-3">
                    <div class="mb-3">
                        <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Lahir</label>
                        <div class="input-group">
                            <input type="text" name="tanggal_lahir" id="tanggal_lahir" value="{{ $data_emp->tanggal_lahir }}" class="form-control form-control-sm rounded-1" readonly="readonly" required="" />
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="text-dark fw-bold fs-7 pb-2 required">Jenis Kelamin</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" name="sex" class="btn-check" id="male" value="m" required="" {{ $chk_sex_m }} />
                            <label class="btn btn-sm btn-light-dark rounded-1" for="male">laki - Laki</label>

                            <input type="radio" name="sex" class="btn-check" id="female" value="f" required="" {{ $chk_sex_f }} >
                            <label class="btn btn-sm btn-light-dark rounded-1" for="female">Perempuan</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-5">

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">Unit</label>
                    {!! $cmb_unit !!}
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Jabatan</label>
                    {!! $cmb_jabatan !!}
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">Tenaga</label>
                    {!! $cmb_tenaga !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-5">
                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Status Pegawai</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                        <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Adalah Dokter</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_dokter" type="checkbox" id="is_dokter" value="t" {{ $chk_dokter }} />
                        <label class="form-check-label fw-bold" for="is_dokter">{{ $txt_dokter }}</label>
                    </div>
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
    $('.modal .form-select').select2({
        width: '100%',
        dropdownParent: $('#popup_form_pegawai')
    })


    $('#tanggal_lahir').flatpickr({
        defaultDate: null,
        dateFormat: "d-m-Y",
    })



    $('#is_aktif').change(function ()
    {
        const $el = $(this)
        const $label = $(`label[for="${$el.attr('id')}"]`)

        if ($el.is(':checked')) $label.text("Aktif")
        else $label.text("Tidak Aktif")
    })

    $('#is_dokter').change(function ()
    {
        const $el = $(this)
        const $label = $(`label[for="${$el.attr('id')}"]`)

        if ($el.is(':checked')) $label.text("Ya, Dokter")
        else $label.text("Bukan Dokter")
    })

    // aksi submit edit / update
    $('#form-input-pegawai').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (checkRequired(this))
        {
            const payload = new FormData(this)

                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.master_data.pegawai.save') }}"

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
                                $("#popup_form_pegawai").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else
                        swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
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