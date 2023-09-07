<?php

namespace app\admin\controller;

use app\admin\controller\Template\TemplateArticle;

class Activity extends TemplateArticle
{
    public function __construct()
    {
        parent::__construct(
            $controller_name    = "Activity",
            $table_name         = "activity",
            $pic_width          = 700, 
            $pic_height         = 475
        );
    }
}

