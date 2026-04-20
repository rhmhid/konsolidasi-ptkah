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
                                Summary A/P Purchasing
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-sap" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Cabang</label>
                                {!! $cmb_cabang !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Status Cabang</label>
                                <div class="nav-group nav-group-sm nav-group-fluid rounded-1 border border-gray-300 bg-white p-1">
                                    <label>
                                        <input type="radio" class="btn-check" name="status_cabang" id="status_cabang_all" value="" checked="" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Semua</span>
                                    </label>
                                    <label>
                                        <input type="radio" class="btn-check" name="status_cabang" id="status_cabang_t" value="t" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Aktif</span>
                                    </label>
                                    <label>
                                        <input type="radio" class="btn-check" name="status_cabang" id="status_cabang_f" value="f" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Non Aktif</span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <label class="text-dark fw-bold fs-7 pb-2">Bulan</label>
                                <select class="form-select form-select-sm rounded-1 w-100" id="s-Month" data-control="select2" required="">
                                    {!! get_combo_option_month_lk(date('m')) !!}
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label class="text-dark fw-bold fs-7 pb-2">Tahun</label>
                                <select class="form-select form-select-sm rounded-1 w-100" id="s-Year" data-control="select2" required="">
                                    {!! get_combo_option_year(date('Y'), 2024, date('Y')+1) !!}
                                </select>
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3 mb-5">
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
    // aksi submit cari
    $('#btnCetak').click(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const $form = $('#form-sap')
        const $sBid = $form.find('[id="s-Bid"]').val()
        const $sMonth = $form.find('[id="s-Month"]').val()
        const $sYear = $form.find('[id="s-Year"]').val()
        const $statusCabang = $form.find('input[name="status_cabang"]:checked').val()

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

        var $param = 'bid=' + $sBid
            $param += '&month=' + $sMonth
            $param += '&year=' + $sYear
            $param += '&status_cabang=' + $statusCabang

        let $link = "{{ route('summary_report.ap_purchasing.cetak') }}"

        popFullScreen($link + '?' + $param)
        return false
    })

    function ParamsForm ()
    {
        const $form = $('#form-sap')

        return {
            bid: $form.find('[id="s-Bid"]').val(),
            month: $form.find('[id="s-Month"]').val(),
            year: $form.find('[id="s-Year"]').val(),
            status_cabang: $form.find('input[name="status_cabang"]:checked').val()
        }
    }

    // aksi submit edit / update
    $('#form-sap').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            showLoading()

            setTimeout((function ()
            {
                const $form = $('#form-sap')
                const name = 'Summary Report AP Purchasing - ' + moment().format('DD-MM-YYYY') + '.xlsx'

                let href = "{{ route('api.summary_report.ap_purchasing.excel') }}"

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