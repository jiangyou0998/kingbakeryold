
function CheckLoginForm()
{
	var strUserName = $('#txtUserName').val();
	var strPassword = $('#txtPassword').val();
	
	if(strUserName.length==0)
	{
		alert('�д��ѵn�J�W��');
		return false;
	}
	
	if(strPassword.length==0)
	{
		alert('�д��ѵn�J�K�X');
		return false;
	}
}