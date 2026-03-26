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
                                Form Neraca ( Balance Sheet )
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-bs" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4 mb-5">
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

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Status C.O.A</label>
                                <div class="nav-group nav-group-sm nav-group-fluid rounded-1 border border-gray-300 bg-white p-1">
                                    <label>
                                        <input type="radio" class="btn-check" name="status_coa" id="status_coa_all" value="" checked="" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Semua</span>
                                    </label>
                                    <label>
                                        <input type="radio" class="btn-check" name="status_coa" id="status_coa_t" value="t" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Aktif</span>
                                    </label>
                                    <label>
                                        <input type="radio" class="btn-check" name="status_coa" id="status_coa_f" value="f" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Non Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mb-5">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Jenis Laporan</label>
                                <select class="form-select form-select-sm rounded-1 w-100" id="s-Tipe" data-control="select2" required="">
                                    <option value="bs-std" selected="">Neraca Standard</option>
                                    <option value="bs-new">Neraca ( Format Baru)</option>
                                    <option value="bs-new-detail">Neraca Detail ( Format Baru)</option>
                                </select>
                            </div>

                            <div class="col-lg-3">
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

        const $form = $('#form-bs')
        const $sBid = $form.find('[id="s-Bid"]').val()
        const $sTipe = $form.find('[id="s-Tipe"] option:selected').val()
        const $sMonth = $form.find('[id="s-Month"]').val()
        const $sYear = $form.find('[id="s-Year"]').val()
        const $statusCabang = $form.find('input[name="status_cabang"]:checked').val()
        const $statusCoa = $form.find('input[name="status_coa"]:checked').val()

        if ($sTipe == '')
        {
            swalShowMessage('Perhatian!', "Tipe Laporan Harus Dipilih.", 'warning')

            return false
        }

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
            $param += '&status_coa=' + $statusCoa

        let $link = "{{ route('akuntansi_report.neraca.cetak', ['mytipe' => ':mytipe']) }}"
            $link = $link.replace(':mytipe', $sTipe)

        popFullScreen($link + '?' + $param)
        return false
    })

    function ParamsForm ()
    {
        const $form = $('#form-bs')

        return {
            bid: $form.find('[id="s-Bid"]').val(),
            month: $form.find('[id="s-Month"]').val(),
            year: $form.find('[id="s-Year"]').val(),
            status_cabang: $form.find('input[name="status_cabang"]:checked').val(),
            status_coa: $form.find('input[name="status_coa"]:checked').val()
        }
    }

    // aksi submit edit / update
    $('#form-bs').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            showLoading()

            setTimeout((function ()
            {
                const $form = $('#form-bs')
                const $sTipe = $form.find('[id="s-Tipe"] option:selected')

                let href = "{{ route('api.akuntansi_report.neraca.excel', ['mytipe' => ':mytipe']) }}"
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