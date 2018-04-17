<?php


/**
 *  vedi https://github.com/mevdschee/php-crud-api/blob/master/api.php
 * Questa classe rappresenta il cliente che acquista uno o piu prodotti
 * e permette di verificare attraverso un algoritmo a che livello gerarchico si trova
 * se qualcuno può guadagnarci e via dicendo
 *
 * @param int $id l'id del cliente all'interno di prestashop
 * @return    void
 * @author  Ettore Bevilacqua
 * @copyright FREE
 */
 

 
include '../crud.php'; 
include 'auth.php'; 
include '../shopper.php';

   //  var_dump(Db::getInstance());      
class Api{
	var $customerId=null;
	var $shopper=null;
	
	function __construct($customerId){
		$this->customerId=$customerId;
		$this->shopper = new Shopper(Db::getInstance(), $customerId);  
	}
	
	function sell($request, $par){ 
		$vals = json_decode($request['post']); var_dump($vals); 	echo "vvvvvv=".$vals->importo; echo "vvvvvv=".$vals->id; 
	
		
		if (isset($vals->importo) && $vals->importo && isset($vals->id) && $vals->id){
		  try {
			 $res = $this->shopper->saveBuy($vals->id, $vals->importo);
			echo "<pre>\n xx saved buy zz"; print_r($res);
          } catch (Exception $e) {
            echo "error"; $e->getMessage();
          }
		
		}

	}
	
	function tree(){
		echo "api tree";
		$this->shopper->clients->getTree();
		
		// $this->shopper->clients->getPay();
	}
	
	function prov(){
		$out=$this->shopper->clients->getProv();
		echo json_encode($out);
	}
	
	function cashBack(){
		$out=$this->shopper->clients->getCahsBack();
		echo json_encode($out);
	}
	
	function balance(){
		$out=$this->shopper->clients->balance();
		echo json_encode($out);
	}
	
	function payment(){
		$out=$this->shopper->clients->getPayerList();
		echo json_encode($out);
	}
	 function userInfo(){
		// $out="sss";
		 $out=$this->shopper->clients->userData();
		echo json_encode($out); // "userInfo "; print_r($out);
	}
	
	function seelList(){
		$out=$this->shopper->getSellList();
		echo json_encode($out);
	}
	
	function testCall(){
		echo "{test:true}";
	}
	function showNetwork(){
	
	}
}


// API ESEMPIO CALL FUNCTION : ?api=sell
			 header('Access-Control-Allow-Credentials: true');
			 header('Access-Control-Allow-Origin: http://localhost:3000');
			 
			 header('Access-Control-Allow-Methods: POST,GET,OPTIONS,PUT,DELETE'); 
			 header('Access-Control-Allow-Headers: Content-Type,Accept');
  
			
$id_customer=$context->customer->id;
// $id_customer=4; //10001;

// echo "custmerid =$id_customer=="; // json_encode(print_r($context->customer, true)); echo "<--- end context";

$api=new Api($id_customer);   
$crud = new Crud($api);   // $crudVals = $crud->toArray(); echo "<pre>crud=";print_r($crudVals);  // var_dump($shopper);

$crud->callApi();



 ?>
