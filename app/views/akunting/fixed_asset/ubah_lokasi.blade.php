<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-clipboard-list text-dark me-4"></span>
        Riwayat Lokasi Asset
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
        <form method="post" id="fi-log-lokasi-fa" novalidate>
            <input type="hidden" name="faid" id="faid" value="{{ $data_db->faid }}" readonly="" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Asset</label>
                    <input type="text" name="facode" id="facode" value="{{ $data_db->facode }}" class="form-control form-control-sm rounded-1 w-100" readonly="" required="" />
                </div>

                <div class="col-lg-8">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Asset</label>
                    <input type="text" name="faname" id="faname" value="{{ $data_db->faname }}" class="form-control form-control-sm rounded-1 w-100" readonly="" required="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Lokasi Asset</label>
                    {!! $cmb_lokasi_fa !!}
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Cost Center</label>
                    {!! $cmb_cost_center !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <div class="d-flex flex-column h-100">
                        <label for="notes" class="text-dark fw-bold fs-7 pb-2">Catatan</label>
                        <textarea id="notes" name="notes" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0"></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
                <button type="button" class="btn btn-danger btn-sm rounded-1 w-100 w-md-auto" id="btn-batal-lokasi" data-bs-dismiss="modal">
                    <i class="las la-undo"></i> Batal
                </button>

                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto ms-md-auto">
                    <button type="submit" class="btn btn-dark btn-sm rounded-1 w-100 w-md-auto" id="btn-simpan-lokasi">
                        <i class="las la-save"></i> Simpan
                    </button>
                </div>
            </div>
            <!--end::Compact form-->
        </form>
    </div>

    <div class="table-responsive">
        <table id="myTableRiwayatLokasi" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
            <thead class="bg-dark text-uppercase fs-7">
                <tr class="fw-bold text-white">
                    <th class="text-center border-start py-5" rowspan="2">Tanggal Perubahan</th>
                    <th class="text-center border-start py-5" colspan="2">Dari</th>
                    <th class="text-center border-start py-5" colspan="2">Ket</th>
                    <th class="text-center border-start py-5" rowspan="2">Catatan</th>
                </tr>
                <tr class="fw-bold text-white">
                    <th class="text-center border-start py-5">Lokasi</th>
                    <th class="text-center border-start py-5">Cost Center</th>
                    <th class="text-center border-start py-5">Lokasi</th>
                    <th class="text-center border-start py-5">Cost Center</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<!--end::Modal body-->

<!--begin::template - Ubah-->
<script type="text/javascript">
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

    $('#myTableRiwayatLokasi').DataTable().clear().destroy()

    // DATATABLE myTableRiwayatLokasi
    var ajaxOptionsRate = {
        url: "{{ route('api.akunting.fixed_asset.ubah_lokasi.histori') }}",
        data: function (params)
            {
                params.faid = $("#faid").val()
            },
    }

    var optionsRate = {
        columns:[
                    {
                        data: 'create_time',
                        name: 'create_time',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'lokasi_from',
                        name: 'lokasi_from',
                    },
                    {
                        data: 'cost_center_from',
                        name: 'cost_center_from',
                    },
                    {
                        data: 'lokasi_to',
                        name: 'lokasi_to',
                    },
                    {
                        data: 'cost_center_to',
                        name: 'cost_center_to',
                    },
                    {
                        data: 'notes',
                        name: 'notes'
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    tableRevaluate = setupDataTable(
                '#myTableRiwayatLokasi',
                ajaxOptionsRate,
                optionsRate
            )

    $('#fi-log-lokasi-fa').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.fixed_asset.save_ubah_lokasi') }}"

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
                                tableRevaluate.ajax.reload(null, false)

                                table.ajax.reload(null, false)

                                $("#mdl-form-fa2").modal('hide')
                            }
                        })
                    }
                    else
                    {
                        swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                    }
                })
                .fail( err => {
                    const res = err?.responseJSON

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>