
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//include(PS_ADMIN_DIR.'/../etto/class_etto_referal.php');
// include_once (_PS_ROOT_DIR_.'/etto/class_cashback.php');
  $global_Tot_sconto=0; $global_Tot_maturato= array();
  $global_Tot_maturato['parziale']=0;
  $global_Tot_maturato['totale']=0;
  

function getSconto($subId){
    global $global_Tot_sconto;
    

    $query="SELECT distinct sum( total_products_wt * perc_sconto/100 ) as sconto FROM `ps_orders` WHERE id_customer=$subId";
    $dbTotali= Db::getInstance()->ExecuteS($query); //print_r($ris);

     $sconto=$dbTotali[0]['sconto'];
     if (!$sconto || $sconto==null) {$sconto=0; }
     $global_Tot_sconto=$global_Tot_sconto+$sconto; return $sconto;

}


//Original by ETTO modificata by FRA 08/06/2012
function getMaturatoCustomer($subId){
    global $global_Tot_maturato;
    

    $query="SELECT distinct sum( total_products_wt * perc_maturato_indiretto/100 ) as maturato FROM `ps_orders` WHERE id_customer=$subId";
    $dbTotali= Db::getInstance()->ExecuteS($query); //print_r($ris);

     $maturato=$dbTotali[0]['maturato'];
     if (!$maturato || $maturato==null) {$maturato=0; }
     
     $global_Tot_maturato['parziale']=$maturato;
     
     return $global_Tot_maturato['parziale'];

}

// by FRA 08/06/2012
function getMaturatoTot($subId){
    global $global_Tot_maturato;
    

    $query="SELECT distinct sum( total_products_wt * perc_maturato_indiretto/100 ) as maturato FROM `ps_orders` WHERE id_customer=$subId";
    $dbTotali= Db::getInstance()->ExecuteS($query); //print_r($ris);

     $maturato=$dbTotali[0]['maturato'];
     if (!$maturato || $maturato==null) {$maturato=0; }
     $global_Tot_maturato['totale']=$global_Tot_maturato['totale']+$maturato; 
     
   
     
     return ;

}



// by FRA 04/06/2012 verifico se i figli del nodo iesimo sono tra i primi due, nel caso li associo al parent del nodo, ovvero il NONNO.


/*
SELECT  `id_customer` ,  `refer_id` ,  `payid` ,`date_add`,`date_upd`
FROM  `ps_customer` 
WHERE 1 =1
ORDER BY  `ps_customer`.`id_customer` ASC 
LIMIT 0 , 300
*/

function setPayidCustomerFra($id_customer,$dbg_param)
{
     
    
    if ( (isset($_GET['asb'])) or ($dbg_param) ) $dbg=1;
    else $dbg=0;
    
    // LEGGO IL  customer 
    $query="SELECT * FROM `ps_customer` WHERE id_customer= ".$id_customer;
    $ris= Db::getInstance()->ExecuteS($query);
    if (!$ris)  { die('ERROR - LEGGO IL PADRE<br>'.$query);return 0;}

    if($dbg) echo'<br><span style="font-family:arial;color:red;font-size:14px;">Cliente: '.$ris[0]['firstname'].' - '.$ris[0]['lastname'].' ->id = '.$id_customer.'</span>';
   
    
    
    $id_padre = $ris[0]['refer_id'];
    
    
    if($id_padre == 0) //IMPLICA CHE SONO AL LIVELLO 0
    {
        echo'<br>'; // IL PAYID di CUSTOMER RESTA ZERO PERCHE' Valore di default
        return;
    
    }
    
    // LEGGO IL NOME DEL PADRE
    $query="SELECT * FROM `ps_customer` WHERE id_customer= ".$id_padre;
    $ris= Db::getInstance()->ExecuteS($query);
    if($dbg) echo'<br> -- REFERAL: '.$ris[0]['firstname'].' - '.$ris[0]['lastname'].' - id_padre = '.$id_padre;
    
    $payid_dal_padre=$ris[0]['payid']; //echo'<br> ---- Payid_padre = '.$payid_dal_padre;
    
    
    if ((int)$id_padre == (int)(19)) // UTENTE MIMMO LIVELLO 0
    {   
         // LEGGO TUTTI le linee del 19
        $query="SELECT id_customer FROM `ps_customer` WHERE refer_id = ".$id_padre;
        $ris= Db::getInstance()->ExecuteS($query);
        $info_linee_livello1 = $ris;    //print_r($info_zio);
        if($dbg);// echo'<br> <span style="font-family:arial;color:blue;font-size:12px;">CUSTOMER CON TOTALE zii '.count($info_zio).'</span><br>';
        
        sort($info_linee_livello1); // ORDINO IN MANIERA CRESCENTE L'array fra 
         
        for($i=0;$i<count($info_linee_livello1);$i++)
        {
            if($i<2)
            {   
                if( ($id_customer == $info_linee_livello1[$i]['id_customer'])  )
                {
                    
                    $payid = $id_customer;
                    
                    $query="UPDATE `ps_customer` SET payid = ".$payid." WHERE id_customer = ".$id_customer; $ris= Db::getInstance()->Execute($query);       

                    if($dbg) echo'<br><span style="font-family:verdana;color:green;font-size:12px;"> ->LINEA ESCLUSA DA (19) <br>LIVELLO 1 - CUSTOMER TRA I PRIMI 2 => SET PAYID('.$payid.')  id='.$id_customer.'</span><br>';

                    return;      



                }
            }
            else
            {   
                $payid = 0;
                    
                $query="UPDATE `ps_customer` SET payid = ".$payid." WHERE id_customer = ".$id_customer; $ris= Db::getInstance()->Execute($query);       

                if($dbg) echo'<br><span style="font-family:verdana;color:green;font-size:12px;"> ->LINEA *INCLUSA* DA (19) <br>LIVELLO 1 - CUSTOMER MAGGIORE I PRIMI 2 => SET PAYID('.$payid.')  id='.$id_customer.'</span><br>';

                return;

            }
                
        }

         
         
     } 
 
    
    // LEGGO IL NONNO di customer 
    $query="SELECT refer_id FROM `ps_customer` WHERE id_customer= ".$id_padre;
    $ris= Db::getInstance()->ExecuteS($query);
    if (!$ris)  { die('ERROR - LEGGO IL NONNO<br>'.$query);return 0;}
    
    $id_nonno = $ris[0]['refer_id'];
    
    if($id_nonno == 0) // IMPLICA CHE SONO AL LIVELLO 1
    {
        echo'<br>'; // IL PAYID di CUSTOMER RESTA ZERO PERCHE' Valore di default
        return;
    
    }
    
    // LEGGO IL NOME DEL NONNO
    $query="SELECT * FROM `ps_customer` WHERE id_customer= ".$id_nonno;
    $ris= Db::getInstance()->ExecuteS($query);
    if($dbg) echo'<br> -- NONNO:  '.$ris[0]['firstname'].' - '.$ris[0]['lastname'].' - id_nonno = '.$id_nonno;
   
    

    
    // LEGGO TUTTI I FRATELLI DEL CUSTOMER; il Primo e il secondo, non diventano nipoti legittimi, CIO' VALE SOLO PER IL LIVELLO 2
    $query="SELECT id_customer FROM `ps_customer` WHERE refer_id = ".$id_padre;
    $ris= Db::getInstance()->ExecuteS($query);
    $info_fratello = $ris;    //print_r($info_fratello);
    if($dbg) echo'<br> <span style="font-family:arial;color:blue;font-size:12px;">CUSTOMER CON TOTALE FRATELLI '.count($info_fratello).'</span><br>';
                  

    
    // SETTO IL PAYID DA EREDITARE
    if($payid_dal_padre==0) {$payid =  $id_nonno;}
    else {$payid =  $payid_dal_padre;}   



 
    sort($info_fratello); // ORDINO IN MANIERA CRESCENTE L'array fra 
    //print_r($info_fratello);
  
    for($i=0;$i<count($info_fratello);$i++)
    {   

        if($i < 2) 
        {   
            if($id_customer == $info_fratello[$i]['id_customer']) // SE IL CUSTOMER E' Primo tra i fretelli
            {
                if($dbg) echo'<br> ---- $payid = '.$payid.'<br>';
  
                 $query="UPDATE `ps_customer` SET payid = ".$payid." WHERE id_customer = ".$id_customer; $ris= Db::getInstance()->Execute($query);       

                 if($dbg) echo'<span style="font-family:verdana;color:green;font-size:12px;"> -> CUSTOMER TRA I PRIMI 2 => SET PAYID('.$payid.') Nipote Leggittimo('.$i.') id='.$id_customer.'</span><br>';
                
                 return;    

                
            }
           
    
    
        }
        else
        {    if($dbg)  echo'<br> ---- $payid = 0 <br>';

            $query="UPDATE `ps_customer` SET payid = 0 WHERE id_customer = ".$id_customer; $ris= Db::getInstance()->Execute($query);       

            if($dbg) echo'<span style="font-family:verdana;color:green;font-size:12px;"> -> CUSTOMER NON TRA I PRIMI 2 => SET PAYID(0) Nipote ILLEGITTIMO*('.$i.') id='.$id_customer.'</span><br>';

            return;  
        }

    }    
    
    
    return;

}



function viewTreeCustomer($id_customer,$payid,$livello,$showStruct=true)
{
    global $global_Tot_maturato; 
    
   //  echo "<br>viewTreeCustomer start";
    if(isset($_GET['dbg']) && ($_GET['dbg']==1)) $dbg=1;
    else $dbg=0;
    
    $dbg=1;
    
   
    $tot_maturato_linee =0;

    // LEGGO TUTTE LE LINEE - (figli diretti CUSTOMER); 
    $query="SELECT * FROM `ps_customer` WHERE refer_id = ".$id_customer." AND payid= ".$payid; // echo'<br> query'.$query;
    $ris= Db::getInstance()->ExecuteS($query);
    
    
    $linea = $ris;
 
    
    $margin = (($livello*20));
    $style = "margin-left: ".$margin."px; ";

    ?>
         <div style="<?php echo $style ?>; "> 
    <?php
    
           
  //  echo "<br>viewTreeCustomer start xx style=$style";
    for($i=0;$i<count($linea);$i++)
    {
       // echo'<br><span style="font-family:arial;color:red;font-size:12px;background-color:#ffffff;">'.$livello.'° - LIVELLO - linea('.$i.') ------> id='.$linea[$i]['id_customer'].' - '.$linea[$i]['firstname'].' - '.$linea[$i]['lastname'].'</span> - <br>';
        
        $tot_maturato_linee += getMaturatoCustomer($linea[$i]['id_customer']); 
           
            
        if($showStruct)
        {   
            
            if($livello > 1)$margin= (($livello*20));
            else $margin = 20;
            $style = "margin-left: ".$margin."px;";

  
            ?>  <div > 
                <table cellspacing="1"  width="90%" style="<?php echo $style ?>; ">
                    <tr>
                        <?php
                        if($livello >0 ){
                        echo '<td  style="">';
                          
                        
                  
                         echo "<br><li style='font-family:arial;color:black;font-size:12px; margin-left: 0px;' >".strtoupper($linea[$i]['firstname'])." - ".strtoupper($linea[$i]['lastname'])."</li>";
                         if($dbg)echo "<span style='font-family:arial;color:narrow;font-size:10px;margin-left: 0px;'>ID". "(".$linea[$i]['id_customer'].") - LIVELLO ".($livello + 1)." - LINEA (".($i+1).")".'</span>';
                        }
                        elseif ($livello == 0) {
                        
                        echo '<td  style="background-color:#ffffcc;">';
   
                         echo "<span style='font-family:arial;color:red;font-size:12px; margin-left: 0px;' >".strtoupper($linea[$i]['firstname'])." - ".strtoupper($linea[$i]['lastname'])."</span><br>";
                         if($dbg)echo "<span style='font-family:arial;color:narrow;font-size:10px; margin-left: 0px;'>ID". "(".$linea[$i]['id_customer'].") - LIVELLO ".($livello + 1)." - LINEA (".($i+1).")".'</span><br><hr>';
                        
                        }
                        
                        ?>
                        </td>
                    
                    </tr>
                </table>
            </div>
            <?php
        }
        
        // SETTO IL PAYID
        if( ($linea[$i]['payid'] == 0) ){$payid= $id_customer;}
        else{$payid=$linea[$i]['payid'];}
        
        viewTreeCustomer($linea[$i]['id_customer'],$payid,$livello+1,true);

    }

    
    if ($showStruct) {
              
       if($livello >1 ) echo "<p  style='font-family:arial;color:narrow;font-size:10px;  margin-left: -27px; '>TOTALE MATURATO LIV. (".($livello)."): ".Tools::displayPrice((float) ($tot_maturato_linee  ) )."</p>";
       elseif ($livello == 1) echo "<br><h4  style='font-family:arial;color:black;font-size:12px; background-color:#ffffff; margin-left: 0px; width: 93%;'>TOTALE LIV. (".($livello)."): ".Tools::displayPrice((float) ($tot_maturato_linee ) )."<hr></h4><br><br>";
       
       

       
       
       ?>  </div><?php
    }

    
    return;
    
}

// by FRA 06/06/2012

function viewScontoCustomer($id_customer)
{
    // LEGGO CUSTOMER mod. by FRA 19/06/2012
    $query="SELECT * FROM `ps_customer` WHERE id_customer = ".$id_customer ; // echo'<br> query'.$query;
    $info= Db::getInstance()->ExecuteS($query);
    
    $tot_sconto = getSconto($id_customer);
    
    // mod. by Fra 19/06/2012 - Visualizzo il customer senza i figli

    echo "<div style='background-color: #ffffcc;'>
        <span style='font-family:arial;color:red;font-size:12px; background-color:#ffffcc; margin-left: 0px;' >".strtoupper($info[0]['firstname'])." - ".strtoupper($info[0]['lastname'])." - ID ". "(".$info[0]['id_customer'].")</span></div>";
    //echo "<h4  style='font-family:arial;color:black;font-size:12px; background-color:#ffffff; margin-left: 0px; '> BONUS ACQUISTI: ".Tools::displayPrice((float) ($tot_sconto ) )."<hr></h4><br><br>";

 
}
// FINE FRA






?>
