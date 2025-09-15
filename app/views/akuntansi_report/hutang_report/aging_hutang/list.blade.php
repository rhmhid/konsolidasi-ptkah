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
                                Laporan Aging Hutang
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-aging-hutang" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal</label>
                                <div class="input-group">
                                    <input type="text" id="sDate" class="form-control form-control-sm rounded-1" value="{{ $sdate }}" readonly="" required="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-9">
                                <label class="text-dark fw-bold fs-7 pb-2">Tanggal Berdasarkan</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" name="date_by" class="btn-check" id="by-invoice" value="apdate" checked="" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="by-invoice">Tanggal Invoice</label>

                                    <input type="radio" name="date_by" class="btn-check" id="by-duedate" value="duedate" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="by-duedate">Tanggal Jatuh Tempo</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-0 gx-4 my-3 mb-5">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Nama Supplier</label>
                                {!! $cmb_supp !!}
                            </div>

                            <div class="col-lg-4 div-doctor">
                                <label class="text-dark fw-bold fs-7 pb-2">Nama Dokter</label>
                                {!! $cmb_doctor !!}
                            </div>

                            <div class="col-lg-4 div-non-doctor"></div>

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
    $("#sDate").flatpickr({
        dateFormat: "d-m-Y"
    })

    SHideDoctor()

    $("#suppid").change(function ()
    {
        SHideDoctor()
    })

    function SHideDoctor ()
    {
        let suppid = $('#suppid option:selected').val()

        if (suppid == -1)
        {
            $('.div-doctor').show()
            $('.div-non-doctor').hide()
        }
        else
        {
            $('.div-doctor').hide()
            $('.div-non-doctor').show()
        }

        $('#doctor-id').val(null).trigger('change');
    }

    // aksi submit cari
    $('#btnCetak').click(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const $form = $('#form-aging-hutang')
        const $sDate = $form.find('[id="sDate"]').val()
        const $dateBy = $form.find('[name="date_by"]:checked').val()
        const $suppid = $form.find('[id="suppid"] option:selected').val()
        const $doctorId = $form.find('[id="doctor-id"] option:selected').val()

        if ($sDate == '')
        {
            swalShowMessage('Perhatian!', "Tanggal Harus Dipilih.", 'warning')

            return false
        }

        let $param = 'sdate=' + $sDate
            $param += '&date_by=' + $dateBy
            $param += '&suppid=' + $suppid
            $param += '&doctor_id=' + $doctorId

        let $link = "{{ route('hutang_report.aging_hutang.cetak') }}"

        popFullScreen($link + '?' + $param)
        return false
    })

    function ParamsForm ()
    {
        const $form = $('#form-aging-hutang')
        const $sDate = $form.find('[id="sDate"]').val()
        const $dateBy = $form.find('[name="date_by"]:checked').val()
        const $suppid = $form.find('[id="suppid"] option:selected').val()
        const $doctorId = $form.find('[id="doctor-id"] option:selected').val()

        return {
            sdate: $sDate,
            date_by: $dateBy,
            suppid: $suppid,
            doctor_id: $doctorId
        }
    }

    // aksi submit edit / update
    $('#form-aging-hutang').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            showLoading()

            setTimeout((function ()
            {
                const href = "{{ route('api.hutang_report.aging_hutang.excel') }}"
                const name = 'Laporan Aging Hutang - ' + moment().format('DD-MM-YYYY') + '.xlsx'

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