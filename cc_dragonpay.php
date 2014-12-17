<?php
    /*******************************************************************************\
    +-------------------------------------------------------------------------------+
    | Dragonpay X-Cart Plugin - http://www.dragonpay.ph                                                |
    | December 2014   
    | Author: Dennis Paler <dpaler@shockwebstudio.com>                                                                 |
    | All rights reserved.                                                          |
    +-------------------------------------------------------------------------------+
    \*******************************************************************************/


    if (!isset($REQUEST_METHOD))
        $REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

        if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

        if (!function_exists("func_nb_convert")) {
            function func_nb_convert($s) {
                    return htmlspecialchars(str_replace("#"," ", $s));
            }
        }

        $orderid   = join("-",$secure_oid); 
        $merchant  = trim($module_params ["param01"]);
        $password  = trim($module_params ["param02"]);
        $returnurl = trim($module_params ["param03"]);

        if(!$duplicate)
            db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,param1) VALUES ('".addslashes($orderid)."','".$XCARTSESSID."','".$cart["total_cost"]."')");

        while( list($key,$val)=each($cart['products']) ) {
            $pdetails.= "(".$val['productcode'].")\t:".$val['product']." x ".$val['amount']. "\n\n";	
        }

         $prod = array();
        if (!empty($products)) {
        foreach ($products as $p) {
            $prod[] = "prod=".str_replace(array(",",";","\n","\r"), array('','','',''), $p['productcode']).",item_amount=".price_format($p['price'])."x".$p['amount'];
            }
        }
        $prod = implode(";", $prod);
        

        $merchant = trim($module_params ["param01"]);
        $txnid = $orderid;
        $amount = $cart['total_cost'];
        $ccy = strtoupper($GLOBALS['config']['General']['currency_symbol']);
        $description  = $prod;
        $email = func_nb_convert($userinfo["email"]);
        $digest = $digest;
        $rate = $GLOBALS['config']['General']['alter_currency_rate'];
        //$returnurl = $current_location."/payment/cc_dragonpay_process.php";


        $digest_str = "$merchant:$txnid:$amount:$ccy:$description:$email:$password";
        $digest = sha1($digest_str);

    ?>
    <html>

    <div style="text-align:center; padding:30px;">
        <br><img src='http://www.shockwebstudio.com/extensions/pic/dragonpay.jpeg' border=0 alt='Dragonpay Online Payment Gateway' title='Dragonpay Online Payment Gateway'>
        <br><br><h3>Please wait for a while. You'll redirect to Dragonpay Online Payment Gateway...</h3>
    </div>

    <body onLoad="document.process.submit();">
    <form action="http://test.dragonpay.ph/Pay.aspx?" method="GET" name="process">
        <input type="hidden" name="merchantid" value="<?php echo $merchant; ?>">
        <input type="hidden" name="txnid" value="<?php echo func_nb_convert($orderid); ?>">
        <input type="hidden" name="amount" value="<?php echo $amount; ?>">
        <input type="hidden" name="ccy" value="<?php echo $ccy; ?>">
        <input type="hidden" name="description" value="<?php echo $description; ?>">
        <input type="hidden" name="email" value="<?php echo $email; ?>">
        <input type="hidden" name="digest" value="<?php echo $digest; ?>">
    </form>
    </body>
    </html>
    <?php

    exit;
?>
