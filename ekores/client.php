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



  function __construct($id)
  {
      $this->id_customer=$id;
  }


  /**
   *  DALL'ID DELL'OGGETO RICAVA IL PROMOTION CODE,
   *    SE PRESENTE È UN FIGLIO
   *   ALTRIMENTI È IL RE
   * @param void
   * @return    boolean TRUE SE È RE , FALSE SE NON LO È
   * @author  Riccardo Amadio
   * @copyright FREE
   */
  public function isRE()
  {
            $promotion_code=$this->getPromotionCodeById($this->id_customer);
            if($promotion_code)
            return false;
            else
            return true;
  }


    /**
    * DALL'ID RICAVA IL PROMOTION CODE
    * DAL PROMOTION CODE RICAVA L'ID DEL PADRE
    * VERIFICA CHE IL PADRE ABBIA IL PROMOTION CODE
    * SE NON CE L'HA VUOL DIRE CHE È IL RE
    * ALTRIMENTI NON È IL RE
    * @param void
    * @return    boolean TRUE SE È FIGLIO DEL RE , FALSE SE NON LO È
    * @author  Riccardo Amadio
    * @copyright FREE
    **/
  public function isFiglioDelRe()
  {
                $id_padre=$this->calcolaIdPadre();
                if($this->getPromotionCodeById($id_padre)==null)
                {
                  $this->id_re=$id_padre;
                    return true;
                }
                    else
                      return false;
  }

    /**
      *  PRENDE L'ID DELL'OGGETO TROVA IL PROMOTION CODE
      *  CALCOLA L'ID DEL PADRE DAL PROMOTION CODE
      *  E LO RESTITUISCE
      * @param void
      * @return  int RESTITUISCE L'ID DEL PADRE
      * @author  Riccardo Amadio
      * @copyright FREE
        **/
    public function calcolaIdPadre()
    {
      $promotion_code=$this->getPromotionCodeById($this->id_customer);
      return $this->calcolaIdByPromotioncode($promotion_code);
    }





    /**
     * RESTITUISCE PROMOTION CODE DELL'ID PASSATO COME PARAMETRO
     *
     * @param int id del cliente all'interno di prestashop
     * @return    string promotion_code
     * @author    Riccardo Amadio
     * @copyright FREE
     */
  public function getPromotionCodeById($id)
  {
        $sql = 'SELECT promotion_code FROM '._DB_PREFIX_.'customer WHERE id_customer='.$id;
          try
          {
            $results = Db::getInstance()->query($sql);
            while ($row=Db::getInstance()->nextRow($results))
              {
                  if($row)
                    {
                        return $row['promotion_code'];
                    }
               }

           }catch(Exception $e)
           {
                 return null;
           }

          return null;
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
     * SELEZIONA TUTTI I CLIENTI CON LO STESSO PROMOTION CODE
     *  MA IN ORDINE CRESCENTE PER DATA
     * @param     String promotion_code
     * @return    Array Of Int  (id_customer)
     * @author
     * @copyright
     */
    public function getClientiOrdinatiDataPromotioncode($pc)
    {
      $sql="SELECT id_customer FROM "._DB_PREFIX_."customer WHERE promotion_code='".$pc;
      $sql=$sql."' ORDER BY date_add ASC";

      $results=Db::getInstance()->query($sql);
      $array=array();
         while ($row=Db::getInstance()->nextRow($results))
            {
                if($row)
                      array_push($array,$row['id_customer']);
            }

      return $array;
    }



  /**
   * Questa funzione verifica che il padre dell'oggetto sia uno
   * dei figli diretti del RE
   * Poi se in ordine d'iscrizione il terzo in su dei figli diretti del RE
   *
   * @param    null
   * @return    boolean   TRUE se il padre o nonno o bisnonno è terzo figlio diretto o maggiore di tre del RE
   * @author  Riccardo Amadio
   * @copyright MIT
   */
  public function isPadreTerzoFiglioDelRe()
  {
      $id_padre=$this->calcolaIdPadre();
      $oggetto=new Client($id_padre);
      if($oggetto->isFiglioDelRe())
      {
        //ME SEGNO CHI È IL RE
        $this->id_re=$this->calcolaIdByPromotioncode($oggetto->getPromotionCodeById($id_padre));

        $figli_del_re=$this->getClientiOrdinatiDataPromotioncode($oggetto->getPromotionCodeById($id_padre));

        for($i=0;$i<count($figli_del_re);$i++)
        {
          if($i>=2 && $figli_del_re[$i]==$id_padre)
          return true;
        }
        return false;
      }
      else
      {
        return false;
      }

  }


  /**
   * Controlla se altri iscritti hanno lo stesso promotion_code
   *  se si verifica che lui sia tra i primi due (lui si intende l'oggetto)
   * @param     NOTHING
   * @return    Boolean  TRUE Sse è tra i primi due ad iscriversi con lo stesso promotion code
   * @author      Riccardo Amadio
   * @copyright   OPEN
   */
  public function sonoTraPrimiDueNipoti()
  {
    $promotion_code=$this->getPromotionCodeById($this->id_customer);
    $clienti=$this->getClientiOrdinatiDataPromotioncode($promotion_code);
    for($i=0;$i<2;$i++)
    {
      if($clienti[$i]==$this->id_customer)
      return true;
    }
    return false;
  }

  /**
   * [Verifica che sia iscritto dopo i primi due clienti]
   * @return [Boolean] [Se non è tra i primi due con lo stesso promotion_code]
   */
  public function clienteMaggioreDiTre(){
    $promotion_code=$this->getPromotionCodeById($this->id_customer);
    $clienti=$this->getClientiOrdinatiDataPromotioncode($promotion_code);
    for($i=2;$i<count($clienti);$i++)
    {
      if($clienti[$i]==$this->id_customer)
      return true;
    }
    return false;
  }



  /**
   * Questa funzione verifica se un qualsiasi cliente
   *  deve dare provvigione al re oppure no
   * @param     id del cliente
   * @return    boolean TRUE se deve darla altrimenti no
   * @author    Riccardo Amadio
   * @copyright OPEN
   */
  public function verificoGuadagnoRe($id)
  {
    $p_c=$this->getPromotionCodeById($id);
    $id_padre=$this->calcolaIdByPromotioncode($p_c);
    $c=new Client($id_padre);
    if($c->isFiglioDelRe())
    {
      $pc=$this->getPromotionCodeById($id_padre);
      $array=$this->getClientiOrdinatiDataPromotioncode($pc);
      for($i=0;$i<count($array);$i++)
      {
        if($i>=2 && $array[$i]==$id_padre)
        return true;
      }//end FOR

        return false;

    }//end IF
    else
    {
      if($c->sonoTraPrimiDueNipoti())
      {
        return $this->verificoGuadagnoRe($id_padre);
      }
      else
      {
        return false;
      }
    }//end ELSE
  }


  /**
   * Calcola univocamente il RE, in qualsiasi condizioni siano i clienti
   * @return [int] [ID customer del RE]
   */
  public function calcolaRe()
  {
    $sql="SELECT * FROM "._DB_PREFIX_."customer WHERE promotion_code IS NULL";
    $results=Db::getInstance()->query($sql);
    $array=array();
       while ($row=Db::getInstance()->nextRow($results))
        {
         if($row)
              array_push($array, $row['firstname'][0].$row['id_customer'].$row['lastname'][0].$row['id_shop']);
        }//END WHILE

    foreach($array as $cliente)
    {
      $sql="SELECT promotion_code FROM "._DB_PREFIX_."customer WHERE promotion_code='".$cliente."'";
      $results=Db::getInstance()->query($sql);
      while ($row=Db::getInstance()->nextRow($results))
       {
            if($row)
                  return $this->calcolaIdByPromotioncode($cliente);
        }//END WHILE
    }//end FOREACH
  }


  /**
   * Restituisce l'id del RE, se salvato (solo in un caso)
   * @return [int] [ID customer del RE]
   */
  public function getRe(){
    return $this->id_re;
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
   * Dato l'id del prodotto seleziono il volume e la ritenuta
   * @param  [int] $id [id_product]
   * @return [Array of Float]     [0=>volume,1=>ritenuta]
   */
  private function getVolumeRitenuta($id)
  {
    $sql="SELECT volume,ritenuta FROM "._DB_PREFIX_."relazione_volume_provvigione WHERE id_product=".$id;
    $results = Db::getInstance()->query($sql);
       while ($row=Db::getInstance()->nextRow($results))
        {
        if($row)
            return array($row['volume'],$row['ritenuta']);
        }
        return null;
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
     * Dato l'id del prodotto cerco il suo nome nel DB
     * @param  [Int] $id [id_product]
     * @return [String]     [name]
     */
    private function getNomeProdottoByIdProduct($id)
    {
      global $cookie;
      $id_lang = $cookie->id_lang;
      $productObj = new Product();
      $products = $productObj -> getProducts($id_lang, 0, 0, 'id_product', 'ASC' );
      foreach($products as $elem)
      {
        if($elem['id_product']==$id)
        return $elem['name'];
      }
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
    private function salvaAcquisto($padre,$name_padre,$figlio,$nome_cognome,
    $prodotto,$nome_prodotto,$total,$provvigione)
    {
      $sql="INSERT INTO ps_acquisti_inviti (id,id_padre,nome_padre,id_figlio,nome_cognome,id_prodotto,nome_prodotto,total_price,provvigione)
       VALUES (null,".$padre.",'".$name_padre."',".$figlio.",'".$nome_cognome."',".$prodotto.",'".$nome_prodotto."',".$total.",".$provvigione.")";

          try {
          Db::getInstance()->query($sql);
          } catch (Exception $e) {

            throw new Exception($e->getMessage());
          }

    }


    /**
      *  PRENDE L'ID DELL'OGGETO TROVA IL PROMOTION CODE
      *  CALCOLA L'ID DEL PADRE DAL PROMOTION CODE
      *  E LO RESTITUISCE
      * @param void
      * @return  int RESTITUISCE L'ID DEL PADRE
      * @author  Riccardo Amadio
      * @copyright FREE
        **/
    public function calcolaIdSuperiore($id)
    {
        $promotion_code=$this->getPromotionCodeById($id);
        return $this->calcolaIdByPromotioncode($promotion_code);
    }



    

}//end..
 ?>
