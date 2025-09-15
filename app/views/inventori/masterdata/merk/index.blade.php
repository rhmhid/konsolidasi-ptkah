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
                                <span class="las la-copyright text-dark me-4"></span>
                                Data Merk
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm rounded-1 btn-light-dark merk-add" data-mmid="0" href="javascript:void(0)">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-merk" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-11">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Merk</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_kode_nama_merk" placeholder="Kode / Nama Merk" />
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
                <table id="myTableMerk" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kode Merk</th>
                            <th class="border-start py-5">Nama Merk</th>
                            <th class="border-start py-5">Keterangan</th>
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
<div class="modal fade" id="popup_form_merk" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_merk" aria-hidden="true">
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
    // DATATABLE myTableMerk
    const ajaxOptions = {
        url     : "{{ route('api.inventori.master_data.merk') }}",
        data    : function (params)
                {
                    params.s_kode_nama_merk = $("#s_kode_nama_merk").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'kode_merk',
                        name: 'kode_merk',
                    },
                    {
                        data: 'nama_merk',
                        name: 'nama_merk',
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                    },
                    {
                        data: 'is_aktif',
                        name: 'is_aktif',
                        className: 'text-center',

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
                        data: 'mmid',
                        name: 'mmid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-mmid="${row.mmid}" title="Ubah" class="btn btn-dark btn-sm fs-8 merk-update">
                                            <i class="las la-edit"></i> Ubah
                                        </a>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: "text-center",
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableMerk',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-merk').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.merk-add').click(function (e)
    {
        const mmid = $(this).data('mmid')

        openMerkModal(mmid)
    })

    $('#myTableMerk').on('click', '.merk-update', function ()
    {
        const mmid = $(this).data('mmid')

        openMerkModal(mmid)
    })

    function openMerkModal (mmid = 0)
    {
        showLoading()

        getForm(mmid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_merk', res, null, true)
        })
    }

    async function getForm (mmid)
    {
        let result
        let link = mmid == 0 ? "{{ route('api.inventori.master_data.merk.create') }}" : "{{ route('api.inventori.master_data.merk.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { mmid: mmid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush