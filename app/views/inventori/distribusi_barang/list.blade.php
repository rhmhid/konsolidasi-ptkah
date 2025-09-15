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
                                <span class="las la-exchange-alt text-dark me-4"></span>
                                Daftar Distribusi Barang
                            </h2>
                        </div>

                        <a class="btn btn-sm rounded-1 btn-light-dark db-add" href="javascript:void(0)">
                            <i class="bi bi-plus-square-fill fs-3"></i>
                            Tambah
                        </a>
                    </div>
                </div>

                <form method="post" id="form-db" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                           <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="sdb-period" class="form-control form-control-sm rounded-1" readonly="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Gudang Pengirim</label>
                                {!! $cmb_gudang_from !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Gudang Penerima</label>
                                {!! $cmb_gudang_to !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Kategori Barang</label>
                                {!! $cmb_kel_brg !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Barang</label>
                                <input type="text" id="SKodeNama" class="form-control form-control-sm rounded-1" />
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode Transfer</label>
                                <input type="text" id="SKodeTrans" class="form-control form-control-sm rounded-1" />
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Status Konfirmasi</label>
                                {!! $cmb_konfirmasi !!}
                            </div>

                            <div class="col-lg-8">
                               &nbsp;
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
                <table id="myTableDB" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Tanggal Transfer</th>
                            <th class="border-start py-5">Kode. Transfer</th>
                            <th class="border-start py-5">Barang</th>
                            <th class="border-start py-5">Jumlah Kirim</th>
                            <th class="border-start py-5">Keterangan</th>
                            <th class="border-start py-5">Pengirim</th>
                            <th class="border-start py-5">Penerima</th>
                            <th class="border-start py-5">User Input</th>
                            <th class="border-start py-5">Status Konfirmasi</th>
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
<div class="modal fade" id="mdl-form-db" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-db" aria-hidden="true">
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

    $('#sdb-period').flatpickr({
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

    // DATATABLE myTableDB
    const ajaxOptions = {
        url     : "{{ route('api.inventori.distribusi_barang') }}",
        data    : function (params)
                {
                    params.sdate        = sPeriod
                    params.edate        = ePeriod
                    params.gid          = $("#sGid").val()
                    params.reff_gid     = $("#sReffGid").val()
                    params.kbid         = $("#sKbid").val()
                    params.kode_nama    = $("#SKodeNama").val()
                    params.kode_trans   = $("#SKodeTrans").val()
                    params.is_konfirm   = $("#sKonfirm").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'transfer_date',
                        name: 'transfer_date',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'transfer_code',
                        name: 'transfer_code',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'barang',
                        name: 'barang',
                    },
                    {
                        data: 'vol',
                        name: 'vol',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'ket_item',
                        name: 'ket_item',
                    },
                    {
                        data: 'pengirim',
                        name: 'pengirim',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'penerima',
                        name: 'penerima',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'user_input',
                        name: 'user_input',
                    },
                    {
                        data: 'status_konfirm',
                        name: 'status_konfirm',
                        className: 'dt-body-center',

                        render: function (data)
                        {
                            let status = 'danger'
                            let txt = 'Belum Konfirmasi'

                            if (data != 0)
                            {
                                status = 'success'
                                txt = 'Konfirmasi'
                            }

                            let $icon = `<span class="badge badge-light-${status}">
                                            ${txt}
                                        </span>`

                            return $icon
                        }
                    },
                    {
                        data: 'tbid',
                        name: 'tbid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            if (row.button_active == 't')
                                $btnFungsi = `<a href="javascript:void(0)" data-tbid="${row.tbid}" title="Cetak" class="btn btn-dark btn-sm btn-icon fs-8 db-cetak">
                                                <i class="las la-print"></i>
                                            </a>
                                            &nbsp;<a href="javascript:void(0)" data-tbid="${row.tbid}" title="Hapus" class="btn btn-danger btn-sm btn-icon fs-8 db-hapus">
                                                <i class="las la-trash-alt"></i>
                                            </a>`
                            else
                                $btnFungsi = ``

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
                '#myTableDB',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-db').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })
    
    $('.db-add').click(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        showLoading()

        let getForm = async function ()
        {
            let link = "{{ route('api.inventori.distribusi_barang.create') }}"

            try {
                result = await $.ajax({
                    url     : link,
                    type    : 'GET',
                    data    : { }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal memproses data.', 'error')
            }
        }

        getForm().then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-db', res, null, true)
        })
    })

    $('#myTableDB').on('click', '.db-cetak', function ()
    {
        const tbid = $(this).data('tbid')

        let link = "{{ route('inventori.distribusi_barang.cetak', ['myid' => ':myid']) }}"
            link = link.replace(':myid', tbid)

        NewWindow(link, 'Distribusi Barang Cetak', 800, 600, 'yes')
        return false
    })

    $('#myTableDB').on('click', '.db-hapus', function ()
    {
        const tbid = $(this).data('tbid')

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
                    let link = "{{ route('api.inventori.distribusi_barang.delete', ['myid' => ':myid']) }}"
                        link = link.replace(':myid', tbid)

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