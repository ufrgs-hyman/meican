function nextTab(elem){
    var activeTab;
    if (elem.id == "bn1") {
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(1)").addClass("active").show();            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");
        $(activeTab).fadeIn();
    } else if (elem.id == "bn2") {       
        $("ul.tabs li:eq(1)").removeClass("active");
        $("ul.tabs li:eq(2)").addClass("active").show();            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(2)").find("a").attr("href");
        $(activeTab).fadeIn();        
    } else if (elem.id == "bn3") {
        $("ul.tabs li:eq(2)").removeClass("active");
        $("ul.tabs li:eq(3)").addClass("active").show();            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(3)").find("a").attr("href");
        $(activeTab).fadeIn();        
    } else if (elem.id == "bn4") {
        $("ul.tabs li:eq(3)").removeClass("active");
        $("ul.tabs li:eq(4)").addClass("active").show();            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(4)").find("a").attr("href");
        $(activeTab).fadeIn();    
    } 
}

function previousTab(elem){
    var activeTab;
    if (elem.id == "bp2") {
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(0)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(0)").find("a").attr("href");
        $(activeTab).fadeIn();
    } else if (elem.id == "bp3") {
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(1)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");
        $(activeTab).fadeIn();
    } else if (elem.id == "bp4") {
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(2)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(2)").find("a").attr("href");
        $(activeTab).fadeIn();        
    } else if (elem.id == "bp5") {
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(3)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(3)").find("a").attr("href");
        $(activeTab).fadeIn(); 
    } 
}