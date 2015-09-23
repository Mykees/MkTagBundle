jQuery(function($){
    /**
     * STICKY NAVIGATION
     * @type {[type]}
     */
    var $sidebar = $('#aside');
    var $sticky = $('#sticky');
    var $pTop = $sticky.offset().top;

    $(window).scroll(function(){
        var $this = $(this);

        if($this.scrollTop() > $pTop)
        {
            $sticky.stop().css({top:$this.scrollTop()-$sidebar.offset().top+90});
        }else{
            $sticky.stop().css({top:$pTop-$sidebar.offset().top});
        }
    });
    if($(window).scrollTop() > $pTop)
    {
        $sticky.stop().css({top:$(window).scrollTop()-$sidebar.offset().top+90});
    }



    /**
     * SCROLLSPY
     */
     var sections = [];
     var id = false;
     var $nav = $("#sticky");
     var $nav_link = $nav.find('a');
     $nav_link.each(function(){
        sections.push($($(this).attr('href')));
     });
     $(window).scroll(function(e){
        var $scrollTop = $(this).scrollTop() + ($(window).height() / 3);
        for(var i in sections)
        {
            var section = sections[i];
            if($scrollTop > section.offset().top)
            {
                scroll_id = section.attr('id');
            }
        }
        if(scroll_id !== id)
        {
            id= scroll_id;
            $nav_link.removeClass('current');
            $nav.find('a[href="#'+id+'"]').addClass('current');
        }

     });

});