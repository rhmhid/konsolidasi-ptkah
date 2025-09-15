<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-code-branch text-dark me-4"></span>
        Daftarkan Ke Cabang
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
        <form method="post" id="fi-assignBranch" novalidate>
            <input type="hidden" name="item_type" id="item_type" value="{{ $data['item_type'] }}" />
            <input type="hidden" name="base_id" id="base_id" value="{{ $data['base_id'] }}" />

            <!--begin::Compact form-->
            <div class="row g-0 gx-4">
                <div class="col-lg-12">
                    <label class="d-block text-dark fs-7 fw-bold required">
                        List Data Cabang
                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="List ini berasal dari menu data Cabang"></i>
                    </label>

                    <div class="table-responsive">
                        <table class="table table-striped table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100" id="tbl-barang">
                            <thead class="bg-dark text-uppercase fs-7 text-center">
                                <tr class="fw-bold text-white">
                                    <th>Nama Cabang</th>
                                    <th>Utama</th>
                                    <th>
                                        <div class="parent-check">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input parent-check-input" type="checkbox" name="chk_bid_all" id="chk-bid-all" value="t" />
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rs_cabang as $row)
                                    @php
                                        $row = FieldsToObject($row);

                                        if ($row->is_primary == 't')
                                        {
                                            $status = 'success';    
                                            $icon = 'check';    
                                        }
                                        else
                                        {
                                            $status = 'danger';    
                                            $icon = 'times';    
                                        }

                                        $chk_bid = in_array($row->bid, $availableBranch) ? 'checked=""' : '';
                                    @endphp

                                    @if ($data_wilayah != $row->wilayah)
                                        <tr>
                                            <td colspan="3" style="background: #E0E0E0;">
                                                <label class="mx-2 fw-bold">{{ $row->wilayah }}</label>
                                            </td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td>
                                            <label class="mx-4">
                                                {{ $row->branch_name }}
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <div class="symbol symbol-20px symbol-circle">
                                                <div class="symbol-label fs-8 fw-bold bg-light-{{ $status }} text-{{ $status }}">
                                                    <span class="fas fa-{{ $icon }}"></span>  
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="parent-check">
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input parent-check-input list-bid" type="checkbox" name="bid[]" id="bid-{{ $row->bid }}" value="{{ $row->bid }}" {{ $chk_bid }} />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    @php
                                        $data_wilayah = $row->wilayah;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
    $('#chk-bid-all').change(function ()
    {
        let $chk = $(this).prop('checked')

        $('.list-bid').each(function (id)
        {
            $(this).prop('checked', $chk)
        })
    })

    // aksi submit edit / update
    $('#fi-assignBranch').submit(function (e)
    {
        e.preventDefault() // batalkan aksi form submit

        // untuk cek apakah inputan yang mandatory (required) terisi, pastikan form nya novalidate
        if (validasiForm(this))
        {
            const payload = new FormData(this)
                payload.append('_method', 'patch') // ganti ajax method post menjadi patch

            formSubmitUrl = "{{ route('api.master_data.database.cabang.assign_branch.save') }}"

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
                                $("#mdl-branch-assign").modal('hide')
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