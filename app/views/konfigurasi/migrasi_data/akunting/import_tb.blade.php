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
                                Form Upload Data Trial Balance
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="border-bottom border-gray-300">
                    <form method="post" id="form-tb" enctype="multipart/form-data" novalidate="">
                        <!--begin::Compact form-->
                        <div class="p-6 pb-0">
                            <div class="row g-0 gx-4">
                                <div class="col-lg-3">
                                    <label class="text-dark fw-bold fs-7 pb-2 required">Tanggal Migrasi</label>
                                    <div class="input-group">
                                        <input type="text" name="trans_date" id="trans_date" class="form-control form-control-sm rounded-1 mydate-time" required="" value="{{ $now }}" />
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <label class="text-dark fw-bold fs-7 pb-2 required">C.O.A</label>
                                    {!! $cmb_coa !!}
                                </div>

                                <div class="col-lg-4">
                                    <label class="text-dark fw-bold fs-7 pb-2 required">Upload File</label>
                                    <input type="file" name="chooseFile" id="chooseFile" class="form-control form-control-sm rounded-1 w-100" required="" />
                                </div>
                            </div>

                            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                                <button type="button" class="btn btn-danger btn-sm rounded-1 me-4 w-100" id="btn-reset">
                                    <i class="la la-recycle"></i> Reset Data TB
                                </button>
                                <button type="button" class="btn btn-info btn-sm rounded-1 me-4 w-100" id="btn-download">
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
    $(".mydate-time").flatpickr({
        enableTime: true,
        dateFormat: "d-m-Y H:i",
        time_24hr: true,
        minuteIncrement: 1
    })

    $('#btn-download').click(function (e)
    {
        showLoading()

        setTimeout((function ()
        {
            let href = "{{ route('api.migrasi_data.akunting.import_tb.download') }}"

            const name = 'File Migrasi TB - ' + moment().format('DD-MM-YYYY') + '.xls'

            exportExcel({
                name,
                url: href,
                params: { }
            }).finally(() => {
                Swal.close()
            })
        }), 2e3)
    })

    $('#form-tb').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const $form = $(this)
        const $chooseFile = $('[name="chooseFile"]')
        const file = $chooseFile[0].files[0]

        if (!$chooseFile.val())
            return swalShowMessage('Warning!', "Template (.xls) Belum Dipilih.", 'warning')

        if (!file || !file.name.endsWith('.xls'))
            return swalShowMessage('Warning!', "Hanya file .xls yang diperbolehkan.", 'warning')

        const payload = new FormData(this)
            payload.append('_method', 'patch') // ganti ajax method post menjadi patch

        let formSubmitUrl = "{{ route('api.migrasi_data.akunting.import_tb.save') }}"

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

                if (data.success)
                {
                    $("#chooseFile").val('');

                    swalShowMessage('Sukses', data.message, 'success')
                }
                else swalShowMessage('Error', data.message || 'An Error Occured.', 'error')
            })
            .fail( err => {
                swal.close()

                swalShowMessage('Error', err?.responseJSON?.message || 'An Error Occured.', 'error')
            })
        }), 2e3)
    })

    $('#btn-reset').click(function ()
    {
        Swal.fire({
            html: 'Apakah Anda Yakin <span class="badge badge-danger">Menghapus Data TB</span> ?',
            icon: "info",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Ya, Hapus !",
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: "btn btn-dark",
                cancelButton: 'btn btn-danger'
            }
        }).then((result) =>
        {
            if (result.isConfirmed)
            {
                showLoading()

                let getAction = async function ()
                {
                    let link = "{{ route('api.migrasi_data.akunting.import_tb.reset') }}"

                    try {
                        result = await $.ajax({
                            url     : link,
                            type    : 'POST',
                            data    : { }
                        })

                        return result
                    } catch (error) {
                        Swal.close()

                        swalShowMessage('Gagal', 'Gagal memproses data.', 'error')
                    }
                }

                getAction().then((res) => {
                    Swal.close()

                    if (res.success) swalShowMessage('Sukses', res.message, 'success')
                    else swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }
        })
    })
</script>
@endpush