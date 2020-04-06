$(function () {
    "use strict";
	window.WebSocket = window.WebSocket || window.MozWebSocket;
	var connection = new WebSocket('ws://localhost:1452');
	connection.onerror = function (error) {
		$("#status").css("color", "#DE5347");
		console.log(error);
	};
	connection.onmessage = function (message) {
		var v = message.data.charAt(0);
		console.log(v);
		if(v == "0"){
			$("#status").css("color", "#1BA161");
		}else if(v == "3" || v =="1"){
			$("#status").css("color", "#DE5347");
		}
	};
	connection.onopen = function(){
		if(connection.readyState == 1){
			connection.send("start_read");
		}
		var intervalID = setInterval(function(){
			if(connection.readyState !== 1){
				clearInterval(intervalID);
			}
			try{
				connection.send("check_status");
			}catch(err){
				$("#status").css("color", "#DE5347");
				console.log(err);
			}
		}, 5000);
	}
});