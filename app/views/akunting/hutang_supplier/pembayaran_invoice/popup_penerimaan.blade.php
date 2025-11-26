<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-file-invoice text text-dark me-4"></span>
        Data Penerimaan Barang
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
        <form method="post" id="fd-penerimaan-barang" novalidate>
            <input type="hidden" id="SP-suppid" value="{{ $suppid }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Nama Supplier</label>
                    <div>{{ $nama_supp }}</div>
                </div>

                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">No. Faktur / Surat Jalan</label>
                    <input type="text" id="SP-NoFaktur" class="form-control form-control-sm rounded-1" />
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-6">
                    <label class="text-dark fw-bold fs-7 pb-2">Kode PO</label>
                    <input type="text" id="SP-Pocode" class="form-control form-control-sm rounded-1" />
                </div>

                <div class="col-lg-5">
                    <label class="text-dark fw-bold fs-7 pb-2">Kode Penerimaan</label>
                    <input type="text" id="SP-Grcode" class="form-control form-control-sm rounded-1" />
                </div>

                <div class="col-lg-1">
                    <label class="text-dark fw-bold fs-8 pb-2 d-block">&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-dark rounded-1 me-4 w-100" id="btnCariGrn">
                        <i class="la la-search"></i>
                        Cari
                    </button>
                </div>
            </div>
            <!--end::Compact form-->
        </form>
    </div>

    <div class="table-responsive mt-5">
        <table id="myTablePenerimaan" class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
            <thead class="bg-dark text-uppercase fs-7">
                <tr class="fw-bold text-white">
                    <th class="text-center border-start py-5">Kode PO</th>
                    <th class="text-center border-start py-5">Kode Penerimaan</th>
                    <th class="text-center border-start py-5">Tanggal Penerimaan</th>
                    <th class="text-center border-start py-5">No. Faktur / Surat Jalan</th>
                    <th class="text-center border-start py-5">Nominal</th>
                    <th class="text-center border-start py-5">&nbsp;</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    var showListGRN = function ()
    {
        showLoading()

        getGRN().then(res =>
        {
            let listGRN = ""

            res.forEach(data =>
            {
                listGRN += `<tr>
                                <td class="text-center text-nowrap">${data.pocode}</td>
                                <td class="text-center text-nowrap">${data.grcode}</td>
                                <td class="text-center text-nowrap">${data.grdate_txt}</td>
                                <td class="text-center text-nowrap">${data.no_faktur}</td>
                                <td class="text-end text-nowrap">${data.subtotal_txt}</td>
                                <td class="text-center text-nowrap">
                                    <button type="button" class="btn btn-sm btn-dark rounded-1 me-4 pilih-grn">
                                        <i class="la la-list-alt"></i> Pilih
                                    </button>

                                    <input type="hidden" class="grid" value="${data.grid}">
                                    <input type="hidden" class="poid" value="${data.poid}">
                                    <input type="hidden" class="pocode" value="${data.pocode}">
                                    <input type="hidden" class="grcode" value="${data.grcode}">
                                    <input type="hidden" class="tgl_faktur" value="${data.tgl_faktur}">
                                    <input type="hidden" class="no_faktur" value="${data.no_faktur}">
                                    <input type="hidden" class="subtotal" value="${data.subtotal}">
                                    <input type="hidden" class="diskon" value="${data.diskon}">
                                    <input type="hidden" class="ongkir" value="${data.ongkir}">
                                    <input type="hidden" class="materai" value="${data.materai}">
                                    <input type="hidden" class="ppn_persen" value="${data.ppn_persen}">
                                    <input type="hidden" class="ppn_rp" value="${data.ppn_rp}">
                                    <input type="hidden" class="other_cost" value="${data.other_cost}">
                                </td>
                            </tr>`
            })

            if (res.length < 1)
                listGRN = `<tr>
                                <td class="text-center text-nowrap" colspan="5">Data tidak ditemukan</td>
                            </tr>`

            $('#myTablePenerimaan > tbody:last').append(listGRN)

            Swal.close()
        })
    }

    var getGRN = async function ()
    {
        let result
        try {

            result = await $.ajax({
                url     : "{{ route('api.akunting.hutang_supplier.invoice_pembelian.list_penerimaan') }}",
                type    : 'GET',
                data    :
                        {
                            suppid      : $('#SP-suppid').val(),
                            no_faktur   : $('#SP-NoFaktur').val(),
                            pocode      : $('#SP-Pocode').val(),
                            grcode      : $('#SP-Grcode').val()
                        }
            })

            return result
        } catch (error) {
            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
        }
    }

    showListGRN()

    $('#btnCariGrn').on('click', function ()
    {
        $('#myTablePenerimaan tbody').empty()

        showListGRN()
    })

    $('#myTablePenerimaan').on('click', '.pilih-grn', function ()
    {
        let gridValue = $(this).siblings('.grid').val()
        let list_ap = document.querySelectorAll('.grid-row')

        for (var j = 0; j < list_ap.length; j++)
        {
            if (gridValue == list_ap[j].value)
            {
                swalShowMessage('Information, ', 'Data PO sudah masuk ke dalam list pengajuan.', 'info')

                return false
            }
        }

        AddGrn(
            $(this).siblings('.grid').val(),
            $(this).siblings('.poid').val(),
            $(this).siblings('.pocode').val(),
            $(this).siblings('.grcode').val(),
            $(this).siblings('.tgl_faktur').val(),
            '',
	    $(this).siblings('.subtotal').val(),
            $(this).siblings('.diskon').val(),
            $(this).siblings('.ongkir').val(),
            $(this).siblings('.materai').val(),
            $(this).siblings('.ppn_persen').val(),
            $(this).siblings('.ppn_rp').val(),
            $(this).siblings('.other_cost').val(),
	    0,
            $(this).siblings('.no_faktur').val(),
	    0
        )
    })
</script>
