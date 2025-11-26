<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-code-branch text-dark me-4"></span>
        Form Input Wilayah Cabang
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
        <form method="post" id="form-input-wilayah-branch" novalidate>
            <input type="hidden" name="bwid" id="bwid" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Kode Wilayah</label>
                    <input type="text" name="kode_wilayah" id="kode_wilayah" class="form-control form-control-sm rounded-1 w-100" required="" readonly="" />
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Nama Wilayah</label>
                    <input type="text" name="nama_wilayah" id="nama_wilayah" class="form-control form-control-sm rounded-1 w-100" required="" />
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
        <table id="myTableWilayahBranch" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
            <thead class="bg-dark text-uppercase fs-7">
                <tr class="fw-bold text-white">
                    <th class="border-start py-5">Kode Wilayah</th>
                    <th class="border-start py-5">Nama Wilayah</th>
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
    var $KodeWilayah = ''

    $('#myTableWilayahBranch').DataTable().clear().destroy()

    // DATATABLE myTableWilayahBranch
    var ajaxOptionsWilayah = {
        url: "{{ route('api.master_data.database.cabang.list', ['type' => 'wilayah']) }}",
    }

    var optionsWilayah = {
        columns:[
                    {
                        data: 'kode_wilayah',
                        name: 'kode_wilayah',
                    },
                    {
                        data: 'nama_wilayah',
                        name: 'nama_wilayah',
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
                        data: 'bwid',
                        name: 'bwid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-bwid="${row.bwid}" title="Ubah" class="btn btn-dark btn-sm fs-8 edit-wilayah">
                                            <i class="las la-edit"></i> Ubah
                                        </a>

                                        <input type="hidden" name="mykode_wilayah[]" value="${row.kode_wilayah}" />
                                        <input type="hidden" name="mynama_wilayah[]" value="${row.nama_wilayah}" />
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

    tableWilayah = setupDataTable(
                        '#myTableWilayahBranch',
                        ajaxOptionsWilayah,
                        optionsWilayah
                    )

    $('#kode_wilayah').click(function ()
    {
        var bwid = $('#bwid').val()

        Swal.fire({
            title: "Masukkan Kode",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Kode",
            showLoaderOnConfirm: true,

            preConfirm: async (kode_wilayah) => {
                try {
                    if (!kode_wilayah)
                    {
                        const message = 'Kode Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.master_data.database.cabang.cek_kode', ['type' => 'wilayah', 'kode' => ':kode_wilayah']) }}"
                        link = link.replace(':kode_wilayah', kode_wilayah)

                    const response = await $.ajax({
                        url         : link,
                        data        : { id: bwid },
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
    $('#form-input-wilayah-branch').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.master_data.database.cabang.save', ['type' => 'wilayah']) }}"

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
                                tableWilayah.ajax.reload(null, false)

                                ResetForm()

                                $KodeWilayah = ''
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

    $('#myTableWilayahBranch').on('click', '.edit-wilayah', function ()
    {
        const bwid = $(this).closest("a").data('bwid')
        const kode_wilayah = $(this).siblings('input[name="mykode_wilayah[]"]').val()
        const nama_wilayah = $(this).siblings('input[name="mynama_wilayah[]"]').val()
        let is_aktif = $(this).siblings('input[name="myis_aktif[]"]').val()

        $('#form-input-wilayah-branch #bwid').val(bwid)

        $KodeWilayah = kode_wilayah

        $('#form-input-wilayah-branch #kode_wilayah').val(kode_wilayah)

        $('#form-input-wilayah-branch #nama_wilayah').val(nama_wilayah)

        let chk_aktif = is_aktif == 't' ? true : false

        $('#form-input-wilayah-branch #is_aktif').prop('checked', chk_aktif).trigger('change')
    })

    var ResetForm = function ()
    {
        $('#form-input-wilayah-branch #kode_wilayah').val('')

        $('#form-input-wilayah-branch #nama_wilayah').val('')
    }
</script>