
<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-dolly text-dark me-4"></span>
        Form Input Penerimaan Barang
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
            <input type="hidden" name="grid" id="grid" value="{{ $data_head->grid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Penerimaan</label>
                    <div class="input-group">
                        <input type="text" name="grdate" id="grdate" value="{{ $data_head->grdate }}" class="form-control form-control-sm rounded-1" readonly="" required="" />
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Gudang Penerima</label>
                    {!! $cmb_gudang !!}
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Jatuh Tempo</label>
                    <div class="input-group">
                        <input type="text" name="duedate" id="duedate" value="{{ $data_head->duedate }}" class="form-control form-control-sm rounded-1 mydate" readonly="" required="" />
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">No. Faktur / Surat Jalan</label>
                    <input type="text" name="no_faktur" id="no_faktur" value="{{ $data_head->no_faktur }}" class="form-control form-control-sm rounded-1" required="" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Asal Barang</label>
                    <div class="btn-group w-100" role="group">
                        <!--input type="radio" name="asal_brg" class="btn-check" id="asal_brg_1" value="1" {{ $chk_asal_brg1 }} />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="asal_brg_1">Dengan PO</label -->

                        <input type="radio" name="asal_brg" class="btn-check" id="asal_brg_2" value="2" {{ $chk_asal_brg2 }} />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="asal_brg_2">Tanpa PO</label>
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Jenis Barang</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" name="is_medis" class="btn-check" id="is_medis_t" value="t" {{ $chk_is_medis_t }}/>
                        <label class="btn btn-sm btn-light-dark rounded-1" for="is_medis_t">Medis</label>

                        <input type="radio" name="is_medis" class="btn-check" id="is_medis_f" value="f" {{ $chk_is_medis_f }}/>
                        <label class="btn btn-sm btn-light-dark rounded-1" for="is_medis_f">Non Medis</label>
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Cara Pembelian</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" name="cara_beli" class="btn-check" id="cara_beli_1" value="1" {{ $chk_cara_beli1 }} />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="cara_beli_1">Cash</label>

                        <input type="radio" name="cara_beli" class="btn-check" id="cara_beli_2" value="2" {{ $chk_cara_beli2 }} />
                        <label class="btn btn-sm btn-light-dark rounded-1" for="cara_beli_2">Credit</label>
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Supplier</label>
                    {!! $cmb_supplier !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <div class="d-flex flex-column h-100">
                        <label for="keterangan" class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0">{{ $data_head->keterangan }}</textarea>
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Faktur</label>
                    <div class="input-group">
                        <input type="text" name="tgl_faktur" id="tgl_faktur" value="{{ $data_head->tgl_faktur }}" class="form-control form-control-sm rounded-1 mydate" readonly="" required="" />
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>

                @if ($data_head->grid == 0)
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">&nbsp;</label>
                    <select id="Cari-Data" class="form-select form-select-sm rounded-1 w-100" data-control="select2"></select>
                </div>
                @endif
            </div>

            <div class="row g-0 gx-4 mt-3 div-cash">
                <div class="col-lg-3">
                    <label class="text-dark fw-bold fs-7 pb-2">Kas / Bank</label>
                    {!! $cmb_bank !!}
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold required">
                        Detail Barang
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu Inventori -> Masterdata -> Barang"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-barang">
                        <thead class="bg-dark text-uppercase fs-7 text-center fw-bold text-white">
                            <tr>
                                <th>&nbsp;</th>
                                <th>Barang</th>
                                <th>Satuan</th>
                                <th>Exp Date</th>
                                <th>No. Batch</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Diskon (%)</th>
                                <th>Diskon (Rp.)</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td class="fw-bold text-end" colspan="8">Diskon Faktur</td>
                                <td colspan="2">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm w-10px fs-8 rounded-1 currency calc-tot" mytype="persen_disc" name="diskon" id="diskon" value="{{ $data_head->diskon_final_persen }}" />
                                        <span class="input-group-text">%</span>
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-80px fs-8 rounded-1 currency calc-tot" name="diskon_rp" id="diskon_rp" value="{{ $data_head->diskon_final }}" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="8">Total + Diskon</td>
                                <td colspan="2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-tot" name="total" id="total" value="{{ $data_head->subtot }}" readonly="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="8">Ongkos Kirim</td>
                                <td colspan="2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-tot" name="ongkir" id="ongkir" value="{{ $data_head->ongkir }}" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="8">Materai</td>
                                <td colspan="2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-tot" name="materai" id="materai" value="{{ $data_head->materai }}" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="8">PPn</td>
                                <td colspan="2">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm w-10px fs-8 rounded-1 currency calc-tot" mytype="persen" name="ppn" id="ppn" value="{{ $data_head->ppn }}" />
                                        <span class="input-group-text">%</span>
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-80px fs-8 rounded-1 currency calc-tot" name="ppn_rp" id="ppn_rp" value="{{ $data_head->ppn_rp }}" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="8">Other Cost</td>
                                <td colspan="2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-tot" name="other_cost" id="other_cost" value="{{ $data_head->other_cost }}" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-end" colspan="8">Total (Rp.)</td>
                                <td colspan="2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-tot" name="totalall" id="totalall" value="{{ $data_head->totalall }}" />
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
                <button type="button" class="btn btn-danger btn-sm rounded-1 w-100 w-md-auto" id="btn-batal" data-bs-dismiss="modal">
                    <i class="las la-undo"></i> Batal
                </button>

                @if ($data_head->grid == 0)
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto ms-md-auto">
                    <button type="submit" class="btn btn-dark btn-sm rounded-1 w-100 w-md-auto" id="btn-simpan">
                        <i class="las la-save"></i> Simpan
                    </button>
                </div>
                @endif
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    {!! $AddBarang !!}

    initDatePicker()

    initToolTip()

    function initDatePicker ()
    {
        $('.mydate').flatpickr({
            defaultDate: null,
            dateFormat: "d-m-Y",
        })
    }

    function initToolTip ()
    {
        $('.modal [data-bs-toggle="tooltip"]').tooltip({
            trigger: 'hover'
        })
    }

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

    HideDivCash()

    $("[name='cara_beli']").change(function ()
    {
        HideDivCash()
    })

    function HideDivCash ()
    {
        let cara_beli = $('[name="cara_beli"]:checked').val()

        if (cara_beli == 1) $('.div-cash').show()
        else $('.div-cash').hide()
    }

    $("[name='asal_brg'], [name='is_medis']").change(function ()
    {
       // ResetTable()
    })

    function ResetTable ()
    {
        $('#tbl-barang tbody').empty()

        calcTotal()
    }

    $('#Cari-Data').select2({
        dropdownParent      : $('#mdl-form-pb'),
        placeholder         : 'Cari Data...',
        minimumInputLength  : 3,
        ajax                :
                            {
                                url             : "{{ route('api.pembelian.penerimaan_barang.cari_barang') }}",

                                data            : function (params)
                                                {
                                                    var param = {
                                                        q               : params.term,
                                                        is_medis    : $('[name="is_medis"]:checked').val()
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
                                                                hna         : items.hna
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
            /*var mbidList = window.document.getElementsByName('mbid[]')

            for (var j = 0; j < mbidList.length; j++)
            {
                if (items.id == mbidList[j].value)
                {
                    swalShowMessage("Information", 'Transaksi ' + items.text + ' Telah Berada Dalam List !', 'warning')

                    $(this).val(null).trigger("change")

                    return false
                }
            }*/

            AddBarang(items.id, items.text, items.kode_brg, items.nama_brg, items.kode_satuan, items.all_satuan, items.hna, MoneyFormat(items.hna))

            $(this).val(null).trigger("change")
        }
    }).on('select2:opening', function (e)
    {
        let is_medis = $('[name="is_medis"]:checked').val() || ''

        if (is_medis === '')
        {
            swalShowMessage('Information, ', 'Jenis Barang Harap Dipilih Dahulu !', 'warning')

            return false
        }
    })

    function AddBarang (mbid, barang, kode_brg, nama_brg, kode_satuan, all_satuan, harga_dasar = 0, harga = 0, is_bonus = '', exp_date = '', no_batch = '', jml_terima = 0, disc = 0, disc_rp = 0, subtotal = 0, grdid = 0)
    {
        let rowId = $('#tbl-barang > tbody > tr').length

        let satuan = all_satuan.split(';')
        let cmb_satuan = `<select name="kode_satuan[]" id="kode-satuan-${rowId}" class="form-select form-select-sm rounded-1 w-100 data-satuan" data-control="select2">`

        $.each(satuan, function (idx, val)
        {
            let data_sat = val.split(':')
            let sel_sat = (data_sat[1] == kode_satuan) ? 'selected' : ''

            cmb_satuan += `<option value="${data_sat[1]}" data-isi_kecil="${data_sat[2]}" ${sel_sat}>${data_sat[0]}</option>`
        })

        cmb_satuan += `</select>`

        let chk_bonus = is_bonus == 't' ? 'checked=""' : ''

        // B: Default Exp Date untuk barang umum
        let $grid_trans = $('#grid').val() ?? 0
        let $is_medis_trans = $('[name="is_medis"]:checked').val() || ''

        if ($grid_trans == 0 && $is_medis_trans == 'f') exp_date = '30-12-2040'
        // e: Default Exp Date untuk barang umum

        let data_barang = `<tr class="align-middle">
                            <td class="text-center">
                                <i class="las la-trash fs-2 text-danger text-center hapus-barang ms-2" role="button"></i>
                            </td>
                            <td class="text-nowrap">
                                ${barang}
                                <input type="hidden" name="grdid[]" value="${grdid}" />
                                <input type="hidden" name="mbid[]" value="${mbid}" />
                                <input type="hidden" name="barang[]" value="${barang}" />
                                <input type="hidden" name="kode_brg[]" value="${kode_brg}" />
                                <input type="hidden" name="nama_brg[]" value="${nama_brg}" />

                                <span>
                                    <div class="parent-check mt-3">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input parent-check-input is-bonus" type="checkbox" name="is_bonus[${rowId}]" id="is-bonus-${rowId}" value="t" ${chk_bonus} />
                                            <label class="form-check-label fs-7" for="is-bonus-${rowId}">
                                                Barang Bonus
                                                <i class="fas fa-question-circle ms-2 text-hover-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Jika Bonus Maka Akan Mempengaruhi Nilai Persediaan"></i>
                                            </label>
                                        </div>
                                    </div>
                                </span>
                            </td>
                            <td class="text-nowrap">
                                ${cmb_satuan}
                            </td>
                            <td class="text-nowrap text-center">
                                <div class="input-group">
                                    <input type="text" name="exp_date[]" id="exp-date" value="${exp_date}" class="form-control form-control-sm rounded-1 mydate" readonly="" required="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </td>
                            <td class="text-nowrap text-center">
                                <input type="text" name="no_batch[]" id="no-batch" class="form-control form-control-sm rounded-1" value="${no_batch}" required="" />
                            </td>
                            <td class="text-center">
                                <input type="text" name="jml_terima[]" id="jml-terima-${rowId}" class="form-control form-control-sm rounded-1 number-only calc-subtot" value="${jml_terima}" required="" />
                            </td>
                            <td class="text-nowrap text-end">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="harga_grn[]" id="harga-grn-${rowId}" data-hna="${harga_dasar}" value="${harga}" class="form-control form-control-sm rounded-1 currency calc-subtot" required="" />
                                </div>

                                <br /><I class="text-danger fw-semibold">Harga Master : Rp. <font name="txt-hrg-lama[]" id="txt-hrg-lama-${rowId}">${MoneyFormat(harga_dasar)}</font></I>
                                <input type="hidden" name="harga_dasar[]" id="harga-dasar-${rowId}" value="${harga_dasar}" class="form-control form-control-sm rounded-1" readonly="" />
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="disc[]" id="disc-${rowId}" value="${disc}" class="form-control form-control-sm rounded-1 currency calc-subtot" mytype="persen" />
                                    <span class="input-group-text">%</span>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="disc_rp[]" id="disc-rp-${rowId}" value="${disc_rp}" class="form-control form-control-sm rounded-1 currency calc-subtot" />
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="subtotal[]" id="subtotal-${rowId}" value="${subtotal}" class="form-control form-control-sm rounded-1 currency calc-subtot subtot-barang" readonly="" />
                                </div>
                            </td>
                        </tr>`

        $('#tbl-barang > tbody:last').append(data_barang)

        // reinit plugin:
        $(`#kode-satuan-${rowId}`).select2({ minimumResultsForSearch: Infinity })

        $('[data-bs-toggle="tooltip"]').tooltip()
    
        initDatePicker()
    }

    $('#tbl-barang').on('click', '.hapus-barang', function ()
    {
        $(this).closest("tr").remove()

        // RegenerateRowId()
    })

    $('#tbl-barang').on('change', '.is-bonus', function ()
    {
        ResetMoney()

        let $tr = $(this).closest("tr")
        let $kode_satuan = $tr.find('[name="kode_satuan[]"]')
        let $harga_grn = $tr.find('[name="harga_grn[]"]')
        let $harga_dasar = $tr.find('[name="harga_dasar[]"]')
        let $disc = $tr.find('[name="disc[]"]')
        let $disc_rp = $tr.find('[name="disc_rp[]"]')

        if ($(this).is(':checked'))
        {
            $harga_grn.val(0)
            $disc.val(0)
            $disc_rp.val(0)

            $harga_grn.prop('readonly', true);
            $disc.prop('readonly', true);
            $disc_rp.prop('readonly', true);
        }
        else
        {
            $harga_grn.val(parseFloat($harga_dasar.val()))

            $harga_grn.prop('readonly', false);
            $disc.prop('readonly', false);
            $disc_rp.prop('readonly', false);
        }

        FormatMoney()

        $harga_grn.trigger('change')
    })

    $('#tbl-barang').on('change', '.calc-subtot', function ()
    {
        ResetMoney()

        let $tr = $(this).closest("tr")
        let $jml_terima = parseFloat($tr.find('[name="jml_terima[]"]').val()) || 0
        let $harga_grn = parseFloat($tr.find('[name="harga_grn[]"]').val()) || 0
        let mytype = $(this).attr('mytype')
        let $disc = $tr.find('[name="disc[]"]')
        let $disc_rp = $tr.find('[name="disc_rp[]"]')
        let $subtotal = $tr.find('[name="subtotal[]"]')

        if (mytype == 'persen')
        {
            var $diskon_persen = parseFloat($disc.val()) || 0
            var $diskon_rp = (parseFloat($harga_grn) * parseFloat($diskon_persen)) / 100

            $disc_rp.val(parseFloat($diskon_rp))
        }
        else
        {
            var $diskon_rp = parseFloat($disc_rp.val()) || 0
            var $diskon_persen = (parseFloat($diskon_rp) / parseFloat($harga_grn)) * 100

            $disc.val(parseFloat($diskon_persen))
        }

        let $subtot = (parseFloat($harga_grn) - parseFloat($diskon_rp)) * parseFloat($jml_terima)

        $subtotal.val($subtot)

        FormatMoney()

        calcTotal()
    })

    $('#tbl-barang').on('change', '.calc-tot', function ()
    {
        let mytype = $(this).attr('mytype')

        calcTotal(mytype)
    })

    function calcTotal (mytype = '')
    {
        ResetMoney()

        let $listAmount = $('#tbl-barang .subtot-barang')
        let $totAmount = 0

        let $diskon = $('#diskon')
        let $diskon_rp = $('#diskon_rp')
        let $ongkir = parseFloat($('#ongkir').val()) || 0
        let $materai = parseFloat($('#materai').val()) || 0
        let $ppn = $('#ppn')
        let $ppn_rp = $('#ppn_rp')
        let $other_cost = parseFloat($('#other_cost').val()) || 0

        $listAmount.each(function ()
        {
            const $field = $(this)
            const val = isNaN(parseFloat($field.val())) ? 0 : parseFloat($field.val())

            $totAmount = parseFloat($totAmount) + parseFloat(val)
        })

        if (mytype == 'persen_disc')
        {
            var $disc_persen = parseFloat($diskon.val()) || 0
            var $disc_rp = (parseFloat($totAmount) * parseFloat($disc_persen)) / 100

            $diskon_rp.val(parseFloat($disc_rp))
        }
        else
        {
            var $disc_rp = parseFloat($diskon_rp.val()) || 0
            var $disc_persen = (parseFloat($disc_rp) / parseFloat($totAmount)) * 100

            $diskon.val(parseFloat($disc_persen))
        }

        $totAmount = parseFloat($totAmount) - parseFloat($disc_rp)

        $('#total').val($totAmount)

        if (mytype == 'persen')
        {
            var $pph_persen = parseFloat($ppn.val()) || 0
            var $pph_val = (parseFloat($totAmount) * parseFloat($pph_persen)) / 100

            $ppn_rp.val(parseFloat($pph_val))
        }
        else
        {
            var $pph_val = parseFloat($ppn_rp.val()) || 0
            var $pph_persen = (parseFloat($pph_val) / parseFloat($totAmount)) * 100

            $ppn.val(parseFloat($pph_persen))
        }

        let $totalAll = parseFloat($totAmount) + parseFloat($ongkir) + parseFloat($materai) + parseFloat($pph_val) + parseFloat($other_cost)

        $('#totalall').val($totalAll)

        FormatMoney()
    }

    function RegenerateRowId()
    {
        $('#tbl-barang > tbody > tr').each(function (index)
        {
            const row = $(this)

            // Update data attribute
            row.attr('data-row-id', index)

            // Ganti semua ID dan for di dalam baris
            row.find('[id]').each(function ()
            {
                const oldId = $(this).attr('id')
                const newId = oldId.replace(/-\d+$/, '-' + index)

                $(this).attr('id', newId)
            })

            // Ganti semua `for="id"` pada label (khusus checkbox bonus)
            row.find('label[for]').each(function ()
            {
                const oldFor = $(this).attr('for')
                const newFor = oldFor.replace(/-\d+$/, '-' + index)

                $(this).attr('for', newFor)
            })

            // Kalau kamu pakai `select2`, re-init select2 di sini
            row.find('select.data-satuan').select2({ minimumResultsForSearch: Infinity })
        })
    }

    var isi_kecil_pre = ''

    $('#tbl-barang').on('select2:opening', '.data-satuan', function (e)
    {
        isi_kecil_pre = $(this).find('option:selected').data('isi_kecil')
    }).on('select2:select', '.data-satuan', function (e)
    {
        e.preventDefault()

        ResetMoney()

        let $tr = $(this).closest("tr")
        let isi_kecil = $(this).find('option:selected').data('isi_kecil')
        let satuan = $(this).find('option:selected').val().split(':')
        let $harga_grn = $tr.find('[name="harga_grn[]"]')
        let $harga_grn_attr = $tr.find('[name="harga_grn[]"]').data('hna')
        let $harga_dasar = $tr.find('[name="harga_dasar[]"]')
        let $harga_dasar_txt = $tr.find('[name="txt-hrg-lama[]"]')

        let $hna = parseFloat($harga_grn_attr) || 0
        let $konversi = parseFloat(isi_kecil) * parseFloat($hna)

        $harga_grn.val(parseFloat($konversi))

        $harga_dasar.val(parseFloat($konversi))

        $harga_dasar_txt.html(MoneyFormat($konversi))

        FormatMoney()

        $harga_grn.trigger('change')
    })

    // aksi submit edit / update
    $('#form-input-pb').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            let cara_beli = $('input[name="cara_beli"]:checked').val()
            let bank_id = $('#bank_id option:selected').val()

            if (cara_beli == 1 && bank_id == '')
            {
                swalShowMessage('Information, ', 'Bank harus dipilih jika pembelian Cash/Tunai !', 'warning')

                return
            }

            let jml_barang = $('#tbl-barang > tbody').find("tr").length

            if (jml_barang == 0)
            {
                swalShowMessage("Information", 'Belum ada barang yang diterima.', 'warning')

                return
            }

            ResetMoney()

            const list_grdid = document.getElementsByName('grdid[]')
            const barang = document.getElementsByName('barang[]')

            for (var i = 0; i < list_grdid.length; i++)
            {
                const is_bonus = $('#is-bonus-' + i)
                const jml_terima = parseFloat(document.getElementsByName('jml_terima[]')[i].value) || 0
                const harga_grn = parseFloat(document.getElementsByName('harga_grn[]')[i].value) || 0

                if (!is_bonus.is(':checked') && parseFloat(harga_grn) == 0)
                {
                    FormatMoney()

                    Swal.fire('Peringatan', 'Harga penerimaan ' + barang[i].value + ' belum diisi / masih 0.', 'warning')

                    return false
                }

                if (parseFloat(jml_terima) == 0)
                {
                    FormatMoney()

                    Swal.fire('Peringatan', 'Jumlah Terima ' + barang[i].value + ' belum dipilih.', 'warning')

                    return false
                }
            }

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.pembelian.penerimaan_barang.save') }}"

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

                    FormatMoney()

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

                    FormatMoney()
                })
            }), 2e3)
        }
    })
</script>
