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
                                <span class="las la-users text-dark me-4"></span>
                                Form Upload Data Pegawai
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="border-bottom border-gray-300">
                    <form method="post" id="form-upload-pegawai" novalidate="">
                        <!--begin::Compact form-->
                        <div class="p-6 pb-0">
                            <div class="row g-0 gx-4">
                                <div class="col-lg-12">
                                    <label class="text-dark fw-bold fs-7 pb-2 required">Upload File</label>
                                    <input type="file" name="chooseFile" id="chooseFile" class="form-control form-control-sm rounded-1 w-100" required="" />
                                </div>
                            </div>

                            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                                <button type="button" class="btn btn-danger btn-sm rounded-1 me-4 w-100" id="me_btn_download">
                                        <i class="la la-file-excel"></i> Download Template (.xls)
                                </button>
                                <button type="submit" class="btn btn-dark btn-sm rounded-1 w-100" id="me_btn_upload">
                                    <i class="la la-file-excel"></i> Upload Template (.xls)
                                </button>
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
    $('#me_btn_download').click(function (e)
    {
        showLoading()

        setTimeout((function ()
        {
            swal.close()

            myUrl = "{{ route('migrasi_data.download_file', ['xls' => 'std_pegawai']) }}"

            window.location.replace(myUrl)
        }), 2e3)
    })

    $('#form-upload-pegawai').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const $form = $(this)
        const url = $form.attr('action')
        const $chooseFile = $('[name="chooseFile"]')

        if (!$chooseFile.val())
            return swalShowMessage('Warning!', "Template (.xls) Belum Dipilih.", 'warning')

        const payload = new FormData(this)
            payload.append('_method', 'patch') // ganti ajax method post menjadi patch

        formSubmitUrl = "{{ route('api.migrasi_data.updol.pegawai.save') }}"

        showLoading()

        setTimeout((function ()
        {
            doAjax(
                formSubmitUrl,
                payload,
                "POST"
            )
            .done( data => {
                swal.close()

                if (data.success) swalShowMessage('Sukses', data.message, 'success')
                else swalShowMessage('Error', data.message || 'An Error Occured.', 'error')
            })
            .fail( err => {
                swal.close()

                swalShowMessage('Error', err?.responseJSON?.message || 'An Error Occured.', 'error')
            })
        }), 2e3)
    })
</script>
@endpush