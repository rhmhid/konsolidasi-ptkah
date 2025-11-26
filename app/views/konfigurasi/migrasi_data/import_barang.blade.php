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
                                <span class="las la-prescription-bottle text-dark me-4"></span>
                                Form Upload Data Barang
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="border-bottom border-gray-300">
                    <form method="post" id="form-upload-barang" novalidate="">
                        <!--begin::Compact form-->
                        <div class="p-6 pb-0">
                            <div class="row g-0 gx-4">
                                <div class="col-lg-12">
                                    <label class="text-dark fw-bold fs-7 pb-2 required">Upload File</label>
                                    <input type="file" name="chooseFile" id="chooseFile" class="form-control form-control-sm rounded-1 w-100" required="" />
                                </div>
                            </div>

                            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                                <button type="button" class="btn btn-danger btn-sm rounded-1 me-4 w-100" id="down-file">
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

                <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                    <div class="row g-4">
                        <!-- Tabel 1 -->
                        <div class="col-lg-6">
                            <div class="table-responsive">
                                <table id="myTableKategori" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 fs-8 w-100">
                                    <thead class="bg-dark text-uppercase fs-7">
                                        <tr class="fw-bold text-white">
                                            <th class="border-start py-5">ID Kategori</th>
                                            <th class="border-start py-5">Kode Kategori Barang</th>
                                            <th class="border-start py-5">Nama Kategori Barang</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tabel 2 -->
                        <div class="col-lg-6">
                            <div class="table-responsive">
                                <table id="myTableSatuan" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 fs-8 w-100">
                                    <thead class="bg-dark text-uppercase fs-7">
                                        <tr class="fw-bold text-white">
                                            <th class="border-start py-5">Kode Satuan</th>
                                            <th class="border-start py-5">Nama Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    // DATATABLE myTableKategori
    const ajaxOptionsKategori = {
        url: "{{ route('api.migrasi_data.import_barang.list_kategori') }}",
    }

    const optionsKategori = {
        columns:[
                    {
                        data: 'kbid', 
                        name: 'kbid',
                        className: 'text-center',
                    },
                    {
                        data: 'kode_kategori', 
                        name: 'kode_kategori',
                        className: 'text-center',
                    },
                    {
                        data: 'nama_kategori', 
                        name: 'nama_kategori',
                    },
                ],

        order: [[0, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    tableKategori = setupDataTable(
                '#myTableKategori',
                ajaxOptionsKategori,
                optionsKategori
            )

    // DATATABLE myTableSatuan
    const ajaxOptionsSatuan = {
        url: "{{ route('api.migrasi_data.import_barang.list_satuan') }}",
    }

    const optionsSatuan = {
        columns:[
                    {
                        data: 'kode_satuan', 
                        name: 'kode_satuan',
                        className: 'text-center',
                    },
                    {
                        data: 'nama_satuan', 
                        name: 'nama_satuan',
                    },
                ],

        order: [[0, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    tableSatuan = setupDataTable(
                '#myTableSatuan',
                ajaxOptionsSatuan,
                optionsSatuan
            )

    $('#down-file').click(function ()
    {
        showLoading()

        setTimeout((function ()
        {
            const href = "{{ route('api.migrasi_data.akunting.import_barang.download') }}"
            const name = 'File Migrasi Barang - ' + moment().format('DD-MM-YYYY') + '.xls'

            exportExcel({
                name,
                url: href,
                params: {}
            }).finally(() => {
                Swal.close()
            })
        }), 2e3)
    })

    $('#form-upload-barang').submit(function (e)
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

        let formSubmitUrl = "{{ route('api.migrasi_data.import_barang.save') }}"

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
</script>
@endpush