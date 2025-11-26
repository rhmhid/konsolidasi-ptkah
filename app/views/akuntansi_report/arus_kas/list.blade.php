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
                                <span class="las la-file-alt text-dark me-4"></span>
                                Form Arus Kas ( Cashflow )
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-cf" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4 mb-5">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Jenis Laporan</label>
                                <select class="form-select form-select-sm rounded-1 w-100" id="s-Tipe" data-control="select2" required="">
                                    <option value="cf-direct" selected="">Cashflow - Direct</option>
                                    <option value="cf-direct-daily">Cashflow - Direct ( Daily )</option>
                                    <!--option value="cf-indirect">Cashflow - Inirect</option-->
                                </select>
                            </div>

                            <div class="col-lg-3 monthly">
                                <label class="text-dark fw-bold fs-7 pb-2">Bulan</label>
                                <select class="form-select form-select-sm rounded-1 w-100" id="s-Month" data-control="select2" required="">
                                    {!! get_combo_option_month_lk(date('m')) !!}
                                </select>
                            </div>

                            <div class="col-lg-2 monthly">
                                <label class="text-dark fw-bold fs-7 pb-2">Tahun</label>
                                <select class="form-select form-select-sm rounded-1 w-100" id="s-Year" data-control="select2" required="">
                                    {!! get_combo_option_year(date('Y'), 2024, date('Y')+1) !!}
                                </select>
                            </div>

                            <div class="col-lg-5 daily d-none">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="sDate-period" class="form-control form-control-sm rounded-1" readonly="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
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

    $('#s-Tipe').change(function ()
    {
        let tipe = $(this).val()

        if (tipe == 'cf-direct-daily')
        {
            $('.monthly').addClass('d-none')
            $('.daily').removeClass('d-none')
        }
        else
        {
            $('.monthly').removeClass('d-none')
            $('.daily').addClass('d-none')
        }
    })

    // aksi submit cari
    $('#btnCetak').click(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const $form = $('#form-cf')
        const $sTipe = $form.find('[id="s-Tipe"] option:selected').val()
        const $sMonth = $form.find('[id="s-Month"]').val()
        const $sYear = $form.find('[id="s-Year"]').val()
        const $sDatePeriod = $form.find('[id="sDate-period"]').val()

        if ($sTipe == '')
        {
            swalShowMessage('Perhatian!', "Tipe Laporan Harus Dipilih.", 'warning')

            return false
        }

        if ($sTipe == 'cf-direct-daily')
        {
            if ($sDatePeriod == '')
            {
                swalShowMessage('Perhatian!', "Periode Harus Dipilih.", 'warning')

                return false
            }

            var $param = 'sdate=' + sPeriod
                $param += '&edate=' + ePeriod
        }
        else
        {
            if ($sMonth == '')
            {
                swalShowMessage('Perhatian!', "Bulan Harus Dipilih.", 'warning')

                return false
            }

            if ($sYear == '')
            {
                swalShowMessage('Perhatian!', "Tahun Harus Dipilih.", 'warning')

                return false
            }

            var $param = 'month=' + $sMonth
                $param += '&year=' + $sYear
        }

        let $link = "{{ route('akuntansi_report.arus_kas.cetak', ['mytipe' => ':mytipe']) }}"
            $link = $link.replace(':mytipe', $sTipe)

        popFullScreen($link + '?' + $param)
        return false
    })

    function ParamsForm ()
    {
        const $form = $('#form-cf')
        const $sTipe = $form.find('[id="s-Tipe"]').val()
        const $sMonth = $form.find('[id="s-Month"]').val()
        const $sYear = $form.find('[id="s-Year"]').val()
        const $sDatePeriod = $form.find('[id="sDate-period"]').val()

        if ($sTipe == 'cf-direct-daily')
        {
            if ($sDatePeriod == '')
            {
                swalShowMessage('Perhatian!', "Periode Harus Dipilih.", 'warning')

                return false
            }

            return {
                sdate: sPeriod,
                edate: ePeriod,
            }
        }
        else
        {
            if ($sMonth == '')
            {
                swalShowMessage('Perhatian!', "Bulan Harus Dipilih.", 'warning')

                return false
            }

            if ($sYear == '')
            {
                swalShowMessage('Perhatian!', "Tahun Harus Dipilih.", 'warning')

                return false
            }

            return {
                month: $form.find('[id="s-Month"]').val(),
                year: $form.find('[id="s-Year"]').val(),
            }
        }
    }

    // aksi submit edit / update
    $('#form-cf').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            showLoading()

            setTimeout((function ()
            {
                const $form = $('#form-cf')
                const $sTipe = $form.find('[id="s-Tipe"] option:selected')

                let href = "{{ route('api.akuntansi_report.arus_kas.excel', ['mytipe' => ':mytipe']) }}"
                    href = href.replace(':mytipe', $($sTipe).val())

                const name = $($sTipe).text() + ' - ' + moment().format('DD-MM-YYYY') + '.xlsx'

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