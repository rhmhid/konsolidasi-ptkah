<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-puzzle-piece text-dark me-4"></span>
        Form Data Setup C.O.A
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
        <form method="post" id="form-input-setup-coa" novalidate>
            <input type="hidden" name="scid" id="scid" value="0" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-12">
                    <label class="text-dark fw-bold fs-7 pb-2 required">C.O.A</label>
                    {!! $cmb_coa !!}
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm rounded-1" name="btn_close_coa" id="btn_close_coa" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" name="btn_save_coa" id="btn_save_coa">
                    <span class="indicator-label">Simpan</span>
                    <span class="indicator-progress">
                        Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>

    <div class="table-responsive">
        <table id="myTableSetupCoa" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
            <thead class="bg-dark text-uppercase fs-7">
                <tr class="fw-bold text-white">
                    <th class="border-start py-5">C.O.A</th>
                    <th class="border-start py-5">User Input</th>
                    <th class="border-start py-5">Waktu Input</th>
                    <th class="border-start py-5">Status</th>
                    <th class="border-start py-5">Fungsi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    var mysctype = '{{ $sctype }}'
    var myUrl_list = "{{ route('api.akunting.setup.master_coa.coa_default.list', ['sctype' => ':sctype']) }}"
        myUrl_list = myUrl_list.replace(':sctype', mysctype)

    $('.modal .form-select').select2({
        width: '100%',
        dropdownParent: $('#popup_form_setup_coa')
    })

    $('#myTableSetupCoa').DataTable().clear().destroy()

    // DATATABLE myTableSetupCoa
    var ajaxOptionsSetupCoa = {
        url: myUrl_list,
    }

    var optionsSetupCoa = {
        columns:[
                    {
                        data: 'coa',
                        name: 'coa',
                    },
                    {
                        data: 'create_by',
                        name: 'create_by',
                    },
                    {
                        data: 'create_time',
                        name: 'create_time',
                    },
                    {
                        data: 'is_aktif',
                        name: 'is_aktif',

                        render: function (data, type, row, meta)
                        {
                            $btnstatus = `<span class="badge badge-${row.status_css}">
                                            <i class="bi bi-${row.status_icon} text-light fs-3"></i>
                                            &nbsp;${row.status_txt}
                                        </span>`

                            return $btnstatus
                        },

                        className: "text-center",
                    },
                    {
                        data: 'scid',
                        name: 'scid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<button class="btn btn-light-dark btn-icon btn-sm rounded-1 coa-update" data-bs-toggle="tooltip" title="Ubah Status" data-scid="${row.scid}">
                                            <i class="la la-edit fs-4"></i>
                                        </button>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: "text-center",
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    tableSetupCoa = setupDataTable(
                '#myTableSetupCoa',
                ajaxOptionsSetupCoa,
                optionsSetupCoa
            )

    // aksi submit edit / update
    $('#form-input-setup-coa').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.setup.master_coa.coa_default.save', ['sctype' => ':sctype']) }}"
            formSubmitUrl = formSubmitUrl.replace(':sctype', mysctype)

            e = document.querySelector("#btn_save_coa")

            e.setAttribute("data-kt-indicator", "on"), e.disabled = !0, setTimeout((function ()
            {
                e.removeAttribute("data-kt-indicator"), e.disabled = !1, doAjax(
                    formSubmitUrl,
                    payload,
                    "POST"
                )
                .done( data => {
                    if (data.success)
                    {
                        Swal.fire('Sukses', data.message, 'success')
                        .then((result) =>
                        {
                            if (result.isConfirmed)
                            {
                                tableSetupCoa.ajax.reload(null, false)

                                ResetForm()
                            }
                        })
                    }
                    else Swal.fire('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    const res = err?.responseJSON
                    Swal.fire('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })

    var ResetForm = function ()
    {
        $('#form-input-setup-coa #sc_coaid').val(null).trigger('change')
    }

    $('#myTableSetupCoa').on('click', '.coa-update', function ()
    {
        const scid = $(this).data('scid')

        myUrl = "{{ route('api.akunting.setup.master_coa.coa_default.update', ['sctype' => ':sctype']) }}"
        myUrl = myUrl.replace(':sctype', mysctype)

        $.ajax({
            url         : myUrl,
            data        : { scid: scid },
            type        : 'POST',

            error       : function (req, stat, err)
                        {
                            Swal.fire('Gagal', 'Gagal update Data', 'error')
                        },

            success     : function (response)
                        {
                            if (response.success)
                            {
                                Swal.fire('Sukses', response.message, 'success')
                                .then((result) =>
                                {
                                    if (result.isConfirmed)
                                        tableSetupCoa.ajax.reload(null, false)
                                })
                            }
                            else Swal.fire('Gagal', res.message || 'Terjadi Kesalahan saat proses response', 'error')
                        },

            async       : false,
            cache       : false
        })
    })
</script>