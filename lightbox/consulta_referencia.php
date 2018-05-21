<?php

$data = array(
	//DADOS DO VENDEDOR
	'email' => 'SEU E-MAIL',
	'token' => 'TOKEN',
	//REFERENCIA
	'reference' => '10',
	//PERIODO (OPCIONAL) INTERVALO MÁXIMO DE 30 DIAS
	//'initialDate' => '2018-05-01T00:00',
	//'finalDate' => '2018-05-30T00:00'//NÃO PODE SER MAIOR QUE O DIA ATUAL
);

$data = http_build_query($data);//PREPARA A ARRAY PARA SER ENVIADA COMO GET

$url = 'https://ws.pagseguro.uol.com.br/v2/transactions'.'?'.$data;
//$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions'.'?'.$data;

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);
$retorno = curl_exec($curl);
curl_close($curl);

$retorno = simplexml_load_string($retorno);
echo $retorno->transactions->transaction->status;
?>