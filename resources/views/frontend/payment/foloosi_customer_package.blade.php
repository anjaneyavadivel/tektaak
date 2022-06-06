<!DOCTYPE html>
<html>
    <head>
    <title>Payment</title>
        <meta charset="utf-8"/>
    </head>
    @php
        $customer_package = \App\Models\CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
    @endphp
    <body>
        <?php
            //  CURLOPT_POSTFIELDS => "transaction_amount=".Session::get('payment_data')['amount'] ."&currency=".\App\Models\Currency::findOrFail(get_setting('system_default_currency'))->code."&customer_address=''&customer_city=''&billing_country=''&billing_state=''&billing_postal_code=''&customer_name=". Auth::user()->name ."&customer_email=".Auth::user()->email ."&customer_mobile=". Auth::user()->phone ,
               
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://foloosi.com/api/v1/api/initialize-setup",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "transaction_amount=".$customer_package->amount ."&currency=".\App\Models\Currency::findOrFail(get_setting('system_default_currency'))->code."&customer_address=''&customer_city=''&billing_country=''&billing_state=''&billing_postal_code=''&customer_name=". Auth::user()->name ."&customer_email=".Auth::user()->email ."&customer_mobile=". Auth::user()->phone,
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/x-www-form-urlencoded',
                    'merchant_key: '.env("FOLOOSI_MERCHANT_KEY")
                ),
            ));
            $response = curl_exec($curl);
           // dd($response);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                $responseData = json_decode($response,true);
                $reference_token = $responseData['data']['reference_token'];
            }
        ?>
        <script type="text/javascript" src="https://www.foloosi.com/js/foloosipay.v2.js"></script> 
        <script type="text/javascript">
            var reference_token = "<?= $reference_token; ?>";
            var options = {
                "reference_token" : reference_token, 
                "merchant_key" : '{{env("FOLOOSI_MERCHANT_KEY")}}',
                "redirect" : false 
            }
            var fp1 = new Foloosipay(options);
            fp1.open();
            foloosiHandler(response, function (e) {
                if(e.data.status == 'success'){
                    //console.log(e.data.data.transaction_no)
                    location.href = '{{ env('APP_URL') }}'+'foloosi/success/'+e.data.data.transaction_no
                }
                if(e.data.status == 'error'){
                    //console.log(e.data)
                    location.href = '{{ env('APP_URL') }}'+'foloosi/failure/'+e.data.data.transaction_no
                }
                if(e.data.status == 'closed'){
                    //console.log(e.data)
                    alert('window closed');
                    location.href = '{{ env('APP_URL') }}'
                }
            }); 
        </script>
    </body>
</html>