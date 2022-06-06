<!DOCTYPE html>
<html>
    <head>
    <title>Payment</title>
        <meta charset="utf-8"/>
    </head>
    <body>
        <?php
            $FOLOOSI_MERCHANT_KEY = {{env('FOLOOSI_MERCHANT_KEY')}};
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://foloosi.com/api/v1/api/initialize-setup",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "transaction_amount=1&currency=AED&customer_address=Address&customer_city=Dubai&billing_country=ARE&billing_state=Dubai&billing_postal_code=000000&customer_name=Test&customer_email=test%40email.com&customer_mobile=9876543210",
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/x-www-form-urlencoded',
                    'merchant_key: '. $FOLOOSI_MERCHANT_KEY
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
                "merchant_key" : {{$FOLOOSI_MERCHANT_KEY}},
                "redirect" : false 
            }
            var fp1 = new Foloosipay(options);
            fp1.open();
            foloosiHandler(response, function (e) {
                if(e.data.status == 'success'){
                    console.log(e.data)
                }
                if(e.data.status == 'error'){
                    console.log(e.data)
                }
                if(e.data.status == 'closed'){
                    console.log(e.data)
                }
            }); 
        </script>
    </body>
</html>