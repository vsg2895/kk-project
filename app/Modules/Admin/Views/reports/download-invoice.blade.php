<style>
    body {
        font-size: 0.9rem;
    }

    table {
        margin-bottom: 4rem;
    }

    .title {
        font-size: 1.3rem;
        font-weight: bold;
    }

    .sub-title {
        font-size: 1rem;
        font-weight: bold;
    }

    .border {
        border: 1px solid black;
    }
</style>

<table>
    <tr>
        <td class="title" style="width: 20rem">Självfaktura</td>
        <td class="title">Körkortsjakten AB</td>
    </tr>
    <tr>
        <td style="width: 20rem">
            <div class="sub-title">Fakturadatum</div>
            <div>{{ \Illuminate\Support\Carbon::now()->format('Y-m-d') }}</div>
        </td>
        <td>
            <div class="sub-title">Fakturaadress</div>
        </td>
    </tr>
    <tr>
        <td style="width: 20rem">
            <div class="sub-title">Kundnummer</div>
            <div>{{ $school->id }}</div>
        </td>
        <td class="border">
            <div class="sub-title">Körkortsjakten AB</div>
            <div>Slottsgatan 27</div>
            <br>
            <div>722 11 Västerås</div>
            <div>Org.nr 556569-2125</div>
        </td>
    </tr>
    <tr>
        <td style="width: 20rem">
            <div class="sub-title">Fakturanummer</div>
            <div>{{ $invoice_id }}</div>
            <br>
        </td>
    </tr>
    <tr>
        <td style="width: 20rem">
            <div class="sub-title">Er ref</div>
            <div>Tony Carlsson</div>
        </td>
        <td>
            <div class="sub-title">Betalningsvillkor</div>
            <div>10 dagar netto</div>
        </td>
    </tr>
    <tr>
        <td style="width: 20rem">
            <div class="sub-title">Vår ref</div>
        </td>
        <td>
            <div class="sub-title">Förfallodatum</div>
            <div class="sub-title">{{ \Illuminate\Support\Carbon::now()->addDays(10)->format('Y-m-d') }}</div>
        </td>
    </tr>
</table>

<table style="width: 100%">
    <tr class="sub-title">
        <td>Benämning</td>
        <td>Antal</td>
        <td>á pris</td>
        <td>Summa</td>
    </tr>

        <tr>
            <td>Sålda kurser enligt spec</td>
            <td>{{ $courses['amount'] ?? 0 + $packages['amount'] ?? 0 }}</td>
            <td>1</td>
            <td>{{ number_format((($courses['total'] ?? 0) + ($packages['total'] ?? 0)), 2, ',', ' ') }}</td>
        </tr>
</table>

<table>
    <tr class="sub-title">
        <td style="width: 33rem;">&nbsp;</td>
        <td style="width: 9rem">Netto</td>
    </tr>
    <tr>
        <td style="width: 20rem;">&nbsp;</td>
        <td class="border" style="width: 9rem">{{ number_format($netto, 2, ',', ' ') }}</td>
    </tr>
</table>

<table>
    <tr class="sub-title">
        <td style="width: 20rem;">&nbsp;</td>
        <td style="width: 9rem">Moms</td>
        <td style="width: 9rem">Att betala</td>
    </tr>
    <tr>
        <td style="width: 24rem;">&nbsp;</td>
        <td class="border" style="width: 8rem">{{ number_format($vat, 2, ',', ' ') }}</td>
        <td class="border" style="width: 8rem">{{ number_format($total, 2, ',', ' ') }}</td>
    </tr>
</table>

<table style="width: 100%">
    <tr class="sub-title">
        <td>Address</td>
        <td>Bankgironummer</td>
        <td>Org nr</td>
    </tr>
    <tr>
        <td>
            <div>{{ $school->name }}</div>
            <div>{{ $school->address }}</div>
            <div>{{ $school->zip }} {{ $school->postal_city }}</div>
        </td>
        <td>{{ $school->bankgiro_number }}</td>
        <td>{{ $school->organization_number }}</td>
    </tr>
</table>

<div class="sub-title footer">Moms reg.nr: {{ $school->moms_reg_nr }}</div>
<div>Företaget är registrerat för F-skatt</div>
<hr/>
Moms: <br/>
@if($moms_edu) {{ config('fees.moms_edu') }}% {{ number_format($moms_edu , 2, ',', ' ') }} ({{ number_format($total_moms_edu, 2, ',', ' ') }}) <br/>@endif
@if($moms) {{ config('fees.moms') }}% {{ number_format($moms, 2, ',', ' ') }} ({{ number_format($total_moms, 2, ',', ' ') }}) <br/>@endif
