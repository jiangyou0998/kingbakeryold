
function CheckLoginForm()
{
	var strUserName = $('#txtUserName').val();
	var strPassword = $('#txtPassword').val();
	
	if(strUserName.length==0)
	{
		alert('’à›î»Î”√ëÙ£°');
		return false;
	}
	
	if(strPassword.length==0)
	{
		alert('’à›î»Î√‹¥a£°');
		return false;
	}
}