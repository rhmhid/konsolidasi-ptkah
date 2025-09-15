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
                                Laporan Kartu Piutang
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-kp" novalidate="">
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

                            <div class="col-lg-5">
                                <label class="text-dark fw-bold fs-7 pb-2 required">Nama Customer</label>
                                {!! $cmb_cust !!}
                            </div>

                            <div class="col-lg-4 div-bank">
                                <label class="text-dark fw-bold fs-7 pb-2">Nama Bank</label>
                                {!! $cmb_bank !!}
                            </div>

                            <div class="col-lg-4 div-karyawan">
                                <label class="text-dark fw-bold fs-7 pb-2">Nama Karyawan</label>
                                {!! $cmb_karyawan !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 my-3 mb-5">
                            <div class="col-lg-8"></div>

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

    hideDetailCust()

    $("#sCust").change(function ()
    {
        hideDetailCust()
    })

    function hideDetailCust ()
    {
        let custid = $('#sCust').val()

        $('.div-bank').hide()
        $('.div-karyawan').hide()

        if (custid == -1)
        {
            $('.div-bank').hide()
            $('.div-karyawan').show()
        }
        else if (custid == -2)
        {
            $('.div-bank').show()
            $('.div-karyawan').hide()
        }
        else if (custid == -3)
        {
            $('.div-bank').hide()
            $('.div-karyawan').hide()
        }

        $('#sbank_id').val(null).trigger('change')
        $('#spegawai_id').val(null).trigger('change')
    }

    // aksi submit cari
    $('#btnCetak').click(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const $form = $('#form-kp')
        const $sDatePeriod = $form.find('[id="sDate-period"]').val()
        const $custid = $form.find('[id="sCust"] option:selected').val()
        const $pegawaiID = $form.find('[id="spegawai_id"] option:selected').val()
        const $bankID = $form.find('[id="sbank_id"] option:selected').val()

        if ($sDatePeriod == '')
        {
            swalShowMessage('Perhatian!', "Periode Harus Dipilih.", 'warning')

            return false
        }

        if ($custid == '')
        {
            swalShowMessage('Perhatian!', "Customer Harus Dipilih.", 'warning')

            return false
        }

        if ($custid == -1 && $pegawaiID == '')
        {
            swalShowMessage('Perhatian!', "Karyawan Harus Dipilih.", 'warning')

            return false
        }
        else if ($custid == -2 && $bankID == '')
        {
            swalShowMessage('Perhatian!', "Bank Harus Dipilih.", 'warning')

            return false
        }

        let $param = 'sdate=' + sPeriod
            $param += '&edate=' + ePeriod
            $param += '&custid=' + $custid
            $param += '&pegawai_id=' + $pegawaiID
            $param += '&bank_id=' + $bankID

        let $link = "{{ route('piutang_report.kartu_piutang.cetak') }}"

        popFullScreen($link + '?' + $param)
        return false
    })

    function ParamsForm ()
    {
        const $form = $('#form-kp')
        const $sDatePeriod = $form.find('[id="sDate-period"]').val()
        const $custid = $form.find('[id="sCust"] option:selected').val()
        const $pegawaiID = $form.find('[id="spegawai_id"] option:selected').val()
        const $bankID = $form.find('[id="sbank_id"] option:selected').val()

        return {
            sdate: sPeriod,
            edate: ePeriod,
            custid: $custid,
            pegawai_id: $pegawaiID,
            bank_id: $bankID
        }
    }

    // aksi submit edit / update
    $('#form-kp').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const $form = $('#form-kp')
            const $custid = $form.find('[id="sCust"] option:selected').val()
            const $pegawaiID = $form.find('[id="spegawai_id"] option:selected').val()
            const $bankID = $form.find('[id="sbank_id"] option:selected').val()

            if ($custid == -1 && $pegawaiID == '')
            {
                swalShowMessage('Perhatian!', "Karyawan Harus Dipilih.", 'warning')

                return false
            }
            else if ($custid == -2 && $bankID == '')
            {
                swalShowMessage('Perhatian!', "Bank Harus Dipilih.", 'warning')

                return false
            }

            showLoading()

            setTimeout((function ()
            {
                const href = "{{ route('api.piutang_report.kartu_piutang.excel') }}"
                const name = 'Laporan Kartu Piutang - ' + moment().format('DD-MM-YYYY') + '.xlsx'

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