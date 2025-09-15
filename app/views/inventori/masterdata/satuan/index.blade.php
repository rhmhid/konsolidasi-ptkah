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
                                <span class="las la-book-medical text-dark me-4"></span>
                                Data Satuan
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm rounded-1 btn-light-dark satuan-add" data-msid="0" href="javascript:void(0)">
                                <i class="bi bi-plus-square-fill fs-3"></i> Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-satuan" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-11">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Satuan</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_kode_nama_sat" placeholder="Kode / Nama Satuan" />
                                </div>
                            </div>

                            <div class="col-lg-1">
                                <label class="text-dark fw-bold fs-8 pb-2 d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-dark rounded-1 me-4 w-100" id="btncari">
                                    <i class="la la-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Compact form-->
                </form>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTableSatuan" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="text-center border-start py-5">Kode Satuan</th>
                            <th class="text-center border-start py-5">Nama Satuan</th>
                            <th class="text-center border-start py-5">Keterangan</th>
                            <th class="text-center border-start py-5">Status</th>
                            <th class="text-center border-start py-5 w-100px">Fungsi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Ubah-->
<div class="modal fade" id="popup_form_satuan" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_satuan" aria-hidden="true">
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
    // DATATABLE myTableSatuan
    const ajaxOptions = {
        url     : "{{ route('api.inventori.master_data.satuan') }}",
        data    : function (params)
                {
                    params.s_kode_nama_sat = $("#s_kode_nama_sat").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'kode_satuan',
                        name: 'kode_satuan',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_satuan',
                        name: 'nama_satuan',
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
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
                                            &nbsp;${txt}
                                        </span>`

                            return $icon
                        }
                    },
                    {
                        data: 'msid',
                        name: 'msid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-msid="${row.msid}" title="Ubah" class="btn btn-dark btn-sm fs-8 satuan-update">
                                            <i class="las la-edit"></i> Ubah
                                        </a>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: 'dt-body-center',
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableSatuan',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-satuan').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.satuan-add').click(function (e)
    {
        const msid = $(this).data('msid')

        openSatuanModal(msid)
    })

    $('#myTableSatuan').on('click', '.satuan-update', function ()
    {
        const msid = $(this).data('msid')

        openSatuanModal(msid)
    })

    function openSatuanModal (msid = 0)
    {
        showLoading()

        getForm(msid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_satuan', res, null, true)
        })
    }

    async function getForm (msid)
    {
        let result
        let link = msid == 0 ? "{{ route('api.inventori.master_data.satuan.create') }}" : "{{ route('api.inventori.master_data.satuan.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { msid: msid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush