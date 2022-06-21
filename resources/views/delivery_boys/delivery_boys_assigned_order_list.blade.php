@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All Assigned Order for Delivery Boys')}}</h1>
        </div>
    </div>
</div>

<div class="card">
    <form id="sort_orders" action="" method="GET">
      <div class="card-header row gutters-5">
        <div class="col text-center text-md-left">
          <h5 class="mb-md-0 h6">{{ translate('Assigned Order for Delivery Boys') }}</h5>
        </div>
          <div class="col-md-3 ml-auto">
              <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Filter by Payment Status')}}" name="payment_status" onchange="sort_orders()">
                  <option value="">{{ translate('Filter by Payment Status')}}</option>
                  <option value="paid" @isset($payment_status) @if($payment_status == 'paid') selected @endif @endisset>{{ translate('Paid')}}</option>
                  <option value="unpaid" @isset($payment_status) @if($payment_status == 'unpaid') selected @endif @endisset>{{ translate('Un-Paid')}}</option>
              </select>
          </div>

          <div class="col-md-3 ml-auto">
            <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Filter by Payment Status')}}" name="delivery_status" onchange="sort_orders()">
                <option value="">{{ translate('Filter by Deliver Status')}}</option>
                <option value="pending" @isset($delivery_status) @if($delivery_status == 'pending') selected @endif @endisset>{{ translate('Pending')}}</option>
                <option value="confirmed" @isset($delivery_status) @if($delivery_status == 'confirmed') selected @endif @endisset>{{ translate('Confirmed')}}</option>
                <option value="on_delivery" @isset($delivery_status) @if($delivery_status == 'on_delivery') selected @endif @endisset>{{ translate('On delivery')}}</option>
                <option value="delivered" @isset($delivery_status) @if($delivery_status == 'delivered') selected @endif @endisset>{{ translate('Delivered')}}</option>
            </select>
          </div>
          <div class="col-md-3">
            <div class="from-group mb-0">
                <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code, Delivery boy name & hit Enter') }}">
            </div>
          </div>
      </div>
    </form>

    @if (count($orders) > 0)
        <div class="card-body p-3">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Order Code')}}</th>
                        <th data-breakpoints="lg">{{ translate('Delivery Boy')}}</th>
                        <th data-breakpoints="lg">{{ translate('Num. of Products')}}</th>
                        <th data-breakpoints="lg">{{ translate('Customer')}}</th>
                        <th data-breakpoints="md">{{ translate('Amount')}}</th>
                        <th data-breakpoints="lg">{{ translate('Delivery Status')}}</th>
                        <th>{{ translate('Payment Status')}}</th>
                        <th class="text-right">{{ translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $key => $order_id)
                        @php
                            $order = \App\Models\Order::find($order_id->id);
                            $delivery_boys = \App\Models\User::find($order->assign_delivery_boy);
                        @endphp
                        @if($order != null)
                            <tr>
                                <td>
                                    {{ $key+1 }}
                                </td>
                                <td>
                                    <a href="#{{ $order->code }}" onclick="show_order_details({{ $order->id }})">{{ $order->code }}</a>
                                </td>
                                <td>
                                
                                @if ($order->assign_delivery_boy != null)
                                    {{ optional($delivery_boys)->name }}
                                @endif
                            </td>
                                <td>
                                    {{ count($order->orderDetails->where('seller_id', Auth::user()->id)) }}
                                </td>
                                <td>
                                    @if ($order->user_id != null)
                                        {{ optional($order->user)->name }}
                                    @else
                                        {{ translate('Guest') }} ({{ $order->guest_id }})
                                    @endif
                                </td>
                                <td>
                                    {{ single_price($order->grand_total) }}
                                </td>
                                <td>
                                    @php
                                        $status = $order->delivery_status;
                                    @endphp
                                    {{ translate(ucfirst(str_replace('_', ' ', $status))) }}
                                </td>
                                <td>
                                    @if ($order->payment_status == 'paid')
                                        <span class="badge badge-inline badge-success">{{ translate('Paid')}}</span>
                                    @else
                                        <span class="badge badge-inline badge-danger">{{ translate('Unpaid')}}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('all_orders.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $orders->links() }}
              </div>
        </div>
    @endif
</div>

@endsection

@section('script')
<script type="text/javascript">
    function sort_orders(el){
        $('#sort_orders').submit();
    }
</script>
@endsection
