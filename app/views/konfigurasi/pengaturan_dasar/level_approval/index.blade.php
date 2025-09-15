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
                                <span class="las la-stream text-dark me-4"></span>
                                Leveling Approval
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <button type="button" class="btn btn-sm rounded-1 btn-light-dark" id="btn-tambah" modal-trigger="1" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </button>

                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-dark fw-bold fs-7 w-200px py-4 setup-data" data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a class="menu-link px-3 Open-Data-Form" href="javascript:void(0)" data-type="branch" data-bid="0">
                                        Data Cabang
                                    </a>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a class="menu-link px-3 Open-Data-Form" href="javascript:void(0)" data-type="tipe">
                                        Data Tipe
                                    </a>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a class="menu-link px-3 Open-Data-Form" href="javascript:void(0)" data-type="wilayah">
                                        Data Wilayah
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                        </span>
                    </div>
                </div>

                <form method="post" id="form-la" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Tipe Cabang</label>
                                {!! $cmb_tipe !!}
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Wilayah Cabang</label>
                                {!! $cmb_wilayah !!}
                            </div>

                            <div class="col-lg-5">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama Cabang</label>
                                <div class="position-relative me-md-2">
                                    <span class="la la-search svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6"></span>
                                    <input type="text" class="form-control form-control-sm rounded-1 w-100 ps-10" name="s_kode_nama_branch" id="s_kode_nama_branch" placeholder="Kode / Nama Cabang" />
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
                <table id="myTableLA" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Wilayah</th>
                            <th class="border-start py-5">Kode Cabang</th>
                            <th class="border-start py-5">Nama Cabang</th>
                            <th class="border-start py-5">Company Group</th>
                            <th class="border-start py-5">Tipe</th>
                            <th class="border-start py-5">Alamat Cabang</th>
                            <th class="border-start py-5">Keterangan</th>
                            <th class="border-start py-5">Cabang Utama</th>
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
<div class="modal fade" id="popup_form_branch" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_branch" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>

<div class="modal fade" id="popup_wilayah_tipe" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_wilayah_tipe" aria-hidden="true">
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
    // // DATATABLE myTableLA
    // const ajaxOptions = {
    //     url: "{{ route('api.master_data.database.cabang.list', ['type' => 'data']) }}",
    //     data: function(params) {
    //             params.s_bwid               = $("#s_bwid").val()
    //             params.s_btid               = $("#s_btid").val()
    //             params.s_kode_nama_branch   = $("#s_kode_nama_branch").val()
    //         },
    // }

    // const options = {
    //     columns:[
    //                 {
    //                     data: 'wilayah',
    //                     name: 'wilayah',
    //                 },
    //                 {
    //                     data: 'branch_code',
    //                     name: 'branch_code',
    //                 },
    //                 {
    //                     data: 'branch_name',
    //                     name: 'branch_name',
    //                 },
    //                 {
    //                     data: 'branch_sub_corp',
    //                     name: 'branch_sub_corp',
    //                 },
    //                 {
    //                     data: 'tipe',
    //                     name: 'tipe',
    //                 },
    //                 {
    //                     data: 'branch_addr',
    //                     name: 'branch_addr',
    //                 },
    //                 {
    //                     data: 'branch_desc',
    //                     name: 'branch_desc',
    //                 },
    //                 {
    //                     data: 'is_primary',
    //                     name: 'is_primary',
    //                     className: 'text-center',

    //                     render: function (data)
    //                     {
    //                         let status = 'danger'
    //                         let icon = 'times'

    //                         if (data == 't')
    //                         {
    //                             status = 'success'
    //                             icon = 'check'
    //                         }

    //                         let $icon = `<div class="symbol symbol-20px symbol-circle">
    //                                         <div class="symbol-label fs-8 fw-bold bg-light-${status} text-${status}">
    //                                             <span class="fas fa-${icon}"></span>  
    //                                         </div>
    //                                     </div>`

    //                         return $icon
    //                     }
    //                 },
    //                 {
    //                     data: 'is_aktif',
    //                     name: 'is_aktif',

    //                     render: function (data, type, row, meta)
    //                     {
    //                         $btnstatus = `<span class="badge badge-${row.status_css}">
    //                                         <i class="bi bi-${row.status_icon} text-light fs-3"></i>
    //                                         &nbsp;${row.status_txt}
    //                                     </span>`

    //                         return $btnstatus
    //                     },

    //                     className: "text-center",
    //                 },
    //                 {
    //                     data: 'bid',
    //                     name: 'bid',

    //                     render: function (data, type, row, meta)
    //                     {
    //                         $btnFungsi = `<a href="javascript:void(0)" data-bid="${row.bid}" title="Ubah" class="btn btn-dark btn-sm fs-8 branch-update">
    //                                         <i class="las la-edit"></i> Ubah
    //                                     </a>`

    //                         return $btnFungsi
    //                     },

    //                     width: "60px",
    //                     sortable: false,
    //                     className: "text-center",
    //                 },
    //             ],

    //     order: [[0, 'asc'], [1, 'asc']],

    //     /*rowGroup:   {
    //                     dataSrc: [ 'wilayah']
    //                 },*/

    //     "drawCallback": function (settings) {
    //         var api = this.api()
    //         var rows = api.rows({
    //             page: "current"
    //         }).nodes()
    //         var last = null

    //         api.column(0, {
    //             page: "current"
    //         }).data().each(function(group, i) {
    //             if (last !== group) {
    //                 $(rows).eq(i).before(
    //                     "<tr class=\"group fs-5 fw-bolder\"><td colspan=\"10\">" + group + "</td></tr>"
    //                 )

    //                 last = group
    //             }
    //         })
    //     },

    //     columnDefs: [ {
    //                     targets: [ 0 ],
    //                     visible: false
    //                 } ],

    //     pagingType: 'simple_numbers',
    //     autoWidth: false
    // }

    // table = setupDataTable(
    //             '#myTableLA',
    //             ajaxOptions,
    //             options
    //         )

    // // aksi submit cari
    // $('#form-la').submit(function (e)
    // {
    //     e.preventDefault() // batalkan aksi form submit

    //     table.ajax.reload()
    // })

    // $('#myTableLA').on('click', '.branch-update', function (e)
    // {
    //     const bid = $(this).data('bid')

    //     openBranchForm(bid)
    // })

    // $('.setup-data').on('click', '.Open-Data-Form', function (e)
    // {
    //     const type = $(this).data('type')

    //     if (type == 'branch')
    //     {
    //         const bid = $(this).data('bid')

    //         openBranchForm(bid)
    //     }
    //     else
    //     {
    //         showLoading()

    //         getForm2(type).then((res) => {
    //             Swal.close()

    //             // Show modal
    //             openModal('popup_form_branch', res, null, true)
    //         })
    //     }
    // })

    // function openBranchForm (bid)
    // {
    //     showLoading()

    //     getForm(bid).then((res) => {
    //         Swal.close()

    //         // Show modal
    //         openModal('popup_form_branch', res, null, true)
    //     })
    // }

    // async function getForm (bid)
    // {
    //     let result
    //     let link = bid == 0 ? "{{ route('api.master_data.database.cabang.create', ['type' => ':mybid']) }}" : "{{ route('api.master_data.database.cabang.edit', ['type' => ':mybid']) }}"
    //         link = link.replace(':mybid', bid)

    //     try {
    //         result = await $.ajax({
    //             url     : link,
    //             type    : 'GET',
    //             data    : { bid: bid }
    //         })

    //         return result
    //     } catch (error) {
    //         Swal.close()

    //         swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
    //     }
    // }

    // async function getForm2 (type)
    // {
    //     let result
    //     let link = "{{ route('api.master_data.database.cabang', ['type' => ':mytype']) }}"
    //         link = link.replace(':mytype', type)

    //     try {
    //         result = await $.ajax({
    //             url     : link,
    //             type    : 'GET',
    //             data    : { }
    //         })

    //         return result
    //     } catch (error) {
    //         Swal.close()

    //         swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
    //     }
    // }
</script>
@endpush