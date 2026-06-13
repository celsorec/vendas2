<?php

class GridHelper
{
    private $title;
    private $head;
    private $body;
    private $foot;

    public function __construct($title='')
    {
        $this->title = $title;
    }

    public function addHead(array $head): void
    {
        $headHtml = "<thead><tr>";
        foreach($head as $key => $value)
        {
            $headHtml .= "<th class='$key'>{$value}</th>";
        }
        $headHtml .= "</tr></thead>";

        $this->head = $headHtml;
    }

    public function addBody($body): void
    {
        $bodyHtml = "<tbody>";
        if(is_array($body))
        {
            foreach($body as $value)
            {
                $bodyHtml .= "<tr>";
                foreach($value as $key => $item)
                {
                    $bodyHtml .= "<td class='$key'>{$item}</td>";
                }
                $bodyHtml .= "</tr>";
            }
        }

        $this->body = "{$bodyHtml}</tbody>";
    }

    public function addFoot($foot): void
    {
        $this->foot = "<tfoot><tr><td>{$foot}</td></tr></tfoot>";
    }

    public function renderGrid(): string
    {
        
        $table  = "<h3>{$this->title}</h3>";
        $table .= "<table>";
        $table .= $this->head;
        $table .= $this->body;
        $table .= $this->foot;
        $table .= "</table>";

        return $table;
    }

}