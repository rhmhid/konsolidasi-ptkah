<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-clipboard-list text-dark me-4"></span>
        Form Input Data Asset
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
        <form method="post" id="fi-fas" novalidate>
            <input type="hidden" name="faid" id="faid" value="{{ $data_db->faid }}" readonly="" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kategori Asset</label>
                    {!! $cmb_kategori_fa !!}
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">
                        Kode Asset
                        <i class="fas fa-exclamation-circle ms-2 text-hover-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Generate Otomatis Ketika Simpan Berdasarkan Default Kode Kategorinya"></i>
                    </label>
                    <input type="text" name="facode" id="facode" value="{{ $data_db->facode }}" class="form-control form-control-sm rounded-1 w-100" readonly="" placeholder="Generate Otomatis Ketika Simpan" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Asset</label>
                    <input type="text" name="faname" id="faname" value="{{ $data_db->faname }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Nilai Perolehan</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="nilai_perolehan" id="nilai_perolehan" value="{{ $data_db->nilai_perolehan }}" required="" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2">Nilai Minimum</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency" name="nilai_minimum" id="nilai_minimum" value="{{ $data_db->nilai_minimum }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Efektif</label>
                            <div class="input-group">
                                <input type="text" name="fadate" id="fadate" value="{{ $data_db->fadate }}" class="form-control form-control-sm rounded-1 mydate" readonly="" required="" />
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Masa Manfaat</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 number-only" name="umur_thn" id="umur_thn" value="{{ $data_db->umur_thn }}" required="" maxlength="3" />
                                <span class="input-group-text">Tahun</span>
                                <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 number-only" name="umur_bln" id="umur_bln" value="{{ $data_db->umur_bln }}" required="" maxlength="2" />
                                <span class="input-group-text">Bulan</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2">Periode ?</label>
                            <div class="nav-group nav-group-sm nav-group-fluid rounded-1 border border-gray-300 bg-white p-1">
                                <label>
                                    <input type="radio" class="btn-check" name="is_monthly" id="is_monthly_thn" value="f" {{ $chk_monthly_thn }}/>
                                    <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Tahunan</span>
                                </label>
                                <label>
                                    <input type="radio" class="btn-check" name="is_monthly" id="is_monthly_bln" value="t" {{ $chk_monthly_bln }}/>
                                    <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Bulanan</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label form-label-sm text-dark">Depresiasi ?</label>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" name="skip_depresiasi" type="checkbox" id="skip_depresiasi" value="t" {{ $chk_depresiasi }} />
                                <label class="form-check-label fw-bold" for="skip_depresiasi">Centang Jika Tidak Mengalami Depresiasi</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="d-flex flex-column h-100">
                        <label for="fadesc" class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                        <textarea id="fadesc" name="fadesc" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0">{{ $data_db->fadesc }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Lokasi Asset</label>
                    {!! $cmb_lokasi_fa !!}
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Cost Center</label>
                    {!! $cmb_cost_center !!}
                </div>

                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Header ?</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_header" type="checkbox" id="is_header" value="t" {{ $chk_header }} />
                        <label class="form-check-label fw-bold" for="is_header">Centang Jika Sebagai Header Asset</label>
                    </div>
                </div>

                <div class="col-lg-3 div-parent">
                    <label class="text-dark fw-bold fs-7 pb-2">Header Asset</label>
                    {!! $cmb_header_fa !!}
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
                <button type="button" class="btn btn-danger btn-sm rounded-1 w-100 w-md-auto" id="btn-batal" data-bs-dismiss="modal">
                    <i class="las la-undo"></i> Batal
                </button>

                @if ($data_db->fastatus == 1 || $data_db->faid == 0)
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto ms-md-auto">
                    <button type="submit" class="btn btn-dark btn-sm rounded-1 w-100 w-md-auto" id="btn-simpan">Simpan</button>
                </div>
                @endif
            </div>
            <!--end::Compact form-->
        </form>

        @if ($data_db->fastatus == 2)
        <form class="mt-5" method="post" id="fi-fas-approve" novalidate>
            <div class="row g-0 gx-4">
                <div class="col-lg-12">
                    <div class="d-flex flex-column h-100">
                        <label for="fastatus_notes" class="text-dark fw-bold fs-7 pb-2 required">Catatan Approval</label>
                        <textarea id="fastatus_notes" name="fastatus_notes" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0" required=""></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top">
                <button type="submit" class="btn btn-dark btn-sm rounded-1 float-end" id="btn-approve">
                    <i class="las la-check-circle"></i> Approve
                </button>
            </div>
        </form>
        @endif

        @if ($data_db->fastatus >= 3)
            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fw-bold">
                        History Approval Asset
                        <i class="fas fa-exclamation-circle ms-2" data-bs-toggle="tooltip" title="History Approval Asset Muncul Setelah Asset Dilakukan Proses Approval / Aktivasi"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 w-100">
                        <thead class="bg-dark text-uppercase text-center">
                            <tr class="fw-bold text-white">
                                <th>Tanggal Approval</th>
                                <th>Catatan</th>
                                <th>Approval By</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">{{ dbtstamp2stringlong_ina($data_db->fastatus_time) }}</td>
                                <td>{{ $data_db->fastatus_notes }}</td>
                                <td>{{ $data_db->fastatus_byname }}</td>
                                <td class="text-center">{{ $data_db->fastatus_text }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fw-bold">
                        History Depresiasi Asset
                        <i class="fas fa-exclamation-circle ms-2" data-bs-toggle="tooltip" title="History Depresiasi Asset Muncul Setelah Proses Depresiasi Asset Dilakukan"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 w-100">
                        <thead class="bg-dark text-uppercase text-center">
                            <tr class="fw-bold text-white">
                                <th>Tanggal Depresiasi</th>
                                <th>Keterangan</th>
                                <th>Harta</th>
                                <th>Akumulasi</th>
                                <th>Nilai Buku</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($depresiasi_status)
                                @foreach ($data_depresiasi as $idx => $row)
                                    @php
                                        $row = FieldsToObject($row);
                                    @endphp

                                    <tr>
                                        <td class="text-center">{{ $row->depre_date }}</td>
                                        <td>{{ $row->depre_notes }}</td>
                                        <td class="text-end">{{ $row->nilai_perolehan }}</td>
                                        <td class="text-end">{{ $row->nilai_akumulasi }}</td>
                                        <td class="text-end">{{ $row->nilai_buku }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
<!--end::Modal body-->

<!--begin::template - Ubah-->
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

    $(".mydate-time").flatpickr({
        enableTime: true,
        dateFormat: "d-m-Y H:i",
        time_24hr: true,
        minuteIncrement: 1
    })

    $(".mydate").flatpickr({
        defaultDate: null,
        dateFormat: "d-m-Y",
    })

    HideParent()

    // aksi submit edit / update
    $('#is_header').change(function (e)
    {
        e.preventDefault()

        HideParent()
    })

    function HideParent ()
    {
        if ($('#is_header').is(':checked'))
            $('.div-parent').hide()
        else
            $('.div-parent').show()
    }

    $('#fi-fas').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            ResetMoney()

            let $form = $(this)
            let nilai_perolehan = $form.find('[id="nilai_perolehan"]').val()
            let umur_thn = $form.find('[id="umur_thn"]').val()
            let umur_bln = $form.find('[id="umur_bln"]').val()

            nilai_perolehan = isNaN(parseFloat(nilai_perolehan)) ? 0 : parseFloat(nilai_perolehan)

            if (parseFloat(nilai_perolehan) == 0)
            {
                FormatMoney()

                swalShowMessage('Peringatan', 'Nilai Perolehan belum diisi.', 'warning')

                return false
            }

            let masa_manfaat = (parseInt(umur_thn) * 12 ) + parseInt(umur_bln)

            if (parseFloat(masa_manfaat) == 0)
            {
                FormatMoney()

                swalShowMessage('Peringatan', 'Masa Manfaat belum diisi.', 'warning')

                return false
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.fixed_asset.save') }}"

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
                                FormatMoney()

                                $("#mdl-form-fa").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else
                    {
                        swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')

                        FormatMoney()
                    }
                })
                .fail( err => {
                    const res = err?.responseJSON

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')

                    FormatMoney()
                })
            }), 2e3)
        }
    })

    $('#fi-fas-approve').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            let $faid = $('#faid').val()

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch
                payload.append('faid', $faid) // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.fixed_asset.approve') }}"

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
                                $("#mdl-form-fa").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else
                        swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    const res = err?.responseJSON

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>