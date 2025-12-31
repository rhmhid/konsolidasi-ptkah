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
                            <h2 class="pt-2">
                                <span class="las la-puzzle-piece text-dark me-4"></span>
                                Chart Of Account
                            </h2>
                        </div>

                        {!! $cmb_type !!}

                        <span class="font-weight-bolder label label-xl label-light-success label-inline ms-2">
                            <a href="javascript:void(0)" class="btn btn-dark btn-sm rounded-1 btn-add-coa">
                                <i class="las la-plus"></i> Tambah COA
                            </a>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTableCoa" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="text-center border-start py-5">Chart Of Account</th>
                            <th class="text-center border-start py-5">Header C.O.A</th>
                            <th class="text-center border-start py-5">Default</th>
                            <th class="text-center border-start py-5">Postable</th>
                            <th class="text-center border-start py-5">Valid</th>
                            <th class="text-center border-start py-5">Group C.O.A</th>
                            <th class="text-center border-start py-5">Mapping POS</th>
                            <th class="text-center border-start py-5">Mapping Arus Kas</th>
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
<div class="modal fade" id="popup-form-coa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup-form-coa" aria-hidden="true">
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
    // DATATABLE myTableCoa
    const ajaxOptions = {
        url: "{{ route('api.akunting.setup.master_coa.coa') }}",
        data: function(params) {
                params.s_coatid = $("#s_coatid").val()
            },
    }

    const options = {
        columns:[
                    {
                        data: 'coa',
                        name: 'coa',
                    },
                    {
                        data: 'header_coa', 
                        name: 'header_coa',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'default_coa', 
                        name: 'default_coa',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'postable_coa', 
                        name: 'postable_coa',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'valid_coa', 
                        name: 'valid_coa',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'group_coa', 
                        name: 'group_coa',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'mapping_pos', 
                        name: 'mapping_pos',
                    },
                    {
                        data: 'mapping_cf', 
                        name: 'mapping_cf',
                    },
                    {
                        data: 'coaid',
                        name: 'coaid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi =`<div class="btn-group">
                                            <button type="button" class="btn btn-circle fs-8" data-bs-toggle="dropdown" aria-expanded="false" id="menu-dropdown">
                                                    <i class="las la-ellipsis-v fw-bold fs-3 text-dark"></i>
                                            </button>

                                            <ul class="dropdown-menu p-3" aria-labelledby="menu-dropdown">
                                                <li>
                                                    <a href="javascript:void(0)" data-coaid="${row.coaid}" data-coatid="${row.coatid}" title="Ubah" class="text-dark fs-6 btn-update-coa">
                                                        <i class="las la-edit fs-6 text-dark"></i> Ubah
                                                    </a>                             
                                                </li>
                                                <li class="separator border-secondary my-1"></li>
                                                <li>
                                                    <a href="javascript:void(0)" data-coaid="${row.coaid}" title="Mapping" class="text-dark fs-6 coa-mapping">
                                                        <i class="las la-database fs-6 text-dark"></i> Mapping
                                                    </a>                            
                                                </li>
                                            </ul>
                                        </div>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: 'dt-body-center',
                    }
                ],

        order: [[0, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false,
        displayLength: 100,
    }

    table = setupDataTable(
                '#myTableCoa',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#s_coatid').change(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.btn-add-coa').click(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        let coatid = $('#s_coatid').val()

        openCoaModal(0, coatid)
    })

    $('#myTableCoa').on('click', '.btn-update-coa', function ()
    {
        const coaid = $(this).data('coaid')
        const coatid = $(this).data('coatid')

        openCoaModal(coaid, coatid)
    })

    function openCoaModal (coaid = 0, coatid)
    {
        showLoading()

        var getForm = async function (coaid, coatid)
        {
            let result
            let link = coaid == 0 ? "{{ route('api.akunting.setup.master_coa.coa.create') }}" : "{{ route('api.akunting.setup.master_coa.coa.edit') }}"

            try {
                result = await $.ajax({
                    url     : link,
                    type    : 'GET',
                    data    : { coaid: coaid, coatid: coatid }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
            }
        }

        getForm(coaid, coatid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup-form-coa', res, null, true)
        })
    }

    $('#myTableCoa').on('click', '.coa-mapping', function ()
    {
        const coaid = $(this).data('coaid')

        showLoading()

        var getForm = async function (coaid)
        {
            let result
            let link = "{{ route('api.akunting.setup.master_coa.coa.mapping') }}"

            try {
                result = await $.ajax({
                    url     : link,
                    type    : 'GET',
                    data    : { coaid: coaid }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
            }
        }

        getForm(coaid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup-form-coa', res, null, true)
        })
    })
</script>
@endpush