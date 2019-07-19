@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')

@if(!is_payment_method_exist())
<div class="container">
	<a href="{{ route('admin.payments.setup') }}" class="btn btn-danger btn-between w-100 mgb-1-5x user-wallet">Important: Please setup at least one payment method to active your sale.<em class="ti ti-arrow-right"></em></a>
	<div class="gaps-1x mgb-0-5x d-lg-none d-none d-sm-block"></div>
</div>
@endif

@if(!is_mail_setting_exist())
<div class="container">
	<a href="{{ route('admin.settings.email').'?setup=mailSetting' }}" class="btn btn-warning-alt btn-between w-100 mgb-1-5x user-wallet">Please setup your application mail settings<em class="ti ti-arrow-right"></em></a>
	<div class="gaps-1x mgb-0-5x d-lg-none d-none d-sm-block"></div>
</div>
@endif

<div class="page-content">
	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-md-6">
                <div class="card height-auto">
                    <div class="card-innr">
                        <div class="tile-header">
                            <h6 class="tile-title">Token Sale - {{ $stage->stage->name }}</h6>
                        </div>
                        <div class="tile-data">
                            <span class="tile-data-number">{{ number_format($stage->stage->total_tokens) }}</span>
                            <span class="tile-data-status tile-data-active" title="Sales %" data-toggle="tooltip" data-placement="right">{{ $stage->trnxs->percent }}%</span>
                        </div>
                        <div class="tile-footer">
                            <div class="tile-recent">
                                <span class="tile-recent-number">{{ $stage->trnxs->last_week }}</span>
                                <span class="tile-recent-text">since last week</span>
                            </div>
                            <div class="tile-link">
                                <a href="{{ route('admin.stages') }}" class="link link-thin link-ucap link-dim">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card height-auto">
                    <div class="card-innr">
                        <ul class="tile-nav nav">
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#view-kycs">KYC</a></li>
                        	<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#view-users">User</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="view-users">
                                <div class="tile-header">
                                    <h6 class="tile-title">Total Users</h6>
                                </div>
                                <div class="tile-data">
                                    <span class="tile-data-number">{{ number_format($users->all) }}</span>
                                    <span class="tile-data-status tile-data-active" title="Verified" data-toggle="tooltip" data-placement="right">{{ $users->unverified }}%</span>
                                </div>
                                <div class="tile-footer">
                                    <div class="tile-recent">
                                        <span class="tile-recent-number">{{ $users->last_week }}</span>
                                        <span class="tile-recent-text">since last week</span>
                                    </div>
                                    <div class="tile-link">
                                        <a href="{{ route('admin.users') }}" class="link link-thin link-ucap link-dim">View</a>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="view-kycs">
                                <div class="tile-header">
                                    <h6 class="tile-title">Total KYC</h6>
                                </div>
                                <div class="tile-data">
                                    <span class="tile-data-number">{{ number_format($users->kyc_submit) }}</span>
                                    <span class="tile-data-status tile-data-active" title="Submitted" data-toggle="tooltip" data-placement="right">{{ $users->kyc_missing }}%</span>
                                </div>
                                <div class="tile-footer">
                                    <div class="tile-recent">
                                        <span class="tile-recent-number">{{ $users->kyc_last_week }}</span>
                                        <span class="tile-recent-text">since last week</span>
                                    </div>
                                    <div class="tile-link">
                                        <a href="{{ route('admin.kycs') }}" class="link link-thin link-ucap link-dim">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="token-statistics card card-token height-auto">
                    <div class="card-innr">
                        <div class="token-balance token-balance-s3">
                            <div class="token-balance-text">
                                <h6 class="card-sub-title">AMOUNT COLLECTED</h6>
                                <span class="lead">{{ $trnxs->currency->usd }} <span>USD</span></span>
                            </div>
                        </div>
                        <div class="token-balance token-balance-s2">
                            <ul class="token-balance-list">
                                <li class="token-balance-sub">
                                    <span class="lead">{{ $trnxs->currency->eth }}</span>
                                    <span class="sub">ETH</span>
                                </li>
                                <li class="token-balance-sub">
                                    <span class="lead">{{ $trnxs->currency->btc }}</span>
                                    <span class="sub">BTC</span>
                                </li>
                                <li class="token-balance-sub">
                                    <span class="lead">{{ $trnxs->currency->ltc }}</span>
                                    <span class="sub">LTC</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="token-transaction card card-full-height">
                    <div class="card-innr">
                        <div class="card-head has-aside">
                            <h4 class="card-title card-title-sm">Recent Transaction</h4>
                            <div class="card-opt">
                                <a href="{{ route('admin.transactions') }}" class="link ucap">View ALL <em class="fas fa-angle-right ml-2"></em></a>
                            </div>
                        </div>
                        <table class="table tnx-table">
                            <tbody>
                            	@forelse($trnxs->all as $tnx)
                                <tr>
                                    <td>
                                        <h5 class="lead mb-1">{{ $tnx->tnx_id}}</h5>
                                        <span class="sub">{{ _date($tnx->tnx_time) }}</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <h5 class="lead mb-1">+{{ number_format($tnx->total_tokens) }}</h5>
                                        <span class="sub ucap">{{ number_format($tnx->amount, 3).' '.$tnx->currency }}</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="data-state data-state-{{ __status($tnx->status, 'icon') }}"></div>
                                    </td>
                                </tr>
                                @empty
								<tr class="data-item text-center">
									<td class="data-col" colspan="4">No available transaction!</td>
								</tr>
								@endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="token-sale-graph card card-full-height">
                    <div class="card-innr">
                        <div class="card-head has-aside">
                            <h4 class="card-title card-title-sm">Token Sale Graph</h4>
                            <div class="card-opt">
                                <a href="{{ url()->current() }}" class="link ucap link-light toggle-tigger toggle-caret">{{ isset($_GET['chart']) ? $_GET['chart'] : 7 }} Days</a>
								<div class="toggle-class dropdown-content">
									<ul class="dropdown-list">
										<li><a href="{{ url()->current() }}?chart=7">7 Days</a></li>
										<li><a href="{{ url()->current() }}?chart=15">15 Days</a></li>
										<li><a href="{{ url()->current() }}?chart=30">30 Days</a></li>
									</ul>
								</div>
                            </div>
                        </div>
                        <div class="chart-tokensale chart-tokensale-long">
                            <canvas id="tknSale"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="reg-statistic-graph card card-full-height">
                    <div class="card-innr">
                        <div class="card-head has-aside">
                            <h4 class="card-title card-title-sm">Registration Statistics</h4>
                            <div class="card-opt">
                                <a href="{{ url()->current() }}" class="link ucap link-light toggle-tigger toggle-caret">{{ isset($_GET['user']) ? $_GET['user'] : 15 }} Days</a>
                                <div class="toggle-class dropdown-content">
                                    <ul class="dropdown-list">
                                        <li><a href="{{ url()->current() }}?user=7">7 Days</a></li>
                            			<li><a href="{{ url()->current() }}?user=15">15 Days</a></li>
                            			<li><a href="{{ url()->current() }}?user=30">30 Days</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="chart-statistics mb-0">
                            <canvas id="regStatistics"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card card-full-height">
                    <div class="card-innr">
                        <div class="phase-block guttar-20px">
                            <div class="fake-class">
                                <div class="card-head has-aside">
                                    <h4 class="card-title card-title-sm">Stage - {{ $stage->stage->name }}</h4>
                                </div>
                                <ul class="phase-status">
                                    <li>
                                        <div class="phase-status-title">Total</div>
                                        <div class="phase-status-subtitle">{{ number_format($stage->stage->total_tokens) }}</div>
                                    </li>
                                    <li>
                                        <div class="phase-status-title">Sold</div>
                                        <div class="phase-status-subtitle">{{ number_format($stage->stage->sales_token) }} <span>*</span></div>
                                    </li>
                                    <li>
                                        <div class="phase-status-title">Sale %</div>
                                        <div class="phase-status-subtitle">{{ $stage->trnxs->percent }}% Sold</div>
                                    </li>
                                    <li>
                                        <div class="phase-status-title">Unsold</div>
                                        <div class="phase-status-subtitle">{{ number_format($stage->stage->total_tokens - $stage->stage->sales_token) }}</div>
                                    </li>
                                </ul>
                                <div class="notes">* Pending sales token included.</div>
                            </div>
                            <div class="fake-class">
                                <div class="chart-phase">
                                    <div class="phase-status-total">
                                        <span class="lead">{{ number_format($stage->stage->total_tokens) }}</span>
                                        <span class="sub">{{ token_symbol() }}</span>
                                    </div>
                                    <div class="chart-tokensale-s2">
                                        <canvas id="phaseStatus"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>{{-- .card --}}
            </div>
		</div>{{-- .row --}}
	</div>{{-- .container --}}
</div>{{-- .page-content --}}

@endsection

@push('footer')
<script type="text/javascript">
	var tnx_labels = [<?=$trnxs->chart->days?>],
	tnx_data = [<?=$trnxs->chart->data?>],
	user_labels = [<?=$users->chart->days?>],
	user_data = [<?=$users->chart->data?>],
	phase_data = [{{ $stage->stage->sales_token }}, {{ ($stage->stage->total_tokens - $stage->stage->sales_token ) }}];
</script>


<script src="{{ asset('assets/js/admin.chart.js') }}"></script>
@endpush