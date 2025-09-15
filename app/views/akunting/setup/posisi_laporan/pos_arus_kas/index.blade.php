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
                                Setup Pos Arus Kas
                            </h2>
                        </div>

                        {!! $cmb_jenis_pos !!}

                        <span class="font-weight-bolder label label-xl label-light-success label-inline ms-2">
                            <a href="javascript:void(0)" class="btn btn-dark btn-sm rounded-1 btn-add-pos-na">
                                <i class="las la-plus"></i> Tambah Data
                            </a>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-6 mt-n2 border-top-3 border-dark" id="kt_content_body">
                <table id="myTablePosCF" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                    <thead class="bg-dark text-uppercase fs-7">
                        <tr class="fw-bold text-white">
                            <th class="text-center border-start py-5">Urutan</th>
                            <th class="text-center border-start py-5">Kode</th>
                            <th class="text-center border-start py-5">Nama Pos</th>
                            <th class="text-center border-start py-5">Direct</th>
                            <th class="text-center border-start py-5">Parent POS</th>
                            <th class="text-center border-start py-5">Summary ?</th>
                            <th class="text-center border-start py-5">Status</th>
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
<div class="modal fade" id="mdl-fi-pos-cf" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-fi-pos-cf" aria-hidden="true">
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
    // DATATABLE myTablePosCF
    const ajaxOptions = {
        url     : "{{ route('api.akunting.setup.posisi_laporan.pos_arus_kas') }}",
        data    : function (params)
                {
                    params.s_jenis_pos = $("#s_jenis_pos").val()
                },
    }

    const options = {
        columns:[
                    {
                        data: 'urutan',
                        name: 'urutan',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'kode_pos', 
                        name: 'kode_pos',
                        className: 'dt-body-center',
                    },
                    {
                        data: 'nama_pos', 
                        name: 'nama_pos',
                    },
                    {
                        data: 'jenis_pos', 
                        name: 'jenis_pos',

                        render: function (data)
                        {
                            let txt = data == 1 ? 'Direct' : 'Indirect'

                            let $icon = `<span class="badge badge-light-danger">
                                            &nbsp;${txt}&nbsp;
                                        </span>`

                            return $icon
                        },

                        className: 'dt-body-center',
                    },
                    {
                        data: 'parent_pos', 
                        name: 'parent_pos',
                    },
                    {
                        data: 'sum_total', 
                        name: 'sum_total',

                        render: function (data)
                        {
                            let status = 'danger'
                            let icon = 'times'

                            if (data == 't')
                            {
                                status = 'success'
                                icon = 'check'
                            }

                            let $icon = `<div class="symbol symbol-20px symbol-circle">
                                            <div class="symbol-label fs-8 fw-bold bg-light-${status} text-${status}">
                                                <span class="fas fa-${icon}"></span>  
                                            </div>
                                        </div>`

                            return $icon
                        },

                        className: 'dt-body-center',
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
                        data: 'pcfid',
                        name: 'pcfid',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<a href="javascript:void(0)" data-pcfid="${row.pcfid}" data-jenis_pos="${row.jenis_pos}" title="Ubah" class="btn btn-dark btn-sm fs-8 btn-update-pos-na">
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
        displayLength: 100,
    }

    table = setupDataTable(
                '#myTablePosCF',
                ajaxOptions,
                options
            )

    // aksi submit cari
    $('#s_jenis_pos').change(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        table.ajax.reload()
    })

    $('.btn-add-pos-na').click(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        let jenis_pos = $('#s_jenis_pos').val()

        openPosNaModal(0, jenis_pos)
    })

    $('#myTablePosCF').on('click', '.btn-update-pos-na', function ()
    {
        const pcfid = $(this).data('pcfid')
        const jenis_pos = $(this).data('jenis_pos')

        openPosNaModal(pcfid, jenis_pos)
    })

    function openPosNaModal (pcfid = 0, jenis_pos)
    {
        showLoading()

        getForm(pcfid, jenis_pos).then((res) => {
            Swal.close()

            // Show modal
            openModal('mdl-fi-pos-cf', res, null, true)
        })
    }

    async function getForm (pcfid, jenis_pos)
    {
        let result
        let link = pcfid == 0 ? "{{ route('api.akunting.setup.posisi_laporan.pos_arus_kas.create') }}" : "{{ route('api.akunting.setup.posisi_laporan.pos_arus_kas.edit') }}"

        try {
            result = await $.ajax({
                url     : link,
                type    : 'GET',
                data    : { pcfid: pcfid, jenis_pos: jenis_pos }
            })

            return result
        } catch (error) {
            Swal.close()

            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }
</script>
@endpush