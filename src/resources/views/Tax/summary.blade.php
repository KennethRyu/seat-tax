@extends('web::layouts.grids.12')

@section('title', trans('seat_tax::tax.summary'))
@section('page_header', trans('seat_tax::tax.summary-live'))

@section('full')
    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('seat_tax::tax.previousbill') }}</h3>
        </div>
        {{--以前的账单--}}
        <div class="box-body">
            @foreach($dates->chunk(3) as $date)
                <div class="row ">
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
                <i class="fa fa-line-chart"></i> {{ trans('seat_tax::tax.current-live-numbers') }}
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab2">
                <table class="table table-striped" id="livenumbers">
                    <thead>
                    <tr>
                        <th>公司</th>
                        <th>总赏金</th>
                        <th>税率</th>
                        <th>欠税</th>
                        <th>注册用户</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($corps as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td class="text-right" data-order="{{ $row->bounties }}">{{ number_format($row->bounties, 2) }} ISK</td>

                            @if($row->actives / $row->members < (setting('irate', true) / 100))
                                <td class="text-right" data-order="{{ setting('bountytaxrate', true) }}">{{ setting('bountytaxrate', true) }}%</td>
                            @else
                                <td class="text-right" data-order="{{ setting('ibountytaxrate', true) }}">{{ setting('ibountytaxrate', true) }}%</td>
                            @endif

                            @if($row->actives / $row->members < (setting('irate', true) / 100))
                                <td class="text-right" data-order="{{ $row->bounties * (setting('bountytaxrate', true)) }}">{{ number_format(($row->bounties * (setting('bountytaxrate', true) / 100)),2) }} ISK</td>
                            @else
                                <td class="text-right" data-order="{{ $row->bounties * (setting('ibountytaxrate', true)) }}">{{ number_format(($row->bounties * (setting('ibountytaxrate', true) / 100)),2) }} ISK</td>
                            @endif

                            @if ($row->members > 0)
                                <td class="text-right" data-order="{{ $row->actives / $row->members }}">
                                    {{ $row->actives }} / {{ $row->members }}
                                    ({{ round(($row->actives / $row->members) * 100)  }}%)
                                </td>
                            @else
                                <td class="text-right" data-order="0">0 / 0 (0%)</td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('javascript')
    @include('web::includes.javascript.id-to-name')
    <script type="application/javascript">
        // table = $('#indivmining').DataTable({
        //     paging: false,
        // });

        $('#corpspinner').change(function () {

            $('#indivmining').find('tbody').empty();
            id = $('#corpspinner').find(":selected").val();
            if (id > 0) {
                $.ajax({
                    headers: function () {
                    },
                    url: "/billing/getindbilling/" + id,
                    type: "GET",
                    dataType: 'json',
                    timeout: 10000
                }).done(function (result) {
                    if (result) {
                        table.clear();
                        for (var chars in result) {
                            table.row.add(['<a href=""><span class="id-to-name" data-id="' + chars + '">{{ trans('web::seat.unknown') }}</span></a>',
                                (new Intl.NumberFormat('en-US').format(result[chars].amount)) + " ISK",
                                (result[chars].modifier * 100) + "%",
                                (result[chars].taxrate * 100) + "%",
                                (new Intl.NumberFormat('en-US', {maximumFractionDigits: 2}).format(result[chars].amount * result[chars].taxrate * result[chars].modifier)) + " ISK"]);
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

        $('#alliancespinner').change(function () {
            id = $('#alliancespinner').find(":selected").val();
            window.location.href = '/billing/alliance/' + id;
        });

        $('#livenumbers').DataTable({
            "language": {
                "lengthMenu": "每页 _MENU_ 条记录",
                "zeroRecords": "没有找到记录",
                "info": "第 _PAGE_ 页 ( 总共 _PAGES_ 页 )",
                "infoEmpty": "无记录",
                "infoFiltered": "(从 _MAX_ 条记录过滤)",
                "search": '搜索',
                "paginate":{
                    "next":'下一页',
                    "previous":'上一页'
                }
            }
        });
        // $('#livepve').DataTable();
    </script>
@endpush
