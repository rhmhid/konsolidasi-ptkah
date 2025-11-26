<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-book-medical text-dark me-4"></span>
        Tambah Satuan Barang
    </h2>

    <div class="btn btn-sm btn-icon btn-active-color-dark" data-bs-dismiss="modal">
        <i class="fas fa-times"></i>
    </div>
    <!--end::Modal title-->
</div>
<!--end::Modal header-->

<!--begin::Modal body-->
<div class="modal-body py-6 px-lg-7">
    <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">                    
        <form method="post" id="form-barang-satuan" novalidate>
            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-12">
                    <label class="text-dark fw-bold fs-7 pb-2">Cari Satuan</label>
                    <input type="text" id="s_kode_nama_sat" class="form-control form-control-sm rounded-1" placeholder="Masukkan Kode / Nama Satuan Yang Akan Dicari" />
                </div>
            </div>
            <!--end::Compact form-->
        </form>
    </div>

    <div class="table-responsive mt-3">
        <table id="myTableBarangSatuan" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
            <thead class="bg-dark text-uppercase fs-7">
                <tr class="fw-bold text-white">
                    <th class="border-start py-5">Kode Satuan</th>
                    <th class="border-start py-5">Satuan</th>
                    <th class="border-start py-5">Fungsi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    $('#myTableBarangSatuan').DataTable().clear().destroy()

    // DATATABLE myTablePeserta
    var ajaxOptionsSatuan = {
        url     : "{{ route('api.inventori.master_data.satuan') }}",
        data    : function (params)
            {
                params.s_kode_nama_sat  = $("#s_kode_nama_sat").val()
            },
    }

    var optionsSatuan = {
        columns:[
                    {
                        data: 'kode_satuan',
                        name: 'kode_satuan',
                    },
                    {
                        data: 'nama_satuan',
                        name: 'nama_satuan',
                    },
                    {
                        data: 'kode_satuan',
                        name: 'kode_satuan',

                        render: function (data, type, row, meta)
                        {
                            $btnFungsi = `<span role="button" class="btn btn-icon btn-sm btn-light-dark pilih-satuan"><i class="fa fa-plus"></i></span>
                                            <input type="hidden" name="mysatuan[]" value="${row.nama_satuan}" />
                                            <input type="hidden" name="mykode_satuan[]" value="${row.kode_satuan}" />`

                            return $btnFungsi
                        },

                        sortable: false,
                        className: "text-center",
                    },
                ],

        order: [[0, 'asc'], [1, 'asc']],

        pagingType: 'simple',
        autoWidth: false
    }

    tableSatuan = setupDataTable(
                    '#myTableBarangSatuan',
                    ajaxOptionsSatuan,
                    optionsSatuan
                )

    $('#form-barang-satuan').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        tableSatuan.ajax.reload()
    })

    $('#myTableBarangSatuan').on('click', '.pilih-satuan', function ()
    {
        AddSatuan($(this).siblings('input[name="mykode_satuan[]"]').val(), $(this).siblings('input[name="mysatuan[]"]').val(), '', 't', 'Aktif', '')
    })
</script>