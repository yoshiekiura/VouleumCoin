@extends('layouts.admin')
@section('title', 'Kycs List')


@section('content')

<div class="page-content">
    <div class="container">
        <div class="card content-area content-area-mh">
            <div class="card-innr">
                <div class="card-head has-aside">
                    <h4 class="card-title">KYC List</h4>
                    <div class="card-opt">
                        <ul class="btn-grp btn-grp-block guttar-20px">
                            <li>
                                <a href="javascript:void(0)" data-type="kyc_settings" class="btn btn-auto btn-sm btn-primary get_kyc">
                                    <em class="ti ti-settings"></em><span>KYC <span class="d-none d-md-inline-block">Form</span> Settings</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="gaps-1x"></div>
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="float-right position-relative">
                            <a href="#" class="btn btn-light-alt dt-filter-text btn-xs btn-icon toggle-tigger"> <em class="ti ti-settings"></em> </a>
                            <div class="toggle-class toggle-datatable-filter dropdown-dt-filter-text dropdown-content dropdown-content-top-left text-left">
                                <ul class="pdt-1x pdb-1x">
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" id="all" checked value="">
                                        <label for="all">All</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" id="approved" value="approved">
                                        <label for="approved">Approved</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" value="missing" id="missing">
                                        <label for="missing">Missing</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" value="pending" id="pending">
                                        <label for="pending">Progress</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" value="rejected" id="rejected">
                                        <label for="rejected">Rejected</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="data-table dt-filter-init kyc-list">
                    <thead>
                        <tr class="data-item data-head">
                            <th class="data-col filter-data dt-user">User</th>
                            <th class="data-col dt-doc-type">Doc Type</th>
                            <th class="data-col dt-doc-front">Documents</th>
                            <th class="data-col dt-doc-back">&nbsp;</th>
                            <th class="data-col dt-doc-proof">&nbsp;</th>
                            <th class="data-col dt-sbdate">Submitted</th>
                            <th class="data-col dt-status">Status</th>
                            <th class="data-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kycs as $kyc)
                        <tr class="data-item data-item-{{ $kyc->id }}">
                            <td class="data-col dt-user">
                                <span class="d-none">{{ $kyc->status }}</span>
                                <span class="lead user-name">{{ $kyc->firstName.' '.$kyc->lastName }}</span>
                                <span class="sub user-id">UD{{ set_id($kyc->userId) }}</span>
                            </td>
                            <td class="data-col dt-doc-type">
                                <span class="sub sub-s2 sub-dtype">{{ ucfirst($kyc->documentType) }}</span>
                            </td>
                            
                            <td class="data-col dt-docs dt-doc-front">
                                @if($kyc->document != NULL)
                                    @if(pathinfo(storage_path('app/'.$kyc->document), PATHINFO_EXTENSION) != 'pdf')
                                        <a href="{{ route('admin.kycs.file', ['file'=>$kyc->id, 'doc'=>1]) }}" class="image-popup">{{ ($kyc->documentType == 'nidcard') ? 'Front Side' : 'Document' }}</a>
                                    @else 
                                        {{ ($kyc->documentType == 'nidcard') ? 'Front Side' : 'Document' }}
                                    @endif
                                    &nbsp; <a title="Download" href="{{ route('admin.kycs.file', ['file'=>$kyc->id, 'doc'=>1]) }}" target="_blank"><em class="fas fa-download"></em></a>
                                @else 
                                &nbsp;
                                @endif
                            </td>
                            <td class="data-col dt-docs dt-doc-back">
                                @if($kyc->document2 != NULL)
                                    @if(pathinfo(storage_path('app/'.$kyc->document2), PATHINFO_EXTENSION) != 'pdf')
                                        <a href="{{ route('admin.kycs.file', ['file'=>$kyc->id, 'doc'=>2]) }}" class="image-popup">{{ ($kyc->documentType == 'nidcard') ? 'Back Side' : 'Proof' }}</a>
                                    @else 
                                        {{ ($kyc->documentType == 'nidcard') ? 'Back Side' : 'Proof' }}
                                    @endif
                                    &nbsp; <a title="Download" href="{{ route('admin.kycs.file', ['file'=>$kyc->id, 'doc'=>2]) }}" target="_blank"><em class="fas fa-download"></em></a>
                                @else 
                                &nbsp;
                                @endif
                            </td>
                            <td class="data-col dt-docs dt-doc-proof">
                                @if($kyc->document3 != NULL)
                                    @if(pathinfo(storage_path('app/'.$kyc->document3), PATHINFO_EXTENSION) != 'pdf')
                                        <a href="{{ route('admin.kycs.file', ['file'=>$kyc->id, 'doc'=>3]) }}" class="image-popup">Proof</a>
                                    @else 
                                        Proof
                                    @endif
                                    &nbsp; <a title="Download" href="{{ route('admin.kycs.file', ['file'=>$kyc->id, 'doc'=>3]) }}" target="_blank"><em class="fas fa-download"></em></a>
                                @else 
                                &nbsp;
                                @endif
                            </td>
                            <td class="data-col dt-sbdate">
                                <span class="sub sub-s2 sub-time">{{ _date($kyc->created_at) }}</span>
                            </td>
                            <td class="data-col dt-status">
                                <span class="dt-status-md badge badge-outline badge-md badge-{{ __status($kyc->status,'status') }}">{{ __status($kyc->status,'text') }}</span>
                                <span class="dt-status-sm badge badge-sq badge-outline badge-md badge-{{ __status($kyc->status,'status') }}">{{ substr(__status($kyc->status,'text'), 0, 1) }}</span>
                            </td>
                            <td class="data-col text-right">
                                <div class="relative d-inline-block">
                                    <a href="#" class="btn btn-light-alt btn-xs btn-icon toggle-tigger"><em class="ti ti-more-alt"></em></a>
                                    <div class="toggle-class dropdown-content dropdown-content-top-left">
                                        <ul class="dropdown-list more-menu more-menu-{{$kyc->id}}">
                                            <li><a href="{{route('admin.kyc.view', [$kyc->id, 'kyc_details' ])}}"><em class="ti ti-eye"></em> View Details</a></li>
                                            @if($kyc->status != 'approved')
                                            <li><a class="kyc_action kyc_approve" href="#" data-id="{{ $kyc->id }}" data-toggle="modal" data-target="#actionkyc"><em class="far fa-check-square"></em>Approve</a></li>
                                            @endif
                                            @if($kyc->status != 'rejected')
                                            <li><a href="javascript:void(0)" data-current="{{ __status($kyc->status,'status') }}" data-id="{{ $kyc->id }}" class="kyc_reject"><em class="fas fa-ban"></em>Reject</a></li>
                                            @endif
                                            @if($kyc->status == 'missing' || $kyc->status == 'rejected')
                                            <li><a href="javascript:void(0)" data-id="{{ $kyc->id }}" class="kyc_delete"><em class="fas fa-trash-alt"></em>Delete</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>{{-- .card-innr --}}
        </div>{{-- .card --}}
    </div>{{-- .container --}}
</div>{{-- .page-content --}}

@endsection

@section('modals')

<div class="modal fade" id="actionkyc" tabindex="-1">
    <div class="modal-dialog modal-dialog-md modal-dialog-centered">
        <div class="modal-content">
            <a href="#" class="modal-close" data-dismiss="modal"><em class="ti ti-close"></em></a>
            <div class="popup-body popup-body-md">
                <h3 class="popup-title">Approve the KYC Information</h3>
                <p>Please check details carefully of the application before take any action. User can not re-submit the application if you invalidated this application.</p>
                <form action="{{ route('admin.ajax.kyc.update') }}" method="POST" id="kyc_status_form">
                    @csrf
                    <input type="hidden" name="req_type" value="update_kyc_status">
                    <input type="hidden" name="kyc_id" id="kyc_id" required="required">
                    <div class="input-item input-with-label">
                        <label class="input-item-label">Admin Note</label>
                        <textarea name="notes" class="input-bordered input-textarea input-textarea-sm"></textarea>
                    </div>
                    <div class="input-item">
                        <input class="input-checkbox" id="send-email" checked type="checkbox">
                        <label for="send-email">Send Notification to Applicant</label>
                    </div>
                    <div class="gaps-1x"></div>
                    <ul class="btn-grp guttar-20px">
                        <li><button name="status" data-value="approved" class="update_kyc form-progress-btn btn btn-md btn-primary ucap">Approve</button></li>
                        <li><button name="status" data-value="missing" class="update_kyc form-progress-btn btn btn-md btn-light ucap">Missing</button></li>
                        <li><button name="status" data-value="rejected" class="update_kyc form-progress-btn btn btn-md btn-danger ucap">Reject</button></li>
                    </ul>
                </form>
            </div>
        </div>{{-- .modal-content --}}
    </div>{{-- .modal-dialog --}}
</div>
{{-- Modal End --}}
@endsection
