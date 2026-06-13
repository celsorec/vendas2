<?php

$menu =
[
    [
        //Página inicial
        'label' => 'Início',
        'url'   => '?view=home',
        'class' => 'home-menu',
        'child' => []
    ],
    [
        //Vendas; carrinho de compras
        'label' => 'Vendas',
        'url'   => '?view=orders',
        'class' => 'orders-menu',
        'child' => []
    ],
    [
        //Consultar estoque
        'label' => 'Estoque',
        'url'   => '?view=stock',
        'class' => 'stock-menu disabled',
        'child' => []
    ],
    [
        //Trocar produtos
        'label' => 'Troca',
        'url'   => '?view=swap',
        'class' => 'swap-menu disabled',
        'child' => []
    ],
    [
        //Mais opções do menu principal
        'label' => 'Mais',
        'url'   => '#',
        'class' => 'plus-menu disabled',
        'child' =>
        [
            [
                'label' => 'Sair do Sistema',
                'url'   => 'auth.php?logout=logout',
                'class' => 'load'
            ],
            [
                'label' => 'Ajuda',
                'url'   => '#',
                'class' => 'disabled hidden'
            ]
        ]
    ]
];

//Nome da view para ativar menu
$activeMenu = $_GET['view'] ?? '';

//RENDER MENU
$html = '<ul class="menu">';
foreach($menu as $item)
{
    //Ativando menu (class active) compatível com o parâmetro view
    if($activeMenu === str_replace('?view=', '', $item['url'])) $item['class'] .= ' active';

    $html .= '<li>';
    $html .= '<a href="'.$item['url'].'" class="'.$item['class'].'">';
    $html .= '<span class="icon"></span>';
    $html .= '<span class="text">'.$item['label'].'</span>';
    $html .= '</a>';

    //Se tem submenu
    if(!empty($item['child']))
    {
        $html .= '<ul class="submenu">';
        foreach($item['child'] as $child)
        {
            $html .= '<li><a class="'.$child['class'].'" href="'.$child['url'].'">'.$child['label'].'</a></li>';
        }
        $html .= '</ul>';
    }
    $html .= '</li>';
}
$html .= '</ul>';
return $html;