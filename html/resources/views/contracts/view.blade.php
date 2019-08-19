@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('admin/contracts/general.view') }} {{ $item->name }}
@parent
@stop
{{-- Right header --}}
@section('header_right')

@can('manage', \App\Models\Contract::class)
<div class="dropdown pull-right">
  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">{{ trans('button.actions') }}
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
      @can('update', \App\Models\Contract::class)
    <li role="presentation"><a href="{{ route('contracts.edit', $item->id) }}">{{ trans('admin/contracts/general.edit') }}</a></li>
      @endcan
  </ul>
</div>
@endcan
@stop


{{-- Page content --}}
@section('content')

<div class="row">
        <div class="col-md-12">
             <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#details" data-toggle="tab"><span class="hidden-lg hidden-md"><i class="fa fa-info-circle"></i></span> <span class="hidden-xs hidden-sm">{{ trans('general.details') }}</span></a>
                    </li>
                    <li>
                        <a href="#history_contract" data-toggle="tab"><span class="hidden-lg hidden-md"><i class="fa fa-history"></i></span> <span class="hidden-xs hidden-sm">{{ trans('general.history') }}</span></a>
                    </li>
                </ul>
                <!-- Tab-content -->
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="details">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="table-responsive" style="margin-top: 10px;">
                                    <table class="table">
                                            <tbody>
                                                @if ($item->name)
                                                <tr>
                                                    <td>{{ trans('admin/hardware/form.name') }}</td>
                                                    <td>{{ $item->name }}</td>
                                                </tr>
                                                @endif
                      
                                                @if ($item->company) 
                                                    <tr>
                                                        <td>{{ trans('general.company') }}</td>
                                                        <td><a href="{{ url('/companies/' . $item->company->id) }}">{{ $item->company->name }}</a></td>
                                                    </tr>
                                                @endif

                                                @if ($item->location)
                                                    <tr>
                                                    <td>{{ trans('general.location') }}</td>
                                                    <td>
                                                        @can('superuser')
                                                        <a href="{{ route('locations.show', ['location' => $item->location->id]) }}">
                                                            {{ $item->location->name }}
                                                        </a>
                                                        @else
                                                        {{ $item->location->name }}
                                                        @endcan
                                                    </td>
                                                    </tr>
                                                @endif

                                                @if ($item->contact_id_1)
                                                    <tr>
                                                    <td>{{ trans('general.user') }}</td>
                                                    <td>
                                                        @can('superuser')
                                                        <a href="{{ route('users.show', ['user' => $item->user->id]) }}">
                                                            {{ $item->user->first_name . ' ' . $item->user->last_name}}
                                                        </a>
                                                        @else
                                                        {{ $item->user->first_name . ' ' . $item->user->last_name }}
                                                        @endcan
                                                    </td>
                                                    </tr>
                                                @endif
                                                
                                                @if ($item->contact_id_2)
                                                    <tr>
                                                    <td>{{ trans('general.user') }}</td>
                                                    <td>
                                                        @can('superuser')
                                                            <a href="{{ route('users.show', ['user' => $item->user2->id]) }}">
                                                                {{ $item->user2->first_name . ' ' . $item->user2->last_name}}
                                                            </a>
                                                        @else
                                                            {{ $item->user2->first_name . ' ' . $item->user2->last_name }}
                                                        @endcan
                                                    </td>
                                                    </tr>
                                                @endif

                                                @if ($item->start_date!='')
                                                    <tr>
                                                        <td>{{ trans('admin/contracts/table.start_date') }}</td>
                                                        <td>
                                                            {{$item->start_date }}
                                                        </td>
                                                    </tr>
                                                @endif

                                                @if ($item->end_date!='')
                                                    <tr>
                                                        <td>{{ trans('admin/contracts/table.end_date') }}</td>
                                                        <td>
                                                            {{ $item->end_date }}
                                                        </td>
                                                    </tr>
                                                @endif

                                                @if ($item->billing_date!='')
                                                    <tr>
                                                        <td>{{ trans('admin/contracts/table.billing_date') }}</td>
                                                        <td>
                                                            {{ $item->billing_date }}
                                                        </td>
                                                    </tr>
                                                @endif

                                                @if ($item->payment_date!='')
                                                    <tr>
                                                        <td>{{ trans('admin/contracts/table.payment_date') }}</td>
                                                        <td>
                                                            {{ $item->payment_date }}
                                                        </td>
                                                    </tr>
                                                @endif
                      
                                                @if ($item->created_at!='')
                                                    <tr>
                                                        <td>{{ trans('general.created_at') }}</td>
                                                        <td>
                                                            {{ $item->created_at}}
                                                        </td>
                                                    </tr>
                                                @endif
                      
                                                @if ($item->updated_at!='')
                                                    <tr>
                                                        <td>{{ trans('general.updated_at') }}</td>
                                                        <td>
                                                            {{ \App\Helpers\Helper::getFormattedDateObject($item->updated_at, 'datetime', false) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="history_contract">
                        <div class="row">
                            <div class="col-md-12">
                              
                                <table
                                    class="table table-striped snipe-table"
                                    id="contractHistory"
                                    data-pagination="true"
                                    data-id-table="contractHistory"
                                    data-search="true"
                                    data-side-pagination="server"
                                    data-show-columns="true"
                                    data-show-refresh="true"
                                    data-sort-order="desc"
                                    data-sort-name="created_at"
                                    data-show-export="true"
                                    data-export-options='{
                                        "fileName": "export-contracts-{{  $item->id }}-history",
                                        "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                                    }'
                                    data-url="{{ route('api.activity.index', ['item_id' => $item->id]) }}"
                                    data-cookie-id-table="contractHistory">
                                    <thead>
                                        <tr>
                                            <th data-field="icon" data-visible="true" style="width: 40px;" class="hidden-xs" data-formatter="iconFormatter"></th>
                                            <th class="col-sm-2" data-visible="true" data-field="created_at" data-formatter="dateDisplayFormatter">{{ trans('general.date') }}</th>
                                            <th class="col-sm-1" data-visible="true" data-field="admin" data-formatter="usersLinkObjFormatter">{{ trans('general.admin') }}</th>
                                            <th class="col-sm-1" data-visible="true" data-field="action_type">{{ trans('general.action') }}</th>
                                            <th class="col-sm-1" data-field="type" data-formatter="itemTypeFormatter">{{ trans('general.type') }}</th>
                                            <th class="col-sm-2" data-visible="true" data-field="item" data-formatter="polymorphicItemFormatter">{{ trans('general.item') }}</th>
                                            <th class="col-sm-2" data-visible="true" data-field="target" data-formatter="polymorphicItemFormatter">{{ trans('general.target') }}</th>
                                            <th class="col-sm-2" data-field="note">{{ trans('general.notes') }}</th>
                                            @if  ($snipeSettings->require_accept_signature=='1')
                                            <th class="col-md-3" data-field="signature_file" data-visible="false"  data-formatter="imageFormatter">{{ trans('general.signature') }}</th>
                                            @endif
                                            <th class="col-md-3" data-visible="false" data-field="file" data-visible="false"  data-formatter="fileUploadFormatter">{{ trans('general.download') }}</th>
                                            <th class="col-sm-2" data-field="log_meta" data-visible="true" data-formatter="changeLogFormatter">Changed</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tab-content -->
            </div>
            <!-- Custom Tabs -->
        </div>
</div>

@stop

@section('moar_scripts')
  @include ('partials.bootstrap-table')
@stop