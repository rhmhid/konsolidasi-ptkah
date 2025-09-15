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
                                <span class="las la-file-invoice text-dark me-4"></span>
                                Daftar DP Supplier
                            </h2>
                        </div>

                        <a class="btn btn-sm rounded-1 btn-light-dark dps-add" data-dsid="0" href="javascript:void(0)">
                            <i class="bi bi-plus-square-fill fs-3"></i>
                            Tambah
                        </a>
                    </div>
                </div>

                <form method="post" id="form-dp-supp" novalidate="">
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
                                <label class="text-dark fw-bold fs-7 pb-2">Status Posting</label>
                                {!! $cmb_posted !!}
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">No. Jurnal</label>
                                <input type="text" id="sGlDoc" class="form-control form-control-sm rounded-1" />
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3">
                            <div class="col-lg-11">
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
                <table id="myTableDPSupp" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
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
<div class="modal fade" id="mdl-form-dps" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-dps" aria-hidden="true">
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
    // var sPeriod, ePeriod

    // $('#sJurnal-period').flatpickr({
    //     defaultDate: new Date(),
    //     altInput: true,
    //     altFormat: "d-m-Y",
    //     dateFormat: "d-m-Y",
    //     weekNumbers: true,
    //     mode: "range",
    //     onClose: function (selectedDates, dateStr, instance)
    //     {
    //         var dateArr = selectedDates.map(date => this.formatDate(date, "d-m-Y"))

    //         sPeriod = dateArr[0]
    //         ePeriod = dateArr[1]
    //     }
    // })

    // // DATATABLE myTableDPSupp
    // const ajaxOptions = {
    //     url     : "{{ route('api.akunting.jurnal_manual') }}",
    //     data    : function (params)
    //             {
    //                 params.jurnal_speriod   = sPeriod
    //                 params.jurnal_eperiod   = ePeriod
    //                 params.is_posted        = $("#sPosted").val()
    //                 params.gldoc            = $("#sGlDoc").val()
    //                 params.keterangan       = $("#sKeterangan").val()
    //             },
    // }

    // const options = {
    //     columns:[
    //                 {
    //                     data: 'jurnal_date',
    //                     name: 'jurnal_date',
    //                     className: 'dt-body-center',
    //                 },
    //                 {
    //                     data: 'jurnal_doc',
    //                     name: 'jurnal_doc',
    //                     className: 'dt-body-center',
    //                 },
    //                 {
    //                     data: 'keterangan',
    //                     name: 'keterangan',
    //                 },
    //                 {
    //                     data: 'is_posted',
    //                     name: 'is_posted',
    //                     className: 'dt-body-center',

    //                     render: function (data)
    //                     {
    //                         let status = 'danger'
    //                         let txt = 'NOT POSTED'

    //                         if (data == 't')
    //                         {
    //                             status = 'success'
    //                             txt = 'POSTED'
    //                         }

    //                         let $icon = `<span class="badge badge-light-${status}">
    //                                         ${txt}
    //                                     </span>`

    //                         return $icon
    //                     }
    //                 },
    //                 {
    //                     data: 'user_input',
    //                     name: 'user_input',
    //                 },
    //                 {
    //                     data: 'jmid',
    //                     name: 'jmid',
    //                     className: 'dt-body-center',

    //                     render: function (data, type, row, meta)
    //                     {
    //                         if (row.is_posted == 't')
    //                             $btnFungsi = `<a href="javascript:void(0)" data-glid="${row.glid}" title="Detail Jurnal" class="btn btn-dark btn-sm btn-icon fs-8 jm-detail">
    //                                             <i class="las la-eye"></i>
    //                                         </a>`
    //                         else
    //                             $btnFungsi = `<a href="javascript:void(0)" data-dsid="${row.jmid}" title="Posting Jurnal" class="btn btn-info btn-sm btn-icon fs-8 jm-posted">
    //                                             <i class="las la-check-circle"></i>
    //                                         </a>`

    //                         $btnFungsi += `&nbsp;<a href="javascript:void(0)" data-dsid="${row.jmid}" title="Hapus" class="btn btn-danger btn-sm btn-icon fs-8 jm-hapus">
    //                                             <i class="las la-trash-alt"></i>
    //                                         </a>`

    //                         return $btnFungsi
    //                     },

    //                     width: "60px",
    //                     sortable: false,
    //                 },
    //             ],

    //     order: [[0, 'asc'], [1, 'asc']],

    //     pagingType: 'simple_numbers',
    //     autoWidth: false
    // }

    // table = setupDataTable(
    //             '#myTableDPSupp',
    //             ajaxOptions,
    //             options
    //         )

    // // aksi submit cari
    // $('#form-dp-supp').submit(function (e)
    // {
    //     e.preventDefault() // batalkan aksi form submit

    //     table.ajax.reload()
    // })

    $('.dps-add').on('click', function (e)
    {
        const dsid = $(this).data('dsid')

        openModalDSP(dsid)
    })

    function openModalDSP (dsid = 0)
    {
        showLoading()

        getForm(dsid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-dps', res, null, true)
        })
    }

    async function getForm (dsid)
    {
        let result
        let link = dsid == 0 ? "{{ route('api.akunting.hutang_supplier.dp_supplier.create') }}" : "{{ route('api.akunting.hutang_supplier.dp_supplier.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { dsid: dsid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }

    // $('#myTableDPSupp').on('click', '.jm-detail', function ()
    // {
    //     const glid = $(this).data('glid')

    //     let getForm = async function (glid)
    //     {
    //         let link = "{{ route('api.akunting.daftar_jurnal.detail', ['myglid' => ':myglid']) }}"
    //             link = link.replace(':myglid', glid)

    //         try {
    //             result = await $.ajax({
    //                 url     : link,
    //                 type    : 'GET',
    //                 data    : { }
    //             })

    //             return result
    //         } catch (error) {
    //             Swal.close()

    //             swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
    //         }
    //     }

    //     showLoading()

    //     getForm(glid).then((res) => {
    //         Swal.close()

    //         // Show modal
    //         openModal('mdl-form-dps', res, null, true)
    //     })
    // })

    // $('#myTableDPSupp').on('click', '.jm-posted', function ()
    // {
    //     const jmid = $(this).data('jmid')

    //     Swal.fire({
    //         html: 'Apakah Anda Yakin <span class="badge badge-danger">Posting Jurnal</span> ?',
    //         icon: "info",
    //         buttonsStyling: false,
    //         showCancelButton: true,
    //         confirmButtonText: "Ya, Posting !",
    //         cancelButtonText: 'Batal',
    //         customClass: {
    //             confirmButton: "btn btn-dark",
    //             cancelButton: 'btn btn-danger'
    //         }
    //     }).then((result) =>
    //     {
    //         if (result.isConfirmed)
    //         {
    //             showLoading()

    //             let getAction = async function ()
    //             {
    //                 let link = "{{ route('api.akunting.jurnal_manual.posting', ['myid' => ':myid']) }}"
    //                     link = link.replace(':myid', jmid)

    //                 try {
    //                     result = await $.ajax({
    //                         url     : link,
    //                         type    : 'POST',
    //                         data    : { }
    //                     })

    //                     return result
    //                 } catch (error) {
    //                     Swal.close()

    //                     swalShowMessage('Gagal', 'Gagal memproses data.', 'error')
    //                 }
    //             }

    //             getAction().then((res) => {
    //                 Swal.close()

    //                 if (res.success)
    //                 {
    //                     swalShowMessage('Sukses', res.message, 'success')
    //                     .then((result) =>
    //                     {
    //                         if (result.isConfirmed) table.ajax.reload(null, false)
    //                     })
    //                 }
    //                 else swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
    //             })
    //         }
    //     })
    // })

    // $('#myTableDPSupp').on('click', '.jm-hapus', function ()
    // {
    //     const jmid = $(this).data('jmid')

    //     Swal.fire({
    //         html: 'Apakah Anda Yakin <span class="badge badge-danger">Menghapus Data Jurnal</span> ?',
    //         icon: "info",
    //         buttonsStyling: false,
    //         showCancelButton: true,
    //         confirmButtonText: "Ya, Hapus !",
    //         cancelButtonText: 'Batal',
    //         customClass: {
    //             confirmButton: "btn btn-dark",
    //             cancelButton: 'btn btn-danger'
    //         }
    //     }).then((result) =>
    //     {
    //         if (result.isConfirmed)
    //         {
    //             showLoading()

    //             let getAction = async function ()
    //             {
    //                 let link = "{{ route('api.akunting.jurnal_manual.delete', ['myid' => ':myid']) }}"
    //                     link = link.replace(':myid', jmid)

    //                 try {
    //                     result = await $.ajax({
    //                         url     : link,
    //                         type    : 'POST',
    //                         data    : { }
    //                     })

    //                     return result
    //                 } catch (error) {
    //                     Swal.close()

    //                     swalShowMessage('Gagal', 'Gagal memproses data.', 'error')
    //                 }
    //             }

    //             getAction().then((res) => {
    //                 Swal.close()

    //                 if (res.success)
    //                 {
    //                     swalShowMessage('Sukses', res.message, 'success')
    //                     .then((result) =>
    //                     {
    //                         if (result.isConfirmed) table.ajax.reload(null, false)
    //                     })
    //                 }
    //                 else swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
    //             })
    //         }
    //     })
    // })
</script>
@endpush