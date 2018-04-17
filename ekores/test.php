<?php

include 'db.php';

echo "xxx yy<pre>";


$context = Context::getContext();
$customerId = $context->customer->id;
echo "\n customer ".$customerId;
 $sql = 'SELECT promotion_code FROM '._DB_PREFIX_.'customer '; //WHERE id_customer='.$id;
 
$results = Db::getInstance()->query($sql);
echo "\n dump \n</pre>"; var_dump($results);
die();



$connection = mysqli_connect(_DB_SERVER_,_DB_USER_,_DB_PASSWD_) or die(mysqli_error());

mysqli_select_db($connection,_DB_NAME_) or die(mysqli_error());



$result1 = "SELECT * FROM "._DB_PREFIX_."storeinfo "; // WHERE `store_name`='".$_GET['storedetails']."'";
 $sql = 'SELECT promotion_code FROM '._DB_PREFIX_.'customer '; //WHERE id_customer='.$id;
 

$result = mysqli_query($connection,$sql) or var_dump(mysqli_error()); // die(mysqli_error());
echo "yyyy";

echo "<pre>\n dump \n</pre>"; var_dump($result);

while($row = mysqli_fetch_array($result)){

 echo '<div class="store-info-wrap">';



  $phone_num = $row['store_name'];

  $address = $row['address'];

  echo "<strong>Phone Number</strong> ". $phone_num;

  echo "<strong>Store Address</strong> ". $address;  

  echo '</div>';

  }
