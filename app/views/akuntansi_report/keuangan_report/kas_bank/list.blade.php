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
                                Laporan Kas & Bank
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-kas-bank" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2 required">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="sDate-period" class="form-control form-control-sm rounded-1" readonly="" required="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <label class="text-dark fw-bold fs-7 pb-2">Tipe Jurnal</label>
                                {!! $cmb_tipe_jurnal !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 my-3 mb-5">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Kas Bank</label>
                                {!! $cmb_bank !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Status Jurnal</label>
                                {!! $cmb_posted !!}
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

        const $form = $('#form-kas-bank')
        const $sDatePeriod = $form.find('[id="sDate-period"]').val()
        const $bankID = $form.find('[id="bank-ID"] option:selected').val()
        const $jtID = $form.find('[id="jt-ID"] option:selected').val()
        const $isPosted = $form.find('[id="is-Posted"] option:selected').val()

        if ($sDatePeriod == '')
        {
            swalShowMessage('Perhatian!', "Periode Harus Dipilih.", 'warning')

            return false
        }

        if ($bankID == '')
        {
            swalShowMessage('Perhatian!', "Kas / Bank Harus Dipilih.", 'warning')

            return false
        }

        let $param = 'sdate=' + sPeriod
            $param += '&edate=' + ePeriod
            $param += '&bank_id=' + $bankID
            $param += '&jtid=' + $jtID
            $param += '&is_posted=' + $isPosted

        let $link = "{{ route('keuangan_report.kas_bank.cetak') }}"

        popFullScreen($link + '?' + $param)
        return false
    })

    function ParamsForm ()
    {
        const $form = $('#form-kas-bank')
        const $sDatePeriod = $form.find('[id="sDate-period"]').val()
        const $bankID = $form.find('[id="bank-ID"] option:selected').val()
        const $jtID = $form.find('[id="jt-ID"] option:selected').val()
        const $isPosted = $form.find('[id="is-Posted"] option:selected').val()

        return {
            sdate: sPeriod,
            edate: ePeriod,
            bank_id: $bankID,
            jtid: $jtID,
            is_posted: $isPosted
        }
    }

    // aksi submit edit / update
    $('#form-kas-bank').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            showLoading()

            setTimeout((function ()
            {
                const href = "{{ route('api.keuangan_report.kas_bank.excel') }}"
                const name = 'Laporan Kas & Bank - ' + moment().format('DD-MM-YYYY') + '.xlsx'

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