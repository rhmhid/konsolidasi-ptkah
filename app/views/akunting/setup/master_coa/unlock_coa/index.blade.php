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
                                <span class="las la-puzzle-piece text-dark me-4"></span>
                                Lock Unlock Group COA
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTableGroupCoa" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="text-center border-start py-5">Group COA</th>
                            <th class="text-center border-start py-5">Start Open Periode</th>
                            <th class="text-center border-start py-5">End Open Periode</th>
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
<div class="modal fade" id="popup-edit-unlock-coa" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup-edit-unlock-coa" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-850px">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Ubah-->
@endsection

@push('script')
<script type="text/javascript">
    // DATATABLE myTableGroupCoa
    const ajaxOptions = {
        url: "{{ route('api.akunting.setup.master_coa.unlock_coa') }}",
    }

    const options = {
        columns:[
                    {
                        data: 'coa_group',
                        name: 'coa_group',
                    },
                    {
                        data: 'start_period', 
                        name: 'start_period',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'end_period', 
                        name: 'end_period',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'coagid',
                        name: 'coagid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-coagid="${row.coagid}" title="Unlock" class="btn btn-dark btn-sm fs-8 unlock-coa-edit">
                                            <i class="las la-edit"></i> Unlock
                                        </a>`

                            return $btnFungsi
                        },

                        width: "60px",
                        sortable: false,
                        className: 'dt-body-center',
                    }
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTableGroupCoa',
                ajaxOptions,
                options
            )

    $('#myTableGroupCoa').on('click', '.unlock-coa-edit', function ()
    {
        const coagid = $(this).data('coagid')

        showLoading()

        getForm(coagid).then((res) => {
            Swal.close()

            // Show modal
            openModal('popup-edit-unlock-coa', res, null, true)
        })
    })

    async function getForm (coagid)
    {
        let result

        try {
            result = await $.ajax({
                url     : "{{ route('api.akunting.setup.master_coa.unlock_coa.edit') }}",
                type    : 'GET',
                data    : { coagid: coagid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush