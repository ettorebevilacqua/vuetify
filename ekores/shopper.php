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
 
include_once 'clients.php';
 
class Shopper {
  /**
   * ID del customer per prestashop
   * @var [int]
   */
var $idShopper;
var $clients;
var $DB;

  function __construct($DB, $id)
  {
	  $this->DB=$DB;
      $this->idShopper=$id;
      $this->clients = new Client($id); 
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


    /**
     * Salva l'acquisto con invito
     * @param  [int] $padre         [id_padre]
     * @param  [String] $name_padre    [nome del padre]
     * @return [null]                [/]
     */
    public function saveBuy($idCustomer, $amount)  {
		$sql="INSERT INTO `ek_buy` ( `id_shopper`, `idcliente`, `price`) VALUES ('$this->idShopper','$idCustomer','$amount')";
		
          try {
			  $ris= Db::getInstance()->Execute($sql);  echo "\n shopper class saveBuy query=".$sql; 
			  echo "\n ris ="; var_dump($ris);
          } catch (Exception $e) {
			  echo "error ttt";
            throw new Exception($e->getMessage());
          }

    }
    
    function getSellList($id){
		if (!$id)
			$id=$this->id_customer;
	
			$sql=" SELECT CONCAT( b.`firstname` ,  ' ', b.`lastname` ) AS name, b.`email` , b.`birthday` , b.`is_guest` , b.`refer_id` , b.`payid` , b.`active` , a . * 
				FROM  `ek_buy` AS a
				INNER JOIN tb7s_customer AS b ON a.idcliente = b.id_customer
				WHERE id_shopper =".$this->idShopper." ORDER BY  `a`.`data_ins` DESC  limit 30 ";

		$ris=Db::getInstance()->ExecuteS($sql);

		if ($ris && count($ris)>0)
			return  $ris;
		else return [];

	}
    
    public function getTree(){
		
	}

}//end..

function getShopper($id){
	return new Shopper($id);
}
 ?>
