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
                                <span class="las la-money-bill text-dark me-4"></span>
                                Tipe Transaksi
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm rounded-1 btn-light-dark tipe-add" data-pctid="0" href="javascript:void(0)">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-tipe" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">C.O.A</label>
                                {!! $cmb_coa !!}
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Tipe</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" name="sType" class="btn-check" id="sType0" value="0" checked="" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="sType0">Semua</label>

                                    <input type="radio" name="sType" class="btn-check" id="sType1" value="1" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="sType1">Cash In</label>

                                    <input type="radio" name="sType" class="btn-check" id="sType2" value="2" />
                                    <label class="btn btn-sm btn-light-dark rounded-1" for="sType2">Cash Out</label>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="sDesc" placeholder="Keterangan" />
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
                <table id="myTableTipe" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Keterangan</th>
                            <th class="border-start py-5">Tipe</th>
                            <th class="border-start py-5">C.O.A</th>
                            <th class="border-start py-5">Status</th>
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
<div class="modal fade" id="form-tipe-trans" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="form-tipe-trans" aria-hidden="true">
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
    // DATATABLE myTableTipe
    const ajaxOptions = {
        url     : "{{ route('api.akunting.petty_cash.transaction_type') }}",
        data    : function (params)
        {
                params.coaid        = $("#sCoaid").val()
                params.type_trans   = $('[name="sType"]:checked').val()
                params.keterangan   = $("#sDesc").val()
            },
    }

    const options = {
        columns:[
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                    },
                    {
                        data: 'type_trans',
                        name: 'type_trans',
                        className: 'dt-body-center',

                        render: function (data)
                        {
                            let status = 'success'
                            let txt = 'Cash In'

                            if (data == 2)
                            {
                                status = 'danger'
                                txt = 'Cash Out'
                            }

                            let $icon = `<span class="badge badge-light-${status}">
                                            ${txt}
                                        </span>`

                            return $icon
                        }
                    },
                    {
                        data: 'coa',
                        name: 'coa',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'is_aktif',
                        name: 'is_aktif',
                        className: 'dt-body-center',

                        render: function (data)
                        {
                            let status = 'danger'
                            let txt = 'Non Aktif'

                            if (data == 't')
                            {
                                status = 'success'
                                txt = 'Akitf'
                            }

                            let $icon = `<span class="badge badge-light-${status}">
                                            ${txt}
                                        </span>`

                            return $icon
                        }
                    },
                    {
                        data: 'pctid',
                        name: 'pctid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-pctid="${row.pctid}" title="Ubah" class="btn btn-dark btn-sm fs-8 tipe-update">
                                            <i class="las la-edit"></i> Ubah
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
                '#myTableTipe',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-tipe').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.tipe-add').click(function (e)
    {
        const pctid = $(this).data('pctid')

        openTipeModal(pctid)
    })

    $('#myTableTipe').on('click', '.tipe-update', function ()
    {
        const pctid = $(this).data('pctid')

        openTipeModal(pctid)
    })

    function openTipeModal (pctid = 0)
    {
        showLoading()

        getForm(pctid).then((res) => {
            Swal.close()

            // Show modal
            openModal('form-tipe-trans', res, null, true)
        })
    }

    async function getForm (pctid)
    {
        let result
        let link = pctid == 0 ? "{{ route('api.akunting.petty_cash.transaction_type.create') }}" : "{{ route('api.akunting.petty_cash.transaction_type.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { pctid: pctid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush