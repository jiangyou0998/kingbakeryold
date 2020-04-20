<?
  session_start();
  if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'CMS_order.php';
    header('Location: login.php');
  }
?>
<html>
<head>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>內聯網</title>
<style type="text/css">
    <!--
    li {
        font-size: 12pt;
    }

    ul {
        padding:10px;
    }

    .exheading {cursor: hand; background-color:#EB8201; color:#FFFFFF; padding:3px;}
    .exindent {padding-left: 10px}
    a:link {
        color: #EB8201;
    }
    a:visited {
        color: #EB8201;
    }
    a:hover {
        color: #FF0000;
    }
    a:active {
        color: #EB8201;
    }
    -->
</style>
<script language="JavaScript">
    function hideshow(which)
    {
    if (!document.getElementById|document.all)
        {
        return
        }
    else
        {
        if (document.getElementById)
            {
            oWhich = eval ("document.getElementById('" + which + "')")
            }
        else
            {
            oWhich = eval ("document.all." + which)
            }
        }

        window.focus()

        if (oWhich.style.display=="none")
            {
            oWhich.style.display=""
            }
        else
            {
            oWhich.style.display="none"
            }
    }

    function initclass02Expandible()
    {
        hideshow('class02ChildDreamweaver')
        hideshow('class02ChildUltraDev')
        hideshow('class02ChildOthers')
        hideshow('class02ChildApplication')
    }

    function initclass01Expandible()
    {
        hideshow('class01ChildGraphic')
        hideshow('class01ChildFlash')
        hideshow('class01ChildBasic')
        hideshow('class01ChildHTML')
        hideshow('class01ChildComputer')
    }

</script>
</head>

<body onLoad="initclass02Expandible();initclass01Expandible();">
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr> 
    <td>
	<div id="class02ChildDreamweaver" style="cursor:auto" class="exindent"></div>
      <div id="class02ParentUltraDev" onClick="javascript:hideshow('class02ChildUltraDev')" class="exheading">設定</div>
      <div id="class02ChildUltraDev" style="cursor:auto" class="exindent"> 
        <ul>
			<!--
			<li><a href="CMS_order_c_area_main.php?type=1" target="subMainFrame">區域</a></li>
			<li><a href="CMS_order_c_shop_main.php?type=1" target="subMainFrame">分店</a></li>
			<br>
			<li><a href="CMS_order_unit.php" target="subMainFrame">單位</a></li>
			<li><a href="CMS_order_loc.php" target="subMainFrame">位置</a></li>
			<br>
			<li><a href="CMS_order_cat.php" target="subMainFrame">大類</a></li>
			-->
			<li><a href="CMS_order_group.php" target="subMainFrame">細類</a></li>
			<li><a href="CMS_order_menu.php" target="subMainFrame">貨品</a></li>
        </ul>
      </div>
      <br>

      <div id="class02ParentApplication" onClick="javascript:hideshow('class02ChildApplication')" class="exheading">報表</div>
      <div id="class02ChildApplication" style="cursor:auto" class="exindent"> 
        <ul style="text-align: left;">
          <li><a href="CMS_order_c_check_list.php" target="subMainFrame">列表</a></li>
          <li><a href="CMS_order_c_check_history_list.php" target="subMainFrame">生產表</a></li>
          <li><a href="CMS_order_c_check_order_sort.php" target="subMainFrame">車期排序</a></li>
        </ul>
      </div>
      <br>

      <div id="class02ParentOthers" onClick="javascript:hideshow('class02ChildOthers')" class="exheading">發票</div>
      <div id="class02ChildOthers" style="cursor:auto" class="exindent"> 
        <ul>
          <li><a href="order_z_receipt_list.php" target="subMainFrame">列表</a></li>
        </ul>
    </div>

</td>
  </tr>
</table>
</body>
</html>