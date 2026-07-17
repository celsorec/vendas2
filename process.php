<?php

require_once 'settings.php';
require_once 'Core/Database/DataRecord.php';
require_once 'Core/Message/MessageHelper.php';

$connect = new DataRecord();
$pdo     = $connect->getConnection();
$data    = filter_input_array(INPUT_POST, FILTER_SANITIZE_ADD_SLASHES);

//Formulário enviado sem produto. Quem faria isso? [risos]
if(!isset($data['codpr']) || !isset($data['movnc']))
{
    MessageHelper::setMessage('Ooops! Não foi localizado nenhum produto no seu carrinho de compras.', 'alert');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

//Referências aos nomes das tabelas do banco de dados e numbl para montar código do Faturamento (numbl . data['vende'])
$empre = $connect->read(['exerc', 'codem', 'numbl'], 'empre');
$exerc = $empre[0]['exerc'].$empre[0]['codem'];
$numbl = $empre[0]['numbl'];

if($empre && isset($empre[0]['numbl']))
{
    //Buscando parâmetro para compoição de MOVFA (se MOVFA tem em sua composição o número do vendedor)
    $param = $connect->read(['param'], 'param');
    $param = $param[0]['param'][486];

    //Montagem do código do Faturamento com ou sem código do vendedor (MOVFA + CODVE)
    if($param === 'S') $movfa = ($numbl+1).$_SESSION['codve'];
    else $movfa = $numbl+1; //Sem código do vendedor
    $data['movfa'] = str_repeat(' ', 10 - strlen($movfa)) . $movfa;   
}
else
{
    MessageHelper::setMessage('Não foi possível obter o número de faturamento', 'alert');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

//Comprovante de impressão e Configurações de pagamento
require_once 'app/modules/config-printing.php';
require_once 'app/modules/config-checkout.php';

//Definindo valores padrão
$data['movda'] = date('Y-m-d');
$data['movpe'] = 'C';
$data['movtp'] = 'S';
$data['comis'] = '3';
$data['vende'] = $_SESSION['codve'];
$data['movfu'] = $_SESSION['codve'];

//CONVERTENDO NOMES: NOME ORIGINAL VEM DA TABELA DE ORIGEM, NOVO NOME É PARA TABELA DE DESTINO
$data['movfo'] = $data['codcl']; //Cliente             -> string
$data['movpr'] = $data['codpr']; //Códigos do produto  -> array
$data['movct'] = $data['prcpr']; //Preços de custo (?) -> array
$data['movvc'] = $data['venpr']; //Preços de venda (?) -> array

//Removendo convertidos acima
unset($data['codcl']);
unset($data['codpr']);
unset($data['prcpr']);
unset($data['venpr']);
unset($data['desco']);

//Removendo campos informativos
unset($data['clien01']);   //Nome da tabela para busca ajax
unset($data['produ01']);   //Nome da tabela para busca ajax
unset($data['nompr']);     //Nome do produto; OBS: Lá na frente eu busco novamente, mas deveria ter aproveitado essa informação, não deveria tê-la removido
unset($data['subtotal']);  //Cálculo para cada produto; deveria ser removido?

//GRAVAÇÃO NO BANCO DE DADOS
try
{
    $pdo->beginTransaction(); //Iniciada a transação
    $data['movtb'] = $data['movvc'];
    $data['movlj'] = '01';

    //Atualizando numbl na tabela empre
    $result = $connect->update('empre', ['numbl' => $numbl+1], '1');
    if($result !== true) throw new Exception('Não foi possível atualizar os dados da venda.');

    //TABELA GRD_ANO
    $dataGrade = [];
    foreach($data['movpr'] as $key => $value)
    {
        $infoGrade['progr'] = $data['movpr'][$key];
        $infoGrade['docgr'] = $data['movfa'];
        $infoGrade['gragr'] = $data['gragr'][$key];
        $infoGrade['quagr'] = $data['movqt'][$key];
        $infoGrade['tipgr'] = 'S';
    
        $dataGrade[] = $infoGrade;
    }
    
    foreach($dataGrade as $value)
    {
        $result = $connect->save('grd'.$exerc, $value);
        if($result !== true) throw new Exception('Falha ao salvar na tabela grd'.$empre);
    }
    
    //Campos nulos
    $data['movca'] = 0.00;
    $data['movdp'] = 0.00;
    $data['fjuro'] = 0.00;
    $data['fepre'] = 0.00;
    $data['fcoef'] = 0.00;
    $data['mcomp'] = 0.00;
    $data['mlarg'] = 0.00;
    $data['caxme'] = 0.00;

    //Percorrendo e separando cada produto para gravação em banco de dados
    $dataProducts = [];
    foreach($data['movpr'] as $index => $item)
    {
        $dataSave = [];
        foreach($data as $key => $value)
        {
            if(is_array($value))
            {
                //Converter movqt (qtd) para float para o banco de dados
                if($key == 'movqt') $dataSave[$key] = number_format($value[$index], 3);
                else $dataSave[$key] = $value[$index];
            }
            else
            {
                //Selecionado apenas o código do cliente para salvar no banco de dados
                if($key == 'movfo')
                {
                    $movfo = explode(' - ', $value,);
                    $movfo = $movfo[0];
    
                    $dataSave[$key] = $movfo;
                }
                else $dataSave[$key] = $value;
            }    
        }
        $dataProducts[] = $dataSave;
    }

    //Somando quantidade de produtos que têm o mesmo código e portanto são o mesmo produto para a tabela movYYYY (mesmo com grades diferentes)
    $movprGroup = [];
    foreach($dataProducts as $value)
    {
        $movpr = $value['movpr'];
        
        //Se produto ainda não foi adicionado ao novo array, adiciona ele completo
        if(!isset($movprGroup[$movpr]))
        {
            $movprGroup[$movpr] = $value;
            $movprGroup[$movpr]['movqt'] = 0; //Zera quantidade para iniciar soma
        }
        
        //Acumula quantidade
        $movprGroup[$movpr]['movqt'] += $value['movqt'];
    }

    //Sobrescreve array original por produtos únicos e somados
    $dataProducts = array_values($movprGroup);

    //Salvando produtos no banco de dados
    foreach($dataProducts as $key => $value)
    {
        //Removendo gragr já gravado na tabela grdYYYY
        array_shift($value);
    
        //Banco de dados
        $result = $connect->save('mov'.$exerc, $value);
        if($result !== true) throw new Exception('Falha ao salvar produto');
    }

    //Commit
    $pdo->commit();
    
    //Venda realizada: limpando carrinho de compras e checkout; voltando à página anterior
    echo
    "<script>
        localStorage.removeItem('checkoutItems');
        localStorage.removeItem('productsList');
        window.location.href = 'index.php?view=home';
    </script>";

    MessageHelper::setMessage('Venda realizada com sucesso', 'success');
}
catch(Exception $e)
{
    if($pdo->inTransaction()) $pdo->rollBack();
    MessageHelper::setMessage('ERRO: ' . $e->getMessage(), 'alert');
    header("Location: " . $_SERVER['HTTP_REFERER']);
}