<?php

header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");
header('Content-Type: text/html; charset=utf-8');
setlocale(LC_TIME, 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

if(!isset($_POST["notificationCode"]))
	exit;

$notificationCode = preg_replace('/[^[:alnum:]-]/','',$_POST["notificationCode"]);

$data = array(
	'token' => 'SEU TOKEN',
	'email' => 'SEU E-MAIL'
);

$data = http_build_query($data);

$url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?'.$data;
//$url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?'.$data;

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);
$retorno = curl_exec($curl);
curl_close($curl);

$retorno2 = simplexml_load_string($retorno);

//SALVA O RETORNO NO LOG
$fp = fopen('log/log_pedido_'.$retorno2->reference.'.txt', 'a');//'a' ABRE O ARQUIVO
fwrite($fp, "---------- RETORNO ----------".date('Y-m-d H:i')."\n\n");
fwrite($fp, 'NOTIFICATION CODE: '.$notificationCode."\n\n");
fwrite($fp, $retorno);
fwrite($fp, "\n\n");
fclose($fp);

/*----------------------------------------------------------------------------------------------------------------------
STATUS:
1 = AGUARDANDO PAGAMENTO
2 = EM ANÁLISE
3 = PAGA
4 = DISPONÍVEL
5 = EM DISPUTA
6 = DEVOLVIDA
7 = CANCELADA
8 = DEBITADO
9 = RETENÇÃO TEMPORÁRIA

REFERÊNCIA:
https://dev.pagseguro.uol.com.br/documentacao/pagamento-online/notificacoes/api-de-notificacoes
----------------------------------------------------------------------------------------------------------------------*/

?>