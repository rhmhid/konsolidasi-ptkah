<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-exchange-alt text-dark me-4"></span>
        Form Input Pemakaian Barang Unit
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
        <form method="post" id="form-input-pb" novalidate>
            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Gudang Pengirim</label>
                    {!! $cmb_gudang_from !!}
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2 required">
                        Gudang Penerima
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Data ini akan mempengaruhi cost transaksi"></i>
                    </label>
                    {!! $cmb_gudang_to !!}
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">
                        C.O.A COST
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Jika C.O.A C COST berbeda dengan kategori barang"></i>
                    </label>
                    {!! $cmb_coa_ciu !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-8">
                    <div class="d-flex flex-column h-100">
                        <label for="keterangan" class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0">{{ $data_head->keterangan }}</textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="text-dark fw-bold fs-7 pb-2">&nbsp;</label>
                    <select id="sCari-Barang" class="form-select form-select-sm rounded-1 w-100" data-control="select2"></select>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold required">
                        Detail Barang
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu Inventori -> Masterdata -> Barang"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-barang">
                        <thead class="bg-dark text-uppercase fs-7 text-center">
                            <tr class="fw-bold text-white">
                                <th></th>
                                <th>Barang</th>
                                <th>Satuan</th>
                                <th>Stok Gudang Pengirim</th>
                                <th>Jumlah Kirim</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-danger btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_simpan">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
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

    $('#sCari-Barang').select2({
        dropdownParent      : $('#mdl-form-pb'),
        placeholder         : '-- Pilih barang',
        minimumInputLength  : 3,
        ajax                :
                            {
                                url             : "{{ route('api.inventori.pemakaian_barang.cari_barang') }}",

                                data            : function (params)
                                                {
                                                    var param = {
                                                        q           : params.term,
                                                        gid         : $('#gid option:selected').val(),
                                                        reff_gid    : $('#reff_gid option:selected').val(),
                                                    }

                                                    // Query parameters will be ?search=[term]&type=public
                                                    return param
                                                },

                                processResults  : function (json)
                                                {
                                                    return {
                                                        results: $.map(json, function (items)
                                                        {
                                                            return {
                                                                id          : items.mbid,
                                                                text        : items.kode_brg + ' - ' + items.nama_brg,
                                                                kode_brg    : items.kode_brg,
                                                                nama_brg    : items.nama_brg,
                                                                kode_satuan : items.kode_satuan,
                                                                all_satuan  : items.all_satuan,
                                                                stock_from  : items.stock_from
                                                            }
                                                        })
                                                    }
                                                },

                                cache           : true,
                            }
    }).on('select2:select', function (e)
    {
        var items = e.params.data

        if (items.id != '')
        {
            var mbidList = window.document.getElementsByName('mbid[]')

            for (var j = 0; j < mbidList.length; j++)
            {
                if (items.id == mbidList[j].value)
                {
                    swalShowMessage("Information", 'Barang ' + items.text + ' Telah Berada Dalam List !', 'warning')

                    $(this).val(null).trigger("change")

                    return false
                }
            }

            AddBarang(items.id, items.text, items.kode_brg, items.nama_brg, items.kode_satuan, items.all_satuan, items.stock_from)

            $(this).val(null).trigger("change")
        }
    }).on('select2:opening', function (e)
    {
        if (!CheckGudang())
        {
            $(this).val(null).trigger("change")
            return false
        }
    })       

    function CheckGudang ()
    {
        var gid = $('#gid option:selected').val()
        var reff_gid = $('#reff_gid option:selected').val()

        if (gid == '')
        {
            swalShowMessage('Information, ', 'Gudang Pengirim Harus Dipilih Dahulu !', 'warning')

            return false
        }

        if (reff_gid == '')
        {
            swalShowMessage('Information, ', 'Gudang Penerima Harus Dipilih Dahulu !', 'warning')

            return false
        }

        return true
    }

    function AddBarang (mbid, barang, kode_brg, nama_brg, kode_satuan, all_satuan, stock_from = 0)
    {
        let satuan = all_satuan.split(';')
        let cmb_satuan = `<select name="kode_satuan[${mbid}]" id="kode-satuan-${mbid}" data-mbid="${mbid}" class="form-select form-select-sm rounded-1 w-100 data-satuan" data-control="select2">`

        $.each(satuan, function (idx, val)
        {
            let data_sat = val.split(':')
            let sel_sat = ''

            if (data_sat[1] == kode_satuan) sel_sat = 'selected=""'

            cmb_satuan += `<option value="${data_sat[1]}" data-isi_kecil="${data_sat[2]}" ${sel_sat}>${data_sat[0]}</option>`
        })

        cmb_satuan += `</select>`

        let data_coa = `<tr class="align-middle">
                            <td class="text-center">
                                <i class="las la-trash fs-2 text-danger text-center hapus-barang" role="button"></i>
                            </td>
                            <td class="text-nowrap">
                                ${barang}
                                <input type="hidden" name="mbid[]" id="mbid[]" value="${mbid}" />
                                <input type="hidden" name="barang[]" id="barang[]" value="${barang}" />
                                <input type="hidden" name="kode_brg[]" id="kode_brg[]" value="${kode_brg}" />
                                <input type="hidden" name="nama_brg[]" id="nama_brg[]" value="${nama_brg}" />
                            </td>
                            <td class="text-nowrap">
                                ${cmb_satuan}
                                <input type="hidden" name="kode_satuan[]" id="kode_satuan[]" value="${kode_satuan}" />
                                <input type="hidden" name="all_satuan[]" id="all_satuan[]" value="${all_satuan}" />
                            </td>
                            <td class="text-center">
                                <font id="lbl-stock-from-${mbid}">${stock_from} ${kode_satuan}</font>
                                <input type="hidden" name="stock_from[]" id="stock-from-${mbid}" value="${stock_from}" />
                            </td>
                            <td class="text-center">
                                <input type="text" name="vol_kirim[]" id="vol-kirim" data-mbid="${mbid}" class="form-control form-control-sm rounded-1 number-only" value="" required="" />
                            </td>
                            <td class="text-nowrap">
                                <input type="text" name="ket_item[]" id="ket_item" class="form-control form-control-sm rounded-1" value="" />
                            </td>
                        </tr>`

        $('#tbl-barang > tbody:last').append(data_coa)

        $('#kode-satuan-' + mbid).select2({minimumResultsForSearch: Infinity})
    }

    $('#tbl-barang').on('click', '.hapus-barang', function ()
    {
        $(this).closest("tr").remove()
    })

    var isi_kecil_pre = ''

    $('#tbl-barang').on('select2:opening', '.data-satuan', function (e)
    {
        isi_kecil_pre = $(this).find('option:selected').data('isi_kecil')

    }).on('select2:select', '.data-satuan', function (e)
    {
        e.preventDefault()

        let mbid = $(this).data('mbid')
        let isi_kecil = $(this).find('option:selected').data('isi_kecil')
        let satuan = $(this).find('option:selected').val().split(':')
        let stock_from = $('#stock-from-' + mbid)

        let konversi_from = (parseFloat(isi_kecil_pre) * parseFloat($(stock_from).val())) / parseFloat(isi_kecil)

        $(stock_from).val(konversi_from)
        $('#lbl-stock-from-' + mbid).text(konversi_from + ' ' + satuan)
    })

    $('#tbl-barang').on('change', '#vol-kirim', function (e)
    {
        let mbid = $(this).data('mbid')
        let vol_kirim = $(this).val()
        let stok_from = $('#stock-from-' + mbid).val()

        if (parseFloat(vol_kirim) > parseFloat(stok_from))
        {
            $(this).val(0)

            swalShowMessage('Information, ', 'Jumlah Kirim Melebihi Stok !', 'warning')

            return false
        }
    })

    $('.select-gudang').on('change', function (e)
    {
        let val_gudang =  $(this).find('option:selected').val()
        let jml_brg = $('#tbl-barang > tbody').find("tr").length

        if (val_gudang == '' && jml_brg > 0)
        {
            $("#tbl-barang > tbody").empty()

            swalShowMessage('Information, ', 'List Barang Yang Sudah Dipilih Akan Terhapus Otomatis, Dikarenakan Gudang Dihapus !', 'warning')
        }
    })

    // aksi submit edit / update
    $('#form-input-pb').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            if (!CheckGudang()) return false

            let jml_brg = $('#tbl-barang > tbody').find("tr").length

            if (jml_brg == 0)
            {
                swalShowMessage("Information", 'Harap Pilih Barang Minimal 1 Data/Baris.', 'warning')

                return
            }

            const list_mbid = document.getElementsByName('mbid[]')
            const barang = document.getElementsByName('barang[]')
            const vol_kirim = document.getElementsByName('vol_kirim[]')
            const stock_from = document.getElementsByName('stock_from[]')

            for (var i = 0; i < list_mbid.length; i++)
            {
                if (parseFloat(vol_kirim[i].value) == '')
                {
                    swalShowMessage('Peringatan', 'Jumlah Kirim ' + barang[i].value + ' Belum Diisi/Tidak Boleh 0', 'warning')

                    return false
                }

                if (parseFloat(vol_kirim[i].value) > parseFloat(stock_from[i].value))
                {
                    swalShowMessage('Information, ', 'Jumlah Kirim ' + barang[i].value + ' Melebihi Stok !', 'warning')

                    return false
                }
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.inventori.pemakaian_barang.save') }}"

            showLoading()

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
                                $("#mdl-form-pb").modal('hide')

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
    })
</script>
