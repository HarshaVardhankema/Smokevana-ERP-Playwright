<!-- Commission Settings & Bonus Configuration -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-gift"></i> Bonus Configuration
                </h3>
            </div>
            <div class="box-body">
                <form id="commission_settings_form">
                    @csrf
                    <div class="form-group">
                        <label for="cmmsn_percent">Commission Percentage</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="cmmsn_percent" name="cmmsn_percent"
                                value="{{ $user->cmmsn_percent }}" min="0" max="100" step="0.01">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update Settings
                    </button>
                </form>
                <br>
                <form id="bonus_settings_form">
                    @csrf

                    <!-- Quarterly Bonus -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Quarterly Bonus</label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" id="quarterly_bonus_amount"
                                        name="quarterly_bonus_amount" value="{{ number_format($user->quarterly_bonus_amount ?? 0, 2, '.', '') }}"
                                        min="0" step="0.01" placeholder="Bonus Amount">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Quarterly Sales Target</label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" id="quarterly_sales_target"
                                        name="quarterly_sales_target" value="{{ number_format($user->quarterly_sales_target ?? 0, 2, '.', '') }}"
                                        min="0" step="0.01" placeholder="Sales Target">
                                </div>
                            </div>
                        </div>
                        <small class="help-block">Bonus paid when quarterly sales target is met</small>
                    </div>

                    <!-- Yearly Bonus -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Yearly Bonus</label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" id="yearly_bonus_amount"
                                        name="yearly_bonus_amount" value="{{ number_format($user->yearly_bonus_amount ?? 0, 2, '.', '') }}" min="0"
                                        step="0.01" placeholder="Bonus Amount">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Yearly Sales Target</label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" id="yearly_sales_target"
                                        name="yearly_sales_target" value="{{ number_format($user->yearly_sales_target ?? 0, 2, '.', '') }}" min="0"
                                        step="0.01" placeholder="Sales Target">
                                </div>
                            </div>
                        </div>
                        <small class="help-block">Bonus paid when yearly sales target is met</small>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-save"></i> Update Bonus Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>