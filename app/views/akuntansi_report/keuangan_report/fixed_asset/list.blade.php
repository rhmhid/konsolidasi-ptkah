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
                                <span class="las la-clipboard-list text-dark me-4"></span>
                                Laporan Fixed Asset
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-fa" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Bulan</label>
                                <select class="form-select form-select-sm rounded-1 w-100" id="s-Month" data-control="select2">
                                    {!! get_combo_option_month_long(date('m')) !!}
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label class="text-dark fw-bold fs-7 pb-2">Tahun</label>
                                <select class="form-select form-select-sm rounded-1 w-100" id="s-Year" data-control="select2">
                                    {!! get_combo_option_year(date('Y'), 2024, date('Y')+1) !!}
                                </select>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Kategori Asset</label>
                                {!! $cmb_kategori_fa !!}
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Lokasi Asset</label>
                                {!! $cmb_lokasi_fa !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 my-3 mb-5">
                            <div class="col-lg-8">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama / Deskripsi Asset</label>
                                <input type="text" id="sKodeNamaDesc" class="form-control form-control-sm rounded-1" placeholder="Masukan Kode / Nama / Deskripsi Asset" />
                            </div>

                            <div class="col-lg-2">
                                <label class="text-dark fw-bold fs-8 pb-2">&nbsp;</label>
                                <button type="button" class="btn btn-sm btn-danger rounded-1 me-4 w-100" id="btnCetak">
                                    <i class="la la-print"></i>
                                    Cetak
                                </button>
                            </div>

                            <div class="col-lg-2">
                                <label class="text-dark fw-bold fs-8 pb-2">&nbsp;</label>
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

        const $form = $('#form-fa')
        const $sMonth = parseInt($form.find('[id="s-Month"] option:selected').val())
        const $sYear = $form.find('[id="s-Year"] option:selected').val()
        const $sFacid = $form.find('[id="sFacid"] option:selected').val()
        const $sFalid = $form.find('[id="sFalid"] option:selected').val()
        const $sKodeNamaDesc = $form.find('[id="sKodeNamaDesc"]').val()

        let $param = 'smonth=' + $sMonth
            $param += '&syear=' + $sYear
            $param += '&facid=' + $sFacid
            $param += '&falid=' + $sFalid
            $param += '&kode_nama_desc=' + $sKodeNamaDesc

        let $link = "{{ route('keuangan_report.fixed_asset.cetak') }}"

        popFullScreen($link + '?' + $param)
        return false
    })

    function ParamsForm ()
    {
        const $form = $('#form-fa')
        const $sMonth = parseInt($form.find('[id="s-Month"] option:selected').val())
        const $sYear = $form.find('[id="s-Year"] option:selected').val()
        const $sFacid = $form.find('[id="sFacid"] option:selected').val()
        const $sFalid = $form.find('[id="sFalid"] option:selected').val()
        const $sKodeNamaDesc = $form.find('[id="sKodeNamaDesc"]').val()

        return {
            smonth: $sMonth,
            syear: $sYear,
            facid: $sFacid,
            falid: $sFalid,
            kode_nama_desc: $sKodeNamaDesc
        }
    }

    // aksi submit edit / update
    $('#form-fa').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            showLoading()

            setTimeout((function ()
            {
                const href = "{{ route('api.keuangan_report.fixed_asset.excel') }}"
                const name = 'Laporan Fixed Asset - ' + moment().format('DD-MM-YYYY') + '.xlsx'

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