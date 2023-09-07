<!DOCTYPE html>
<html lang="zh-Hans-TW" ng-app="app">
    <head>
        <!-- ///////////////////////////////////////////////////////////////////////////// -->
        <!-- ///////////////////////////////////////////////////////////////////////////// -->
        <!-- ///////////////////////////////////////////////////////////////////////////// -->
        <meta charset="UTF-8">
        <title>Notification</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2, user-scalable=0">

        <link rel="shortcut icon" href="/public/static/index//upload/01/f4b144e44d85a256221e28387d8491.png" />
        <link rel="bookmark" href="/public/static/index//upload/01/f4b144e44d85a256221e28387d8491.png" />
        <!-- ///////////////////////////////////////////////////////////////////////////// -->
        <!-- ///////////////////////////////////////////////////////////////////////////// -->
        <!-- ///////////////////////////////////////////////////////////////////////////// -->
        <!-- RWD -->
        <meta http-equiv="x-ua-compatible" content="ie=edge"><!-- [if lte IE 8] [if lte IE 9] -->
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="manifest" href="/public/manifest.json">
        <link rel="stylesheet" href="/public/static/index/css/reset.css">
        <!-- //////////////////////////////////////////////////////////////////////////////////////////// -->
        <!-- //////////////////////////////////////////////////////////////////////////////////////////// -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- //////////////////////////////////////////////////////////////////////////////////////////// -->
        <!-- [if lte IE 8] [if lte IE 9] -->
        <script src="//cdn.jsdelivr.net/gh/coliff/bootstrap-ie8/js/bootstrap-ie9.min.js"></script>
        <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script src="//cdn.jsdelivr.net/gh/coliff/bootstrap-ie8/js/bootstrap-ie8.min.js"></script>
        <script src="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.js"></script>
        <!-- //////////////////////////////////////////////////////////////////////////////////////////// -->
        <link href="//fonts.googleapis.com/css?family=Noto+Sans+TC:100,300,400,500,700,900" rel="stylesheet">
        <!-- //////////////////////////////////////////////////////////////////////////////////////////// -->
        <link rel="stylesheet" type="text/css" href="/public/static/index/css/owl.carousel.min.css">
        <link rel="stylesheet" type="text/css" href="/public/static/index/css/owl.theme.default.min.css">
        <script type="text/javascript" src="/public/static/index/js/owl.carousel.min.js"></script>
        <!-- //////////////////////////////////////////////////////////////////////////////////////////// -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/shave/2.1.3/shave.min.js"></script>
        <!-- //////////////////////////////////////////////////////////////////////////////////////////// -->
        <link rel='stylesheet' href='//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css'>
        <script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>
        <!-- //////////////////////////////////////////////////////////////////////////////////////////// -->
        <!-- font-family: 'Open Sans', sans-serif; -->
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&display=swap" rel="stylesheet">
        <!-- //////////////////////////////////////////////////////////////////////////////////////////// -->
        <link rel="stylesheet" href="/public/static/index/css/bootstrap-icons.css">
        <link rel="stylesheet" href="/public/static/index/css/iconstyle.css">
        <link rel="stylesheet" href="/public/static/index/css/style.css">
        <link rel="stylesheet" href="/public/static/index/css/phone.css">
        <link rel="stylesheet" href="/public/static/index/css/layout_style.css">

        
        <!-- anuenuemusic -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://kit.fontawesome.com/9a2047a60f.js" crossorigin="anonymous"></script>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/public/static/index/anuenuemusic/css/bootstrap-icons.css">
        <link rel="stylesheet" href="/public/static/index/anuenuemusic/css/animate.css">
        <link rel="stylesheet" href="/public/static/index/anuenuemusic/css/RWD_eidtor_style.css">
        <link rel="stylesheet" href="/public/static/index/anuenuemusic/css/all.css">
        <link rel="stylesheet" href="/public/static/index/anuenuemusic/css/all_andy.css">
        <!-- ///////////////////////////////////////////////////////////////////////////// -->
        
        <style>
            .bg_anuenue{
                height: 100vh;
            }
            .reg_prod_area{
                height: 72.8%;
                max-width: 100%;
            }

            img{
                max-width: 100%;
                width: fit-content;
                padding-right: 2rem;
                padding-left: 2rem;
            }
            h2{
                font-size: 2.125rem;
            }
            h2{
                font-weight: bold;
                color: #646464;
            }
            p{
                font-weight: 500;
                color: #646464;
            }
            .mb_6vh{
                margin-bottom: 6vh !important;
            }
            .mb_5vh{
                margin-bottom: 5vh !important;
            }

            .jump .jump_btn{
                color: #ffffff !important;
                background-color: #646464 !important;
                width: 496px;
                max-width: 91%;
            }
        </style>

    </head>
    <body class="anuenueshop" ng-controller="ContentController as contCtrl">
        <div class="d-flex justify-content-center">
            <div class="w-100 bg_anuenue bg_pic d-flex justify-content-center align-items-center">
                <section class="reg_prod_area box_show bg_white d-flex align-items-center justify-content-center">
                    <div class="w-100">
                        <div class="text-center">
                            <?php switch ($code) {?>
                                <?php case 1:?>
                                {notempty name="$admin_info['success_logo']"}
                                    <img class="profile-img-card mb_6vh" src="/public/static/index/{$admin_info['success_logo']}"  />
                                {/notempty}
                                <h2 class="success mb_6vh">
                                    <span><?php echo($msg);?></span>
                                </h2>
                                <?php break;?>
                                
                                <?php case 0:?>
                                {notempty name="$admin_info['error_logo']"}
                                    <img class="profile-img-card mb_6vh" src="/public/static/index/{$admin_info['error_logo']}"  />
                                {/notempty}
                                <h2 class="error mb_6vh">
                                    <span><?php echo($msg);?></span>
                                </h2>
                                <?php break;?>
                            <?php } ?>
                            
                            {notempty name="$data"}
                            <p class="mb_5vh">
                                {$data}
                            </p>
                            {/notempty}
                        </div>
                        <p class="detail text-center mb_5vh">{$lang_menu['頁面自動跳轉，等待']}：<b id="wait"><?php echo($wait);?></b> {$lang_menu['秒']}</p>
                        <p class="jump text-center">
                            <?php switch ($code) {?>
                                <?php case 1:?>
                                    <a id="href" class="btn jump_btn"  href="<?php echo($url);?>">{$lang_menu['直接跳轉']}</a> 
                                    <?php break;?>
                                <?php case 0:?>
                                    <a id="href" class="btn jump_btn"  href="<?php echo($url);?>">{$lang_menu['直接跳轉']}</a> 
                                    <?php break;?>
                            <?php } ?>
                        </p>
                    </div>
                </section>
            </div>
        </div>

        <!-- 載入Bootstrap JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
        <!-- <script src="/public/static/index/anuenuemusic/js/all.js"></script> -->
        <!-- <script src="/public/static/index/anuenuemusic/js/template.js"></script> -->
        <!-- <script src="/public/static/index/anuenuemusic/js/all_andy.js"></script> -->

    </body>
    <script type="text/javascript">
        (function(){
        
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            if(wait.innerHTML != -1){
                var interval = setInterval(function(){
                    var time = --wait.innerHTML;
                    if(time <= 0) {
                        location.href = href;
                        clearInterval(interval);
                    };
                }, 1000);
            }
        })();
    </script>
</html>