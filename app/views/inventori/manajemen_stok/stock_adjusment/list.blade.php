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
                                <span class="las la-box text-dark me-4"></span>
                                Daftar Penyesuaian Stok
                            </h2>
                        </div>

                        <a class="btn btn-sm rounded-1 btn-light-dark adj-add" href="javascript:void(0)">
                            <i class="bi bi-plus-square-fill fs-3"></i>
                            Tambah
                        </a>
                    </div>
                </div>

                <form method="post" id="form-adj" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="spc-period" class="form-control form-control-sm rounded-1" readonly="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Gudang</label>
                                {!! $cmb_gudang !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Kategori Barang</label>
                                {!! $cmb_kel_brg !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3">
                            <div class="col-lg-9">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Barang</label>
                                <input type="text" id="sKodeNama" class="form-control form-control-sm rounded-1" placeholder="Masukan Kode / Nama Barang" />
                            </div>

                            <div class="col-lg-1">
                                <label class="text-dark fw-bold fs-8 pb-2 d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-dark rounded-1 me-4 w-100" id="btncari">
                                    <i class="la la-search"></i>
                                    Cari
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

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTableAdj" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Gudang</th>
                            <th class="border-start py-5">Tanggal Adjustment</th>
                            <th class="border-start py-5">Kode Adjustment</th>
                            <th class="border-start py-5">Kode Barang</th>
                            <th class="border-start py-5">Nama Barang</th>
                            <th class="border-start py-5">Kode Satuan</th>
                            <th class="border-start py-5">Stock System Sebelum Adjustment</th>
                            <th class="border-start py-5">Adjustment</th>
                            <th class="border-start py-5">Stock Fisik Sesudah Adjustment</th>
                            <th class="border-start py-5">Keterangan</th>
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
<div class="modal fade" id="mdl-form-adj" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-adj" aria-hidden="true">
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

    $('#spc-period').flatpickr({
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

    // DATATABLE myTableAdj
    const ajaxOptions = {
        url     : "{{ route('api.inventori.manajemen_stok.stock_adjusment') }}",
        data    : function (params)
                {
                    params.sdate        = sPeriod
                    params.edate        = ePeriod
                    params.gid          = $("#sGid").val()
                    params.kbid         = $("#sKbid").val()
                    params.kode_nama    = $("#sKodeNama").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'gudang',
                        name: 'gudang',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'adjdate',
                        name: 'adjdate',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'adjcode',
                        name: 'adjcode',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'kode_brg',
                        name: 'kode_brg',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_brg',
                        name: 'nama_brg',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'kode_satuan',
                        name: 'kode_satuan',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'bstok',
                        name: 'bstok',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'vol',
                        name: 'vol',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'fstok',
                        name: 'fstok',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                    },
                    {
                        data: 'user_input',
                        name: 'user_input',
                    },
                    {
                        data: 'aid',
                        name: 'aid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-aid="${row.aid}" title="Hapus" class="btn btn-danger btn-sm btn-icon fs-8 adj-hapus">
                                            <i class="las la-trash-alt"></i>
                                        </a>`

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
                '#myTableAdj',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-adj').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.adj-add').click(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const pid = ''
        const user = ''
        const tgl = ''

        let notes = 'Adjustment Stok Barang, PID : ' + pid + ' - Nama : ' + user + ' - Tanggal : ' + tgl

        modalAuth(2, 'form-adj', notes)
    })

    function NextStep (form, alasan)
    {
        let link = "{{ route('inventori.manajemen_stok.stock_adjusment.stock') }}"

        window.location.replace(link)
    }

    $('#myTableAdj').on('click', '.adj-hapus', function ()
    {
        const aid = $(this).data('aid')

        Swal.fire({
            html: 'Apakah Anda Yakin <span class="badge badge-danger">Menghapus Data Adjustment</span> ?',
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
                    let link = "{{ route('api.inventori.manajemen_stok.stock_adjusment.delete', ['myid' => ':myid']) }}"
                        link = link.replace(':myid', aid)

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