function msToString(ms, toDB){
	var date = new Date(ms);
	
	var year  = date.getFullYear();
	var month = ('0' + (date.getMonth() + 1)).slice(-2);
	var day   = ('0' + date.getDate()).slice(-2);
	var hour  = ('0' + date.getHours()).slice(-2);
	var min   = ('0' + date.getMinutes()).slice(-2);

	if(toDB){
		result = day + "/" + month + "/" + year + ";" + hour + ":" + min;
	}else{
		result = day + "/" + month + "/" + year + " " + hour + ":" + min;
	}
	return result;
}
function msToObject(ms){
	var date = new Date(ms);
	
	var year  = date.getFullYear();
	var month = ('0' + (date.getMonth() + 1)).slice(-2);
	var day   = ('0' + date.getDate()).slice(-2);
	var hour  = ('0' + date.getHours()).slice(-2);
	var min   = ('0' + date.getMinutes()).slice(-2);
	var sec   = ('0' + date.getSeconds()).slice(-2); 
	
	var result = {
		"year"       : year,
		"month"      : month,
		"day"        : day,
		"hour"       : hour,
		"min"        : min,
		"sec"        : sec,
		"date"       : day + "/" + month + "/" + year,
		"time"       : hour + ":" + min + ":" + sec,
		"time_nosec" : hour + ":" + min,
		"datetime_DB": day + "/" + month + "/" + year + ";" + hour + ":" + min,
		"ms"		 : ms
	}
	
	return result;
}

function stringToMs(str){
	if (str == null || str == "")
		return 0;
	
	var aryStr = []
	var fromDB = false;
	if(str.match(/;/g))
		fromDB = true;
	
	
	if(fromDB){
		var aryStr = str.split(";");
	}else{
		var aryStr = str.split(" ");
	}
	
	var date = aryStr[0].split("/");
	var time = aryStr[1].split(":");
	
	return new Date(date[2], date[1] - 1, date[0], time[0], time[1], 0, 0).getTime();
}
function stringToObject(str){
	if (str == null || str == "")
		return msToObject(0);
	
	return msToObject(stringToMs(str));
}

function numToString(day, month, year, hour, min, toDB){
	day = ('0' + day).slice(-2);
	month = ('0' + month).slice(-2);
	hour = ('0' + hour).slice(-2);
	min = ('0' + min).slice(-2);

	
	if(toDB){
		result = day + "/" + month + "/" + year + ";" + hour + ":" + min;
	}else{
		result = day + "/" + month + "/" + year + " " + hour + ":" + min;
	}
	return result;
}
function numToMs(day, month, year, hour, min){
	return stringToMs(numToString(day, month, year, hour, min));
}
function numToObject(day, month, year, hour, min){
	return msToObject(numToMs(day, month, year, hour, min));
}

function findElementsByName(name){
	var result = [];
	var all = document.getElementsByTagName('*');
	for(var i=0;i<all.length;i++){
		if(all[i].name == name)
			result.push(all[i]);
	}
	return result;
}
function findElementsByClassName(name, tag){
	var all;
	if(tag != null)
		all = document.getElementsByTagName(tag);
	else
		all = document.getElementsByTagName('*');
	var result = [];
	for(var i=0;i<all.length;i++){
		if(all[i].className == name)
			result.push(all[i]);
	}
	return result;
}