<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-code-branch text-dark me-4"></span>
        Form Input Tipe Cabang
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
        <form method="post" id="form-input-tipe-branch" novalidate>
            <input type="hidden" name="btid" id="btid" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Tipe</label>
                    <input type="text" name="kode_tipe" id="kode_tipe" class="form-control form-control-sm rounded-1 w-100" required="" readonly="" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Tipe</label>
                    <input type="text" name="nama_tipe" id="nama_tipe" class="form-control form-control-sm rounded-1 w-100" required="" />
                </div>

                <div class="col-lg-3">
                    <label class="form-label form-label-sm text-dark">Status Aktif</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" checked="" />
                        <label class="form-check-label fw-bold" for="is_aktif">Aktif</label>
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
        <table id="myTableTipeBranch" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
            <thead class="bg-dark text-uppercase fs-7">
                <tr class="fw-bold text-white">
                    <th class="border-start py-5">Kode Tipe</th>
                    <th class="border-start py-5">Nama Tipe</th>
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
    var $KodeTipe = ''

    $('#myTableTipeBranch').DataTable().clear().destroy()

    // DATATABLE myTableTipeBranch
    var ajaxOptionsTipe = {
        url: "{{ route('api.master_data.database.cabang.list', ['type' => 'tipe']) }}",
    }

    var optionsTipe = {
        columns:[
                    {
                        data: 'kode_tipe',
                        name: 'kode_tipe',
                    },
                    {
                        data: 'nama_tipe',
                        name: 'nama_tipe',
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
                        data: 'btid',
                        name: 'btid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-btid="${row.btid}" title="Ubah" class="btn btn-dark btn-sm fs-8 edit-tipe">
                                            <i class="las la-edit"></i> Ubah
                                        </a>

                                        <input type="hidden" name="mykode_tipe[]" value="${row.kode_tipe}" />
                                        <input type="hidden" name="mynama_tipe[]" value="${row.nama_tipe}" />
                                        <input type="hidden" name="myis_aktif[]" value="${row.is_aktif}" />`

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

    tableTipe = setupDataTable(
                        '#myTableTipeBranch',
                        ajaxOptionsTipe,
                        optionsTipe
                    )

    $('#kode_tipe').click(function ()
    {
        var btid = $('#btid').val()

        Swal.fire({
            title: "Masukkan Kode",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Kode",
            showLoaderOnConfirm: true,

            preConfirm: async (kode_tipe) => {
                try {
                    if (!kode_tipe)
                    {
                        const message = 'Kode Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.master_data.database.cabang.cek_kode', ['type' => 'tipe', 'kode' => ':kode_tipe']) }}"
                        link = link.replace(':kode_tipe', kode_tipe)

                    const response = await $.ajax({
                        url         : link,
                        data        : { id: btid },
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
    $('#form-input-tipe-branch').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.master_data.database.cabang.save', ['type' => 'tipe']) }}"
            
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
                                tableTipe.ajax.reload(null, false)

                                ResetForm()

                                $KodeTipe = ''
                            }
                        })
                    }
                    else swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    const res = err?.responseJSON
                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })

    $('#myTableTipeBranch').on('click', '.edit-tipe', function ()
    {
        const btid = $(this).closest("a").data('btid')
        const kode_tipe = $(this).siblings('input[name="mykode_tipe[]"]').val()
        const nama_tipe = $(this).siblings('input[name="mynama_tipe[]"]').val()
        let is_aktif = $(this).siblings('input[name="myis_aktif[]"]').val()

        $('#form-input-tipe-branch #btid').val(btid)

        $KodeTipe = kode_tipe

        $('#form-input-tipe-branch #kode_tipe').val(kode_tipe)

        $('#form-input-tipe-branch #nama_tipe').val(nama_tipe)

        let chk_aktif = is_aktif == 't' ? true : false

        $('#form-input-tipe-branch #is_aktif').prop('checked', chk_aktif).trigger('change')
    })

    var ResetForm = function ()
    {
        $('#form-input-tipe-branch #kode_tipe').val('')

        $('#form-input-tipe-branch #nama_tipe').val('')
    }
</script>