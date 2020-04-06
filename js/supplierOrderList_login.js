
function CheckLoginForm()
{
	var strUserName = $('#txtUserName').val();
	var strPassword = $('#txtPassword').val();
	
	if(strUserName.length==0)
	{
		alert('請提供登入名稱');
		return false;
	}
	
	if(strPassword.length==0)
	{
		alert('請提供登入密碼');
		return false;
	}
}