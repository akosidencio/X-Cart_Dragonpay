<?php
    /*****************************************************************************\
    +-----------------------------------------------------------------------------+
    | Dragonpay - http://www.dragonpay.ph                                             |
    | December 2014		                                                  |
    | All rights reserved.                                                        |
    +-----------------------------------------------------------------------------+
    \*****************************************************************************/

    require "./top.inc.php";
    require "./init.php";

    global $sql_tbl;

    $check = "SELECT module_name FROM $sql_tbl[ccprocessors]"; 
    $res   = func_query($check); 
    $size     = sizeof($res);
    $flag = 0; 

    for ($i=0; $i<$size; $i++) {

      if( $res[$i]['module_name']=="Dragonpay Online Payment Gateway" ) { $flag = 1; }

    }
    $insert="INSERT INTO xcart_ccprocessors SET module_name='Dragonpay Online Payment Gateway', type='C', processor='cc_dragonpay.php', template='cc_dragonpay.tpl', disable_ccinfo='Y', background='N', testmode='N', paymentid=0";

    if ( $flag == 0 ) {
      mysql_query($insert);  
      echo "<h3 style='font-family:Arial; font-size:10pt; text-align:center; padding:40px;'>
             Dragonpay Payment Module successfully install in your shopping cart. <br><br>Please configure this payment module on Admin site.
           </h3>";  
    } else { 
      echo "<h3 style='font-family:Arial; font-size:10pt; text-align:center; padding:40px; color:red;'>
             Dragonpay Payment Module already exist. <br><br>Please configure this payment module on Admin site.
           </h3>";  
    }       
?>