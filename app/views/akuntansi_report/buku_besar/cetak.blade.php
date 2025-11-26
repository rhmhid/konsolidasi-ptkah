@extends('layouts.print')

@push('css')
<style type="text/css">
    table tr th { vertical-align: middle; }
    h2 span { font-weight: bold; }

    #loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.7);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        font-size: 20px;
        font-weight: bold;
    }
</style>

<style media="print">
    body { margin-top: -2cm; }
    table { border-collapse: unset; }
</style>
@endpush

@push('script')
<script type="text/javascript">
    function detail_gl (glid)
    {
        let link = "{{ route('akunting.daftar_jurnal.cetak', ['myglid' => ':myglid']) }}"
            link = link.replace(':myglid', glid)

        NewWindow(link, 'jurnal_detail', 1000, 500, 'yes')
        return false
    }

    function ParamsForm ()
    {
        const $form = $('#form-buku-besar')

        return {
            sdate: '{{ $data['sdate'] }}',
            edate: '{{ $data['edate'] }}',
            jtid: '{{ $data['jtid'] }}',
            is_posted: '{{ $data['is_posted'] }}',
            coaid_from: '{{ $data['coaid_from'] }}',
            coaid_to: '{{ $data['coaid_to'] }}',
            pccid: '{{ $data['pccid'] }}',
            with_bb: '{{ $data['with_bb'] }}',
            coa_vs: '{{ $coa_vs }}'
        }
    }

    function ExportExcel ()
    {
        const overlay = document.getElementById("loading-overlay")
            overlay.style.display = "flex" // tampilkan loading

        setTimeout((function ()
        {
            const href = "{{ route('api.akuntansi_report.buku_besar.excel') }}"
            const name = 'Buku Besar - ' + moment().format('DD-MM-YYYY') + '.xlsx'

            exportExcel({
                name,
                url: href,
                params: ParamsForm()
            }).finally(() => {
                overlay.style.display = "none" // tutup loading
            })
        }), 2e3)
    }
</script>
@endpush

@push('function')
<div id="functions">
    <ul>
        <li><a href="javascript:void(0)" onclick="JavaScript:window.print();">Print</a></li>
        <li><a href="javascript:void(0)" onclick="JavaScript:window.close();">Close</a></li>
        <li><a href="javascript:void(0)" onclick="JavaScript:ExportExcel();">Export Excel</a></li>
    </ul>        
</div>
@endpush

@push('kop')
<div id="lhd">
    <div class="ttl">
        @if (isMultiTenants() == 't')
        <span class="nme">{{ Auth::user()->branch->branch_name }}</span>
        <span>
            {{ Auth::user()->branch->branch_addr }}
        </span>
        @else
        <span class="nme">{{ dataConfigs('company_name') }}</span>
        <span>
            {{ dataConfigs('company_address') }}, Phone : {{ dataConfigs('company_telp') }}, {{ dataConfigs('company_city') }} - {{ dataConfigs('company_provinsi') }}
        </span>
        @endif
    </div>
</div>
@endpush

@section('content')
<div id="loading-overlay">
    ⏳ Sedang membuat file Excel...
</div>

<h2 class="bdr">
    Overview Ledger
    <span style="text-transform: uppercase;">Periode : {{ $data['sdate'] }} UNTIL {{ $data['edate'] }}</span>
    <span style="text-transform: uppercase;">C.O.A : {{ $coa_from }} UNTIL {{ $coa_to }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>Tgl Trans</th>
            <th>GL Trans</th>
            <th>Account Short Tex</th>
            <th>Description</th>

            @if ($coa_vs == 't')
                <th>C.O.A Lawan</th>
            @endif

            <th>User Entry</th>
            <th>Supplier/Customer</th>
            <th>Debet</th>
            <th>Credit</th>
            <th>Balance</th>
            <th>Doc Number</th>
            <th>Reff Code</th>
            <th>Transaction Type</th>
            <th>Short Text</th>
            <th>Cost Center</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rs as $row)
            @php
                $no++;
                $row = FieldsToObject($row);

                $debet = $row->debet;
                $credit = $row->credit;
                
                if ($old_coaid != $row->coaid && $data['with_bb'] == 't') $balance = $row->opbal;
                elseif ($old_coaid != $row->coaid && $data['with_bb'] == 'f') $balance = 0;

                $balance += $row->default_debet == 't' ? ($debet - $credit) : ($credit - $debet);
                $row_blank = $old_coaid != $row->coaid && $no > 1 ? 't' : 'f';
                $row_opening = $old_coaid != $row->coaid && $data['with_bb'] == 't' ? 't' : 'f';
                $old_coaid = $row->coaid;

                if ($coa_vs == 't')
                {
                    $rsd = BukuBesarMdl::detail_jurnal($row->glid, $row->gldid);

                    $row->coa_vs = $br = '';
                    $mulai = false;
                    while (!$rsd->EOF)
                    {
                        if ($mulai) $br = '<br />';

                        if ($rsd->fields['debet'] > 0) $stat_amount = 'Dr : '.format_uang($rsd->fields['debet'], 2);
                        else $stat_amount = 'Cr : '.format_uang($rsd->fields['credit'], 2);

                        $row->coa_vs .= $br.'- '.$rsd->fields['coacode'].' '.$rsd->fields['coaname'].' [ '.$stat_amount.' ]';
                        $mulai = true;

                        $rsd->MoveNext();
                    }
                }
            @endphp

            @if ($row_blank == 't')
                <tr>
                    <td colspan="14">&nbsp;</td>
                </tr>
            @endif

            @if ($row_opening == 't')
                <tr>
                    <td align="center">{{ dbtstamp2stringlong_ina($row->gldate) }}</td>
                    <td align="center">{{ $row->coacode }}</td>
                    <td>{{ $row->coaname }}</td>
                    <td>BEGINING BALANCE</td>

                    @if ($coa_vs == 't')
                        <td colspan="5">&nbsp;</td>
                    @else
                        <td colspan="4">&nbsp;</td>
                    @endif

                    <td align="right">{{ format_uang($row->opbal, 2) }}</td>
                    <td colspan="5">&nbsp;</td>
                </tr>
            @endif

            <tr>
                <td align="center">{{ dbtstamp2stringlong_ina($row->gldate) }}</td>
                <td align="center">{{ $row->coacode }}</td>
                <td>{{ $row->coaname }}</td>
                <td>{{ $row->gldesc }}</td>

                @if ($coa_vs == 't')
                    <td>{!! $row->coa_vs !!}</td>
                @endif

                <td>{{ $row->nama_lengkap }}</td>
                <td>{{ $row->nama_supp }}</td>
                <td align="right">{{ format_uang($debet, 2) }}</td>
                <td align="right">{{ format_uang($credit, 2) }}</td>
                <td align="right">{{ format_uang($balance, 2) }}</td>
                <td align="center">
                    <a href="javascript:void(0)" onclick="detail_gl({{ $row->glid }});">{{ $row->gldoc }}</a>
                </td>
                <td align="center">{{ $row->reff_code }}</td>
                <td align="center">{{ $row->journal_name }}</td>
                <td>{{ $row->notes }}</td>
                <td>{{ $row->cost_center }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
