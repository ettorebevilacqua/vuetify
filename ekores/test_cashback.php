<?php
 
 include_once 'db.php';
 include_once ('cashback.php');
  
 $cust=0;
 if (isset($_GET['cust']))
	$cust=$_GET['cust'];

 $isSetPayid=false;

	  if ($isSetPayid){
		$query="SELECT * FROM `ps_customer` WHERE refer_id  is not null;"; // echo'<br> query'.$query;
		$linea= Db::getInstance()->ExecuteS($query);
		
		for($i=0;$i<count($linea);$i++)
		{
			setPayidCustomerFra($linea[$i]['id_customer']);
		 }
		
		 // echo "test cashback idcust".$cust;
		 if ($cust!=0)
			setPayidCustomerFra($cust,1);
	  }


	viewTreeCustomer(10001, 0, 0, true);

?>
