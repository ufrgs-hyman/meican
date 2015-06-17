function createSucessMessage(obj, msg, id){
	var msgDiv = '<div id="alertMsg'+id+'" align="center" style="margin-top: 10px; font-weight: bold; padding: 5px; background-color:#DDFFCC; color:#007700; border:1px solid #007700;">';
	msgDiv += msg;
	msgDiv += '<div>';

    $(obj).before(msgDiv); 
    document.getElementById("alertMsg".concat(id)).addEventListener('click', function(){
    	document.getElementById("alertMsg".concat(id)).style.display = 'none';
    });
}

function createSucessMessage(obj, msg, id){
	var a = obj;
 
	var columns = '<div id="alertMsg'+id+'" align="center" style="margin-top: 10px; font-weight: bold; padding: 5px; background-color:#DDFFCC; color:#007700; border:1px solid #007700;">'+msg+'<div>';

    $(a).before(columns); 
    document.getElementById("alertMsg".concat(id)).addEventListener('click', function(){
    	document.getElementById("alertMsg".concat(id)).style.display = 'none';
    });
}