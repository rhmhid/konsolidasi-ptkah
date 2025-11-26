<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-dollar-sign text-dark me-4"></span>
        Form Input Masterdata Currency Rate
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
        <form method="post" id="form-input-currency-rate" novalidate>
            <input type="hidden" name="cid" id="cid" value="{{ $cid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Mulai</label>
                    <div class="input-group">
                        <input type="text" name="curr_start" id="curr_start" value="{{ $curr_start }}" class="form-control form-control-sm rounded-1" readonly="" required="" />
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Rate</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-money-bill"></i>
                        </span>
                        <input type="text" name="curr_rate" id="curr_rate" value="{{ $curr_rate }}" class="form-control form-control-sm rounded-1 currency" required="" />
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Status Currency</label>
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

    <div class="table-responsive">
        <table id="myTableCurrencyRate" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
            <thead class="bg-dark text-uppercase fs-7">
                <tr class="fw-bold text-white">
                    <th class="text-center border-start py-5">Tanggal Mulai</th>
                    <th class="text-center border-start py-5">Rate</th>
                    <th class="text-center border-start py-5">User Input</th>
                    <th class="text-center border-start py-5">Waktu Input</th>
                    <th class="text-center border-start py-5">Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    $('#myTableCurrencyRate').DataTable().clear().destroy()

    // DATATABLE myTableCurrencyRate
    var ajaxOptionsRate = {
        url: "{{ route('api.akunting.setup.mata_uang.rates.list') }}",
        data: function (params)
            {
                params.cid = $("#cid").val()
            },
    }

    var optionsRate = {
        columns:[
                    {
                        data: 'curr_start',
                        name: 'curr_start',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'curr_rate',
                        name: 'curr_rate',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'create_by',
                        name: 'create_by',
                    },
                    {
                        data: 'create_time',
                        name: 'create_time',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'is_aktif',
                        name: 'is_aktif',

                        render: function (data, type, row, meta)
                        {
                            $btnstatus = `<span class="badge badge-` + row.status_css + `">
                                            <i class="bi bi-` + row.status_icon + ` text-light fs-3"></i>
                                            &nbsp;` + row.status_txt + `
                                        </span>`

                            return $btnstatus
                        },

                        className: 'dt-body-center',
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableCurrencyRate',
                ajaxOptionsRate,
                optionsRate
            )

    $('#curr_start').flatpickr({
        defaultDate: null,
        enableTime: true,
        time_24hr: true,
        minuteIncrement: 1,
        dateFormat: "d-m-Y H:i",
    })

    $('#is_aktif').change(function ()
    {
        const $el = $(this)
        const $label = $(`label[for="${$el.attr('id')}"]`)

        if ($el.is(':checked')) $label.text("Aktif")
        else $label.text("Tidak Aktif")
    })

    // aksi submit edit / update
    $('#form-input-currency-rate').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        ResetMoney()

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const $curr_rate = $('#curr_rate')

            if (parseFloat($curr_rate.val()) == 0)
            {
                FormatMoney()

                swalShowMessage('Peringatan', 'Rate Masih 0 Atau Belum Diisi !', 'error')
                $curr_rate.focus()

                return false
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.setup.mata_uang.rates.save') }}"
            
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
                                $('#myTableCurrencyRate').DataTable().ajax.reload()

                                ResetForm()

                                FormatMoney()
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

    var ResetForm = function ()
    {
        $('#form-input-currency-rate #curr_start').val('')

        $('#form-input-currency-rate #curr_rate').val('')
    }
</script>