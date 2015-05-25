/**
 * Created by Nefa on 28.04.2015.
 */
(function($){

    var edPostText = null;
    var images = [];
    var $edImageCover = $("#edPostCoverImage");

    CKEDITOR.replace("edPostText", {
        language: 'de',
        toolbar:[
            { name: 'tools', items: [ 'Maximize' ] },
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'links', items: [ 'Link', 'Unlink'] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
            { name: 'others', items: [ '-' ] },
            '/',
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
            { name: 'styles', items: [ 'Styles', 'Format' ] },
            { name: 'about', items: [ 'About' ] }
        ]
    });

    $(document).ready(function(){
        edPostText = CKEDITOR.instances.edPostText;
        initEvents();

        updateImageCoverList();
        initImageCoverFirstLoad();
    });


    function initEvents(){
        edPostText.on('change', updateImageCoverList);
    }

    function initImageCoverFirstLoad(){
        var sel = $edImageCover.data("selected");
        if(sel && sel != ""){
            $edImageCover.find("option").prop("selected",false);
            $edImageCover.find("option[value='"+sel+"']").prop("selected", true);
            $edImageCover.data("picker").sync_picker_with_select();
        }
    }

    function updateImageCoverList(){
        console.log("update img cover list");
        var $data = $(edPostText.getData());
        var $imgList = $data.find("img");
        if($imgList.length != images){
            images = [];
            $imgList.each(function(){
                var src = $(this).attr("src");
                if(!src) return;
                images.push(src);
            });
            updateImageCoverSelect();
        }
        console.log($imgList);
        $("#edPostCoverImage_noImage").toggle(images.length == 0);
    }

    function updateImageCoverSelect(){
        console.log(images);
        $edImageCover.val(null).empty();
        var len = images.length;
        for(var i = 0; i < len; i++){
            var src = images[i];
            var label = "Bild "+(i+1);
            var $op = $("<option>", {
                "data-img-src": src,
                value: src
            }).text(label);
            $edImageCover.append($op);
        }
        var picker = $edImageCover.data("picker");
        if(picker) picker.destroy();
        $edImageCover.imagepicker();
    }

}(jQuery));