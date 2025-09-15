<!--begin::Form-->
<form class="form" id="form-branch-change" method="post">
    <!--begin::Modal header-->
    <div class="modal-header" id="kt_modal_auth">
        <!--begin::Modal title-->
        <h2 id="headerModal">
            <span class="las la-code-branch text-dark me-4"></span>
            Pilih Cabang
        </h2>
        <!--end::Modal title-->

        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-dark" data-bs-dismiss="modal">
            <i class="fas fa-times"></i>
        </div>
        <!--end::Close-->
    </div>
    <!--end::Modal header-->

    <!--begin::Modal body-->
    <div class="modal-body">
        <!--begin::Scroll-->
        <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_auth" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
            <!--begin::Input group-->
            <div class="row gx-4">
                <div class="col-lg-12">
                    <select name="branch_id_mdl" id="branch_id-modal" class="form-select form-select-sm rounded-1" required>
                        <option value="{{ Auth::user()->branch->bid }}">{{ Auth::user()->branch->branch_name }}</option>
                    </select>
                </div>
            </div>
        </div>
        <!--end::Scroll-->
    </div>
    <!--end::Modal body-->

    <!--begin::Modal footer-->
    <div class="modal-footer bg-light">
        <div class="w-100 border-top d-flex justify-content-between">
            <button type="button" class="btn btn-danger btn-sm rounded-1" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-dark btn-sm rounded-1">Simpan</button>
        </div>
    </div>
    <!--end::Modal footer-->
</form>
<!--end::Form-->

<script type="text/javascript">
    $('#branch_id-modal').select2({
        width: '100%',
        dropdownParent: $('#popup-branch')
    })

    $('#branch_id-modal').select2({
        dropdownParent  : $('#popup-branch'),
        placeholder     : '-- Pilih Cabang',
        ajax            :
                        {
                            url             : "{{ route('api.auth.change_branch.cari_branch') }}",

                            data            : function (params)
                                            {
                                                var param = {
                                                    q   : params.term,
                                                }

                                                // Query parameters will be ?search=[term]&type=public
                                                return param
                                            },

                            processResults  : function (json)
                                            {
                                                return {
                                                    results: $.map(json, function (items)
                                                    {
                                                        return {
                                                            id      : items.bid,
                                                            text    : items.branch_name
                                                        }
                                                    })
                                                }
                                            }
                        }
    })

    $('#form-branch-change').submit(function (e)
    {
        e.preventDefault()

        if (validasiForm(this))
        {
            const payloadAuth = new FormData(this)
                payloadAuth.append('_method', 'patch') // ganti ajax method post menjadi patch

            formAuthUrl = "{{ route('api.auth.change_branch.save') }}"

            showLoading()

            setTimeout((function ()
            {
                doAjax(
                    formAuthUrl,
                    payloadAuth,
                    "POST"
                )
                .done( data => {
                    swal.close()

                    if (data.success)
                    {
                        swalShowMessage('Sukses', data.message, 'success')
                        .then((result) =>
                        {
                            if (result.isConfirmed)
                            {
                                localStorage.setItem('auth_branch', $('#branch_id-modal option:selected').val())

                                $("#popup_auth_user").modal('hide')

                                location.reload()
                            }
                        })
                    }
                    else swalShowMessage('Gagal', data.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
                .fail( err => {
                    const res = err?.responseJSON

                    swal.close()

                    swalShowMessage('Gagal', res.message || 'Terjadi Kesalahan saat proses data', 'error')
                })
            }), 2e3)
        }
    })
</script>