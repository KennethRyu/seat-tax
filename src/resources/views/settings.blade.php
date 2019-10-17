@extends('web::layouts.grids.6-6')

@section('title', trans('billing::billing.settings'))
@section('page_header', trans('billing::billing.settings'))

@section('left')
<div class="box box-success box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">{{ trans('billing::billing.settings') }}</h3>
    </div>
    <form method="POST" action="{{ route('billing.savesettings')  }}" class="form-horizontal">
        <div class="box-body">
            {{ csrf_field() }}
            <h4>{{ trans('billing::billing.default-settings') }}</h4>
            <div class="form-group">
                <label for="oremodifier" class="col-sm-3 control-label">{{ trans('billing::billing.Ore value modifier') }}</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="oremodifier" id="oremodifier" size="4" value="{{ setting('oremodifier', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="oretaxrate" class="col-sm-3 control-label">{{ trans('billing::billing.Ore TAX Rate') }}</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="oretaxrate" id="oretaxrate" size="4" value="{{ setting('oretaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="ioretaxrate" class="col-sm-3 control-label">{{ trans('billing::billing.Ore Refining Rate') }}</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="refinerate" id="refinerate" size="4" value="{{ setting('refinerate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="bountytaxrate" class="col-sm-3 control-label">{{ trans('billing::billing.Bounty TAX Rate') }}</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="bountytaxrate" id="bountytaxrate" size="4" value="{{ setting('bountytaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <hr />
            <h4>{{ trans('billing::billing.incentivized-settings') }}</h4>
            <div class="form-group">
                <label for="ioremodifier" class="col-sm-3 control-label">{{ trans('billing::billing.Ore value modifier') }}</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="ioremodifier" id="ioremodifier" size="4" value="{{ setting('ioremodifier', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="ioretaxrate" class="col-sm-3 control-label">{{ trans('billing::billing.Ore TAX Rate') }}</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="ioretaxrate" id="ioretaxrate" size="4" value="{{ setting('ioretaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="ibountytaxrate" class="col-sm-3 control-label">{{ trans('billing::billing.Bounty TAX Rate') }} </label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="ibountytaxrate" id="ibountytaxrate" size="4" value="{{ setting('ibountytaxrate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="irate" class="col-sm-3 control-label">{{ trans('billing::billing.Rates Threshold') }}</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm-3">
                        <input class="form-control" type="text" name="irate" id="irate" size="4" value="{{ setting('irate', true) }}" />
                        <div class="input-group-addon">%</div>
                    </div>
                    <p class="help-block">{{ trans('billing::billing.Rates Threshold Desc') }}</p>
                </div>
            </div>
            <hr />
            <h4>{{ trans('billing::billing.valuation-of-ore') }}</h4>
            <div class="form-group">
                <label for="ibountytaxrate" class="col-sm-3 control-label">{{ trans('billing::billing.Value at Ore Price') }}</label>
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
                <label for="ibountytaxrate" class="col-sm-3 control-label">{{ trans('billing::billing.Value at Mineral Price') }}</label>
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
            <input class="btn btn-success pull-right" type="submit" value="Update">
        </div>
    </form>
</div>
@endsection

@section('right')
<div class="box box-success box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">{{ trans('billing::billing.settings-instructions') }}</h3>
    </div>
    <div class="box-body">
        <div class="col-sm-12">
            <p><label>{{ trans('billing::billing.Ore value modifier') }}:</label> {{ trans('billing::billing.Ore value modifier Desc') }} </p>
        </div>
        <div class="col-sm-12">
            <p><label>{{ trans('billing::billing.Ore TAX Rate') }}:</label> {{ trans('billing::billing.Ore TAX Rate Desc') }} </p>
        </div>
        <div class="col-sm-12">
            <p><label>{{ trans('billing::billing.Ore Refining Rate') }}:</label> {{ trans('billing::billing.Ore Refining Rate Dedc') }} </p>
        </div>
        <div class="col-sm-12">
            <p><label>{{ trans('billing::billing.Bounty TAX Rate') }}:</label> {{ trans('billing::billing.Bounty TAX Rate Desc') }} </p>
        </div>
        <div class="col-sm-12">
            <p><label>{{ trans('billing::billing.Incentivised Rates') }}:</label> {{ trans('billing::billing.Incentivised Rates Desc') }}</p>
        </div>
        <div class="col-sm-12">
            <p><label>{{ trans('billing::billing.Valuation of Ore') }}:</label> {{ trans('billing::billing.Valuation of Ore Desc') }}</p>
        </div>
    </div>

</div>
@endsection
