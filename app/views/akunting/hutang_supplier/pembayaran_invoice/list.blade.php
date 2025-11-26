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
                                <span class="las la-file-invoice text-dark me-4"></span>
                                Daftar Pembayaran Invoice
                            </h2>
                        </div>

                        <a class="btn btn-sm rounded-1 btn-light-dark app-add" data-appid="0" href="javascript:void(0)">
                            <i class="bi bi-plus-square-fill fs-3"></i>
                            Tambah
                        </a>
                    </div>
                </div>

                <form method="post" id="form-app" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                           <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="sAP-period" class="form-control form-control-sm rounded-1" readonly="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Nama Supplier</label>
                                {!! $cmb_supp !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">AP#</label>
                                <input type="text" id="sPayCode" class="form-control form-control-sm rounded-1" placeholder="Masukan No. Invoice Untuk Pencarian" />
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
                <table id="myTableAPP" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Tanggal Payment</th>
                            <th class="border-start py-5">No. Payment</th>
                            <th class="border-start py-5">Nama Supplier</th>
                            <th class="border-start py-5">Amount</th>
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
<div class="modal fade" id="mdl-form-app" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-app" aria-hidden="true">
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
    var sPeriod = '{{ $sPeriod }}'
    var ePeriod = '{{ $ePeriod }}'

    $('#sAP-period').flatpickr({
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

    // DATATABLE myTableAPP
    const ajaxOptions = {
        url     : "{{ route('api.akunting.hutang_supplier.pembayaran_invoice') }}",
        data    : function (params)
                {
                    params.sdate    = sPeriod
                    params.edate    = ePeriod
                    params.suppid   = $("#sSuppid").val()
                    params.paycode  = $("#sPayCode").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'paydate',
                        name: 'paydate',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'paycode',
                        name: 'paycode',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_supp',
                        name: 'nama_supp',
                    },
                    {
                        data: 'totpay',
                        name: 'totpay',
                        className: 'dt-body-right',
                    },
                    {
                        data: 'user_input',
                        name: 'user_input',
                    },
                    {
                        data: 'appid',
                        name: 'appid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<button class="btn btn-sm btn-icon btn-dark" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="las la-ellipsis-h fs-4"></i>
                                        </button>

                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item py-2 app-edit" data-appid="${row.appid}">
                                                    <i class="las la-edit fs-3 text-dark me-2"></i> Detail Invoice
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item py-2 app-detail" data-glid="${row.glid}">
                                                    <i class="las la-eye fs-3 text-dark me-2"></i> Detail Jurnal
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item py-2 app-cetak-bukti" data-appid="${row.appid}">
                                                    <i class="las la-print fs-3 text-dark me-2"></i> Bukti Pembayaran
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item py-2 app-hapus" data-appid="${row.appid}">
                                                    <i class="las la-trash-alt fs-3 text-dark me-2"></i> Hapus
                                                </a>
                                            </li>
                                        </ul>`

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
                '#myTableAPP',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-app').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.app-add').on('click', function (e)
    {
        const appid = $(this).data('appid')

        openModalAPP(appid)
    })

    $('#myTableAPP').on('click', '.app-edit', function ()
    {
        const appid = $(this).data('appid')

        openModalAPP(appid)
    })

    function openModalAPP (appid = 0)
    {
        showLoading()

        getForm(appid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-app', res, null, true)
        })
    }

    async function getForm (appid)
    {
        let result
        let link = appid == 0 ? "{{ route('api.akunting.hutang_supplier.pembayaran_invoice.create') }}" : "{{ route('api.akunting.hutang_supplier.pembayaran_invoice.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { appid: appid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }

    $('#myTableAPP').on('click', '.app-detail', function ()
    {
        const glid = $(this).data('glid')

        let getForm = async function (glid)
        {
            let link = "{{ route('api.akunting.daftar_jurnal.detail', ['myglid' => ':myglid']) }}"
                link = link.replace(':myglid', glid)

            try {
                result = await $.ajax({
                    url     : link,
                    type    : 'GET',
                    data    : { }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
            }
        }

        showLoading()

        getForm(glid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-app', res, null, true)
        })
    })

    $('#myTableAPP').on('click', '.app-cetak-bukti', function ()
    {
        const appid = $(this).data('appid')

        let link = "{{ route('akunting.hutang_supplier.pembayaran_invoice.cetak_bukti', ['myid' => ':myid']) }}"
            link = link.replace(':myid', appid)

        NewWindow(link, 'Invoice Pembelian Cetak', 1000, 500, 'yes')
        return false
    })

    $('#myTableAPP').on('click', '.app-hapus', function ()
    {
        const appid = $(this).data('appid')

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
                    let link = "{{ route('api.akunting.hutang_supplier.pembayaran_invoice.delete', ['myid' => ':myid']) }}"
                        link = link.replace(':myid', appid)

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