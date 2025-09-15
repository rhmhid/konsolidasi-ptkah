<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_new_address_header">
    <!--begin::Modal title-->
    <h2>
        <span class="las la-file-alt text-dark me-4"></span>
        Detail Jurnal
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
        <div class="table-responsive">
            <table class="table table-row-bordered border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                <tr>
                    <td style="width: 10%;">Doc No.</td>
                    <td style="width: 15%;">{{ $data_db->gldoc }}</td>
                    <td style="width: 10%;">Doc Type</td>
                    <td style="width: 15%;">{{ $data_db->journal_name }}</td>
                </tr>
                <tr>
                    <td>Entry Date</td>
                    <td>{{ dbtstamp2stringina($data_db->create_time) }}</td>
                    <td>Doc Date</td>
                    <td>{{ dbtstamp2stringina($data_db->gldate) }}</td>
                </tr>
                <tr>
                    <td>Posting Status</td>
                    <td>{{ $data_db->posted }}</td>
                    <td>Posted By</td>
                    <td>{{ $data_db->posted_by }}</td>
                </tr>
                <tr>
                    <td>Ref. Code</td>
                    <td>{{ $data_db->reff_code }}</td>
                    <td>Supplier / Customer Name</td>
                    <td>{{ $data_db->supp_cust }}</td>
                </tr>
                <tr>
                    <td>Short Text</td>
                    <td colspan="3">{{ $data_db->gldesc }}</td>
                </tr>
            </table>
        </div>

        <div class="table-responsive mt-2">
            <table id="myTableCurrencyRate" class="table table-row-bordered border border-bottom border-gray-300 align-middle rounded-0 g-4 fs-8 w-100">
                <thead class="bg-dark text-uppercase fs-7">
                    <tr class="fw-bold text-white">
                        <th class="text-center align-middle border-start py-5" rowspan="2">GL Account</th>
                        <th class="text-center align-middle border-start py-5" rowspan="2">Description</th>
                        <th class="text-center align-middle border-start py-5" colspan="2">Amount</th>
                        <th class="text-center align-middle border-start py-5" rowspan="2">Cost Center</th>
                    </tr>
                    <tr class="fw-bold text-white">
                        <th class="text-center align-middle border-start py-5">Debet</th>
                        <th class="text-center align-middle border-start py-5">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rsd as $row)
                        @php
                            $row = FieldsToObject($row);
                            $tot_deb += $row->debet;
                            $tot_cre += $row->credit;
                        @endphp

                        <tr>
                            <td class="text-center">{{ $row->coacode }}</td>
                            <td>{{ $row->notes }}<br /><I>[ {{ $row->coaname }} ]</I></td>
                            <td class="text-end">Rp. {{ format_uang($row->debet, 2) }}</td>
                            <td class="text-end">Rp. {{ format_uang($row->credit, 2) }}</td>
                            <td>{{ $row->cost_center }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end"><b>TOTAL</b></td>
                        <td class="text-end"><b>Rp. {{ format_uang($tot_deb, 2) }}</b></td>
                        <td class="text-end"><b>Rp. {{ format_uang($tot_cre, 2) }}</b></td>
                        <td><b>&nbsp;</b></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-3 w-100 py-4 border-top d-flex justify-content-between">
            <button type="button" class="btn btn-danger btn-sm rounded-1" id="me_btn_tutup" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-dark btn-sm rounded-1" id="me_btn_cetak" data-glid="{{ $myglid}}">
                <i class="las la-print"></i> Cetak
            </button>
        </div>
    </div>
</div>
<!--end::Modal body-->
<script type="text/javascript">
    $('#me_btn_cetak').on('click', function ()
    {
        let myglid = $(this).data('glid')

        let link = "{{ route('akunting.daftar_jurnal.cetak', ['myglid' => ':myglid']) }}"
            link = link.replace(':myglid', myglid)

        NewWindow(link, 'jurnal_detail', 1000, 500, 'yes')
        return false
    })
</script>