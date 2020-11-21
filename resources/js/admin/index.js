
$("#search-choice").click(function () {
    if($("#filter-body").is(':visible')){
        $("#filter-body").addClass('hidden')
        $(this).removeClass('active')
    }else{
        $("#filter-body").removeClass('hidden')
        $(this).addClass('active')
    }
});
