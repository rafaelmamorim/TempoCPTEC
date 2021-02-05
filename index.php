<?php
/**
 * Previsão do tempo
 * 
 * @copyright 2021 Rafael Amorim <www.rafaelamorim.com.br>
 * @license GPLv3
 * Referencia: http://servicos.cptec.inpe.br/XML/
 * 
 * 
 * Variaveis possíveis de serem informadas na url: 
 * >>>CIDADE: Código que pode ser obtito em http://servicos.cptec.inpe.br/XML/listaCidades 
 * (para principais cidades) ou ainda este código pode ser localizado via url 
 * http://servicos.cptec.inpe.br/XML/listaCidades?city=santana%20do%20livramento
 * Caso não seja informado um código de cidade, o código utilizará o código 4679 
 * para fins de demonstração do script
 * 
 * >>>ESTILO: Arquivo css que deve ser utilizado para dar a formatação a página.
 * Caso não seja informado um, será carregado o arquivo estilo.css
 * 
 * >>>MODO: Quantidade de dias de previsão a ser exibido. Valores aceitáveis são 
 * 1 (4 dias de previsão) ou 2 (7 dias de previsão)
 * 
 * 
 */
// AVISO: QUALQUER MODIFICAÇÃO NO CÓDIGO ABAIXO É POR SUA CONTA E RISCO
//                           TEJE AVISADO :-)

$config['cidade'] = isset($_GET['cidade']) ? $_GET['cidade'] : '4679';
$config['estilo'] = isset($_GET['estilo']) ? $_GET['estilo'] . ".css" : 'estilo.css';

$rModo = isset($_GET['modo']) ? $_GET['modo'] : "1";
switch ($rModo) {
    case "1":
        $config['url'] = "http://servicos.cptec.inpe.br/XML/cidade/" . $config['cidade'] . "/previsao.xml";
        break;
    case "2":
        $config['url'] = "http://servicos.cptec.inpe.br/XML/cidade/7dias/" . $config['cidade'] . "/previsao.xml";
        break;
    default:
        echo "<H1>ERRO: VARIÁVEL \"MODO\" DEFINIDA INCORRETAMENTE</H1>";
        exit();
        break;
}
$xml = simplexml_load_file($config['url']);

// outras configurações
$config['formatoData'] = 'd/m/Y';

$tempo['condicao']['ec'] = 'Encoberto com Chuvas Isoladas';
$tempo['condicao']['ci'] = 'Chuvas Isoladas';
$tempo['condicao']['c'] = 'Chuva';
$tempo['condicao']['in'] = 'Instável';
$tempo['condicao']['pp'] = 'Poss. de Pancadas de Chuva';
$tempo['condicao']['cm'] = 'Chuva pela Manhã';
$tempo['condicao']['cn'] = 'Chuva a Noite';
$tempo['condicao']['pt'] = 'Pancadas de Chuva a Tarde';
$tempo['condicao']['pm'] = 'Pancadas de Chuva pela Manhã';
$tempo['condicao']['np'] = 'Nublado e Pancadas de Chuva';
$tempo['condicao']['pc'] = 'Pancadas de Chuva';
$tempo['condicao']['pn'] = 'Parcialmente Nublado';
$tempo['condicao']['cv'] = 'Chuvisco';
$tempo['condicao']['ch'] = 'Chuvoso';
$tempo['condicao']['t'] = 'Tempestade';
$tempo['condicao']['ps'] = 'Predomínio de Sol';
$tempo['condicao']['e'] = 'Encoberto';
$tempo['condicao']['n'] = 'Nublado';
$tempo['condicao']['cl'] = 'Céu Claro';
$tempo['condicao']['nv'] = 'Nevoeiro';
$tempo['condicao']['g'] = 'Geada';
$tempo['condicao']['ne'] = 'Neve';
$tempo['condicao']['nd'] = 'Não Definido';
$tempo['condicao']['pnt'] = 'Pancadas de Chuva a Noite';
$tempo['condicao']['psc'] = 'Possibilidade de Chuva';
$tempo['condicao']['pcm'] = 'Possibilidade de Chuva pela Manhã';
$tempo['condicao']['pct'] = 'Possibilidade de Chuva a Tarde';
$tempo['condicao']['pcn'] = 'Possibilidade de Chuva a Noite';
$tempo['condicao']['npt'] = 'Nublado com Pancadas a Tarde';
$tempo['condicao']['npn'] = 'Nublado com Pancadas a Noite';
$tempo['condicao']['ncn'] = 'Nublado com Poss. de Chuva a Noite';
$tempo['condicao']['nct'] = 'Nublado com Poss. de Chuva a Tarde';
$tempo['condicao']['ncm'] = 'Nubl. c/ Poss. de Chuva pela Manhã';
$tempo['condicao']['npm'] = 'Nublado com Pancadas pela Manhã';
$tempo['condicao']['npp'] = 'Nublado com Possibilidade de Chuva';
$tempo['condicao']['vn'] = 'Variação de Nebulosidade';
$tempo['condicao']['ct'] = 'Chuva a Tarde';
$tempo['condicao']['ppn'] = 'Poss. de Panc. de Chuva a Noite';
$tempo['condicao']['ppt'] = 'Poss. de Panc. de Chuva a Tarde';
$tempo['condicao']['ppm'] = 'Poss. de Panc. de Chuva pela Manhã';

// FUNÇÕES AUXILIARES
function riscoUV(int $valor) {
    $saida = "";
    if ($valor <= 2) {
        $saida = "Baixo";
    }
    if ($valor >= 3 || $valor <= 5) {
        $saida = "Moderado";
    }
    if ($valor >= 6 || $valor <= 7) {
        $saida = "Alto";
    }
    if ($valor >= 8 || $valor <= 10) {
        $saida = "Muito alto";
    }
    if ($valor >= 11) {
        $saida = "Extremo";
    }
    return $saida;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8"/>
        <title>Previsão do tempo</title>
        <link rel="stylesheet" href="<?= $config['estilo']; ?>" />
        <link rel="apple-touch-icon" sizes="180x180" href="imagens/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="imagens/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="imagens/favicon-16x16.png">
        <link rel="manifest" href="imagens/site.webmanifest">
    </head>
    <body>
        <h1> Previsão do tempo em <?= $xml->nome . "/" . $xml->uf; ?></h1>
        <h2> Atualizado em <?= date_format(date_create($xml->atualizacao), $config['formatoData']) ?> </h2>
        <div class="previsao">
            <? foreach ($xml->previsao as $prev) : ?>
            <div class="tempo">
                <img src="imagens/<?= $prev->tempo ?>.png">
                <h3><?= date_format(date_create($prev->dia), $config['formatoData']) ?></h3>
                <p><?= $tempo['condicao'][strval($prev->tempo)] ?></p>

                <div class="maxima">
                    <img src="imagens/hightemp-32.png">&nbsp;
                    <?= $prev->maxima ?>º C
                </div>
                <div class="minima">
                    <img src="imagens/lowtemp-32.png">&nbsp;
                    <?= $prev->minima ?>º C
                </div>
                <div class="iuv">
                    <img src="imagens/uv-32.png">&nbsp;
                    UV <?= riscoUV(intval($prev->iuv)) ?>
                </div>
            </div>
            <? endforeach; ?>
        </div>
        <div class="fonte">
            Autor: <a href="https://www.rafaelamorim.com.br/" target="_blank">Rafael Amorim</a><br/>
            Dados: <a href="https://www.cptec.inpe.br/" target="_blank">CPTEC/INPE</a>
        </div>
    </body>
</html>