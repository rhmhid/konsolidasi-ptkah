<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-exchange-alt text-dark me-4"></span>
        Form Input Konfirmasi Distribusi
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
        <form method="post" id="form-input-kd" novalidate>
            <input type="hidden" name="tbid" id="tbid" value="{{ $data_head->tbid }}" readonly="" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-2">
                    <label class="text-dark fw-bold fs-7 pb-2">Gudang Pengirim</label>
                    <div class="text-dark fw-normal fs-7">{{ $data_head->pengirim }}</div>
                </div>

                <div class="col-lg-2">
                    <label class="text-dark fw-bold fs-7 pb-2">Gudang Penerima</label>
                    <div class="text-dark fw-normal fs-7">{{ $data_head->penerima }}</div>
                </div>

                <div class="col-lg-2">
                    <label class="text-dark fw-bold fs-7 pb-2">Kode Transfer</label>
                    <div class="text-dark fw-normal fs-7">{{ $data_head->transfer_code }}</div>
                </div>

                <div class="col-lg-2">
                    <label class="text-dark fw-bold fs-7 pb-2">Tanggal Transfer</label>
                    <div class="text-dark fw-normal fs-7">{{ dbtstamp2stringlong_ina($data_head->transfer_date) }}</div>
                </div>

                <div class="col-lg-2">
                    <label class="text-dark fw-bold fs-7 pb-2">Keterangan</label>
                    <div class="text-dark fw-normal fs-7">{{ $data_head->keterangan }}</div>
                </div>

                <div class="col-lg-2">
                    <label class="text-dark fw-bold fs-7 pb-2">User Distribusi</label>
                    <div class="text-dark fw-normal fs-7">{{ $data_head->petugas }}</div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <div class="d-flex flex-column h-100">
                        <label for="keterangan" class="text-dark fw-bold fs-7 pb-2 required">Keterangan Konfirmasi</label>
                        <textarea id="keterangan" name="keterangan" class="form-control form-control-sm rounded-1 flex-grow-1 py-md-0" required=""></textarea>
                    </div>
                </div>
            </div>

            <div class="row g-0 gx-4 mt-3">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold required">
                        Detail Barang
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu Inventori -> Masterdata -> Barang"></i>
                    </label>

                    <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-barang">
                        <thead class="bg-dark text-uppercase fs-7 text-center">
                            <tr class="fw-bold text-white">
                                <th>Barang</th>
                                <th>Jumlah Kirim</th>
                                <th>Jumlah Terima</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-danger btn-sm rounded-1" id="me_btn_batal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_simpan">Simpan</button>
            </div>
            <!--end::Compact form-->
        </form>
    </div>
</div>
<!--end::Modal body-->

<script type="text/javascript">
    {!! $AddBarang !!}

    $('.modal [data-bs-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    })

    $('.modal .form-select').each(function ()
    {
        var obj, parent

        // you can set your default select2 options in obj
        obj = {
            // default options
            width: '100%',
        }

        // if there is a modal that select is inside it
        parent = $(this).closest('.modal')

        if (parent.length) obj['dropdownParent'] = parent

        $(this).select2(obj)
    })

    function AddBarang (tbdid, barang, kode_satuan, vol_kirim)
    {
        let data_coa = `<tr class="align-middle">
                            <td class="text-nowrap">
                                <font class="mx-3">${barang}</font>
                                <input type="hidden" name="tbdid[]" id="tbdid[]" value="${tbdid}" />
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 number-only" name="vol_kirim[]" value="${vol_kirim}" />
                                    <span class="input-group-text">${kode_satuan}</span>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm w-50px fs-8 rounded-1 number-only" name="vol_terima[]" value="${vol_kirim}" />
                                    <span class="input-group-text">${kode_satuan}</span>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <input type="text" name="ket_item[]" id="ket_item" class="form-control form-control-sm rounded-1" required="" />
                            </td>
                        </tr>`

        $('#tbl-barang > tbody:last').append(data_coa)
    }

    // aksi submit edit / update
    $('#form-input-kd').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.inventori.konfirmasi_distribusi.save') }}"

            showLoading()

            setTimeout((function ()
            {
                doAjax(
                    formSubmitUrl,
                    payload,
                    "POST"
                )
                .done( data => {
                    Swal.close()

                    if (data.success)
                    {
                        swalShowMessage('Sukses', data.message, 'success')
                        .then((result) =>
                        {
                            if (result.isConfirmed)
                            {
                                $("#mdl-form-kd").modal('hide')

                                table.ajax.reload(null, false)
                            }
                        })
                    }
                    else swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    Swal.close()

                    const res = err?.responseJSON

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>