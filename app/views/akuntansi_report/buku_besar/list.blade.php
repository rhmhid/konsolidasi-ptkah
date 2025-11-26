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
                                Form Buku Besar ( Overview Ledger )
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-buku-besar" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="sDate-period" class="form-control form-control-sm rounded-1" readonly="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Tipe Jurnal</label>
                                {!! $cmb_tipe_jurnal !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Status Posting</label>
                                {!! $cmb_posted !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2 required">Dari COA</label>
                                {!! $cmb_coa_from !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2 required">Sampai COA</label>
                                {!! $cmb_coa_to !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Cost Center</label>
                                {!! $cmb_cost_center !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 my-3 mb-5">
                            <div class="col-lg-6">
                                <label class="text-dark fw-bold fs-7 pb-2">Tipe Laporan</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" name="with_bb" class="btn-check" id="bb-faster" value="f" checked="" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="bb-faster">Without Beginning Balance</label>

                                    <input type="radio" name="with_bb" class="btn-check" id="bb-slower" value="t" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="bb-slower">With Beginning Balance</label>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="text-dark fw-bold fs-7 pb-2">C.O.A Lawan</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" name="coa_vs" class="btn-check" id="coa-vs-t" value="t" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="coa-vs-t">Tampilkan C.O.A Lawan</label>

                                    <input type="radio" name="coa_vs" class="btn-check" id="coa-vs-f" value="f" checked="" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="coa-vs-f">Sembunyikan C.O.A Lawan</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mb-5">
                            <div class="col-lg-8">&nbsp;</div>

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

        const $form = $('#form-buku-besar')
        const $coaid_from = $form.find('[id="sCoaFrom"]').val()
        const $coaid_to = $form.find('[id="sCoaTo"]').val()

        if ($coaid_from == '')
        {
            swalShowMessage('Perhatian!', "Dari COA Harus Dipilih.", 'warning')

            return false
        }

        if ($coaid_to == '')
        {
            swalShowMessage('Perhatian!', "Sampai COA Harus Dipilih.", 'warning')

            return false
        }

        let $param = 'sdate=' + sPeriod
            $param += '&edate=' + ePeriod
            $param += '&jtid=' + $form.find('[id="sJtid"]').val()
            $param += '&is_posted=' + $form.find('[id="sPosted"]').val()
            $param += '&coaid_from=' + $coaid_from
            $param += '&coaid_to=' + $coaid_to
            $param += '&pccid=' + $form.find('[id="sPccid"]').val()
            $param += '&with_bb=' + $form.find('[name="with_bb"]:checked').val()
            $param += '&coa_vs=' + $form.find('[name="coa_vs"]:checked').val()

        let $link = "{{ route('akuntansi_report.buku_besar.cetak') }}"

        popFullScreen($link + '?' + $param)
        return false
    })

    function ParamsForm ()
    {
        const $form = $('#form-buku-besar')

        return {
            sdate: sPeriod,
            edate: ePeriod,
            jtid: $form.find('[id="sJtid"]').val(),
            is_posted: $form.find('[id="sPosted"]').val(),
            coaid_from: $form.find('[id="sCoaFrom"]').val(),
            coaid_to: $form.find('[id="sCoaTo"]').val(),
            pccid: $form.find('[id="sPccid"]').val(),
            with_bb: $form.find('[name="with_bb"]:checked').val(),
            coa_vs: $form.find('[name="coa_vs"]:checked').val()
        }
    }

    // aksi submit edit / update
    $('#form-buku-besar').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            showLoading()

            setTimeout((function ()
            {
                const href = "{{ route('api.akuntansi_report.buku_besar.excel') }}"
                const name = 'Buku Besar - ' + moment().format('DD-MM-YYYY') + '.xlsx'

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