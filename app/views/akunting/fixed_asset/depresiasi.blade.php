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
                                Form Transaksi Depresiasi Asset
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="fm-fa-depre" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                           <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2" required>Depresiasi Pada</label>
                                <div class="input-group">
                                    <input type="text" name="trans_date" id="trans_date" class="form-control form-control-sm rounded-1 mydate" readonly="" required="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Kategori Asset</label>
                                {!! $cmb_kategori_fa !!}
                            </div>

                            <div class="col-lg-5">
                                <label class="text-dark fw-bold fs-7 pb-2">Depresiasi Per Kode F/A</label>
                                <input type="text" name="facode" id="facode" class="form-control form-control-sm rounded-1" placeholder="Masukan Kode Asset Jika Depresiasi Per Asset" />
                            </div>
                        </div>

                        <div class="mt-3 w-100 py-4 border-top mb-15">
                            <button type="submit" class="btn btn-dark btn-sm rounded-1 float-end" id="btn-simpan">
                                <i class="las la-save"></i> Simpan
                            </button>
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
    $(".mydate").flatpickr({
        defaultDate: "{{ $trans_date }}",
        dateFormat: "m-Y",
    })

    $('#fm-fa-depre').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        let $form = $(this)
        let trans_date = $form.find('[id="trans_date"]').val()

        if (trans_date == '')
        {
            swalShowMessage('Peringatan', 'Bulan & Tahun Depresiasi Belum Dipilih.', 'warning')

            return false
        }

        const payload = new FormData(this)
            payload.append('_method', 'patch') // ganti ajax method post menjadi patch

        formSubmitUrl = "{{ route('api.akunting.fixed_asset.depresiasi.proses') }}"

        showLoading()

        setTimeout((function ()
        {
            doAjax(
                formSubmitUrl,
                payload,
                "POST"
            )
            .done( data => {
                if (data.success)
                {
                    $('#facid').val(null).trigger('change')

                    $('#facode').val('')

                    swalShowMessage('Sukses', data.message, 'success')
                }
                else
                    swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
            })
            .fail( err => {
                const res = err?.responseJSON

                swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
            })
        }), 2e3)
    })
</script>
@endpush