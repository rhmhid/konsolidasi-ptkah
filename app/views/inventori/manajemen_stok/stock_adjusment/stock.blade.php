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
                    </div>
                </div>

                <form method="post" id="form-adj" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Gudang</label>
                                {!! $cmb_gudang !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Kategori Barang</label>
                                {!! $cmb_kel_brg !!}
                            </div>

                            <div class="col-lg-4">
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
                        </div>
                    </div>
                    <!--end::Compact form-->
                </form>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTableAdj" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kode Barang</th>
                            <th class="border-start py-5">Nama Barang</th>
                            <th class="border-start py-5">Kode Satuan</th>
                            <th class="border-start py-5">Stock</th>
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
    // DATATABLE myTableAdj
    const ajaxOptions = {
        url     : "{{ route('api.inventori.manajemen_stok.stock_adjusment.stock') }}",
        data    : function (params)
                {
                    params.gid          = $("#sGid").val()
                    params.kbid         = $("#sKbid").val()
                    params.kode_nama    = $("#sKodeNama").val()
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
                        data: 'kode_satuan',
                        name: 'kode_satuan',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'stock',
                        name: 'stock',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'mbid',
                        name: 'mbid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-gid="${row.gid}" data-mbid="${row.mbid}" title="Adjustment" class="btn btn-dark btn-sm btn-icon fs-8 adj-add">
                                            <i class="las la-edit"></i>
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

        let $gid = $('#sGid option:selected').val()

        if ($gid == '')
        {
            swalShowMessage('Information', 'Gudang Belum Dipilih.', 'error')

            return false
        }

        table.ajax.reload()
    })

    $('#myTableAdj').on('click', '.adj-add', function ()
    {
        const gid = $(this).data('gid')
        const mbid = $(this).data('mbid')

        openModalAdj(gid, mbid)
    })

    function openModalAdj (gid = 0, mbid = 0)
    {
        showLoading()

        getForm(gid, mbid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-adj', res, null, true)
        })
    }

    async function getForm (gid, mbid)
    {
        let result
        let link ="{{ route('api.inventori.manajemen_stok.stock_adjusment.create') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { gid: gid, mbid: mbid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush