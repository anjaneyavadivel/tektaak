<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Session;
use Auth;

class FoloosiController extends Controller
{
   
    public function pay()
    {
        if (Session::get('payment_type') == 'cart_payment') {
            return view('frontend.payment.foloosi_cart');
        }
        elseif (Session::get('payment_type') == 'wallet_payment') {
            return view('frontend.payment.foloosi_wallet');
        }
        elseif (Session::get('payment_type') == 'customer_package_payment') {
            return view('frontend.payment.foloosi_customer_package');
        }
       
    }
    
    public function paymentSuccess($id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://foloosi.com/api/v1/api/transaction-detail/'.$id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_POSTFIELDS => '',
          CURLOPT_HTTPHEADER => array(
            'secret_key: '.env('FOLOOSI_SECRET'),
            'Content-Type: application/x-www-form-urlencoded'
          ),
        ));
        
        $response = curl_exec($curl);
        
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                flash(translate('Payment Failed'))->error();
                return redirect()->route('home');
            } else {
                $obj = json_decode($response,true);
                
            }
            
        if($obj['data']['status'] == 'success'){
            $payment_detalis = json_encode($obj);
            // dd($payment_detalis);
            if(Session::has('payment_type')){
                if(Session::get('payment_type') == 'cart_payment'){
                    return (new CheckoutController)->checkout_done(Session::get('combined_order_id'), $payment_detalis);
                }
                elseif (Session::get('payment_type') == 'wallet_payment') {
                    return (new WalletController)->wallet_payment_done(Session::get('payment_data'), $payment_detalis);
                }
                elseif (Session::get('payment_type') == 'customer_package_payment') {
                    return (new CustomerPackageController)->purchase_payment_done(Session::get('payment_data'), $payment_detalis);
                }
            }
        }
       
        flash(translate('Payment Failed'))->error();
        return redirect()->route('home');
        
    }

    public function paymentFailure($id)
    {
        flash(translate('Payment Failed'))->error();
        return redirect()->route('home');
    }
}
