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
                                <span class="las la-recycle text-dark me-4"></span>
                                Form Reset Data
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="border-bottom border-gray-300">
                    <form method="post" id="form-reset-data" novalidate="">
                        <input type="hidden" name="_method" value="patch" />

                        <!--begin::Compact form-->
                        <div class="p-6 pb-0">
                            <div class="row g-0 gx-4">
                                <div class="col-lg-6">
                                    <label class="text-dark fw-bold fs-7 pb-2">Opsi Reset</label>
                                    <select name="opsi_reset" id="opsi_reset" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Opsi Reset" required="">
                                        <option value=""></option>
                                        <option value="all">Reset All</option>
                                        <option value="only_trans">Reset Hanya Transaksi</option>
                                    </select>
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-dark fw-bold fs-7 pb-2">Tipe Eksekusi</label>
                                    <select name="tipe_reset" id="tipe_reset" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Tipe Eksekusi" required="">
                                        <option value=""></option>
                                        <option value="echo">Echo SQL saja, tanpa eksekusi</option>
                                        <option value="exec">Langsung Eksekusi</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                                <button type="submit" class="btn btn-dark btn-sm rounded-1 w-100" id="me_btn_simpan">Simpan</button>
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
    $('#form-reset-data').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const $form = $(this)
        const url = $form.attr('action')
        const $opsi_reset = $('[name="opsi_reset"]').val()
        const $tipe_reset = $('[name="tipe_reset"]').val()

        if (!$opsi_reset)
            return swalShowMessage('Warning!', "Opsi reset belum dipilih.", 'warning')

        if (!$tipe_reset)
            return swalShowMessage('Warning!', "Tipe Eksekusi belum dipilih.", 'warning')

        const payload = {
            _method: $form.find('[name="_method"]').val(),
            opsi_reset: $form.find('[name="opsi_reset"]').val(),
            tipe_reset: $form.find('[name="tipe_reset"]').val(),
        }

        showLoading()

        setTimeout((function ()
        {
            $.ajax({
                url         : "{{ route('api.migrasi_data.reset_data.save') }}",
                data        : payload,
                type        : 'POST',

                error       : function (err)
                            {
                                swal.close()

                                swalShowMessage('Error', err?.responseJSON?.message || 'An Error Occured.', 'success')
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