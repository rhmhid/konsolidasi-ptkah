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
                                <span class="las la-prescription-bottle text-dark me-4"></span>
                                Data Barang - Supplier
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm rounded-1 btn-light-dark brgsupp-add" data-bsid="0" href="javascript:void(0)">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-brgsupp" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Supplier</label>
                                {!! $cmb_supp !!}
                            </div>

                            <div class="col-lg-5">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Barang</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_kode_nama_brg" placeholder="Kode / Nama Barang" />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Status Barang</label>
                                <div class="nav-group nav-group-sm nav-group-fluid rounded-1 border border-gray-300 bg-white p-1">
                                    <label>
                                        <input type="radio" class="btn-check" name="s_is_supp_utama" id="s_is_supp_utama_all" value="" checked="" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Semua</span>
                                    </label>
                                    <label>
                                        <input type="radio" class="btn-check" name="s_is_supp_utama" id="s_is_supp_utama_t" value="t" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Utama</span>
                                    </label>
                                    <label>
                                        <input type="radio" class="btn-check" name="s_is_supp_utama" id="s_is_supp_utama_f" value="f" />
                                        <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fs-8 p-2 pb-1">Non Utama</span>
                                    </label>
                                </div>
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
                <table id="myTableBrgSupp" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kode Barang</th>
                            <th class="border-start py-5">Nama Barang</th>
                            <th class="border-start py-5">Kode Satuan</th>
                            <th class="border-start py-5">Harga</th>
                            <th class="border-start py-5">Disc ( % )</th>
                            <th class="border-start py-5">Supplier</th>
                            <th class="border-start py-5">Supplier Utama</th>
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
<div class="modal fade" id="md-fi-BrgSupp" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="md-fi-BrgSupp" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Ubah-->
@endsection

@push('script')
<script type="text/javascript">
    // DATATABLE myTableBrgSupp
    const ajaxOptions = {
        url     : "{{ route('api.inventori.setup.barang_supplier') }}",
        data    : function (params)
                {
                    params.s_suppid         = $("#sSuppid").val()
                    params.s_kode_nama_brg  = $("#s_kode_nama_brg").val()
                    params.s_is_supp_utama  = $('input[name="s_is_supp_utama"]:checked').val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'kode_brg',
                        name: 'kode_brg',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_brg',
                        name: 'nama_brg',
                    },
                    {
                        data: 'satuan',
                        name: 'satuan',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'harga',
                        name: 'harga',
                        className: 'dt-body-right',
                    },
                    {
                        data: 'disc',
                        name: 'disc',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'supplier',
                        name: 'supplier',
                    },
                    {
                        data: 'is_supp_utama',
                        name: 'is_supp_utama',
                        className: 'text-center',

                        render: function (data)
                        {
                            let status = 'danger'
                            let icon = 'times'

                            if (data == 't')
                            {
                                status = 'success'
                                icon = 'check'
                            }

                            let $icon = `<div class="symbol symbol-20px symbol-circle">
                                            <div class="symbol-label fs-8 fw-bold bg-light-${status} text-${status}">
                                                <span class="fas fa-${icon}"></span>  
                                            </div>
                                        </div>`

                            return $icon
                        }
                    },
                    {
                        data: 'bsid',
                        name: 'bsid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-bsid="${row.bsid}" title="Ubah" class="btn btn-dark btn-icon btn-sm fs-8 brgsupp-update">
                                            <i class="las la-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" data-bsid="${row.bsid}" title="Hapus" class="btn btn-danger btn-icon btn-sm fs-8 brgsupp-hapus">
                                            <i class="las la-trash"></i>
                                        </a>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: "dt-body-center",
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableBrgSupp',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-brgsupp').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.brgsupp-add').click(function (e)
    {
        const bsid = $(this).data('bsid')

        openBrgSuppModal(bsid)
    })

    $('#myTableBrgSupp').on('click', '.brgsupp-update', function ()
    {
        const bsid = $(this).data('bsid')

        openBrgSuppModal(bsid)
    })

    function openBrgSuppModal (bsid = 0)
    {
        showLoading()

        getForm(bsid).then((res) => {
            Swal.close()

            // Show modal
            openModal('md-fi-BrgSupp', res, null, true)
        })
    }

    async function getForm (bsid)
    {
        let result
        let link = bsid == 0 ? "{{ route('api.inventori.setup.barang_supplier.create') }}" : "{{ route('api.inventori.setup.barang_supplier.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { bsid: bsid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }

    $('#myTableBrgSupp').on('click', '.brgsupp-hapus', function ()
    {
        const bsid = $(this).data('bsid')

        Swal.fire({
            html: 'Apakah Anda Yakin <span class="badge badge-danger">Menghapus Data</span> ?',
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
                    let link = "{{ route('api.inventori.setup.barang_supplier.delete', ['myid' => ':myid']) }}"
                        link = link.replace(':myid', bsid)

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