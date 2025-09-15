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
                                <span class="la la-gear text-dark me-4"></span>
                                Control Panel
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-control-panel" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Group</label>
                                {!! $cmb_group !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Deskripsi</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_deskripsi" placeholder="Deskripsi" />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Data</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" id="s_data" placeholder="Data" />
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
                <table id="myTableConfigs" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 gy-5 gs-7 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="py-5">Group</th>
                            <th class="border-start py-5 text-center">Deskripsi</th>
                            <th class="border-start py-5 text-center">Data</th>
                            <th class="border-start py-5 text-center">User Edit</th>
                            <th class="border-start py-5 text-center">Waktu Edit</th>
                            <th class="border-start py-5 text-center w-100px">Fungsi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Ubah-->
<div class="modal fade" id="popup_edit_control_panel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_edit_control_panel" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Ubah-->
@endsection

@push('script')
<script type="text/javascript">
    // DATATABLE myTableConfigs
    const ajaxOptions = {
        url: "{{ route('api.pengaturan_dasar.control_panel') }}",
        data: function(params) {
                params.s_cgid       = $("#s_cgid").val()
                params.s_deskripsi  = $("#s_deskripsi").val()
                params.s_data       = $("#s_data").val()
            },
    }

    const options = {
        columns:[
                    {
                        data: 'cg_name', 
                        name: 'cg_name',
                    },
                    {
                        data: 'description', 
                        name: 'description',
                    },
                    {
                        data: 'data', 
                        name: 'data',
                        width: "260px"
                    },
                    {
                        data: 'user', 
                        name: 'user',
                    },
                    {
                        data: 'waktu', 
                        name: 'wa ktu',
                    },
                    {
                        data: 'cid',
                        name: 'cid',

                        render: function (data, type, row, meta)
                        {
                            if (row.is_editable == 't')
                                $btnFungsi = `<a href="javascript:void(0)" data-cid="${row.cid}" title="Ubah" class="btn btn-dark btn-sm btn-icon fs-8 config-ubah">
                                                <i class="las la-edit fs-4"></i>
                                            </a>`
                            else
                                $btnFungsi = ``

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: "text-center",
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        // rowGroup:   {
        //                 dataSrc: [ 'cg_name']
        //             },

        "drawCallback": function (settings) {
            var api = this.api()
            var rows = api.rows({
                page: "current"
            }).nodes()
            var last = null

            api.column(0, {
                page: "current"
            }).data().each(function(group, i) {
                if (last !== group) {
                    $(rows).eq(i).before(
                        "<tr class=\"group fs-5 fw-bolder\"><td colspan=\"5\">" + group + "</td></tr>"
                    )

                    last = group
                }
            })
        },

        columnDefs: [ {
                        targets: [ 0 ],
                        visible: false
                    } ],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableConfigs',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-control-panel').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('#myTableConfigs').on('click', '.config-ubah', function ()
    {
        const cid = $(this).data('cid')

        showLoading()

        getForm(cid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_edit_control_panel', res, null, true)
        })
    })

    async function getForm (cid)
    {
        let result

        try {
            result = await $.ajax({
                url     : "{{ route('api.pengaturan_dasar.control_panel.edit') }}",
                type    : 'GET',
                data    : { cid: cid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush