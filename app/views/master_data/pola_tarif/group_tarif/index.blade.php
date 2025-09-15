@extends('layouts.main')

@section('content')
<!--<div class="row g-5 g-xl-10 mb-5 mb-xl-10">-->
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
                                <span class="la la-handshake text-dark me-4"></span>
                                Group Tarif
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline text-end">
                            <a class="btn btn-sm rounded-1 btn-light-dark group-add" data-gtid="0" href="javascript:void(0)" modal-trigger="1">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-group-tarif" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-11">
                                <label class="text-dark fw-bold fs-7 pb-2">Nama / Kode Group Tarif</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" name="s_kode_nama" id="s_kode_nama" value="" placeholder="Nama / Kode Group Tarif">
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
                <table id="myTableGroup" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kode Group</th>
                            <th class="border-start py-5">Nama Group</th>
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
<div class="modal fade" id="popup_form_group_tarif" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_group_tarif" aria-hidden="true">
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
    // DATATABLE myTableGroup
    const ajaxOptions = {
        url: "{{ route('api.master_data.pola_tarif.group_tarif') }}",
        data: function(params) {
                params.s_kode_nama  = $("#s_kode_nama").val()
            },
    }

    const options = {
        columns:[
                    {
                        data: 'group_code', 
                        name: 'group_code',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'group_name', 
                        name: 'group_name',
                    },
                    {
                        data: 'is_aktif',
                        name: 'is_aktif',

                        render: function (data, type, row, meta)
                        {
                            $btnstatus = `<span class="badge badge-${row.status_css}">
                                            <i class="bi bi-${row.status_icon} text-light fs-3"></i>
                                            &nbsp;${row.status_txt}
                                        </span>`

                            return $btnstatus
                        },

                        className: 'dt-body-center',
                    },
                    {
                        data: 'gtid',
                        name: 'gtid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<button class="btn btn-light-success btn-icon btn-sm rounded-1 group-update" data-bs-toggle="tooltip" title="Ubah Data" data-gtid="${row.gtid}">
                                            <i class="la la-edit fs-4"></i>
                                        </button>`

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
                '#myTableGroup',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-group-tarif').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('#myTableGroup').on('click', '.group-update', function ()
    {
        const gtid = $(this).data('gtid');

        openGroupModal(gtid);

    })

    $('.group-add').on('click', function ()
    {
        const gtid = $(this).data('gtid')

        openGroupModal(gtid)
    })

    function openGroupModal (gtid = 0)
    {
        showLoading()

        getForm(gtid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_group_tarif', res, null, true)
        })
    }

    async function getForm (gtid)
    {
        let result
        let link = gtid == 0 ? "{{ route('api.master_data.pola_tarif.group_tarif.create') }}" : "{{ route('api.master_data.pola_tarif.group_tarif.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { gtid: gtid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush