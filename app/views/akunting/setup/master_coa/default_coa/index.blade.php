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
                                Default C.O.A
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTableDefaultCoa" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="text-center border-start py-5">Deskripsi Default C.O.A</th>
                            <th class="text-center border-start py-5">C.O.A</th>
                            <th class="text-center border-start py-5">Type Default C.O.A</th>
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
<div class="modal fade" id="popup_form_default_coa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_form_default_coa" aria-hidden="true">
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
    // DATATABLE myTableDefaultCoa
    const ajaxOptions = {
        url: "{{ route('api.akunting.setup.master_coa.default_coa') }}",
    }

    const options = {
        columns:[
                    {
                        data: 'default_desc', 
                        name: 'default_desc',
                    },
                    {
                        data: 'coa',
                        name: 'coa',
                    },
                    {
                        data: 'default_type', 
                        name: 'default_type',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'dcid',
                        name: 'dcid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-dcid="${row.dcid}" title="Ubah" class="btn btn-dark btn-sm fs-8 default-coa-update">
                                            <i class="las la-edit"></i> Ubah
                                        </a>`

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
        displayLength: 50,
    }

    table = setupDataTable(
                '#myTableDefaultCoa',
                ajaxOptions,
                options
            )

    $('#myTableDefaultCoa').on('click', '.default-coa-update', function ()
    {
        const dcid = $(this).data('dcid')

        showLoading()

        getForm(dcid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup_form_default_coa', res, null, true)
        })
    })

    async function getForm (dcid)
    {
        let result

        try {
            result = await $.ajax({
                url     : "{{ route('api.akunting.setup.master_coa.default_coa.edit') }}",
                type    : 'GET',
                data    : { dcid: dcid },
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush