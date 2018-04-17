<?php
include 'db.php';


echo "xxxx";

$context = Context::getContext();
$customerId = $context->customer->id;

 
$cliente = new Client($customerId);

$idPadre = $cliente->calcolaIdPadre(); // echo "idpadre="; var_dump($idPadre);

$re = $cliente->calcolaRe();
var_dump($re);


?>
