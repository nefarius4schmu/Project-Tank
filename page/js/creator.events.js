/**
 * Created by Nefa on 28.04.2015.
 */
(function($){
    var inText = null,
        $tmpEventMap = $("#tmpEventMap");

    // init ddslick
    $("select.data-select").each(function(){
        var id = $(this).attr("id");
        var name = $(this).attr("name");
        $(this).ddslick({
            //selectText: "Auswahl..",
            //truncateDescription: false,
        });
        //$("#"+id).find(".dd-selected-value").attr("name", name);
    });

    // init datepicker3
    var $dtPicker = $(".datetime-picker");
    $dtPicker.each(function(){
        var $this = $(this),
            val = $this.find("input").val();
        $this.datetimepicker({locale:'de'});
        // set date if given by input value
        if(!jsTools.empty(val)) $this.data("DateTimePicker").date(moment(val));
        // use today as min date
        $this.data("DateTimePicker").minDate(moment());
    });
    // init min max on change events
    $dtPicker.filter("[data-min],[data-max]").on("dp.change", function(event){
        var $min = $($(this).data("min"));
        var $max = $($(this).data("max"));
        if($min.length > 0) $min.data("DateTimePicker").minDate(event.date);
        if($max.length > 0) $max.data("DateTimePicker").maxDate(event.date);
    });

    // init ckeditor
    if($("#inText").length !== 0)
        CKEDITOR.replace("inText", {
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
        if(!window.jsTools) console.error("Error: jsTools libs not found");
        inText = CKEDITOR.instances.inText ? CKEDITOR.instances.inText : null;

        initEvents();
    });

    function initEvents(){
        $(".bt-add-map").click(onBtAddMapClick);
        $(".bt-add-item").click(onBtAddItemClick);
        initEventItem($(".event-item"));
    }

    function initEventItem($item){
        $item.find(".bt-delete").click(onEventItemDeleteClick);
    }

    function onBtAddMapClick(event){
        event.preventDefault();

        var $this = $(this),
            $map = $("#inMaps"),
            $mode = $("#inGameMode"),
            tmp = $($this.data("template")).text(),
            selMap = $map.data('ddslick').selectedData,
            selMode = $mode.data('ddslick').selectedData,
            $before = $($this.data("before")),
            index = $(this).closest(".event-maps").find(".event-item").length;

        if(!selMap || !selMode) return false;

        var data = {
            mapID: selMap.value,
            modeID: selMode.value,
            mapName: selMap.text,
            modeName: selMode.text,
            image: selMap.imageSrc,
            index: index,
        };
        var $item = $(jsTools.template(tmp, data));
        $before.before($item);
        initEventItem($item);
    }

    function onBtAddItemClick(event){
        event.preventDefault();

        var $this = $(this),
            $input = $($this.data("input")),
            $target = $($this.data("target")),
            method = $this.data("method"),
            tmp = $($this.data("template")).text(),
            index = $this.data("index");

        if(!method || $target.length === 0 || $input.length === 0) return console.error("error add item event: missing data");

        var data = {};
        var error = false;
        $input.each(function(){
            if(error) return;
            var $self = $(this),
                isReq = $self.data("required"),
                field = $self.data("field"),
                append = $self.data("append") ? $self.data("append") : '',
                prepend = $self.data("prepend") ? $self.data("prepend") : '',
                val = $self.val(),
                isEmpty = jsTools.empty(val);
            error = isReq && isEmpty;
            if(!isEmpty && typeof field !== 'undefined') data[field] = prepend + val + append;
        });
        if(error) return false;
        if(index) data["index"] = $(index).length;

        //console.log(data);
        // add new row
        if($target[method]){
            var $item = $(jsTools.template(tmp, data));
            $target[method]($item);
            initEventItem($item);
        }
        //console.log($target, method, $target[method]);
        // cls
        $input.val(null);
    }

    function onEventItemDeleteClick(event){
        var $this = $(this);
        $this.hide();
        $this.closest(".event-item").hide("fast", function(){$(this).remove();});
    }

}(jQuery));