<?php

namespace app\admin\controller;

use app\admin\controller\Template\TemplateArticle;



class Experience extends TemplateArticle
{
    public function __construct()
    {
        parent::__construct(
            $controller_name    = "Experience",
            $table_name         = "experience",
            $pic_width          = 700, 
            $pic_height         = 475
        );
    }
}