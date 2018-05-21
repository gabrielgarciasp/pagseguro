<?php

header('Access-Control-Allow-Origin: *');//REMOVER DA VERSÃO FINAL
header('Content-Type: text/html; charset=utf-8');
setlocale(LC_TIME, 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

/*----------------------------------------------------------------------------------------------------------------------
A MAIORIA DOS CAMPOS NÃO ACEITAM CARACTERES ESPECIAIS

CAMPOS COM //* SÃO OBRIGATÓRIOS

OS CAMPOS DE PREÇOS DEVEM SER ENVIADOS COM . E DUAS CASAS DECIMAIS

TIPOS DE FRETE (SHIPPINGTYPE):
1 = ENCOMENDA NORMAL (PAC)
2 = SEDEX
3 = TIPO DE FRETE NÃO ESPECIFICADO

PARA USAR O FRETE DO PAGSEGURO, (ENVIO FÁCIL) É NECESSARIO CONSIGURAR NA CONTA (PREFERÊNCIA -> FRETE)

NO AMBIENTE SANDBOX, NÃO É POSSIVEL VER OS DADOS PESSOIS DO COMPRADOR (NOME, EMAIL...)

É IMPORTANTE SALVAR CÓDIGO DA TRANSAÇÃO, POIS EM OPERAÇÕES COMO EXTORNO OU CANCELAMENTO, É NECESSÁRIO TER O CÓDIGO

LISTA COM TODOS PARÂMETROS
https://m.pagseguro.uol.com.br/v3/guia-de-integracao/api-de-pagamentos.html#!v2-item-api-de-pagamentos-parametros-api
----------------------------------------------------------------------------------------------------------------------*/

if(!isset($_POST["pedido"]))
	exit;

$pedido = $_POST['pedido'];

$data = array(
	//DADOS DO VENDEDOR
	'email' => 'SEU E-MAIL',
	'token' => 'TOKEN',
	//DADOS DO COMPRADOR
	'senderEmail' => 'aanthonymarciodapaz@xerocopiadora.com.br',
	'senderName' => 'Anthony Márcio da Paz',
	'senderAreaCode' => '61',
	'senderPhone' => '997980922',
	'senderCPF' => '41647032539',
	'bornDate' => '06/12/1996',
	'shippingAddressPostalCode' => '71805714',
	'shippingAddressStreet ' => 'Quadra QN 7 Conjunto 14',
	'shippingAddressNumber' => '526',
	'shippingAddressComplement' => '',
	'shippingAddressDistrict' => 'Riacho Fundo I',
	'shippingAddressCity' => 'Brasília',
	'shippingAddressState' => 'DF',//MAIUSCULO
	'shippingAddressCountry' => 'BRA',
	//DADOS DA VENDA
	'currency' => 'BRL',//*
	'extraAmount' => number_format('-4,99', 2, '.', ''),
	'itemId1' => '1',//*
	'itemQuantity1' => '1',//*
	'itemDescription1' => 'Produto 1',//*
	'itemAmount1' => number_format('29,90', 2, '.', ''),//*
	'itemWeight1' => '100',//USADO PARA O PAGSEGURO CALCULAR O FRETE
	'itemId2' => '2',
	'itemQuantity2' => '3',
	'itemDescription2' => 'Produto 2',
	'itemAmount2' => number_format('11,90', 2, '.', ''),
	'itemWeight2' => '50',
	//FRETE
	'shippingType' => '1',//*
	//'shippingCost' => number_format('17,90', 2, '.', ''),//ENVIA O VALOR DO FRETE
	//ADICIONAIS
	'reference' => $pedido,
	'notificationURL' => 'http://SEU_SITE.com.br/pagseguro/lightbox/notificacao.php'
);

$data = http_build_query($data);

$url = 'https://ws.pagseguro.uol.com.br/v2/checkout';
//$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout';

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
$retorno = curl_exec($curl);
curl_close($curl);

//SALVA O RETORNO NO LOG
$fp = fopen('log/log_pedido_'.$pedido.'.txt', 'a');//'a' ABRE O ARQUIVO
fwrite($fp, "---------- GERAÇÃO DO PEDIDO ----------".date('Y-m-d H:i')."\n\n");
fwrite($fp, $retorno."\n\n");
fwrite($fp, "\n\n");
fclose($fp);

//RETORNA O CODIGO DA COMPRA PARA ABRIR O LIGHTBOX
$retorno = simplexml_load_string($retorno);
echo $retorno->code;

?>