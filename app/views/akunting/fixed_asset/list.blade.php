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
                                <span class="las la-clipboard-list text-dark me-4"></span>
                                Daftar Data Asset
                            </h2>
                        </div>

                        <a class="btn btn-sm rounded-1 btn-light-dark fas-add" data-faid="0" href="javascript:void(0)">
                            <i class="bi bi-plus-square-fill fs-3"></i>
                            Tambah
                        </a>
                    </div>
                </div>

                <form method="post" id="form-fa" novalidate="">
                    <!--begin::Compact form-->
                    <div class="p-6 pb-0">
                        <div class="row g-0 gx-4">
                           <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode</label>
                                <div class="input-group">
                                    <input type="text" id="sJurnal-period" class="form-control form-control-sm rounded-1" readonly="" />
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Kategori Asset</label>
                                {!! $cmb_kategori_fa !!}
                            </div>

                            <div class="col-lg-5">
                                <label class="text-dark fw-bold fs-7 pb-2">Lokasi Asset</label>
                                {!! $cmb_lokasi_fa !!}
                            </div>
                        </div>

                        <div class="row g-0 gx-4 mt-3">
                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Status Asset</label>
                                {!! $cmb_status_fa !!}
                            </div>

                            <div class="col-lg-8">
                                <label class="text-dark fw-bold fs-7 pb-2">Kode / Nama / Deskripsi Asset</label>
                                <input type="text" id="sKodeNamaDesc" class="form-control form-control-sm rounded-1" placeholder="Masukan Kode / Nama / Deskripsi Asset" />
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
                <table id="myTableFA" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7 text-center">
                        <tr class="fw-bold text-white">
                            <th class="border-start py-5">Kategori Asset</th>
                            <th class="border-start py-5">Nama Asset</th>
                            <th class="border-start py-5">Nilai Perolehan</th>
                            <th class="border-start py-5">Tanggal Efektif</th>
                            <th class="border-start py-5">Keterangan</th>
                            <th class="border-start py-5">Lokasi</th>
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
<div class="modal fade" id="mdl-form-fa" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-fa" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <!--begin::Modal content-->
        <div class="modal-content"></div>
        <!--end::Modal content-->
    </div>
</div>

<div class="modal fade" id="mdl-form-fa2" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-form-fa2" aria-hidden="true">
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
    var sPeriod = '{{ $sPeriod }}'
    var ePeriod = '{{ $ePeriod }}'

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

    // DATATABLE myTableFA
    const ajaxOptions = {
        url     : "{{ route('api.akunting.fixed_asset') }}",
        data    : function (params)
                {
                    params.sdate            = sPeriod
                    params.edate            = ePeriod
                    params.facid            = $("#sFacid option:selected").val()
                    params.falid            = $("#sFalid option:selected").val()
                    params.fastatus         = $("#sFAStatus option:selected").val()
                    params.kode_nama_desc   = $("#sKodeNamaDesc").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'facategory',
                        name: 'facategory',

                        render: function (data, type, row, meta)
                        {
                            let $ret = row.facategory + `<br /><I class="text-danger fw-semibold">Kode : ${row.facode}</I>`

                            return $ret
                        }
                    },
                    {
                        data: 'faname',
                        name: 'faname',

                        render: function (data, type, row, meta)
                        {
                            let $ret = row.faname + `<br /><I class="text-danger fw-semibold">Masa Manfaat : ${row.masa_manfaat}</I>`

                            return $ret
                        }
                    },
                    {
                        data: 'nilai_perolehan',
                        name: 'nilai_perolehan',
                        className: 'dt-body-right',
                    },
                    {
                        data: 'fadate',
                        name: 'fadate',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'fadesc',
                        name: 'fadesc',
                    },
                    {
                        data: 'falokasi',
                        name: 'falokasi',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'fastatus',
                        name: 'fastatus',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            let status = 'danger'
                            let txt = 'Non Aktif'
                            let show_notes = false
                            let status_notes = ''

                            if (data == 4)
                            {
                                status = 'danger'
                                icon = 'minus-circle'
                                txt = 'Write Off'
                                show_notes = true
                                status_notes = row.wo_notes
                            }
                            else if (data == 3)
                            {
                                status = 'success'
                                icon = 'check-circle'
                                txt = 'Approved'
                                show_notes = true
                                status_notes = row.fastatus_notes
                            }
                            else if (data == 2)
                            {
                                status = 'info'
                                icon = 'hourglass-half'
                                txt = 'To Be Approved'
                            }
                            else
                            {
                                status = 'primary'
                                icon = 'ban'
                                txt = 'Not Processed'
                            }

                            let $ret = `<span class="badge badge-light-${status}">
                                            <i class="las la-${icon} text-${status}"></i>&nbsp;${txt}
                                        </span>`

                            if (show_notes)
                                    $ret += `<br /><I class="text-${status} fw-semibold text-start d-block">Notes : ${status_notes}</I>`

                            return $ret
                        }
                    },
                    {
                        data: 'faid',
                        name: 'faid',
                        className: 'dt-body-center',

                        render: function (data, type, row, meta)
                        {
                            if (row.fastatus == 3)
                                $btnFungsi = `<button class="btn btn-sm btn-icon btn-dark" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="las la-ellipsis-h fs-4"></i>
                                            </button>

                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <li>
                                                    <a href="javascript:void(0)" class="dropdown-item py-2 fas-detail" data-faid="${row.faid}">
                                                        <i class="las la-eye fs-3 text-dark me-2"></i> Detail Asset
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" class="dropdown-item py-2 fas-change-locate" data-faid="${row.faid}">
                                                        <i class="las la-map-marker-alt fs-3 text-dark me-2"></i> Ubah Lokasi
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" class="dropdown-item py-2 fas-revaluate" data-faid="${row.faid}">
                                                        <i class="las la-retweet fs-3 text-dark me-2"></i> Revaluate This Asset
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" class="dropdown-item py-2 fas-write-off" data-faid="${row.faid}">
                                                        <i class="las la-minus-circle fs-3 text-dark me-2"></i> Write Off This Asset
                                                    </a>
                                                </li>
                                            </ul>`
                            else
                                $btnFungsi = `<a href="javascript:void(0)" data-faid="${row.faid}" title="Detail" class="btn btn-dark btn-sm btn-icon fs-8 fas-detail">
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
                '#myTableFA',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#form-fa').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.fas-add').on('click', function (e)
    {
        const faid = $(this).data('faid')

        openModalFA(faid)
    })

    $('#myTableFA').on('click', '.fas-detail', function ()
    {
        const faid = $(this).data('faid')

        openModalFA(faid)
    })

    $('#myTableFA').on('click', '.fas-change-locate', function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const faid = $(this).data('faid')

        let getFormLokasi = async function (faid)
        {
            let link = "{{ route('api.akunting.fixed_asset.ubah_lokasi') }}"

            try {
                result = await $.ajax({
                    url     : link,
                    type    : 'GET',
                    data    : { faid: faid }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
            }
        }

        showLoading()

        getFormLokasi(faid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-fa2', res, null, true)
        })
    })

    $('#myTableFA').on('click', '.fas-revaluate', function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const faid = $(this).data('faid')

        let getFormRevaluate = async function (faid)
        {
            let link = "{{ route('api.akunting.fixed_asset.revaluate') }}"

            try {
                result = await $.ajax({
                    url     : link,
                    type    : 'GET',
                    data    : { faid: faid }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
            }
        }

        showLoading()

        getFormRevaluate(faid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-fa2', res, null, true)
        })
    })

    $('#myTableFA').on('click', '.fas-write-off', function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        const faid = $(this).data('faid')

        let getFormWriteOff = async function (faid)
        {
            let link = "{{ route('api.akunting.fixed_asset.write_off') }}"

            try {
                result = await $.ajax({
                    url     : link,
                    type    : 'GET',
                    data    : { faid: faid }
                })

                return result
            } catch (error) {
                Swal.close()

                swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
            }
        }

        showLoading()

        getFormWriteOff(faid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-fa2', res, null, true)
        })
    })

    function openModalFA (faid = 0)
    {
        showLoading()

        getForm(faid).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-form-fa', res, null, true)
        })
    }

    async function getForm (faid)
    {
        let result
        let link = faid == 0 ? "{{ route('api.akunting.fixed_asset.create') }}" : "{{ route('api.akunting.fixed_asset.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { faid: faid }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush