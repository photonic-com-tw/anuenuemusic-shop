function Show(show_div){
    if($(show_div).is(':visible')==true){
        $(show_div).fadeOut(400);
    }
    else{
        $(show_div).fadeIn(400);
    }
}
function ShowAdd(show_div){
    if($(show_div).is(':visible')==false){
        $(show_div).show();
        document.getElementById("block").style.visibility="visible";
    }
}
function CloseAdd(show_div){
    $(show_div).hide();
    document.getElementById("block").style.visibility="hidden";
}

// 單張圖片需更改
function ChangeImage(){

    $(".upl").on("change", function (){
        preview(this);
    })
}

function preview(input) {
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
                
        reader.onload = function (e) {
        $('.preview').attr('src', e.target.result);
    }
    
        reader.readAsDataURL(input.files[0]);
    }
}

// 多張圖片需更改
function ChangeImages(){
    $(".upl").on("change", function (){
        var name = $(this).attr('name');
        previews(this, name);
    })
}

function previews(input, name) {
        
    if (input.files && input.files[0]) {
        var reader = new FileReader();
                    
        reader.onload = function (e) {
            $('.preview[name='+name+']').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}