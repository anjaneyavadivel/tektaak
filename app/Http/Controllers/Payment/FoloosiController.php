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
//         $request_data = array(
//             'transaction_amount'=>1,
//             'currency'=>'AED',
//             'customer_address'=>'Address',
//             'customer_city'=>'Dubai','billing_country'=>'ARE',
//             'billing_state'=>'Dubai',
//             'billing_postal_code'=>'000000',
//             'customer_name'=>'Test',
//             'customer_email'=>'test%40email.com',
//             'customer_mobile'=>'9876543210'

//         );

//         $request_data_json=json_encode($request_data);

//         $header = array(
//             'content-type: application/x-www-form-urlencoded',
//             'merchant_key: test_$2y$10$XC8zrDy6Rc2XYAFB-mPFdu0c.W3rO1tzkxw0m1Eb-xa69HHN3E-AK'
//                 );
//         $url = curl_init();
//         curl_setopt($url, CURLOPT_URL, 'https://foloosi.com/api/v1/api/initialize-setup');
//         curl_setopt($url,CURLOPT_HTTPHEADER, $header);
//         curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($url, CURLOPT_VERBOSE, true);
//         curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
//         curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false);
//         curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
//         curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

//         $resultdata = curl_exec($url);
//         curl_close($url);
// dd($resultdata);
        //$token = json_decode($resultdata)->id_token;

        return view('frontend.payment.foloosi');
    }
}
