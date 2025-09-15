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
                                <span class="las la-calendar text-dark me-4"></span>
                                Periode Akunting
                            </h2>
                        </div>

                        <span class="font-weight-bolder label label-xl label-light-success label-inline min-w-100px">
                            <a class="btn btn-sm w-100 rounded-1 btn-light-dark periode-create" href="javascript:void(0)" modal-trigger="1">
                                <i class="bi bi-plus-square-fill fs-3"></i>
                                Tambah
                            </a>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTablePeriodeAkunting" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5 text-center">Begin</th>
                            <th class="border-start py-5 text-center">End</th>
                            <th class="border-start py-5 text-center">Description</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Ubah-->
<div class="modal fade" id="popup-form-periode-akunting" data-bs-backdrop="static" data-bs-focus="false" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup-form-periode-akunting" aria-hidden="true">
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
    // DATATABLE myTablePeriodeAkunting
    const ajaxOptions = {
        url: "{{ route('api.akunting.setup.periode_akunting') }}",
    }

    const options = {
        columns:[
                    {
                        data: 'pbegin', 
                        name: 'pbegin',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'pend', 
                        name: 'pend',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'desc',
                        name: 'desc',
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple_numbers',
        autoWidth: false
    }

    table = setupDataTable(
                '#myTablePeriodeAkunting',
                ajaxOptions,
                options
            )

    $('.periode-create').click(function ()
    {
        showLoading()

        getForm().then((res) => {
            Swal.close()

            // Show modal
            openModal('popup-form-periode-akunting', res, null, true)
        })
    })

    async function getForm ()
    {
        let result

        try {
            result = await $.ajax({
                url     : "{{ route('api.akunting.setup.periode_akunting.create') }}",
                type    : 'GET',
                data    : { }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush