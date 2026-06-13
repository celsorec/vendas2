<?php

class MenuHelper
{
    private $items = [];
    private $class;

    public function __construct(array $items, string $class='')
    {
        $this->items = $items;
        $this->class = $class;
    }

    public function renderItems(): string
    {
        $groupMenu = [];
        $finalMenu = [];

        //Obtendo grupos de menus
        foreach($this->items as $key => $value)
        {
            if(is_array($value))
            {
                foreach($value as $index => $item)
                {
                    $groupMenu[] = $index;
                }
            }
            else
            {
                $finalMenu[$key] = $value; //Item simples de menu, individual
            }
            $groupMenu = array_unique($groupMenu);
        }

        //Agrupando menus por assunto
        foreach($groupMenu as $group)
        {
            foreach($this->items as $key => $value)
            {
                foreach($value as $index => $item)
                {
                    if($index == $group)
                    {
                        $finalMenu[$index][] = $item;
                    }
                }
            }
        }

        //Render menus
        $i = 1;
        $u = 1;
        $menu = "<div class='{$this->class}'><ul class='itemgroup'>";
        foreach($finalMenu as $link => $label)
        {
            if(is_array($label))
            {
                $menu .= "<li class='item' id='item".$i++."'>";
                $menu .= "<a href='#' data-info='{$link}' class='linknull'><span class='icon'></span><span class='text'>{$link}</span>";
                $menu .= "<span class='arrow'></span></a>";
                $menu .= "<ul class='subitem'>";

                foreach($label as $sublink => $sublabel)
                {
                    foreach($sublabel as $key => $value)
                    {
                        $menu .= "<li class='subitem' id='subitem".$u++."'><a href='?page={$key}' data-info='{$value}'>{$value}</a></li>";
                    }
                }
                $menu .= "</ul></li>";
            }
            else
            {   //Item simples de menu, individual
                $menu .= "<li class='item' id='item".$i++."'><a href='?page={$link}' data-info='{$label}'><span class='icon'></span>";
                $menu .= "<span class='text'>{$label}</span><span class='arrow'></span></a></li>";
            }
        }
        $menu .= "</ul></div>";
        return $menu;
    }
}