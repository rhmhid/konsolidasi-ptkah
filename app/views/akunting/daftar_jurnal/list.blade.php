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
                                <span class="las la-file-alt text-dark me-4"></span>
                                Daftar Jurnal
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-daftar-jurnal" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                           <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="sJurnal-period" class="form-control form-control-sm rounded-1" readonly="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Tipe Jurnal</label>
                                {!! $cmb_tipe_jurnal !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Status Posting</label>
                                {!! $cmb_posted !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">No. Jurnal</label>
                                <input type="text" id="sGlDoc" class="form-control form-control-sm rounded-1" />
                            </div>

                            <div class="col-lg-8">
                                <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                                <input type="text" id="sKeterangan" class="form-control form-control-sm rounded-1" />
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
                <table id="myTableDaftarJurnal" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Tanggal Jurnal</th>
                            <th class="border-start py-5">No. Jurnal</th>
                            <th class="border-start py-5">Keterangan</th>
                            <th class="border-start py-5">Status Posting</th>
                            <th class="border-start py-5">User Input</th>
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
<div class="modal fade" id="mdl-form-jurnal" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-jurnal" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Ubah-->
@endsection

@push('script')
<script type="text/javascript">
    var sPeriod, ePeriod

    $('#sJurnal-period').flatpickr({
        defaultDate: new Date(),
        altInput: true,
        altFormat: "d-m-Y",
        dateFormat: "d-m-Y",
        weekNumbers: true,
        mode: "range",
        onClose: function (selectedDates, dateStr, instance)
        {
            var dateArr = selectedDates.map(date => this.formatDate(date, "d-m-Y"))

            sPeriod = dateArr[0]
            ePeriod = dateArr[1]
        }
    })

    // DATATABLE myTableDaftarJurnal
    const ajaxOptions = {
        url     : "{{ route('api.akunting.daftar_jurnal') }}",
        data    : function (params)
                {
                    params.jurnal_speriod   = sPeriod
                    params.jurnal_eperiod   = ePeriod
                    params.jtid             = $("#sJtid").val()
                    params.is_posted        = $("#sPosted").val()
                    params.gldoc            = $("#sGlDoc").val()
                    params.keterangan       = $("#sKeterangan").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'jurnal_date',
                        name: 'jurnal_date',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            let $ret = `${row.jurnal_date}<br /><I class="text-danger fw-semibold">[ ${row.journal_name} ]</I>`

                            return $ret
                        }
                    },
                    {
                        data: 'jurnal_doc',
                        name: 'jurnal_doc',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                    },
                    {
                        data: 'is_posted',
                        name: 'is_posted',
                        className: 'dt-body-center',

                        render: function (data)
                        {
                            let status = 'danger'
                            let txt = 'NOT POSTED'

                            if (data == 't')
                            {
                                status = 'success'
                                txt = 'POSTED'
                            }

                            let $icon = `<span class="badge badge-light-${status}">
                                            ${txt}
                                        </span>`

                            return $icon
                        }
                    },
                    {
                        data: 'user_input',
                        name: 'user_input',
                    },
                    {
                        data: 'glid',
                        name: 'glid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-glid="${row.glid}" title="Cetak" class="btn btn-dark btn-sm btn-icon fs-8 jurnal-detail">
                                            <i class="las la-eye"></i>
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
                '#myTableDaftarJurnal',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-daftar-jurnal').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('#myTableDaftarJurnal').on('click', '.jurnal-detail', function ()
    {
        const glid = $(this).data('glid')

        let getForm = async function (glid)
        {
            let link = "{{ route('api.akunting.daftar_jurnal.detail', ['myglid' => ':myglid']) }}"
                link = link.replace(':myglid', glid)

            try {
                result = await $.ajax({
                    url     : link,
                    type    : 'GET',
                    data    : { }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
            }
        }

        showLoading()

        getForm(glid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-jurnal', res, null, true)
        })
    })
</script>
@endpush