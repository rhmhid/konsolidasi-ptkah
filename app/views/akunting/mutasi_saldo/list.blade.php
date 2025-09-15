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
                                <span class="las la-balance-scale text-dark me-4"></span>
                                Daftar Mutasi Saldo
                            </h2>
                        </div>

                        <a class="btn btn-sm rounded-1 btn-light-dark mutasi-add" href="javascript:void(0)">
                            <i class="bi bi-plus-square-fill fs-3"></i>
                            Tambah
                        </a>
                    </div>
                </div>

                <form method="post" id="form-mutasi-saldo" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                           <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="sMutasi-period" class="form-control form-control-sm rounded-1" readonly="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Kas / Bank Asal</label>
                                {!! $cmb_bank_from !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Kas / Bank Tujuan</label>
                                {!! $cmb_bank_to !!}
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
                <table id="myTableMutasiSaldo" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Tanggal Mutasi</th>
                            <th class="border-start py-5">Kode Mutasi</th>
                            <th class="border-start py-5">Kas / Bank Asal</th>
                            <th class="border-start py-5">Kas / Bank Tujuan</th>
                            <th class="border-start py-5">Nominal</th>
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
<div class="modal fade" id="mdl-form-mutasi" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-mutasi" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-xl">
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

    $('#sMutasi-period').flatpickr({
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

    // DATATABLE myTableMutasiSaldo
    const ajaxOptions = {
        url     : "{{ route('api.akunting.mutasi_saldo') }}",
        data    : function (params)
                {
                    params.mutasi_speriod   = sPeriod
                    params.mutasi_eperiod   = ePeriod
                    params.bank_from        = $("#sBank-From").val()
                    params.bank_to          = $("#sBank-To").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'mutasi_date',
                        name: 'mutasi_date',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'mutasi_code',
                        name: 'mutasi_code',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'bank_from',
                        name: 'bank_from',
                    },
                    {
                        data: 'bank_to',
                        name: 'bank_to',
                    },
                    {
                        data: 'nominal',
                        name: 'nominal',
                        className: 'dt-body-end',
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
                        data: 'msid',
                        name: 'msid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-msid="${row.msid}" title="Cetak" class="btn btn-primary btn-sm btn-icon fs-8 mutasi-cetak">
                                            <i class="las la-print"></i>
                                        </a>
                                        <a href="javascript:void(0)" data-msid="${row.msid}" title="Hapus" class="btn btn-danger btn-sm btn-icon fs-8 mutasi-hapus">
                                            <i class="las la-times"></i>
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
                '#myTableMutasiSaldo',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-mutasi-saldo').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })
    
    $('.mutasi-add').click(function (e)
    {
        openMutasiForm()
    })

    $('#myTableMutasiSaldo').on('click', '.mutasi-cetak', function ()
    {
        const msid = $(this).data('msid')

        let link = "{{ route('akunting.mutasi_saldo.cetak', ['myid' => ':myid']) }}"
            link = link.replace(':myid', msid)

        NewWindow(link, 'mutasi_saldo_cetak', 1000, 500, 'yes')
        return false
    })

    $('#myTableMutasiSaldo').on('click', '.mutasi-hapus', function ()
    {
        const msid = $(this).data('msid')

        Swal.fire({
            html: 'Apakah Anda Yakin <span class="badge badge-danger">Menghapus Data Mutasi</span> ?',
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
                    let link = "{{ route('api.akunting.mutasi_saldo.delete', ['myid' => ':myid']) }}"
                        link = link.replace(':myid', msid)

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

    function openMutasiForm ()
    {
        showLoading()

        getForm().then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-mutasi', res, null, true)
        })
    }

    async function getForm ()
    {
        try {
            result = await $.ajax({
                url     : "{{ route('api.akunting.mutasi_saldo.create') }}" ,
                type    : 'GET',
                data    : { }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush