<?php


/**
 * Questa classe rappresenta il cliente che acquista uno o piu prodotti
 * e permette di verificare attraverso un algoritmo a che livello gerarchico si trova
 * se qualcuno può guadagnarci e via dicendo
 *
 * @param int $id l'id del cliente all'interno di prestashop
 * @return    void
 * @author  Riccardo Amadio
 * @copyright FREE
 */
class Client
{
  /**
   * ID del customer per prestashop
   * @var [int]
   */
var $id_customer;

/**
 * ID customer del padrone del sito, RE
 * @var [type]
 */
var $id_re;
var $tree=[];
var $client;

  function __construct($id) {	 
      $this->id_customer=$id;
      $this->client= $this->getClient($id);  
      
      // controlla se il cliente ha il refer_id settato se no lo aggiorna
      $this->checkReferal($id);
  }
	
	function findReferal($id){
		
		if (!$id)
			$id=$this->id_customer;
			
		$sql="SELECT id_sponsor FROM "._DB_PREFIX_."rewards_sponsorship where id_customer=$id"; 
		$ris=Db::getInstance()->ExecuteS($sql);

		if ($ris && count($ris)>0)
			return  $ris[0]['id_sponsor'];
		else return false;
	}
	
	function setIdRef($id){
	/*	$idref = $this->findReferal($id);

		if ($idref){
			$idRef=$ris[0]['id_sponsor'];
			$query="UPDATE "._DB_PREFIX_."customer SET refer_id = ".$idref." WHERE id_customer = ".$id; 
			$ris= Db::getInstance()->Execute($query);  
			
			// aggiorna il cliente e restituisce lo sponsor
			
			 $this->client = $this->getClient($id);       
			return   $this->client  ? $this->client['id_sponsor'] : false;
			
		} else return false; */
		
	}
	
	function checkReferal($id){
			
		/*	if (!$id)
				$id=$this->id_customer;
				
			$customer=$this->id_customer==$id ?  $this->client : $this->getClient($id) ;
		
			if ($customer && !$customer['refer_id']){
				$ref = $this->findReferal($id);
				if ($ref && $this->setIdRef($id))
					$this->setPayidCustomerFra($id, 1);
			
			}	*/
		}
	

  /**
  * DATO UN PROMOTION CODE
  * MI RESTITUISCE L'ID CONTENUTO NELL'PROMOTION CODE
  * @param string promotion_code del cliente
  * @return int id ricavato dal promotion_code
  * @author  Riccardo Amadio
  * @copyright FREE
  **/
  private function calcolaIdByPromotioncode($promotion_code)
  {
    $id=null;
    $cont=0;
    for($i=0;$i<strlen($promotion_code);$i++)
    {
      if(ctype_alpha($promotion_code[$i]))
      {
        $cont++;
      }
        else
        {
            if($cont==1)
                $id=$id.$promotion_code[$i];
        }
    }
    return $id;
  }



  /**
   * Procedura di pagamento per ogni algoritmo
   * @param  [PrestashopObject] $cart        [Oggeto Prestashop Carrelo]
   * @param  [int] $id_customer [id cliente]
   * @param  [int] $id_padre    [id padre]
   * @return [null]              [nulla]
   */
  public function pagamento($cart,$id_customer,$id_padre)
  {
    foreach($cart->getProducts() as $elem)
    {
      $price=number_format($elem['total_wt'], 2, '.', ' ');
      $id_product=$elem['id_product'];
      $volume_ritenuta=$this->getVolumeRitenuta($id_product);
      $percentuale_volume=($price*$volume_ritenuta[0])/100;
      $provvigione=($percentuale_volume*$volume_ritenuta[1])/100;
      $provvigione=number_format($provvigione, 2, '.', ' ');
      $first_last_name=$this->getNomeCognomeByIdCustomer($id_customer);
      $name_product=$this->getNomeProdottoByIdProduct($id_product);
      $name_padre=$this->getNomeCognomeByIdCustomer($id_padre);
      //echo "<h1>$id_padre $name_padre $id_customer $first_last_name $id_product $name_product $price $provvigione</h1>";
      $this->salvaAcquisto($id_padre,$name_padre,$id_customer,
      $first_last_name,$id_product,$name_product,$price,$provvigione);
    }//end FOREACH
  }



  /**
   * Trovare nome e cognome cliente dato il suo id
   *
   * @param  [int] $id [id_customer]
   * @return [Array of String]     ["firstname","lastname"]
   */
    private function getNomeCognomeByIdCustomer($id)
    {
        $sql="SELECT firstname,lastname FROM "._DB_PREFIX_."customer WHERE id_customer=".$id;
        $results=Db::getInstance()->query($sql);
        while ($row=Db::getInstance()->nextRow($results))
         {
                if($row)
                  return ($row['firstname']." ".$row['lastname']);
          }
         return null;
    }
    
    function getCustomer($id){
		$sql="SELECT * FROM "._DB_PREFIX_."customer WHERE id_customer=". ($id ? $id : $this->id_customer);
		$cust= Db::getInstance()->ExecuteS($query); //print_r($ris);
		
		 if ( $cust && count($cust)>0 ) 
			return $cust[0];
		 else null;
	}
    
    function getMaturatoCustomer($subId){
		global $global_Tot_maturato;
		
		
		$query="SELECT sum(price) as maturato FROM `ek_buy`  WHERE idcliente=$subId group by price" ;
		$dbTotali= Db::getInstance()->ExecuteS($query); //print_r($ris);
		
		 $maturato=$dbTotali[0]['maturato'];
		 if (!$maturato || $maturato==null) {$maturato=0; }
		 
			return $maturato;

	}


    /**
     * Salva l'acquisto con invito
     * @param  [int] $padre         [id_padre]
     * @param  [String] $name_padre    [nome del padre]
     * @param  [int] $figlio        [id figlio]
     * @param  [String] $nome_cognome  [nome e cognome del figlio]
     * @param  [int] $prodotto      [id_product]
     * @param  [String] $nome_prodotto [Nome Prodotto]
     * @param  [Float] $total         [Prezzo totale con  tasse e tutto]
     * @param  [float] $provvigione   [provvigione/guadagno del padre]
     * @return [null]                [/]
     */
    private function saveBuy($amount, $idShopper)
    {
		$sql="INSERT INTO `ek_buy`( `id_shopper`, `idcliente`, `price`, `data`, `data_ins`) VALUES ('$idShopper','$this->id_customer','$amount')";
		
          try {
			Db::getInstance()->query($sql);
          } catch (Exception $e) {
            throw new Exception($e->getMessage());
          }

    }
    
    function getPayerList($id){
		if (!$id)
			$id=$this->id_customer;
			
			// il 30% di quello che ha speso il custumer viene messo : 10% a se stesso, 10% referid, 10% payid
			
		$sql="select  CONCAT(firstname,' ', lastname) as name, `id_customer`, `id_customer`, `email`, `refer_id`, `payid`, `tot` from v_sum_payment where `refer_id`=$id OR `payid`=$id";  // echo "pp->$sql";
	//	echo "aaa"; var_dump(Db::getInstance());
		return Db::getInstance()->ExecuteS($sql);

	}
	
	function balance($id){
			if (!$id)
				$id=$this->id_customer;
			$prov=$this->getProv($id);
			$cahsBack=$this->getCahsBack($id);
			$res=[];
			
			$a=$prov['prov']['prov'];
			$b=$cahsBack['cashback']; 
			
			$res['prov']=$prov;
			$res['cashBack']=$cahsBack;
			$res['balance']=$prov['prov']+$cahsBack['cashback'];
			
			if (isset($this->client['isshop']) && $this->client['isshop']){
				$res['token']=$this->saldoToken($id);
			}
			
			// if (isset($this->client['isshop']) && $this->client['isshop']){
			//	$res['token']=$this->saldoToken($id);
			// }

			return $res;
	}
	
	function getProv($id){
		if (!$id)
			$id=$this->id_customer;
		$sql="SELECT  sum(tot) as amount, sum(tot)*0.10 as prov FROM `v_sum_payment` where payid=$id or refer_id=$id";
		$ris=Db::getInstance()->ExecuteS($sql);

		if ($ris && count($ris)>0)
			return  $ris[0];
		else return ['amount'=>0, 'prov'=>0];

	}
	
	function getCahsBack($id){
		if (!$id)
			$id=$this->id_customer;
		$sql="SELECT sum(price) as amount, sum(price)*0.10 as cashback FROM `ek_buy` where idcliente=$id"; 
		$ris=Db::getInstance()->ExecuteS($sql);

		if ($ris && count($ris)>0)
			return  $ris[0];
		else return ['amount'=>0, 'cashback'=>0];
		
	}
    
    function getClient($id){
		$sql="SELECT `id_customer` , CONCAT(  `firstname` ,  ' ',  `lastname` ) AS name,  `email` ,  `birthday` ,  `id_lang` ,  `is_guest` ,  `refer_id` ,  `payid` ,  `active`  FROM "._DB_PREFIX_."customer WHERE id_customer = ".$id; // echo'<br> query'.$query;
		$rows= Db::getInstance()->ExecuteS($sql);
		$client = !$rows || $rows==null ? null : $rows[0];
		$client['groups']=[]; 
		
		$sql="SELECT id_group  FROM "._DB_PREFIX_."customer_group WHERE id_customer = ".$id;  // echo'<br> query'.$sql;
		$rowsGroup= Db::getInstance()->ExecuteS($sql);  // echo "\n groups xx=".count($rowsGroup); print_r($rowsGroup);

		$rowsGroup = $rowsGroup ? $rowsGroup : [];
		
		$client['isshop']=false;
		
		for($i=0; $i<count($rowsGroup); $i++){
			if ($rowsGroup[$i]['id_group'] == 4 || $rowsGroup[$i]['id_group'] == "4")
				$client['isshop']=true;
				
				// echo "\n loop groups ".$rowsGroup[$i]['id_group']  ;
			$client['groups'][]=$rowsGroup[$i]['id_group'];
			
		}
		
		return $client;
	}
    
     function getTree (){
		$this->tree=[];
		$this->tree['maturato']=0;
	    $this->walkTree($this->id_customer,  $tree) ;
		
		}
		
    private function walkTree ($id_customer, $node, $payid){
		
		
		$sql="SELECT * FROM "._DB_PREFIX_." WHERE refer_id = ".$id_customer." AND payid= ".$payid; // echo'<br> query'.$query;
		$linea= Db::getInstance()->ExecuteS($query); echo "<br>sql=$sql"; 
		
		$node['id']=$id_customer;
		$node['maturato']=0 ;
		$node['child']=[];
		
		$tot_maturato_linee =0;
		  
		for($i=0; $i<count($linea); $i++) {
			$node['child'][$i]=[];
		
			$maturato= getMaturatoCustomer($linea[$i]['id_customer']);
			$this->tree['maturato'] += $maturato;
			$node['maturato'] += $maturato;
			
			$node['child'][$i]['id'] = $linea[$i]['id'];
			$node['child'][$i]['nome'] = strtoupper($linea[$i]['firstname'])." - ".strtoupper($linea[$i]['lastname']);
			$node['child'][$i]['email'] = $linea[$i]['email'];
			$node['child'][$i]['maturato']=$maturato;
			  
			// SETTO IL PAYID
			if( ($linea[$i]['payid'] == 0) ){$payid = $id_customer;}
			else{$payid=$linea[$i]['payid'];}
        
			walkTree($linea[$i]['id_customer'],$node['child'][$i], $payid);
			
		}
	}
	
		
	function setPayidCustomerFra($id_customer,$dbg_param){
		
		
		
		if ( (isset($_GET['asb'])) or ($dbg_param) ) $dbg=1;
		else $dbg=0;
		
		// LEGGO IL  customer 
		$query="SELECT * FROM "._DB_PREFIX_."customer WHERE id_customer= ".$id_customer;
		$ris= Db::getInstance()->ExecuteS($query);
		if (!$ris)  { die('ERROR - LEGGO IL PADRE<br>'.$query);return 0;}

		if($dbg) echo'<pre>Cliente: '.$ris[0]['firstname'].' - '.$ris[0]['lastname'].' ->id = '.$id_customer.'</span>'."\n";
		
		$id_padre = $ris[0]['refer_id'];
		
		if($id_padre == 0) { //IMPLICA CHE SONO AL LIVELLO 0
				if($dbg) echo'<br>livello 0 esco '."\n"; // IL PAYID di CUSTOMER RESTA ZERO PERCHE' Valore di default
			return;
		}
			echo "ris="; print_r($ris);
		// LEGGO IL NOME DEL PADRE
		$query="SELECT * FROM "._DB_PREFIX_."customer WHERE id_customer= ".$id_padre;
		$ris= Db::getInstance()->ExecuteS($query);
		if($dbg) echo'<br> -- REFERAL: '.$ris[0]['firstname'].' - '.$ris[0]['lastname'].' - id_padre = '.$id_padre;
		
		$payid_dal_padre=$ris[0]['payid']; //echo'<br> ---- Payid_padre = '.$payid_dal_padre;
		
		if ((int)$id_padre == (int)(19)){ // UTENTE MIMMO LIVELLO 0
		   
			 // LEGGO TUTTI le linee del 19
			$query="SELECT id_customer FROM "._DB_PREFIX_."customer WHERE refer_id = ".$id_padre;
			$ris= Db::getInstance()->ExecuteS($query);
			$info_linee_livello1 = $ris;    //print_r($info_zio);
			// if($dbg); echo'<br> <span style="font-family:arial;color:blue;font-size:12px;">CUSTOMER CON TOTALE zii '.count($info_zio).'</span><br>';
			
			sort($info_linee_livello1); // ORDINO IN MANIERA CRESCENTE L'array fra 
			 
			for($i=0;$i<count($info_linee_livello1);$i++) {
				if($i<2){   
					if( ($id_customer == $info_linee_livello1[$i]['id_customer'])  ) {
						$payid = $id_customer;
						$query="UPDATE "._DB_PREFIX_."customer SET payid = ".$payid." WHERE id_customer = ".$id_customer; $ris= Db::getInstance()->Execute($query);       
						if($dbg) echo'<br><span style="font-family:verdana;color:green;font-size:12px;"> ->LINEA ESCLUSA DA (19) <br>LIVELLO 1 - CUSTOMER TRA I PRIMI 2 => SET PAYID('.$payid.')  id='.$id_customer.'</span><br>';
						return;      
					}
				} else{   
					$payid = 0;
					$query="UPDATE "._DB_PREFIX_."customer SET payid = ".$payid." WHERE id_customer = ".$id_customer; $ris= Db::getInstance()->Execute($query);       
					if($dbg) echo'<br><span style="font-family:verdana;color:green;font-size:12px;"> ->LINEA *INCLUSA* DA (19) <br>LIVELLO 1 - CUSTOMER MAGGIORE I PRIMI 2 => SET PAYID('.$payid.')  id='.$id_customer.'</span><br>';
					return;
				}
			}
		 } 
		
		// LEGGO IL NONNO di customer 
		$query="SELECT refer_id FROM "._DB_PREFIX_."customer WHERE id_customer= ".$id_padre;
		$ris= Db::getInstance()->ExecuteS($query);
		if (!$ris)  { die('ERROR - LEGGO IL NONNO<br>'.$query);return 0;}
		
		$id_nonno = $ris[0]['refer_id'];
		
		if($id_nonno == 0){ // IMPLICA CHE SONO AL LIVELLO 1
			if($dbg) echo'<br>'; // IL PAYID di CUSTOMER RESTA ZERO PERCHE' Valore di default
			return;
		}
		
		// LEGGO IL NOME DEL NONNO
		$query="SELECT * FROM "._DB_PREFIX_."customer"._DB_PREFIX_." WHERE id_customer= ".$id_nonno;
		$ris= Db::getInstance()->ExecuteS($query);
		if($dbg) echo'<br> -- NONNO:  '.$ris[0]['firstname'].' - '.$ris[0]['lastname'].' - id_nonno = '.$id_nonno;
	   
		// LEGGO TUTTI I FRATELLI DEL CUSTOMER; il Primo e il secondo, non diventano nipoti legittimi, CIO' VALE SOLO PER IL LIVELLO 2
		$query="SELECT id_customer FROM "._DB_PREFIX_."ps_customer WHERE refer_id = ".$id_padre;
		$ris= Db::getInstance()->ExecuteS($query);
		$info_fratello = $ris;    //print_r($info_fratello);
		if($dbg) echo'<br> <span style="font-family:arial;color:blue;font-size:12px;">CUSTOMER CON TOTALE FRATELLI '.count($info_fratello).'</span><br>';
					  
		// SETTO IL PAYID DA EREDITARE
		if($payid_dal_padre==0) {$payid =  $id_nonno;}
		else {$payid =  $payid_dal_padre;}   

		sort($info_fratello); // ORDINO IN MANIERA CRESCENTE L'array fra 
		//print_r($info_fratello);
	  
		for($i=0;$i<count($info_fratello);$i++) {   

			if($i < 2) {   
				if($id_customer == $info_fratello[$i]['id_customer']){ // SE IL CUSTOMER E' Primo tra i fretelli
				
					 if($dbg) echo'<br> ---- $payid = '.$payid.'<br>';
					 $query="UPDATE "._DB_PREFIX_."customer SET payid = ".$payid." WHERE id_customer = ".$id_customer; $ris= Db::getInstance()->Execute($query);       
					 if($dbg) echo'<span style="font-family:verdana;color:green;font-size:12px;"> -> CUSTOMER TRA I PRIMI 2 => SET PAYID('.$payid.') Nipote Leggittimo('.$i.') id='.$id_customer.'</span><br>';
					 return;    
				}
			} else {
				if($dbg)  echo'<br> ---- $payid = 0 <br>';
				$query="UPDATE "._DB_PREFIX_."customer SET payid = 0 WHERE id_customer = ".$id_customer; $ris= Db::getInstance()->Execute($query);       
				if($dbg) echo'<span style="font-family:verdana;color:green;font-size:12px;"> -> CUSTOMER NON TRA I PRIMI 2 => SET PAYID(0) Nipote ILLEGITTIMO*('.$i.') id='.$id_customer.'</span><br>';
				return;  
			}
		}    
		
		return 1;

	}
	
	function userData(){
		$ris =[];
		$ris['user']=$this->client; // echo "xxxx->";print_r($this->client);
		if (!$this->client)
			return $ris; 
		$ris['c_referal'] = $this->client['refer_id'] ? $this->getClient($this->client['refer_id']) : null;
		$ris['c_pay']	  = $this->client['payid'] ? $this->getClient($this->client['payid']) : null;
		$ris['balance']= $this->balance($id);
		
		return $ris;
		
	}
	
	function totalTokenBuy($id){
		
		if (!$id)
			$id=$this->id_customer;
			
		$query="SELECT distinct sum( total_products_wt  ) as tot FROM `".DB_PREFIX."orders` WHERE id_customer=$id";
		$dbTotali= Db::getInstance()->ExecuteS($query); //print_r($ris);

		 $maturato=$dbTotali[0]['tot'];
		 if (!$maturato || $maturato==null) {$maturato=0; }
		 
		 return $maturato;
	}
	
	function saldoToken($id){ 
		if (!$id)
			$id=$this->id_customer;
			
		$totBuy = $this->totalTokenBuy($id);
		
		$sql="SELECT  sum(tot) as amount FROM `v_sum_payment` where payid=$id";
		$ris=Db::getInstance()->ExecuteS($sql);
		
		$totSpeso=0; // echo "spes-->"; print_r($ris);
		
		if ($ris && count($ris)>0){
			$payment=$ris[0] ? $ris[0]['amount'] :0;
			$totSpeso=$totBuy - $payment;
		}
		return $totBuy - $totSpeso; 
	}


}//end..
 ?>
