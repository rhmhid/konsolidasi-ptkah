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
                                Daftar Manual A/R
                            </h2>
                        </div>

                        <a class="btn btn-sm rounded-1 btn-light-dark mar-add" data-maid="0" href="javascript:void(0)">
                            <i class="bi bi-plus-square-fill fs-3"></i>
                            Tambah
                        </a>
                    </div>
                </div>

                <form method="post" id="form-mar" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                           <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="sMar-period" class="form-control form-control-sm rounded-1" readonly="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Nama Customer</label>
                                {!! $cmb_cust !!}
                            </div>

                            <div class="col-lg-4 div-bank">
                                <label class="text-dark fw-bold fs-7 pb-2">Nama Bank</label>
                                {!! $cmb_bank !!}
                            </div>

                            <div class="col-lg-4 div-karyawan">
                                <label class="text-dark fw-bold fs-7 pb-2">Nama Karyawan</label>
                                {!! $cmb_karyawan !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">No. Invoice</label>
                                <input type="text" id="sNoInv" class="form-control form-control-sm rounded-1" placeholder="Masukan No. Invoice Untuk Pencarian" />
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3">
                            <div class="col-lg-4 sdiv-non-doctor"></div>

                            <div class="col-lg-7"></div>

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
                <table id="myTablemar" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Tanggal A/R</th>
                            <th class="border-start py-5">No. A/R</th>
                            <th class="border-start py-5">No. Invoice</th>
                            <th class="border-start py-5">Nama Customer</th>
                            <th class="border-start py-5">Amount</th>
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
<div class="modal fade" id="mdl-form-mar" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-mar" aria-hidden="true">
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

    $('#sMar-period').flatpickr({
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

    hideDetailCust();

    $("#sCust").change(function ()
    {
        hideDetailCust()
    })

    function hideDetailCust ()
    {
        let custid = $('#sCust').val()
        $('.div-bank').hide()
        $('.div-karyawan').hide()

        if (custid == -1){
            $('.div-bank').hide()
            $('.div-karyawan').show()
        }else if (custid == -2){
            $('.div-bank').show()
            $('.div-karyawan').hide()
        }else if (custid == -3){
            $('.div-bank').hide()
            $('.div-karyawan').hide()
        }

        $('#sbank_id').val(null).trigger('change');
        $('#spegawai_id').val(null).trigger('change');


    }



    // DATATABLE myTablemar
    const ajaxOptions = {
        url     : "{{ route('api.akunting.piutang_pelanggan.manual_ar') }}",
        data    : function (params)
                {
                    params.sdate        = sPeriod
                    params.edate        = ePeriod
                    params.custid       = $("#sCust").val()
                    params.no_inv       = $("#sNoInv").val()
                    params.bank_id      = $("#sbank_id").val()
                    params.pegawai_id   = $("#spegawai_id").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'ardate',
                        name: 'ardate',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'arcode',
                        name: 'arcode',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'no_inv',
                        name: 'no_inv',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_customer',
                        name: 'nama_customer',

                        render: function (data, type, row, meta)
                        {
                            let $ret = row.nama_customer
                            if (row.suppid == -1)
                                $ret += `<br /><I class="text-danger fw-semibold">[ ${row.nama_pegawai} ]</I>`

                            if (row.suppid == -2)
                                $ret += `<br /><I class="text-danger fw-semibold">[ ${row.bank_nama} ]</I>`

                            return $ret
                        }
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        className: 'dt-body-right',
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
                        data: 'maid',
                        name: 'maid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-maid="${row.maid}" title="Detail" class="btn btn-info btn-sm btn-icon fs-8 mar-edit">
                                            <i class="las la-edit"></i>
                                        </a>
                                        &nbsp;<a href="javascript:void(0)" data-glid="${row.glid}" title="Detail Jurnal" class="btn btn-dark btn-sm btn-icon fs-8 mar-detail">
                                            <i class="las la-eye"></i>
                                        </a>
                                        &nbsp;<a href="javascript:void(0)" data-maid="${row.maid}" title="Cetak" class="btn btn-primary btn-sm btn-icon fs-8 mar-cetak">
                                            <i class="las la-print"></i>
                                        </a>
                                        &nbsp;<a href="javascript:void(0)" data-maid="${row.maid}" title="Hapus" class="btn btn-danger btn-sm btn-icon fs-8 mar-hapus">
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
                '#myTablemar',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-mar').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.mar-add').on('click', function (e)
    {
        const maid = $(this).data('maid')

        openModalDSP(maid)
    })

    $('#myTablemar').on('click', '.mar-edit', function ()
    {
        const maid = $(this).data('maid')

        openModalDSP(maid)
    })

    function openModalDSP (maid = 0)
    {
        showLoading()

        getForm(maid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-mar', res, null, true)
        })
    }

    async function getForm (maid)
    {
        let result
        let link = maid == 0 ? "{{ route('api.akunting.piutang_pelanggan.manual_ar.create') }}" : "{{ route('api.akunting.piutang_pelanggan.manual_ar.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { maid: maid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }

    $('#myTablemar').on('click', '.mar-detail', function ()
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
            openModal('mdl-form-mar', res, null, true)
        })
    })

    $('#myTablemar').on('click', '.mar-cetak', function ()
    {
        const maid = $(this).data('maid')

        let link = "{{ route('akunting.piutang_pelanggan.manual_ar.cetak', ['myid' => ':myid']) }}"
            link = link.replace(':myid', maid)

        NewWindow(link, 'Manual A/P Cetak', 1000, 500, 'yes')
        return false
    })

    $('#myTablemar').on('click', '.mar-hapus', function ()
    {
        const maid = $(this).data('maid')

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
                    let link = "{{ route('api.akunting.piutang_pelanggan.manual_ar.delete', ['myid' => ':myid']) }}"
                        link = link.replace(':myid', maid)

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