@extends('web::layouts.grids.6-6')

@section('title', trans('seat_tax::tax.settings'))
@section('page_header', trans('seat_tax::tax.settings'))

@section('left')
    <div class="box box-success box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('seat_tax::tax.settings') }}</h3>
        </div>
        <form method="POST" action="{{ route('seat_tax.savesettings')  }}" class="form-horizontal">
            <div class="box-body">
                {{ csrf_field() }}
                <h4>{{ trans('seat_tax::tax.default-settings') }}</h4>
                <div class="form-group">
                    <label for="oremodifier" class="col-sm-3 control-label">{{ trans('seat_tax::tax.Ore value modifier') }}</label>
                    <div class="col-sm-8">
                        <div class="input-group col-sm-3">
                            <input class="form-control" type="text" name="oremodifier" id="oremodifier" size="4" value="{{ setting('oremodifier', true) }}" />
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="oretaxrate" class="col-sm-3 control-label">{{ trans('seat_tax::tax.Ore Tax Rate') }}</label>
                    <div class="col-sm-8">
                        <div class="input-group col-sm-3">
                            <input class="form-control" type="text" name="oretaxrate" id="oretaxrate" size="4" value="{{ setting('oretaxrate', true) }}" />
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="ioretaxrate" class="col-sm-3 control-label">{{ trans('seat_tax::tax.Ore Refining Rate') }}</label>
                    <div class="col-sm-8">
                        <div class="input-group col-sm-3">
                            <input class="form-control" type="text" name="refinerate" id="refinerate" size="4" value="{{ setting('refinerate', true) }}" />
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="bountytaxrate" class="col-sm-3 control-label">{{ trans('seat_tax::tax.Bounty Tax Rate') }}</label>
                    <div class="col-sm-8">
                        <div class="input-group col-sm-3">
                            <input class="form-control" type="text" name="bountytaxrate" id="bountytaxrate" size="4" value="{{ setting('bountytaxrate', true) }}" />
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>
                </div>
                <hr />
                <h4>{{ trans('seat_tax::tax.incentivized-settings') }}</h4>
                <div class="form-group">
                    <label for="ioremodifier" class="col-sm-3 control-label">{{ trans('seat_tax::tax.Ore value modifier') }}</label>
                    <div class="col-sm-8">
                        <div class="input-group col-sm-3">
                            <input class="form-control" type="text" name="ioremodifier" id="ioremodifier" size="4" value="{{ setting('ioremodifier', true) }}" />
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="ioretaxrate" class="col-sm-3 control-label">{{ trans('seat_tax::tax.Ore Tax Rate') }}</label>
                    <div class="col-sm-8">
                        <div class="input-group col-sm-3">
                            <input class="form-control" type="text" name="ioretaxrate" id="ioretaxrate" size="4" value="{{ setting('ioretaxrate', true) }}" />
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="ibountytaxrate" class="col-sm-3 control-label">{{ trans('seat_tax::tax.Bounty Tax Rate') }} </label>
                    <div class="col-sm-8">
                        <div class="input-group col-sm-3">
                            <input class="form-control" type="text" name="ibountytaxrate" id="ibountytaxrate" size="4" value="{{ setting('ibountytaxrate', true) }}" />
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="irate" class="col-sm-3 control-label">Rates Threshold</label>
                    <div class="col-sm-8">
                        <div class="input-group col-sm-3">
                            <input class="form-control" type="text" name="irate" id="irate" size="4" value="{{ setting('irate', true) }}" />
                            <div class="input-group-addon">%</div>
                        </div>
                        <p class="help-block">Percentage of registered characters to meet Incentivized Rates</p>
                    </div>
                </div>
                <hr />
                <h4>{{ trans('seat_tax::tax.valuation-of-ore') }}</h4>
                <div class="form-group">
                    <label for="ibountytaxrate" class="col-sm-3 control-label">{{ trans('seat_tax::tax.value-at-ore-price') }}</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            @if (setting('pricevalue', true) == "o")
                                <input type="radio" name="pricevalue" id="pricevalue" value="o" checked/>
                            @else
                                <input type="radio" name="pricevalue" id="pricevalue" value="o"/>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="ibountytaxrate" class="col-sm-3 control-label">{{ trans('seat_tax::tax.value-at-mineral-price') }}</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            @if (setting('pricevalue', true) == "m")
                                <input class="radio" type="radio" name="pricevalue" id="pricevalue" value="m" checked/>
                            @else
                                <input type="radio" name="pricevalue" id="pricevalue" value="m"/>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <input class="btn btn-success pull-right" type="submit" value="{{ trans('seat_tax::tax.update') }}">
            </div>
        </form>
    </div>
@endsection

@section('right')
    <div class="box box-success box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('seat_tax::tax.setting-instructions') }}</h3>
        </div>
        <div class="box-body">
            <div class="col-sm-12">
                <p><label>{{ trans('seat_tax::tax.Ore value modifier') }}:</label> {{ trans('seat_tax::tax.Ore value modifier Desc') }}</p>
            </div>
            <div class="col-sm-12">
                <p><label>{{ trans('seat_tax::tax.Ore Tax Rate') }}:</label> {{ trans('seat_tax::tax.Ore Tax Rate Desc') }}</p>
            </div>
            <div class="col-sm-12">
                <p><label>{{ trans('seat_tax::tax.Ore Refining Rate') }}:</label> {{ trans('seat_tax::tax.Ore Refining Rate Desc') }}</p>
            </div>
            <div class="col-sm-12">
                <p><label>{{ trans('seat_tax::tax.Bounty Tax Rate') }}:</label> {{ trans('seat_tax::tax.Bounty Tax Rate Desc') }}</p>
            </div>
            <div class="col-sm-12">
                <p><label>{{ trans('seat_tax::tax.Incentivised Rates') }}:</label> {{ trans('seat_tax::tax.Incentivised Rates Desc') }}</p>
            </div>
            <div class="col-sm-12">
                <p><label>{{ trans('seat_tax::tax.Valuation of Ore') }}:</label> {{ trans('seat_tax::tax.Valuation of Ore Desc') }}</p>
            </div>
        </div>
    </div>
@endsection
