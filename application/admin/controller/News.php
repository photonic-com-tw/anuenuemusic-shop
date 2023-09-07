<?php

namespace app\admin\controller;

use app\admin\controller\Template\TemplateArticle;



class News extends TemplateArticle
{
    public function __construct()
    {
        parent::__construct(
            $controller_name    = "News",
            $table_name         = "news",
            $pic_width          = 700, 
            $pic_height         = 475
        );
    }
}
