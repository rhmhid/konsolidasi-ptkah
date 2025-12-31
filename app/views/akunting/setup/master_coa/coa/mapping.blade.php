<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-puzzle-piece text-dark me-4"></span>
        Form Mapping Chart Of Account
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
        <form method="post" id="fi-coa-mapping" novalidate>
            <input type="hidden" name="coaid" id="coaid" value="{{ $data_coa->coaid }}" />

            <div class="row g-0 gx-4">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode C.O.A</label>
                    <div class="rounded-1 bg-white d-flex align-items-center">{{ $data_coa->coacode }}</div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama C.O.A</label>
                    <div class="rounded-1 bg-white d-flex align-items-center">{{ $data_coa->coaname }}</div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold required">
                        Data Cabang
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu Masterdata -> Database -> Cabang & Wilayah"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-branch">
                        <thead class="bg-dark text-uppercase fs-7 text-center">
                            <tr class="fw-bold text-white">
                                <th rowspan="2">Nama Cabang</th>
                                <th colspan="2">Range COACODE</th>
                            </tr>
                            <tr class="fw-bold text-white">
                                <th>Dari</th>
                                <th>Sampai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp

                            @forelse ($rs_cabang as $row)
                                @php
                                    $hasData = true;
                                    $row = FieldsToObject($row);
                                @endphp

                                <tr>
                                    <td class="mx-4">
                                        {{ $row->branch }}
                                        <input type="hidden" name="bid[{{ $row->bid }}]" id="bid[{{ $row->bid }}]" value="{{ $row->bid }}" />
                                    </td>
                                    <td>
                                        <input type="text" name="coacode_from[{{ $row->bid }}]" id="coacode_from[{{ $row->bid }}]" value="{{ $row->coacode_from }}" class="form-control form-control-sm rounded-1 w-100 text-center" />
                                    </td>
                                    <td>
                                        <input type="text" name="coacode_to[{{ $row->bid }}]" id="coacode_to[{{ $row->bid }}]" value="{{ $row->coacode_to }}" class="form-control form-control-sm rounded-1 w-100 text-center" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"><em>Data Cabang Tidak Ditemukan.</em></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">
                    <i class="las la-undo"></i> Batal
                </button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_simpan">
                    <i class="las la-save"></i> Simpan
                </button>
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

    // aksi submit edit / update
    $('#fi-coa-mapping').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)

            payload.append('_method', 'patch') // ganti ajax method post menjadi patch
            formSubmitUrl = "{{ route('api.akunting.setup.master_coa.coa.save_mapping') }}"

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