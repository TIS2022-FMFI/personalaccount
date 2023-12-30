@include('common.navigation')

<?php
    $from = filter_input(INPUT_GET, 'from', FILTER_SANITIZE_URL);
    $to = filter_input(INPUT_GET, 'to', FILTER_SANITIZE_URL);
?>

<div class="flex-between">
    <div class="main_info">
        <a href={{ route('home') }} class="return_home"><i class="bi bi-chevron-left"></i> Späť na prehľad</a>
        <h1>{{ $account_title }}</h1>
        <label for="sap-id-detail-sap"><b>SAP ID:</b></label>
        <p id="sap-id-detail-sap">{{ $account->sap_id }}</p>    </div>
    <div class="switch-box">
        <p>Výpis účtu</p>
        <label class="switch">
            <input data-account-id="{{ $account->id }}" class="toggle-button" checked="true" type="checkbox">
            <span class="slider round"></span>
        </label>
        <p>SAP</p>
    </div>
</div>

<div class="filter-box">
    <div>
        <label>Od:</label><input type="date" id="filter-reports-from" value="<?php echo $from ?>"></input>
        <label>Do:</label><input type="date" id="filter-reports-to" value="<?php echo $to ?>"></input>
        <button data-account-id="{{ $account->id }}" type="button" id="reports-filter">Filtrovať</button>
    </div>
    <div>
        <button data-account-id="{{ $account->id }}" id="add-sap-report" type="button" title="Nový výkaz">+</i></button>
    </div>
</div>
<table>
    <tr>
        <th>Poradie</th>
        <th>Dátum exportu / nahratia</th>
        <th></th>
    </tr>

     @foreach ($reports as $key=>$report)
        
        <tr class="sap-row">
            <td>{{ $key+1 }}.</td>
            <td>{{ $report->exported_or_uploaded_on->format('d.m.Y') }}</td>
            <td class="sap-icons">
                <a href="{{ route('sap-report-raw', [ $report->id ]) }}"><i class="bi bi-download" title="Stiahnuť výkaz"></i></a>
                <button type="button" data-report-id="{{ $report->id }}" class="report-delete"><i  class="bi bi-trash3" title="Zmazať výkaz"></i></button>
            </td>
        </tr>

    @endforeach

</table>
<div class="pagination"> {{ $reports->links("pagination::semantic-ui") }} </div>



@include('common.footer')