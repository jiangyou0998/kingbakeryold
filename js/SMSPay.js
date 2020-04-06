
function getSMSPostInfo(id, token, noAlert){
	var url = "https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyDIN8SQk_LoxD6-0gy_z1O6nzkfpdOMXDA";
	var info = {
		url: url,
		type: "POST",
		dataType: "json",
		contentType: "application/json; charset=utf-8",
		data: '{"longUrl": "http://taihingroast.com/soap/SMSPay/SMSPayment.php?token=' + token + '"}',
		success: function(data){
			var url = data.id,
				req = "http://192.168.2.254/sendSMS.php?action=send&url=" + url + "&id=" + id;
			
			$.get(req, function(resp){
				if(!noAlert){
					if(resp == "1")
						alert("已成功發送SMS");
					else
						alert("發送失敗，請重試");
				}
			});
		}
	}
	return info;
}
function getSMSPostInfoItem(id, token, itemID, noAlert){
	var url = "https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyDIN8SQk_LoxD6-0gy_z1O6nzkfpdOMXDA";
	var info = {
		url: url,
		type: "POST",
		dataType: "json",
		contentType: "application/json; charset=utf-8",
		data: '{"longUrl": "http://taihingroast.com/soap/SMSPay/SMSPayment.php?token=' + token + '&itemID=' + itemID + '"}',
		success: function(data){
			var url = data.id,
				req = "http://192.168.2.254/sendSMS.php?action=send&url=" + url + "&id=" + id + "&itemID=" + itemID;
			
			$.get(req, function(resp){
				if(!noAlert){
					if(resp == "1")
						alert("已成功發送SMS");
					else
						alert("發送失敗，請重試");
				}
			});
		}
	}
	return info;
}