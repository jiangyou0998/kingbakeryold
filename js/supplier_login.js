
function CheckLoginForm()
{
	var strUserName = $('#txtUserName').val();
	var strPassword = $('#txtPassword').val();
	
	if(strUserName.length==0)
	{
		alert('Ոݔ���Ñ���');
		return false;
	}
	
	if(strPassword.length==0)
	{
		alert('Ոݔ���ܴa��');
		return false;
	}
}