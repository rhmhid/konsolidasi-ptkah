<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-file-invoice text-dark me-4"></span>
        Form Input Invoice Pembelian
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
        <form method="post" id="fi-aps" novalidate>
            <input type="hidden" name="apsid" id="apsid" value="{{ $data_head->apsid }}" readonly="" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-8">
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Transaksi</label>
                            <div class="input-group">
                                <input type="text" name="apdate" id="apdate" value="{{ $data_head->apdate }}" class="form-control form-control-sm rounded-1 mydate-time" readonly="" required="" />
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Duedate</label>
                            <div class="input-group">
                                <input type="text" name="duedate" id="duedate" value="{{ $data_head->duedate }}" class="form-control form-control-sm rounded-1 mydate" readonly="" required="" />
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="text-dark fw-bold fs-7 pb-2 required">Nama Supplier</label>
                            {!! $cmb_supp !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                            <textarea class="form-control form-control-sm rounded-1" rows="4" name="keterangan" id="keterangan">{{ $data_head->keterangan }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="d-flex align-items-center fs-7 text-dark fw-bolder mb-2">
                        <span>Kelengkapan Dokumen</span>
                        <i class="fas ms-2 fs-7"></i>
                    </label>

                    <!-- Dipecah menjadi 2 baris -->
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check form-check-custom form-check-solid mb-2">
                                <input class="form-check-input" type="checkbox" name="is_kwitansi" id="is_kwitansi" value="t" {{ $chk_kwitansi }} />
                                <label class="form-check-label fs-7" for="is_kwitansi">Kwitansi</label>
                            </div>

                            <div class="form-check form-check-custom form-check-solid mb-2">
                                <input class="form-check-input" name="is_faktur_pajak" type="checkbox" id="is_faktur_pajak" value="t" {{ $chk_faktur_pajak }} />
                                <label class="form-check-label fs-7" for="is_faktur_pajak">Faktur Pajak</label>
                            </div>

                            <div class="form-check form-check-custom form-check-solid mb-2">
                                <input class="form-check-input" type="checkbox" name="is_surat_jalan" id="is_surat_jalan" value="t" {{ $chk_surat_jalan }} />
                                <label class="form-check-label fs-7" for="is_surat_jalan">Surat Jalan</label>
                            </div>

                            <div class="form-check form-check-custom form-check-solid mb-2">
                                <input class="form-check-input" type="checkbox" name="is_po" id="is_po" value="t" {{ $chk_po }} />
                                <label class="form-check-label fs-7" for="is_po">PO/SPK</label>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-check form-check-custom form-check-solid mb-2">
                                <input class="form-check-input" type="checkbox" name="is_terima_barang" id="is_terima_barang" value="t" {{ $chk_terima_barang }} />
                                <label class="form-check-label fs-7" for="is_terima_barang">Tanda Terima Barang</label>
                            </div>

                            <div class="form-check form-check-custom form-check-solid mb-2">
                                <input class="form-check-input" type="checkbox" name="is_nota_retur" id="is_nota_retur" value="t" {{ $chk_nota_retur }} />
                                <label class="form-check-label fs-7" for="is_nota_retur">Nota Retur</label>
                            </div>

                            <div class="form-check form-check-custom form-check-solid mb-2">
                                <input class="form-check-input" type="checkbox" name="is_berita_acara" id="is_berita_acara" value="t" {{ $chk_berita_acara }} />
                                <label class="form-check-label fs-7" for="is_berita_acara">Berita Acara</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold required">
                        Detail Transaksi
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu Pembelian -> Penerimaan Barang"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-penerimaan">
                        <thead class="bg-dark text-uppercase fs-7 text-center">
                            <tr class="fw-bold text-white">
                                <th class="align-middle py-5 w-50px">&nbsp;</th>
                                <th class="align-middle border-start py-5">Kode PO</th>
                                <th class="align-middle border-start py-5">Kode Penerimaan/PO</th>
                                <th class="align-middle border-start py-5">Tanggal Faktur</th>
                                <th class="align-middle border-start py-5">Faktur Pajak</th>
                                <th class="align-middle border-start py-5">Invoice</th>
                                <th class="align-middle border-start py-5">Total GRN</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <div class="bg-light-success p-3 mt-n4">
                        <div class="row">
                            <div class="col-10 text-end fw-bold">Total</div>
                            <div class="col-2 text-end fw-bold">Rp. <font id="vtotal">0</font></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex flex-wrap gap-2">
                <div class="d-flex flex-column flex-md-row gap-2">
                    <button type="button" class="btn btn-danger btn-sm rounded-1 w-100 w-md-auto" id="btn-batal" data-bs-dismiss="modal">
                        <i class="las la-undo"></i> Batal
                    </button>

                    <button type="button" class="btn btn-primary btn-sm rounded-1 w-100 w-md-auto" id="btn-grn">
                        <i class="las la-search"></i> Pilih GRN / PO
                    </button>
                </div>

                <!-- Kanan: Simpan -->
                <div class="ms-auto">
                    <button type="submit" class="btn btn-dark btn-sm rounded-1 w-100 w-md-auto" id="btn-simpan">
                        <i class="las la-save"></i> Simpan
                    </button>
                </div>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<!--begin::template - Ubah-->
<script type="text/javascript">
    {!! $AddGrn !!}

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

    $(".mydate-time").flatpickr({
        enableTime: true,
        dateFormat: "d-m-Y H:i",
        time_24hr: true,
        minuteIncrement: 1
    })

    $('#btn-grn').on('click', function (e)
    {
        e.preventDefault()

        let suppid = $('#suppid option:selected').val()

        if (suppid == '')
        {
            swalShowMessage('Information, ', 'Supplier Belum Dipilih !', 'warning')

            return false
        }

        showLoading()

        var getGrn = async function (suppid)
        {
            let result
            let link = "{{ route('api.akunting.hutang_supplier.invoice_pembelian.popup_penerimaan') }}"

            try {
                result = await $.ajax({
                    url     : link,
                    type    : 'GET',
                    data    : { suppid: suppid }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
            }
        }

        getGrn(suppid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup-data-penerimaan', res, null, true)
        })
    })

    function AddGrn (grid, poid, pocode, grcode, tgl_faktur, no_faktur='', total_grn = 0, diskon = 0, ongkir = 0, materai = 0, ppn_persen = 0, ppn_rp = 0, other_cost = 0, total_ap = 0, no_inv = '', apsdid = 0)
    {
        let row_pocode = pocode ? `<i class="las la-file-alt text-primary cursor-pointer print-po" data-id="${poid}"></i> ${pocode}` : ""

        let data_grn = `<tr class="row-${grid}">
                            <td class="text-center">
                                <i class="las la-trash text-danger hapus-grn" data-grid="${grid}" role="button"></i>
                            </td>
                            <td class="text-nowrap text-center">
                                ${row_pocode}
                                <input type="hidden" name="pocode[${grid}]" value="${pocode}" />
                            </td>
                            <td class="text-nowrap text-center">
                                <i class="las la-file-alt text-primary cursor-pointer print-grn" data-id="${grid}"></i> ${grcode}
                                <input type="hidden" name="grcode[${grid}]" value="${grcode}" />
                            </td>
                            <td class="text-nowrap text-center">
                                <div class="input-group">
                                    <input type="text" name="tgl_faktur[${grid}]" value="${tgl_faktur}" class="form-control form-control-sm rounded-1 mydate" readonly="" required="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </td>
                            <td class="text-nowrap text-center">
                                <input type="text" name="no_faktur[${grid}]" value="${no_faktur}" class="form-control form-control-sm rounded-1" required="" />
                            </td>
                            <td class="text-nowrap text-center">
                                <input type="text" name="no_inv[${grid}]" value="${no_inv}" class="form-control form-control-sm rounded-1" required="" />
                            </td>
                            <td class="text-nowrap text-end">
                                <font id="total-grn-txt[${grid}]">Rp. ${MoneyFormat(total_grn)}</font>
                            </td>
                        </tr>
                        <tr class="row-${grid}">
                            <td class="bg-light-primary"></td>
                            <td class="bg-light-primary" colspan="6">
                                <div class="row px-0 fs-8">
                                    <div class="col-2">
                                        <div class="d-flex justify-content-start flex-column">
                                            <label class="fw-bold fs-8">Diskon Faktur</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp.</span>
                                                <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-amount" name="diskon[${grid}]" id="diskon-${grid}" value="${diskon}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="d-flex justify-content-start flex-column">
                                            <label class="fw-bold fs-8">Ongkos Kirim</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp.</span>
                                                <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-amount" name="ongkir[${grid}]" id="ongkir-${grid}" value="${ongkir}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="d-flex justify-content-start flex-column">
                                            <label class="fw-bold fs-8">Materai</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp.</span>
                                                <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-amount" name="materai[${grid}]" id="materai-${grid}" value="${materai}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="d-flex justify-content-start flex-column">
                                            <label class="fw-bold fs-8">PPn</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control form-control-sm w-10px fs-8 rounded-1 currency calc-amount" mytype="persen" name="ppn_persen[${grid}]" id="ppn-persen-${grid}" value="${ppn_persen}" />
                                                <span class="input-group-text">%</span>
                                                <span class="input-group-text">Rp.</span>
                                                <input type="text" class="form-control form-control-sm w-80px fs-8 rounded-1 currency calc-amount" name="ppn_rp[${grid}]" id="ppn-rp-${grid}" value="${ppn_rp}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="d-flex justify-content-start flex-column">
                                            <label class="fw-bold fs-8">Other Cost</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp.</span>
                                                <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 currency calc-amount" name="other_cost[${grid}]" id="other-cost-${grid}" value="${other_cost}" />
                                            </div>
                                        </div>
                                    </div>

                                    <!--div class="col-2">
                                        <div class="d-flex justify-content-start flex-column">
                                            <label class="fw-bold fs-8">Retur</label>
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-icon btn-light-success pilih-retur" data-id="${grid}"><i class="las la-plus"></i></button>
                                                <input type="text" class="form-control form-control-sm  rounded-1 text-end numeric" name="retur[]" id="retur[${grid}]" readonly  onkeypress="return cek_angka(event);">
                                                <input type="text" name="id_retur[]" id="id_retur_${grid}">
                                            </div>
                                        </div>
                                    </div-->
                                </div>

                                <input type="hidden" class="grid-row" name="grid[${grid}]" value="${grid}" />
                                <input type="hidden" name="apsdid[${grid}]" value="${apsdid}" />
                                <input type="hidden" name="total_grn[${grid}]" id="total-grn-${grid}" class="form-control form-control-sm rounded-1 calc-amount" />
                                <input type="hidden" name="total_ap[${grid}]" id="total-ap-${grid}" value="${total_ap}" class="form-control form-control-sm rounded-1 total-ap" />
                            </td>
                        </tr>`

        $('#tbl-penerimaan > tbody:last').append(data_grn)

        initDatePicker()

        SetFormatMoney($('#diskon-' + grid))

        SetFormatMoney($('#ongkir-' + grid))

        SetFormatMoney($('#materai-' + grid))

        SetFormatMoney($('#ppn-persen-' + grid))

        SetFormatMoney($('#ppn-rp-' + grid))

        SetFormatMoney($('#other-cost-' + grid))

        $('#total-grn-' + grid).val(total_grn).trigger('change')
    }

    $('#tbl-penerimaan').on('click', '.hapus-grn', function ()
    {
        let grid = $(this).data('grid')

        $(`.row-${grid}`).remove()

        calcAmunt()
    })

    $('#tbl-penerimaan').on('click', '.print-grn', function ()
    {
        let grid = $(this).data('id')

        let link = "{{ route('pembelian.penerimaan_barang.cetak', ['myid' => ':myid']) }}"
            link = link.replace(':myid', grid)

        NewWindow(link, 'Penerimaan Cetak', 1000, 500, 'yes')
        return false
    })

    $('#tbl-penerimaan').on('change', '.calc-amount', function ()
    {
        ResetMoney()

        let $tr = $(this).closest("tr")
        let $grid = $tr.find('.grid-row').val()

        let $total_grn = parseFloat($tr.find('#total-grn-' + $grid).val()) || 0
        let $diskon = parseFloat($tr.find('#diskon-' + $grid).val()) || 0
        let $ongkir = parseFloat($tr.find('#ongkir-' + $grid).val()) || 0
        let $materai = parseFloat($tr.find('#materai-' + $grid).val()) || 0
        let $ppn = $tr.find('#ppn-persen-' + $grid)
        let $ppn_rp = $tr.find('#ppn-rp-' + $grid)
        let $other_cost = parseFloat($tr.find('#other-cost-' + $grid).val()) || 0
        let $total_ap = $tr.find('#total-ap-' + $grid)
        let mytype = $(this).attr('mytype')

        let $subtotal = parseFloat($total_grn) - parseFloat($diskon)

        if (mytype == 'persen')
        {
            var $ppn_persen = parseFloat($ppn.val()) || 0
            var $ppn_val = (parseFloat($subtotal) * parseFloat($ppn_persen)) / 100

            $ppn_rp.val(parseFloat($ppn_val))
        }
        else
        {
            var $ppn_val = parseFloat($ppn_rp.val()) || 0
            var $ppn_persen = (parseFloat($ppn_val) / parseFloat($subtotal)) * 100

            $ppn.val(parseFloat($ppn_persen))
        }

        let $subtotal_ap = parseFloat($total_grn) - parseFloat($diskon) + parseFloat($ongkir) + parseFloat($materai) + parseFloat($ppn_val) + parseFloat($other_cost)

        $total_ap.val($subtotal_ap)

        FormatMoney()

        calcAmunt()
    })

    function calcAmunt ()
    {
        ResetMoney()

        let $listAmount = $('#tbl-penerimaan .total-ap')
        let $totAmount = 0

        $listAmount.each(function ()
        {
            const $field = $(this)
            const val = isNaN(parseFloat($field.val())) ? 0 : parseFloat($field.val())

            $totAmount = parseFloat($totAmount) + parseFloat(val)
        })

        $('#vtotal').html(MoneyFormat($totAmount))

        FormatMoney()
    }

    // aksi submit edit / update
    $('#fi-aps').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            let jml_coa = $('#tbl-penerimaan > tbody').find("tr").length

            if (jml_coa < 1)
            {
                swalShowMessage("Information", 'Harap Pilih GRN/PO Terlebih Dahulu.', 'warning')
                return
            }

            ResetMoney()

            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.akunting.hutang_supplier.invoice_pembelian.save') }}"

            showLoading()

            setTimeout((function ()
            {
                doAjax(
                    formSubmitUrl,
                    payload,
                    "POST"
                )
                .done( data => {
                    FormatMoney()

                    Swal.close()

                    if (data.success)
                    {
                        swalShowMessage('Sukses', data.message, 'success')
                        .then((result) =>
                        {
                            if (result.isConfirmed)
                            {
                                $("#mdl-form-aps").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    FormatMoney()

                    Swal.close()

                    const res = err?.responseJSON

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>
