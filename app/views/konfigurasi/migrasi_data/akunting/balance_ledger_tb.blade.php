@extends('layouts.main')

@section('content')

@include('layouts.migrasi')

<div class="d-flex flex-column flex-lg-row">
    <!--begin::Content-->
    <div class="flex-lg-row-fluid">
        <!--begin::Contents-->
        <div class="card rounded-1 border border-gray-300">
            <!--begin::Card header-->
            <div class="card-body p-0" id="kt_content_header">
                <div class="d-flex justify-content-between flex-column border-bottom border-gray-300 p-4 px-6">
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column flex-grow-1">
                            <h2 class="pt-2 text-dark">
                                <span class="las la-file-alt text-dark me-4"></span>
                                Form Balance Ledger TB
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="border-bottom border-gray-300">
                    <form method="post" id="form-balance-ledger" novalidate="">
                        <input type="hidden" name="_method" value="patch" />

                        <!--begin::Compact form-->
                        <div class="p-6 pb-0">
                            <div class="row g-0 gx-4">
                                <div class="col-lg-3">
                                    <label class="text-dark fw-bold fs-7 pb-2">Bulan</label>
                                    <select class="form-select form-select-sm rounded-1 w-100" id="s-Month" data-control="select2">
                                        <option value="" selected="">-- All --</option>
                                        {!! get_combo_option_month_lk(date('m')) !!}
                                    </select>
                                </div>

                                <div class="col-lg-3">
                                    <label class="text-dark fw-bold fs-7 pb-2">Tahun</label>
                                    <select class="form-select form-select-sm rounded-1 w-100" id="s-Year" data-control="select2" required="">
                                        <option value="" disabled="">-- Pilih Tahun --</option>
                                        {!! get_combo_option_year(date('Y'), 2024, date('Y')+1) !!}
                                    </select>
                                </div>

                                <div class="col-lg-1">
                                    <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                                        <button type="submit" class="btn btn-dark btn-sm rounded-1 w-100" id="me_btn_simpan">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Compact form-->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $('#form-balance-ledger').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const $form = $(this)

        const payload = {
            _method: $form.find('[name="_method"]').val(),
            month: $form.find('#s-Month option:selected').val(),
            year: $form.find('#s-Year option:selected').val(),
        }

        showLoading()

        setTimeout((function ()
        {
            $.ajax({
                url         : "{{ route('api.migrasi_data.akunting.balance_ledger_tb.save') }}",
                data        : payload,
                type        : 'POST',

                error       : function (err)
                            {
                                swal.close()

                                swalShowMessage('Error', err?.responseJSON?.message || 'An Error Occured.', 'error')
                            },

                success     : function (data)
                            {
                                swal.close()

                                if (data.success) swalShowMessage('Sukses', data.message, 'success')
                                else swalShowMessage('Error', data.message || 'An Error Occured.', 'error')
                            },

                async       : false,
                cache       : false
            })
        }), 2e3)
    })
</script>
@endpush