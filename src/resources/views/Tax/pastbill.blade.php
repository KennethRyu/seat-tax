@extends('web::layouts.grids.12')

@section('title', trans('seat_tax::tax.pastbill'))
@section('page_header', trans('seat_tax::tax.pastbill'))

@section('full')
  <input type="hidden" id="year" value="{{ $year }}">
  <input type="hidden" id="month" value="{{ $month }}">

  <div class="box box-default box-solid">
    <div class="box-header with-border">
      <h4>{{ trans('seat_tax::tax.previousbill') }}</h4>
    </div>
    <div class="box-body">
      @foreach($dates->chunk(3) as $date)
        <div class="row">
          @foreach ($date as $yearmonth)
          <div class="col-xs-4">
            <span class="text-bold">
              <a href="{{ route('seat_tax.pastbilling', ['year' => $yearmonth['year'], 'month' => $yearmonth['month']]) }}">
              {{ date('Y-m', mktime(0,0,0, $yearmonth['month'], 1, $yearmonth['year'])) }}</a>
            </span>
          </div>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>

  <div class="nav-tabs-custom">
    <ul class="nav nav-tabs pull-right bg-gray">
      <li class="active"><a href="#tab2" data-toggle="tab">{{ trans('seat_tax::tax.summary-corp-pve') }}</a></li>
      <li class="pull-left header">
        <i class="fa fa-history"></i> {{ trans('seat_tax::tax.previousbill') }} {{ $year }} - {{ $month }}
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="tab2">
        <table class="table table-striped">
          <tr>
            <th>公司</th>
            <th>总赏金</th>
            <th>税率</th>
            <th>欠税</th>
          </tr>
          @foreach($stats as $row)
            <tr>

              <td>{{ $row->corporation->name }}</td>
              <td>{{ number_format($row->pve_bill / $row->pve_taxrate, 2) }}</td>
              <td>{{ $row->pve_taxrate }}%</td>
              <td>{{ number_format($row->pve_bill, 2) }}</td>
            </tr>
          @endforeach
        </table>
      </div>
    </div>
  </div>

@endsection

@push('javascript')
  @include('web::includes.javascript.id-to-name')

  <script type="application/javascript">

      table = $('#indivmining').DataTable({
          paging: false,
      });

      ids_to_names();

      $('#corpspinner').change(function () {

          $('#indivmining').find('tbody').empty();
          id = $('#corpspinner').find(":selected").val();
          year = $('#year').val();
          month = $('#month').val();

          if (id > 0) {
              $.ajax({
                  headers: function () {
                  },
                  url: "/billing/getindpastbilling/" + id + "/" + year + "/" + month,
                  type: "GET",
                  dataType: 'json',
                  timeout: 10000
              }).done(function (result) {
                  if (result) {
                      table.clear();
                      for (var chars in result) {
                          table.row.add(['<a href=""><span class="id-to-name" data-id="' + result[chars].character_id + '">{{ trans('web::seat.unknown') }}</span></a>', (new Intl.NumberFormat('en-US').format(result[chars].mining_bill)),
                              (result[chars].mining_modifier) + "%", (result[chars].mining_taxrate) + "%",
                              (new Intl.NumberFormat('en-US', {maximumFractionDigits: 2}).format(result[chars].mining_bill * (result[chars].mining_modifier / 100) * (result[chars].mining_taxrate / 100))) + " ISK"]);
                      }
                      table.draw();
                      ids_to_names();
                  }
              });
          }
      });

      $(document).ready(function () {
          $('#corpspinner').select2();
      });
  </script>
@endpush
