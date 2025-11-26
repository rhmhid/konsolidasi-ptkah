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
                                <span class="las la-dolly text-dark me-4"></span>
                                Daftar Penerimaan Barang
                            </h2>
                        </div>

                        <a class="btn btn-sm rounded-1 btn-light-dark pb-add" data-grid="0" href="javascript:void(0)">
                            <i class="bi bi-plus-square-fill fs-3"></i>
                            Tambah
                        </a>
                    </div>
                </div>

                <form method="post" id="form-pb" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                           <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="spb-period" class="form-control form-control-sm rounded-1" readonly="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Gudang Penerima</label>
                                {!! $cmb_gudang !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Supplier</label>
                                {!! $cmb_supplier !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode Penerimaan</label>
                                <input type="text" id="sGrCode" class="form-control form-control-sm rounded-1" />
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode PO</label>
                                <input type="text" id="sPoCode" class="form-control form-control-sm rounded-1" />
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode/Nama Barang</label>
                                <input type="text" id="sKodeNama" class="form-control form-control-sm rounded-1" />
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">No. Faktur</label>
                                <input type="text" id="sNoFaktur" class="form-control form-control-sm rounded-1" />
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3">
                            <div class="col-lg-11">
                                <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                                <input type="text" id="sKeterangan" class="form-control form-control-sm rounded-1" />
                            </div>

                            <div class="col-lg-1">
                                <label class="text-dark fw-bold fs-8 pb-2 d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-dark rounded-1 me-4 w-100" id="btncari">
                                    <i class="la la-search"></i>
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Compact form-->
                </form>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTablePB" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Tanggal/Kode Penerimaan</th>
                            <th class="border-start py-5">No. Faktur/Kode PO</th>
                            <th class="border-start py-5">Supplier/Gudang</th>
                            <th class="border-start py-5">Barang</th>
                            <th class="border-start py-5">Jumlah Terima</th>
                            <th class="border-start py-5">Harga</th>
                            <th class="border-start py-5">User Input</th>
                            <th class="border-start py-5 w-100px">Fungsi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Ubah-->
<div class="modal fade" id="mdl-form-pb" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-pb" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Ubah-->
@endsection

@push('script')
<script type="text/javascript">
    var sPeriod, ePeriod

    $('#spb-period').flatpickr({
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

    // DATATABLE myTablePB
    const ajaxOptions = {
        url     : "{{ route('api.pembelian.penerimaan_barang') }}",
        data    : function (params)
                {
                    params.sdate        = sPeriod
                    params.edate        = ePeriod
                    params.gid          = $("#sGid").val()
                    params.suppid       = $("#sSuppid").val()
                    params.grcode       = $("#sGrCode").val()
                    params.pocode       = $("#sPoCode").val()
                    params.kode_nama    = $("#sKodeNama").val()
                    params.no_faktur    = $("#sNoFaktur").val()
                    params.keterangan   = $("#sKeterangan").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'grdate',
                        name: 'grdate',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            let $ret = row.grdate + `<br /><I class="text-danger fw-semibold text-start">${row.grcode}</I>`

                            return $ret
                        }
                    },
                    {
                        data: 'no_faktur',
                        name: 'no_faktur',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            let $ret = row.no_faktur

                            if (row.asal_brg == 1)
                                $ret = $ret + `<br /><I class="text-danger fw-semibold text-start">${row.pocode}</I>`

                            return $ret
                        }
                    },
                    {
                        data: 'nama_supp',
                        name: 'nama_supp',

                        render: function (data, type, row, meta)
                        {
                            let $ret = row.nama_supp + `<br /><I class="text-danger fw-semibold">${row.nama_gudang}</I>`

                            return $ret
                        }
                    },
                    {
                        data: 'barang',
                        name: 'barang',
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'harga',
                        name: 'harga',
                        className: 'dt-body-right',
                    },
                    {
                        data: 'user_input',
                        name: 'user_input',
                    },
                    {
                        data: 'grid',
                        name: 'grid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = "";

                            if (row.grcode != '')
                            {
                                $btnFungsi = `<button class="btn btn-sm btn-icon btn-dark" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="las la-ellipsis-h fs-4"></i>
                                            </button>

                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <li>
                                                    <a href="javascript:void(0)" class="dropdown-item py-2 gr-detail" data-grid="${row.grid}">
                                                        <i class="las la-eye fs-3 text-dark me-2"></i> Detail Penerimaan
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" class="dropdown-item py-2 gr-cetak" data-grid="${row.grid}">
                                                        <i class="las la-print fs-3 text-dark me-2"></i> Cetak
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" class="dropdown-item py-2 gr-hapus" data-grid="${row.grid}">
                                                        <i class="las la-trash fs-3 text-dark me-2"></i> Hapus
                                                    </a>
                                                </li>
                                            </ul>`
                            }

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTablePB',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-pb').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })
    
    $('.pb-add').click(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const grid = $(this).data('grid')

        openModalPB(grid)
    })

    $('#myTablePB').on('click', '.gr-detail', function ()
    {
        const grid = $(this).data('grid')

        openModalPB(grid)
    })

    function openModalPB (grid = 0)
    {
        showLoading()

        getForm(grid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-pb', res, null, true)
        })
    }

    async function getForm (grid)
    {
        let result
        let link = grid == 0 ? "{{ route('api.pembelian.penerimaan_barang.create') }}" : "{{ route('api.pembelian.penerimaan_barang.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { grid: grid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }

    $('#myTablePB').on('click', '.gr-cetak', function ()
    {
        const grid = $(this).data('grid')

        let link = "{{ route('pembelian.penerimaan_barang.cetak', ['myid' => ':myid']) }}"
            link = link.replace(':myid', grid)

        NewWindow(link, 'Penerimaan Cetak', 1000, 500, 'yes')
        return false
    })

    $('#myTablePB').on('click', '.gr-hapus', function ()
    {
        const grid = $(this).data('grid')

        Swal.fire({
            html: 'Apakah Anda Yakin <span class="badge badge-danger">Menghapus Data Transaksi</span> ?',
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
                    let link = "{{ route('api.pembelian.penerimaan_barang.delete', ['myid' => ':myid']) }}"
                        link = link.replace(':myid', grid)

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

                    if (res.success)
                    {
                        swalShowMessage('Sukses', res.message, 'success')
                        .then((result) =>
                        {
                            if (result.isConfirmed) table.ajax.reload(null, false)
                        })
                    }
                    else swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }
        })
    })
</script>
@endpush