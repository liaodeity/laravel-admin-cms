console.log(222);
$("#search-choice").click(function () {
    console.log(3333);
    if(jQuery("#filter-body").is(':visible')){
        jQuery("#filter-body").addClass('hidden')
        jQuery(this).removeClass('active')
    }else{
        jQuery("#filter-body").removeClass('hidden')
        jQuery(this).addClass('active')
    }
});
