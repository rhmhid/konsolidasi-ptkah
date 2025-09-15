<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-prescription-bottle text-dark me-4"></span>
        Form Input Masterdata Barang
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
        <form method="post" id="form-input-barang" novalidate>
            <input type="hidden" name="mbid" id="mbid" value="{{ $data_brg->mbid }}" />

            <!--begin::Compact form-->
            <div class="row g-5 gx-4">
                <div class="col-lg-6">
                    <div class="row g-4 mb-3">
                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Kategori Barang</label>
                            {!! $cmb_kel_brg !!}
                        </div>

                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">
                                Kode Barang
                                <i class="fas fa-exclamation-circle ms-2 text-hover-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Generate Otomatis Ketika Simpan Berdasarkan Default Kode Barang Kategorinya"></i>
                            </label>
                            <input type="text" name="kode_brg" id="kode_brg" value="{{ $data_brg->kode_brg }}" class="form-control form-control-sm rounded-1 w-100" readonly="" maxlength="10" placeholder="Generate Otomatis Ketika Simpan" />
                        </div>
                    </div>

                    <div class="row g-4 mb-3">
                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Nama Barang</label>
                            <input type="text" name="nama_brg" id="nama_brg" value="{{ $data_brg->nama_brg }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                        </div>

                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Nama Barang ( Tagihan )</label>
                            <input type="text" name="nama_brg_bill" id="nama_brg_bill" value="{{ $data_brg->nama_brg_bill }}" class="form-control form-control-sm rounded-1 w-100" required="" />
                        </div>
                    </div>

                    <div class="row g-4 mb-3">
                        <div class="col-lg-9">
                            <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                            <input type="text" name="keterangan" id="keterangan" value="{{ $data_brg->keterangan }}" class="form-control form-control-sm rounded-1 w-100" />
                        </div>

                        <div class="col-lg-3">
                            <label class="form-label form-label-sm text-dark">Status Barang</label>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" name="is_aktif" type="checkbox" id="is_aktif" value="t" {{ $chk_aktif }} />
                                <label class="form-check-label fw-bold" for="is_aktif">{{ $txt_aktif }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-4">
                            <label class="d-block text-dark fs-7 fw-bold mb-2 required">
                                Harga Dasar (HNA)
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Harga Dasar / Beli Barang Dari Penerimaan"></i>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control form-control-sm rounded-1 currency calc-harga" name="hna" id="hna" value="{{ $data_brg->hna }}" required="" />
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <label class="d-block text-dark fs-7 fw-bold mb-2">
                                PPN HNA
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Nominal Ini Akan Menjadi Acuan Harga Standar Di Setup Harga Jual"></i>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm rounded-1 currency calc-harga" name="persen_hna" id="persen_hna" value="{{ $data_brg->persen_hna }}" maxlength="3" data-precision="0" />
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label class="d-block text-dark fs-7 fw-bold mb-2 required">HNA + PPN</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control form-control-sm rounded-1 currency" name="hna_ppn" id="hna_ppn" value="{{ $data_brg->hna_ppn }}" readonly="" required="" />
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <label class="d-block text-dark fs-7 fw-bold mb-2">
                                PPN Jual
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Konfigurasi PPN Keluaran Untuk Penjualan, Untuk Config Apakah PPN Menambah Margin Jual Atau Tidak, Silahkan Cek Control Panel"></i>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm rounded-1 currency" name="ppn_jual" id="ppn_jual" value="{{ $data_brg->ppn_jual }}" maxlength="3" data-precision="0" />
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="row g-4 mb-3">
                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2">Merk</label>
                            {!! $cmb_merk !!}
                        </div>

                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Satuan Default</label>
                            {!! $cmb_satuan !!}
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-12">
                            <label class="d-block text-dark fs-7 fw-bold required">
                                Input satuan dari yang terkecil
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu Inventory -> Master Data -> Data Satuan"></i>
                            </label>

                            <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="table_satuan">
                                <thead class="bg-dark text-uppercase fs-7 text-center">
                                    <tr class="fw-bold text-white">
                                        <th>Satuan Besar</th>
                                        <th>Isi Kecil</th>
                                        <th>Aktif</th>
                                        <th>Fungsi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end">
                                            <button type="button" class="btn btn-sm btn-dark rounded-1 add-satuan">
                                                <i class="fa fa-plus"></i>
                                                Tambah
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-light-dark btn-sm rounded-1" id="btn_batal_barang" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="btn_save_barang">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    {!! $adds !!}

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

    /*$('#kode_brg').click(function ()
    {
        var mbid = $('#mbid').val()

        Swal.fire({
            title: "Masukkan Kode",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Cek Kode",
            showLoaderOnConfirm: true,

            preConfirm: async (kode_brg) => {
                try {
                    if (!kode_brg)
                    {
                        const message = 'Kode Belum Diisi'

                        return Swal.showValidationMessage(await message)
                    }

                    let link = "{{ route('api.inventori.master_data.barang.cek_kode', ['kode' => ':kode_brg']) }}"
                        link = link.replace(':kode_brg', kode_brg)

                    const response = await $.ajax({
                        url         : link,
                        data        : { jenis: 'barang', id: mbid },
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
    })*/

    $('#kode_satuan').on('select2:opening select2:clearing select2:unselecting', function ()
    {
        const kode_satuan = $(this).val()
        const row = $('#table_satuan > tbody > tr').length

        if (row > 0)
        {
            swalShowMessage('Information', 'Sudah ada satuan dalam list, tidak bisa ubah satuan default.', 'info')
            return false
        }
    })

    $('#is_aktif').change(function ()
    {
        const $el = $(this)
        const $label = $(`label[for="${$el.attr('id')}"]`)

        if ($el.is(':checked')) $label.text("Aktif")
        else $label.text("Tidak Aktif")
    })

    $('.calc-harga').on('change', function (e)
    {
        e.preventDefault()

        ResetMoney()

        let $hna = $('#hna').val()
        let $persen_hna = $('#persen_hna').val()

        let $hna_ppn = parseFloat($hna) * ((parseFloat($persen_hna) + 100) / 100)

        $('#hna_ppn').val($hna_ppn)

        FormatMoney()
    })

    $('.add-satuan').on('click', function (e)
    {
        e.preventDefault()

        let $satuan_default = $('#kode_satuan').val()

        if ($satuan_default == '' || $satuan_default === null)
        {
            swalShowMessage('Peringatan', 'Harap pilih satuan default terlebih dahulu.', 'info')
            return false
        }

        $.ajax({
            url         : "{{ route('api.inventori.master_data.barang.barang_satuan') }}",
            data        : { },
            type        : 'GET',

            error       : function (req, stat, err)
                        {
                            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
                        },

            success     : function (response)
                        {
                            openModal('popup_satuan_barang', response, null, true)
                        },

            async       : false,
            cache       : false
        })
    })

    function AddSatuan (kode_satuan, satuan, isikecil = '', is_aktif = '', txt_aktif = '', used = '', ksid = 0)
    {
        let kode_satuan_default = $('#kode_satuan').val()
        let satuan_default = $("#kode_satuan option:selected").text()

        if (kode_satuan == kode_satuan_default)
        {
            swalShowMessage('Peringatan', 'Satuan besar tidak boleh sama dengan satuan kecil.', 'info')
            return false
        }

        let list_kode_satuan = document.getElementsByName('kode_satuan_add[]')
        let mychecked = is_aktif == 't' ? 'checked=""' : ''

        for (var i = 0; i < list_kode_satuan.length; i++)
        {
            if (kode_satuan == list_kode_satuan[i].value)
            {
                swalShowMessage('Peringatan', 'Satuan sudah berada dalam list.', 'info')
                return false
            }
        }

        let iconDel = used == '' ? '<i class="las la-trash fs-2 text-danger text-center hapus-satuan" role="button"></i>' : ''
        let readOnly = used != '' ? 'readonly=""' : ''
        let solid = used != '' ? 'form-control-solid' : ''

        let barang_satuan = `<tr class="align-middle">
                                <td class="text-center">
                                    ${satuan}
                                    <input type="hidden" name="kode_satuan_add[]" value="${kode_satuan}" />
                                    <input type="hidden" name="ksid[]" value="${ksid}" />
                                </td>
                                <td class="text-nowrap">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm ${solid} w-50px fs-8 rounded-1 currency" name="isikecil[]" id="isikecil_${kode_satuan}" value="${isikecil}" onChange="fn_sat_besar(this.value, \'${kode_satuan}\')" required="" ${readOnly} data-precision="0" />
                                        <span class="input-group-text info-satuan-kecil">${satuan_default}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" name="is_aktif_satuan[]" type="checkbox" id="is_aktif_satuan_${kode_satuan}" value="t" ${mychecked} />
                                        <label class="form-check-label fw-bold" for="is_aktif_satuan">${txt_aktif}</label>
                                    </div>
                                </td>
                                <td class="text-center">${iconDel}</td>
                            </tr>`

        $('#table_satuan > tbody:last').append(barang_satuan)
    }

    function fn_sat_besar (x, satuan)
    {
        if (x == '1')
        {
            swalShowMessage('Invalid Data!', 'Jumlah Satuan Besar tidak boleh sama dengna isi satuan kecil', 'warning')

            $("#isi_kecil_" + satuan).val(0)
            $("#isi_kecil_" + satuan).focus()
        }
    }

    $('#table_satuan').on('click', '.hapus-satuan', function ()
    {
        $(this).closest("tr").remove()

        if (document.getElementsByName('kode_satuan_add[]').length > 0)
        {
            let satuan = document.getElementsByName('kode_satuan_add[]')[0].value

            $('.info-satuan-kecil').text(satuan)
        }
    })

    // aksi submit edit / update
    $('#form-input-barang').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            ResetMoney()

            const mbid = $('#mbid').val()
            const hna = $('#hna').val()
            const list_kode_satuan = document.getElementsByName('kode_satuan_add[]')
            const isikecil = document.getElementsByName('isikecil[]')

            if (parseFloat(hna) < 1)
            {
                FormatMoney()

                swalShowMessage('Peringatan', 'HNA belum diisi atau masih 0.', 'warning')

                return false
            }

            for (var i = 0; i < list_kode_satuan.length; i++)
            {
                isikecil[i].value = isNaN(parseFloat(isikecil[i].value)) ? 0 : parseFloat(isikecil[i].value)

                if (parseFloat(isikecil[i].value) < 1)
                {
                    FormatMoney()

                    swalShowMessage('Peringatan', 'Isi kecil belum diisi atau masih 0.', 'warning')

                    return false
                }
            }

            Swal.fire({
                html: 'Pastikan inputan sudah sesuai, lanjutkan untuk simpan ?',
                icon: "info",
                buttonsStyling: false,
                showCancelButton: true,
                confirmButtonText: "Ya, Simpan Data !",
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: "btn btn-dark",
                    cancelButton: 'btn btn-danger'
                }
            }).then((result) =>
            {
                if (result.isConfirmed)
                {
                    if (mbid == 0) saveData(this)
                    else
                    {
                        const mbid = $('#mbid').val()
                        const kode_brg = $('#kode_brg').val()
                        const nama_brg = $('#nama_brg').val()

                        let notes = 'Perubahan Master Data Barang, ID : ' + mbid + ' - Kode : ' + kode_brg + ' - Nama : ' + nama_brg

                        modalAuth (1, 'form-input-barang', notes)
                    }
                }
            })

            return false
        }
    })

    function saveData (form, alasan = '')
    {
        const payload = new FormData(form)
            payload.append('_method', 'patch') // ganti ajax method post menjadi patch
            payload.set('alasan', alasan) // ganti ajax method post menjadi patch

        formSubmitUrl = "{{ route('api.inventori.master_data.barang.save') }}"

        if (alasan == '') showLoading()

        setTimeout((function ()
        {
            doAjax(
                formSubmitUrl,
                payload,
                "POST"
            )
            .done( data => {
                Swal.close()

                if (data.success)
                {
                    swalShowMessage('Sukses', data.message, 'success')
                    .then((result) =>
                    {
                        if (result.isConfirmed)
                        {
                            $("#popup_form_barang").modal('hide')

                            table.ajax.reload(null, false)
                        }
                    })
                }
                else swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
            })
            .fail( err => {
                Swal.close()

                const res = err?.responseJSON

                swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
            })
        }), 2e3)
    }

    function NextStep (form, alasan)
    {
        saveData(form, alasan)
    }
</script>