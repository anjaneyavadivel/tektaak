<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\BusinessSetting;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\WalletController;
use App\Models\CombinedOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Redirect;

class FoloosiController extends Controller
{
    public function init(Request $request)
    {
        $payment_type = $request->payment_type;
        $combined_order_id = $request->combined_order_id;
        $amount = $request->amount;
        $user_id = $request->user_id;
        $user = User::find($user_id);

        if ($payment_type == 'cart_payment') {
            $combined_order = CombinedOrder::find($combined_order_id);
            $shipping_address = json_decode($combined_order->shipping_address,true);
            return view('frontend.payment.foloosi_order_payment', compact('user','combined_order', 'shipping_address'));
        } elseif ($payment_type == 'wallet_payment') {

            return view('frontend.payment.foloosi_wallet_payment',  compact('user', 'amount'));
        }
    }
    
    public function success(Request $request)
    {
        try {

            $payment_type = $request->payment_type;

            if ($payment_type == 'cart_payment') {

                checkout_done($request->combined_order_id, $request->payment_details);
            }

            if ($payment_type == 'wallet_payment') {

                wallet_payment_done($request->user_id, $request->amount, 'foloosi', $request->payment_details);
            }

            return response()->json(['result' => true, 'message' => translate("Payment is successful")]);


        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()]);
        }
    }

}
