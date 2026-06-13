<?php

class FormHelper
{
    private $action;
    private $attributes;
    private $title;
    private $button;
    private $method;
    private $enctype;
    private $fields = [];

    public function __construct($action, $attributes='', $title='', $button='Salvar', $method='POST', $enctype='application/x-www-form-urlencoded')
    {
        $this->action = $action;
        $this->title = $title;
        $this->attributes = $attributes;
        $this->button = $button;
        $this->method = $method;
        $this->enctype = $enctype;
    }

    public function addInput($label, $name, $type, $attributes='', $class='', $dataFilters=''): void
    {
        $labelHtml = "<label for='{$name}'>{$label}</label>";
        $inputHtml = "<input name='{$name}' id='{$name}' type='{$type}' {$attributes} {$dataFilters}>";
        $listAjax  = !empty($dataFilters) ? '<div class="display-ajax"></div>' : ''; //Se $dataFilters para aJax

        $this->fields[] = "<div class='fields-group {$class}'>{$labelHtml} {$inputHtml} {$listAjax}</div>";
    }

    //Obter dados para 'Selects' a partir do banco de dados
    public static function getSelect(array $columns, string $tableName, string $filters=''): array
    {
        $connect = new DataRecord();
        $data = $connect->read($columns, $tableName, $filters);
        $dataItems = [];
        foreach($data as $key => $value)
        {
            $dataItems[$value[$columns[0]]] = $value[$columns[1]];
        }
        return $dataItems;
    }

    public function addSelect($label, $name, array $options, $attributes='', $selectedValue='', $class=''): void
    {
        $labelHtml = "<label for='{$name}'>{$label}</label>";
        $selectHtml  = "<select name='{$name}' id='{$name}' {$attributes}>";

        if($selectedValue != '')
        {
            $selectDefault = "<option value=''>Selecione</option>";
        }
        else
        {
            $selectDefault = "<option selected disabled value=''>Selecione</option>";
        }

        $selectHtml .= $selectDefault;
            foreach($options as $key => $item)
            {
                if($key != $selectedValue)
                {
                    $selectHtml .= "<option value='{$key}'>{$item}</option>";
                }
                else
                {
                    $selectHtml .= "<option selected value='{$key}'>{$item}</option>";
                }
            }
        $selectHtml .= "</select>";

        $this->fields[] = "<div class='fields-group {$class}'>{$labelHtml} {$selectHtml}</div>";
    }

    public function addCheckbox(array $checkItems, string $checkValues='', string $class='', string $id=''): void
    {
        $checked      = '';
        $checkboxHtml = '';
        $checkValues  = explode(',', $checkValues); //Valores vindos do banco de dados ou de $_SESSION['dataForm]

        foreach($checkItems as $key => $value)
        {
            foreach($value as $index => $option)
            {
                if(in_array($index, $checkValues)) $checked = 'checked';

                $checkboxHtml .= "<div class='fields-subgroup col-12'>";
                $checkboxHtml .= "<input type='checkbox' name='".$key."[".$index."]' id='".$key."-".$index."' value='{$index}' $checked>";
                $checkboxHtml .= "<label for='".$key."-".$index."'>{$option}</label>";
                $checkboxHtml .= "</div>";

                $checked = ''; //Reset
            }
        }

        $this->fields[] = "<div class='fields-group {$class}' id='{$id}'>{$checkboxHtml}</div>";
    }

    public function addRadio(array $labelsValues, $name, $attributes='', $description='', $class=''): void
    {
        $radioHtml = "<h3>$description</h3>";
        foreach($labelsValues as $value => $label)
        {
            $radioHtml .= "<div class='fields-subgroup'>";
            $radioHtml .= "<input type='radio' name='{$name}' id='{$value}' value='{$value}' $attributes>";
            $radioHtml .= "<label for='{$value}'>{$label}</label>";
            $radioHtml .= "</div>";
        }

        $this->fields[] = "<div class='fields-group $class'>{$radioHtml}</div>";
    }

    public function addTextarea($label, $name, $attributes=''): void
    {
        $labelHtml = "<label for='{$name}'>{$label}</label>";
        $inputHtml = "<textarea name='{$name}' id='{$name}' {$attributes}></textarea>";
        
        $this->fields[] = "<div class='fields-group col-3'>{$labelHtml} {$inputHtml}</div>";
    }

    public function addHtml(string $html): void
    {
        $this->fields[] = $html;
    }

    public function renderForm(): string
    {
        $form  = !empty($this->title) ? "<h3>{$this->title}</h3>" : '';
        $form .= "<form action='{$this->action}' method='{$this->method}' {$this->attributes} enctype='{$this->enctype}' autocomplete='off'>";

        $form .= "<div class='columns'>";
        $form .= implode($this->fields);
        $form .= "</div>";

        $form .= "<div class='columns'>";
        $form .= "<div class='col-12'><button class='btn' id='sbmit' type='submit'><span class='icon'></span><span class='text'>{$this->button}</span></button></div>";
        $form .= "</div>";

        $form .= "<div class='columns'>";
        $form .= isset($_GET['page']) && !empty($_GET['page']) ? "<div class='col-12'><a href='?page=".$_GET['page']."&clearForm=true'>Limpar Formulário</a></div>" : '';
        $form .= "</div>";
        
        $form .= "</form>";

        return $form;
    }
}