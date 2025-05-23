{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2, user-scalable=0">
    <title>跳轉提示</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
        crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100;300;400;500;700;900&display=swap"
            rel="stylesheet">    
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        body{ background: #fff; font-family: "Microsoft Yahei","Helvetica Neue",Helvetica,Arial,sans-serif; color: #333; font-size: 16px; }
        .system-message{ padding: 24px 48px; }
        .system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
        .system-message .jump{ padding-top: 10px; }
        .system-message .jump a{ color: #333; }
        .system-message .success,.system-message .error{ line-height: 1.8em; font-size: 36px; }
        .system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display: none; }
        body {
            /*font-family: "Microsoft YaHei", 宋体, "Segoe UI", "Lucida Grande", Helvetica, Arial, sans-serif, FreeSans, Arimo;*/
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Noto Sans TC', sans-serif;
            background-color: rgb(230, 230, 230);
            color: #777;
            font-size: 15px;
            padding: 10px;
        }
        .text-effect{
            height: 100%;
            color: #777;
            /*font-family: 'Frijole', cursive;*/
            font-size: 3rem;
            /*text-transform: uppercase;*/
            font-weight:400;
            letter-spacing:1px;
            text-align: center;
            margin: 4rem auto 0;
            animation: 2s animate infinite linear;
        }/*
        @keyframes animate{
            0%{ text-shadow: 0 0 0 white; }
            40%{
                color: rgba(255,255,255,0.2);
                text-shadow: 0 0 30px white;
            }
            70%{
                color: rgba(255,255,255,0.4);
                text-shadow: 0 0 10px white;
            }
            90%{
                color: rgba(255,255,255,0.6);
                text-shadow: 0 0 30px white;
            }
            100%{ text-shadow: 0 0 40px rgba(255,255,255,0.2); }
        }

        @media only screen and (max-width: 990px){
            .text-effect{ font-size: 80px; }
        }
        @media only screen and (max-width: 767px){
            .text-effect{ font-size: 40px; }
        }
        @media only screen and (max-width: 479px){
            .text-effect{ font-size: 30px; }
        }
        @media only screen and (max-width: 359px){
            .text-effect{ font-size: 25px; }
        }       */    
        .text_center{
            text-align: center;
        }

        @media screen and (min-width : 991px) {
            .text-effect{font-size: 5rem; width:65%;margin-bottom:1rem;}
        }

        img.profile-img-card{
            width: fit-content;
            max-width: 100%;
            margin-bottom: 10px;
        }
        .small, small{
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-effect">
                    <?php switch ($code) {?>
                        <?php case 1:?>
                        {notempty name="$admin_info['success_logo']"}
                            <img class="profile-img-card" src="/public/static/index/{$admin_info['success_logo']}"  />
                        {/notempty}
                        <p class="success">
                            <span><?php echo($msg);?></span>
                        </p>
                        <?php break;?>
                        
                        <?php case 0:?>
                        {notempty name="$admin_info['error_logo']"}
                            <img class="profile-img-card" src="/public/static/index/{$admin_info['error_logo']}"  />
                        {/notempty}
                        <p class="error">
                            <span><?php echo($msg);?></span>
                        </p>
                        <?php break;?>
                    <?php } ?>
                    
                    {notempty name="$data"}
                    <p>
                        {$data}
                    </p>
                    {/notempty}
                </div>
                <p class="detail text_center">頁面自動跳轉，等待：<b id="wait"><?php echo($wait);?></b> 秒</p>
                <p class="jump text_center">
                
                    <?php switch ($code) {?>
                        <?php case 1:?>
                        <a id="href" class="btn btn-primary"  href="<?php echo($url);?>">直接跳轉</a> 
                        <?php break;?>
                        <?php case 0:?>
                        <a id="href" class="btn btn-danger"  href="<?php echo($url);?>">直接跳轉</a> 
                        <?php break;?>
                    <?php } ?>
                    
                </p>
            </div>
        </div>
    </div>
    <div class="system-message">  
    </div>
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
</body>
</html>
