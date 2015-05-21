/**
 * Created by Nefa on 27.04.2015.
 */
!function($){
    "use strict";

    // ==================================================
    // creator type Pager ===============================
    var Pager = function(master, options){
        this.options = options;//$.extend({}, this.DEFAULTS, options);
        this.$master = $(master);

        this.init();
    };

    Pager.DEFAULTS = {
        currentPage: 0,
        endless: false,
        classes: "creator creator-pager",
        selector: ".creator.creator-pager",
        header: {
            eHeader: "<h2></h2>",
            selector: "h2",
        },
        page:{
            selector: ".page",
            classes: "page",
            wrapperClass: "pages",
        },
        navigation:{
            selector: ".navi",
            classes: "navi",
            eNext: "<a class='btn btn-success navi-next'>Weiter<i class='fa fa-fw fa-chevron-right'></i></a>",
            ePrev: "<a class='btn btn-success navi-prev'><i class='fa fa-fw fa-chevron-left'></i>Zurück</a>",
            eFinish: "<a class='btn btn-warning navi-finish'>Breitag Erstellen<i class='fa fa-fw'>+</i></a>",
        },
        //faIcons:{
        //    navNext: "fa-chevron-right",
        //    navPrev: "fa-chevron-left",
        //},
        onNext: function(event){
            var $this = $(event.target);
            if($this.hasClass("disabled")) return false;

            this.options.currentPage++;
            var max = this.$pages.length- 1;
            if(this.options.endless && this.options.currentPage > max)
                this.options.currentPage = 0;
            //else if(!this.options.endless && this.options.currentPage == max)
            //    this.initFinish();

            console.log("next");
            this.render();
        },
        onPrev: function(event){
            var $this = $(event.target);
            if($this.hasClass("disabled")) return false;

            this.options.currentPage--;
            var max = this.$pages.length- 1;
            if(this.options.endless && this.options.currentPage < 0)
                this.options.currentPage = this.$pages.length -1;
                //this.initReplay();
            //else if(!this.options.endless && this.options.currentPage == 0)
                //this.disable(this.$prev);

            console.log("prev");
            this.render();
        },
        onFinish: function(event){
            var $this = $(event.target);
            if($this.hasClass("disabled")) return false;

            console.log("finish");
        },
    };

    /**
     * init pager element
     */
    Pager.prototype.init = function(){
        this.options = $.extend({}, Pager.DEFAULTS, this.options);
        this.$master.addClass(this.options.classes);
        //console.log(this.options);

        this.initHeader();
        this.initPages();
        this.initNavigation();
        this.initEvents();

        this.render();
    };

    /**
     * init elements pages
     */
    Pager.prototype.initPages = function(){
        this.$pages = this.$master.find(this.options.page.selector).addClass(this.options.page.classes);
        if(this.$pages.length == 0){
            this.$pages = $("<div>", {
                class: this.options.page.classes,
            });
        }
        //this.$pages.eq(this.options.currentPage).addClass(this.options.activeClass);
        this.$pageWrapper = $("<div>",{
            class: this.options.page.wrapperClass,
        }).append(this.$pages);

        this.$master.append(this.$pageWrapper);
    };

    /**
     * init header element
     */
    Pager.prototype.initHeader = function(){
        this.$header = $(this.options.header.eHeader);
        this.$master.append(this.$header);
    };

    /**
     * init elements navigation
     */
    Pager.prototype.initNavigation = function(){
        this.$navigation = this.$master.find(this.options.navigation.classes);
        if(this.$navigation.length == 0){

            this.$navigation = $("<div>", {
                class: this.options.navigation.classes,
            });
            this.$next = $(this.options.navigation.eNext, {
                class: this.options.navigation.classes+'-next',
            });
                //.html(this.options.createFa(this.options.faIcons.navNext));

            this.$prev = $(this.options.navigation.ePrev, {
                class: this.options.navigation.classes+'-prev',
            });

            this.$finish = $(this.options.navigation.eFinish,{
                class: this.options.navigation.classes+'-finish',
            });
                //.html(this.options.createFa(this.options.faIcons.navPrev));

            this.$navigation.append(this.$prev).append(this.$next).append(this.$finish);
            this.$master.append(this.$navigation);
        }
    };

    /**
     * init pager events
     */
    Pager.prototype.initEvents = function(){
        // init navigation buttons
        this.$next.click($.proxy(this.options.onNext, this));
        this.$prev.click($.proxy(this.options.onPrev, this));
        this.$finish.click($.proxy(this.options.onFinish, this));
    };

    /**
     * init pager renderer
     */
    Pager.prototype.render = function(){
        console.log("render");
        // activate current page
        this.$pages.removeClass(this.options.activeClass).eq(this.options.currentPage).addClass(this.options.activeClass);

        // toggle button visibility
        var max = this.$pages.length-1;
        var isLast = this.options.currentPage != max;
        if(!this.options.endless){
            this.$prev.toggleClass("disabled", this.options.currentPage == 0);
            this.$next.toggle(isLast);
            this.$finish.toggle(!isLast);
        }else{
            this.$finish.hide();
        }
    };


    // ==================================================
    // app basics =======================================
    var App = {};

    App.DEFAULTS = {
        activeClass: "active",
        createFa: function(type, content){
            return $("<i>", {
                class: "fa "+type,
            }).html(typeof content === 'string' ? content : '');
        },
        onLoad: function(){},
    };

    App.CREATORS = {
        pager: Pager
    };

    // ==================================================
    // extend jQuery ====================================
    $.fn.creator = function(type, options){
        this.each(function(){
            var $this = $(this),
                creator = App.CREATORS[type],
                id = "creator."+type,
                data = $this.data(id);

            // init creator
            if(data) return;
            if(typeof creator !== 'undefined'){
                $this.data(id, new creator(this, $.extend({}, App.DEFAULTS, $this.data(), typeof options === 'object' && options)));
            }else{
                console.log("error", id, type, creator);
                throw new Error("Failed to init Creator. (1)");
            }
        });
        return this;
    };


    $.fn.creator.Constructor = App;
    $.fn.creator.defaults = App.DEFAULTS;
    $.fn.creator.creators = App.CREATORS;

}(jQuery);