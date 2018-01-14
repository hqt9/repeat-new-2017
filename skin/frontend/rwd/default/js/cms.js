$j(function(){
    $j('.services .content-title').on('click', function() {
        var this_block_section = $j(this).closest('.block-section');

        $j('.cms-page-view .cms-content .block-section').not(this_block_section).removeClass('active');
        // var h1_margin_bottom = parseInt($j('.cms-content').find('h1').css('marginBottom'));
        // if (isNaN(h1_margin_bottom)) {
        //  h1_margin_bottom = 0;
        // }
        var position = $j(this).position().top;
        // $j('.scroll-wrapper').animate( { scrollTop: position + h1_margin_bottom },'1000','swing');
        this_block_section.toggleClass('active');
        $j('.scroll-wrapper').animate( { scrollTop: position }, 'slow', 'swing');
    });

    $j('#section-contact-us .content-title').on('click',function(e){
        $j(this).closest('.block-section').removeClass('active')
        $j('.popout-contact-us').addClass('active');
        $j('body').addClass('close-touch');
        return false
    });
    $j('.popout-contact-us .popout-close ').on('click',function(){
        $j('.popout-contact-us').removeClass('active');
        $j('body').removeClass('close-touch');
    });


    $j('#section-newsletter .content-title').on('click',function(e){
        $j(this).closest('.block-section').removeClass('active')
        $j('.popout-newsletter').addClass('active');
        $j('body').addClass('close-touch');
        return false
    });
    $j('.popout-newsletter .popout-close ').on('click',function(){
        $j('.popout-newsletter').removeClass('active');
        $j('body').removeClass('close-touch');
    });
});

