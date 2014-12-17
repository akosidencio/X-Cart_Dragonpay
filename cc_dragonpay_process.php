<?php
    /*****************************************************************************\
    +-----------------------------------------------------------------------------+
    | Dragonpay X-Cart Plugin - http://www.dragonpay.ph                                             |
    | December 2014
    | Author: Dennis Paler <dpaler@shockwebtudio.com>		                                                  |
    | All rights reserved.                                                        |
    +-----------------------------------------------------------------------------+
    \*****************************************************************************/

    require "./auth.php";
    include $xcart_dir."/include/func/func.order.php";


    if (!func_is_active_payment("cc_dragonpay.php")) { exit; }

      $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_dragonpay.php'");
      $merchantid = trim($module_params['param01']);
      $password  = trim($module_params['param02']);
      $info = ( $HTTP_POST_VARS)?$HTTP_POST_VARS:$_POST;

    while ( list($key,$val)=each($info) ) {
        $$key = $val;
    }

     $sessurl = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '".$orderid."'");

     if (!empty($sessurl))
        x_session_id($sessurl);

     $sessurl = $XCART_SESSION_NAME."=".$XCARTSESSID."&";

     # orderid, appcode, tranID, domain, status, amount, currency, paydate, skey , channel 

    $key0 = md5( $tranID.$orderid.$status.$merchantid.$amount.$currency );
    $key1 = md5( $paydate.$domain.$key0.$appcode.$password);


     if ( $skey!=$key1 ) { $status = "-1"; }

        $oid = explode("-",$orderid);
        $n = sizeof($oid);


    if ( $status=="00" && $skey==$key1) { 	 // success (P)

          for ( $i=0; $i<$n; $i++) { func_change_order_status($oid[$i], "P", ""); }

          if ( $n>1 ) { $x_oid = implode(",",$oid); }
          else { $x_oid = $orderid; }

          unset($GLOBALS['XCART_SESSION_VARS']['cart']);

          func_header_location($xcart_catalogs['customer']."/cart.php?".$sessurl."mode=order_message&orderids=".$x_oid);

    } else {   // failed (F)

        for ( $i=0; $i<$n; $i++) { func_change_order_status($oid[$i], "F", ""); }

        func_header_location($xcart_catalogs['customer']."/error_message.php?error_ccprocessor_error");
    }

    exit();

?>
