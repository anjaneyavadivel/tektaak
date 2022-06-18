<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\PurchaseHistoryMiniCollection;
use App\Http\Resources\V2\PurchaseHistoryCollection;
use App\Http\Resources\V2\PurchaseHistoryItemsCollection;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PurchaseHistoryController extends Controller
{
    public function index(Request $request)
    {
        $order_query = Order::query();
        if ($request->payment_status != "" || $request->payment_status != null) {
            $order_query->where('payment_status', $request->payment_status);
        }
        if ($request->delivery_status != "" || $request->delivery_status != null) {
            $delivery_status = $request->delivery_status;
            $order_query->whereIn("id", function ($query) use ($delivery_status) {
                $query->select('order_id')
                    ->from('order_details')
                    ->where('delivery_status', $delivery_status);
            });
        }
        return new PurchaseHistoryMiniCollection($order_query->where('user_id', auth()->user()->id)->latest()->paginate(5));

    }

    public function details($id)
    {
        $order_detail = Order::where('id', $id)->where('user_id', auth()->user()->id)->get();
        // $order_query = auth()->user()->orders->where('id', $id);
        
        // return new PurchaseHistoryCollection($order_query->get());
        return new PurchaseHistoryCollection($order_detail);
    }

    public function items($id)
    {
        $order_id = Order::select('id')->where('id', $id)->where('user_id', auth()->user()->id)->first();
        $order_query = OrderDetail::with('product')->where('order_id', $order_id->id);
        return new PurchaseHistoryItemsCollection($order_query->get());
    }
    
    public function order_cancel(Request $request,$id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->user()->id)->first();
        if($order && ($order->delivery_status == 'pending' && $order->payment_status == 'unpaid')) {
            $order->delivery_status = 'cancelled';
            $order->cancel_notes = $request->cancel_notes??'';
            $order->save();

            return response()->json([
                'result' => true,
                'message' => translate('Order has been canceled successfully')
            ]);
        } else {            
            return response()->json([
                'result' => false,
                'message' => translate('Sorry! Your order inprogress, Please contact seller')
            ]);
        }

    }
}
