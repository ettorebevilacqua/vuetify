<?php


include 'db.php';

die('has insert delete this die');

function insert($id, $nome, $cognome, $mail, $referal){
	
$sql="INSERT INTO `ps_customer` (`id_customer`, `id_shop_group`, `id_shop`, `id_gender`, `id_default_group`, `id_lang`, `id_risk`, `company`, `siret`, `ape`, `firstname`, `lastname`, `email`, `passwd`, `last_passwd_gen`, `birthday`, `newsletter`, `ip_registration_newsletter`, `newsletter_date_add`, `optin`, `website`, `outstanding_allow_amount`, `show_public_prices`, `max_payment_days`, `secure_key`, `note`, `active`, `is_guest`, `deleted`, `date_add`, `date_upd`, `promotion_code`, refer_id) 
	VALUES ($id, '1', '1', '0', '3', '1', '0', NULL, NULL, NULL, '$cognome', '$cognome', '$mail', 'pass', '2017-12-20 09:33:49', '2013-03-05', '0', NULL, '0000-00-00 00:00:00', '0', NULL, '0.000000', '0', '0', '75b3fa9103deba3c8ab8f66b26ad6300', NULL, '1', '0', '0', '2017-12-20 15:33:49', '2017-12-20 15:33:49', '1', $referal);";

	return Db::getInstance()->Execute($sql);       
}

$list=[];
$list[]=['10001','root','root','mail@test.it',null];
$list[]=['10003','nome','a','mail@test.it',10001];
$list[]=['10004','nome','b','mail@test.it',10001];
$list[]=['10005','nome','c','mail@test.it',10001];
$list[]=['10006','nome','d','mail@test.it',10001];
$list[]=['10007','nome','e','mail@test.it',10001];

$list[]=['10008','nome','a1','mail@test.it',10003];
$list[]=['10009','nome','a2','mail@test.it',10003];
$list[]=['10010','nome','a3','mail@test.it',10003];
$list[]=['10011','nome','a4','mail@test.it',10003];
$list[]=['10012','nome','a5','mail@test.it',10003];

$list[]=['10013','nome','a11','mail@test.it',10008];
$list[]=['10014','nome','a12','mail@test.it',10008];
$list[]=['10015','nome','a13','mail@test.it',10008];
$list[]=['10016','nome','a14','mail@test.it',10008];

$list[]=['10017','nome','a111','mail@test.it',10013];
$list[]=['10018','nome','a112','mail@test.it',10013];
$list[]=['10019','nome','a113','mail@test.it',10013];
$list[]=['10020','nome','a114','mail@test.it',10013];

$list[]=['10017','nome','a121','mail@test.it',10014];
$list[]=['10018','nome','a122','mail@test.it',10014];
$list[]=['10019','nome','a123','mail@test.it',10014];
$list[]=['10020','nome','a124','mail@test.it',10014];

$list[]=['10017','nome','a131','mail@test.it',10015];
$list[]=['10018','nome','a132','mail@test.it',10015];
$list[]=['10019','nome','a133','mail@test.it',10015];
$list[]=['10020','nome','a134','mail@test.it',10015];


$list[]=['10121','nome','b1','mail@test.it',10004];
$list[]=['10122','nome','b2','mail@test.it',10004];
$list[]=['10123','nome','b3','mail@test.it',10004];
$list[]=['10124','nome','b4','mail@test.it',10004];

$list[]=['10135','nome','b11','mail@test.it',10121];
$list[]=['10136','nome','b12','mail@test.it',10121];
$list[]=['10137','nome','b13','mail@test.it',10121];
$list[]=['10138','nome','b14','mail@test.it',10121];

$list[]=['10229','nome','c1','mail@test.it',10005];
$list[]=['10230','nome','c2','mail@test.it',10005];
$list[]=['10231','nome','c11','mail@test.it',10005];
$list[]=['10232','nome','c12','mail@test.it',10005];

$list[]=['10333','nome','d1','mail@test.it',10006];
$list[]=['10334','nome','d2','mail@test.it',10006];
$list[]=['10335','nome','d11','mail@test.it',10006];
$list[]=['10336','nome','d111','mail@test.it',10006];

echo "<pre>";
foreach($list as $user){
	if (insert($user[0], $user[2], $user[2], $user[3], $user[4])) 
		echo "\n insert ".$user[0];
	else echo "\n error ".$user[0];
}

?>
