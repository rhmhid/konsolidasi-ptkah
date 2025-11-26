@extends('layouts.main')

@section('content')
<div class="d-flex flex-column flex-lg-row">
    <!--begin::Content-->
    <div class="flex-lg-row-fluid">
        <!--begin::Contents-->
        <div class="card border border-gray-300 rounded-1">
            <!--begin::Card header-->
            <div class="card-body p-0" id="kt_content_header">
                <div class="d-flex justify-content-between flex-column border-bottom border-gray-300 p-4 px-6">
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column flex-grow-1">
                            <h2 class="pt-2 text-dark">
                                <span class="las la-dolly text-dark me-4"></span>
                                Laporan Penerimaan Barang
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-lap-pb" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2 required">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="sDate-period" class="form-control form-control-sm rounded-1" readonly="" required="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Gudang</label>
                                {!! $cmb_gudang !!}
                            </div>

                            <div class="col-lg-5">
                                <label class="text-dark fw-bold fs-7 pb-2">Supplier</label>
                                {!! $cmb_supp !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3 mb-5">
                            <div class="col-lg-8">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Barang</label>
                                <input type="text" id="sKodeNamaBrg" class="form-control form-control-sm rounded-1" />
                            </div>

                            <div class="col-lg-2">
                                <label class="text-dark fw-bold fs-8 pb-2 d-block">&nbsp;</label>
                                <button type="button" class="btn btn-sm btn-danger rounded-1 me-4 w-100" id="btnCetak">
                                    <i class="la la-print"></i>
                                    Cetak
                                </button>
                            </div>

                            <div class="col-lg-2">
                                <label class="text-dark fw-bold fs-8 pb-2 d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-primary rounded-1 me-4 w-100" id="btnExcel">
                                    <i class="la la-file-excel"></i>
                                    Export Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Compact form-->
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    let sPeriod = '{{ $sPeriod }}'
    let ePeriod = '{{ $ePeriod }}'

    $('#sDate-period').flatpickr({
        defaultDate: new Date(),
        altInput: true,
        altFormat: "d-m-Y",
        dateFormat: "d-m-Y",
        weekNumbers: true,
        mode: "range",
        onClose: function (selectedDates, dateStr, instance)
        {
            var dateArr = selectedDates.map(date => this.formatDate(date, "d-m-Y"))

            sPeriod = dateArr[0]
            ePeriod = dateArr[1]
        }
    })

    // aksi submit cari
    $('#btnCetak').click(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const $form = $('#form-lap-pb')
        const $sDate = $form.find('[id="sDate-period"]').val()
        const $sGid = $form.find('[id="sGid"]').val()
        const $sSuppid = $form.find('[id="sSuppid"]').val()
        const $sKodeNamaBrg = $form.find('[id="sKodeNamaBrg"]').val()

        if ($sDate == '')
        {
            swalShowMessage('Perhatian!', "Periode Harus Dipilih.", 'warning')

            return false
        }

        let $param = 'sdate=' + sPeriod
            $param += '&edate=' + ePeriod
            $param += '&gid=' + $sGid
            $param += '&suppid=' + $sSuppid
            $param += '&kode_nama_brg=' + $sKodeNamaBrg

        let $link = "{{ route('pembelian_report.penerimaan_barang.cetak') }}"

        popFullScreen($link + '?' + $param)
        return false
    })

    function ParamsForm ()
    {
        const $form = $('#form-lap-pb')

        return {
            sdate: sPeriod,
            edate: ePeriod,
            gid: $form.find('[id="sGid"]').val(),
            suppid: $form.find('[id="sSuppid"]').val(),
            kode_nama_brg: $form.find('[id="sKodeNamaBrg"]').val()
        }
    }

    // aksi submit edit / update
    $('#form-lap-pb').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            showLoading()

            setTimeout((function ()
            {
                const href = "{{ route('api.pembelian_report.penerimaan_barang.excel') }}"
                const name = 'Laporn Penerimaan Barang - ' + moment().format('DD-MM-YYYY') + '.xlsx'

                exportExcel({
                    name,
                    url: href,
                    params: ParamsForm()
                }).finally(() => {
                    Swal.close()
                })
            }), 2e3)
        }
    })
</script>
@endpush