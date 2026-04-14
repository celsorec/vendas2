<?php

//Configurações de acesso ao sistema
$aceusItems['aceus'][0]  = 'Acessar Logs';                                 //read_logs
$aceusItems['aceus'][1]  = 'Cadastrar Usuários';                           //form_user
$aceusItems['aceus'][2]  = 'Modificar / Excluir Usuários';                 //search_user       -> form_user (idUpdate)
$aceusItems['aceus'][3]  = 'Cadastrar Itens';                              //form_items
$aceusItems['aceus'][4]  = 'Modificar / Excluir Itens';                    //search_items      -> form_items (idUpdate)
$aceusItems['aceus'][5]  = 'Cadastrar Fornecedores';                       //form_supplier
$aceusItems['aceus'][6]  = 'Modificar / Excluir Fornecedores';             //search_supplier   -> form_supplier (idUpdate)
$aceusItems['aceus'][7]  = 'Solicitar Compras';                            //form_request
$aceusItems['aceus'][8]  = 'Modificar / Excluir Solicitações de Compras';  //grid_request      -> form_request (idUpdate)
$aceusItems['aceus'][9]  = 'Adicionar Cotações de Compras';                //grid_request_next -> form_cotation ($cotation em branco) mesmo ID aceus
$aceusItems['aceus'][10] = 'Alterar Cotações de Compras';                  //grid_quoted       -> form_cotation ($cotation, vindo do banco de dados), mesmo ID aceus
$aceusItems['aceus'][11] = 'Moderar Pedido de Compras';                    //grid_quoted_next  -> form_order (já com cotação adicionada) mesmo ID aceus
$aceusItems['aceus'][12] = 'Alterar Status do Pedido de Compras';          //grid_order        -> form_order (para ver grid de pedidos aprovados)
$aceusItems['aceus'][13] = 'Visualizar Recusadas Solicitações de Compras'; //grid_denied

$aceusItems['aceus'][14] = 'Confrontar Compras com Nota Fiscal';           //grid_denied
/**
 * Em referências às páginas que não têm o sufixo _ntext, as que o têm se diferenciam apenas pela coluna ação
 * Ações: editar (sem o _next) ou avançar etapa (com _next)
 */
//Menus conforme funções acima
$menu[0]['Logs']         = ['read_logs'         => 'Acessar'];
$menu[1]['Usuários']     = ['form_user'         => 'Cadastrar'];
$menu[2]['Usuários']     = ['search_user'       => 'Modificar / Excluir'];
$menu[3]['Itens']        = ['form_items'        => 'Cadastrar'];
$menu[4]['Itens']        = ['search_items'      => 'Modificar / Excluir'];
$menu[5]['Fornecedores'] = ['form_supplier'     => 'Cadastrar'];
$menu[6]['Fornecedores'] = ['search_supplier'   => 'Modificar / Excluir'];
$menu[7]['Compras']      = ['form_request'      => 'Solicitar'];
$menu[8]['Compras']      = ['grid_request'      => 'Solicitações'];
$menu[9]['Compras']      = ['grid_request_next' => 'Adicionar Cotações →']; //grid_request_next -> form_cotation ($cotation em branco) mesmo ID aceus
$menu[10]['Compras']     = ['grid_quoted'       => 'Cotações'];             //grid_quoted       -> form_cotation ($cotation, vindo do banco de dados), mesmo ID aceus
$menu[11]['Compras']     = ['grid_quoted_next'  => 'Moderar →'];            //grid_quoted       -> form_cotation ($cotation, vindo do banco de dados), mesmo ID aceus
$menu[12]['Compras']     = ['grid_order'        => 'Pedidos de Compras'];
$menu[13]['Compras']     = ['grid_denied'       => 'Recusadas'];

//$menu[]['Compras'] = ['form_cotation' => 'Gerenciar Cotações de Compras';        -- Desativado para impedir acesso ao formulário em branco; apenas para edição*
//$menu[]['Compras'] = ['form_order'    => 'Moderar Pedido de Compras';            -- Desativado para impedir acesso ao formulário em branco; apenas para edição*
//$menu[]['Compras'] = ['form_order'    => 'Alterar Status do Pedido de Compras']; -- Desativado para impedir acesso ao formulário em branco; apenas para edição*

//*Esses formulários são parte da sequência do processo de solicitação de compras; Eles dão continuidade a uma solicitação e já
//*carregam dados das solicitações; não podem criar algo. Ex: Só é possível fazer uma cotação se já houver uma solicitação de compra

if(isset($_SESSION['aceus'])) //Variável setada quando usuário faz o login
{
    //Selecionando itens do menu permitidos ao usuário logado
    $checkValues = explode(',', $_SESSION['aceus']);
    $menuAllowed = [];
    foreach($menu as $key => $value)
    {
        if(in_array($key, $checkValues)) $menuAllowed[$key] = $value;
    }

    //Função para conceder ou negar acesso ao usuário
    function aceusAllowed(int $aceusPageId)
    {
        global $checkValues;
        if(!in_array($aceusPageId, $checkValues)) //Se ID da página está no array de acesso
        {
            MessageHelper::setMessage('Acesso negado. Contate o administrador do sistema', 'alert');
            if(isset($_SERVER['HTTP_REFERER']))
            {
                header("Location: " .$_SERVER['HTTP_REFERER']);
            }
            else
            {
                header("Location: index.php");
            }
            exit;
        }
    }
}