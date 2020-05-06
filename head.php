<script>
    function redirectPage(redirPage) {
//		alert('hello');
        if (redirPage == 'order.php?head=5')
            parent.location.href = redirPage
    }

    function ImgOver(op, num) {
        op.src = "images/Header_d_" + num + ".jpg";
    }

    function ImgOut(op, num) {
        op.src = "images/Header_" + num + ".jpg";
    }

    function ImgOver_a(op, num) {
        op.src = "images/Head_d_" + num + ".jpg";
    }

    function ImgOut_a(op, num) {
        op.src = "images/Head_" + num + ".jpg";
    }
</script>
<?php
session_start();
?>

<table id="Table_01" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td><a href="index.php"><img src="images/intranet_banner.jpg" width="994" height="126" border="0"></a></td>
    </tr>
    <tr>
        <td height="1"></td>
    </tr>
    <tr>
        <td height="0">
            <table width="994" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td><img src="images/head.png" width="130" height="30" border="0"/></td>
                    <!--
        <?php if ($_REQUEST['head'] == 1) { ?>
        <td><img src="images/Header_d_02.jpg" width="68" height="30" border="0" /></td>
        <?php } else { ?>
        <td><a href="#" onClick="redirectPage('notice.php?head=2')"><img src="images/Header_02.jpg" onmouseover="ImgOver(this,'02')" onmouseout="ImgOut(this,'02')" width="68" height="30" border="0" /></a></td>
        <?php } ?>
-->
                    <?php if ($_REQUEST['head'] == 2) { ?>
                        <td><img src="images/Header_d_02.jpg" width="65" height="30" border="0"/></td>
                    <?php } else { ?>
                        <td><a href="notice.php?head=2" onClick="redirectPage('notice.php?head=2')"><img
                                        src="images/Header_02.jpg" onmouseover="ImgOver(this,'02')"
                                        onmouseout="ImgOut(this,'02')" width="65" height="30" border="0"/></a></td>
                    <?php } ?>

                    <?php if ($_REQUEST['head'] == 3) { ?>
                        <td><img src="images/Header_d_03.jpg" width="69" height="30" border="0"/></td>
                    <?php } else { ?>
                        <td><a href="forms.php?head=3" onClick="redirectPage('forms.php?head=3')"><img
                                        src="images/Header_03.jpg" onmouseover="ImgOver(this,'03')"
                                        onmouseout="ImgOut(this,'03')" width="69" height="30" border="0"/></a></td>
                    <?php } ?>

                    <?php if ($_REQUEST['head'] == 4) { ?>
                        <td><img src="images/Header_d_04.jpg" width="64" height="30" border="0"/></td>
                    <?php } else { ?>
                        <td><a href="http://c.realandbio.com/"><img src="images/Header_04.jpg"
                                                                    onmouseover="ImgOver(this,'04')"
                                                                    onmouseout="ImgOut(this,'04')" width="64"
                                                                    height="30" border="0"/></a></td>
                    <?php } ?>

                    <?php if ($_REQUEST['head'] == 5) { ?>
                        <td><img src="images/Header_d_05.jpg" width="69" height="30" border="0"/></td>
                    <?php } else { ?>
                        <td><a href="order.php?head=5" onClick="redirectPage('order.php?head=5')"><img
                                        src="images/Header_05.jpg" width="69" height="30" border="0"
                                        onmouseover="ImgOver(this,'05')" onmouseout="ImgOut(this,'05')"/></a></td>
                    <?php } ?>
                    <?php if ($_REQUEST['head'] == 6) { ?>
                        <td><img src="images/Header_d_06.jpg" width="65" height="30" border="0"/></td>
                    <?php } else { ?>
                        <td><a href="grpo.php" onClick="redirectPage('grpo.php?head=6')"><img src="images/Header_06.jpg"
                                                                                              onmouseover="ImgOver(this,'06')"
                                                                                              onmouseout="ImgOut(this,'06')"
                                                                                              width="65" height="30"
                                                                                              border="0"/></a></td>
                    <?php } ?>
                    <!--
        <?php if ($_REQUEST['head'] == 7) { ?>
        <td><img src="images/Header_d_07.jpg" width="68" height="30" border="0" /></td>
        <?php } else { ?>
        <td><a href="#" onClick="redirectPage('stocktake.php?head=7')"><img src="images/Header_07.jpg" onmouseover="ImgOver(this,'07')" onmouseout="ImgOut(this,'07')" width="68" height="30" border="0" /></a></td>
        <?php } ?>
		
        <?php if ($_REQUEST['head'] == 8) { ?>
        <td><img src="images/Header_d_08.jpg" width="66" height="30" border="0" /></td>
        <?php } else { ?>
        <td><a href="#" onClick="redirectPage('coupon.php?head=8')"><img src="images/Header_08.jpg" onmouseover="ImgOver(this,'08')" onmouseout="ImgOut(this,'08')" width="66" height="30" border="0" /></a></td>
        <?php } ?>
-->

                    <?php if ($_REQUEST['head'] == 9) { ?>
                        <td><img src="images/Header_d_09.jpg" width="68" height="30" border="0"/></td>
                    <?php } else { ?>
                        <td><a href="#" onClick="redirectPage('apply.php?head=9')"><img src="images/Header_09.jpg"
                                                                                        onmouseover="ImgOver(this,'09')"
                                                                                        onmouseout="ImgOut(this,'09')"
                                                                                        width="68" height="30"
                                                                                        border="0"/></a></td>
                    <?php } ?>
                    <?php if ($_REQUEST['head'] == 10) { ?>
                        <td><img src="images/Header_d_10.jpg" width="88" height="30" border="0"/></td>
                    <?php } else { ?>
                        <td><a href="salesdata.php?head=10" onClick="redirectPage('salesdata.php?head=10')"><img
                                        src="images/Header_10.jpg" width="88" height="30" border="0"
                                        onmouseover="ImgOver(this,'10')" onmouseout="ImgOut(this,'10')"/></a></td>
                    <?php } ?>
                    <?php if ($_REQUEST['head'] == 11) { ?>
                        <td><img src="images/Header_d_11.jpg" width="89" height="30" border="0"/></td>
                    <?php } else { ?>
                        <td><a href="phonebook.php?head=11" onClick="redirectPage('phonebook.php?head=11')"><img
                                        src="images/Header_11.jpg" onmouseover="ImgOver(this,'11')"
                                        onmouseout="ImgOut(this,'11')" width="89" height="30" border="0"/></a></td>
                    <?php } ?>
                    <?php if ($_REQUEST['head'] == 12) { ?>
                        <td><img src="images/Head_d_12.jpg" width="65" height="30" border="0"/></td>
                    <?php } else { ?>
                        <td><a href="report.php?head=12" onClick="redirectPage('report.php?head=12')"><img
                                        src="images/Head_12.jpg" onmouseover="ImgOver_a(this,'12')"
                                        onmouseout="ImgOut_a(this,'12')" width="65" height="30" border="0"/></a></td>
                    <?php } ?>
                    <?php if ($_REQUEST['head'] == 13) { ?>
                        <td><img src="images/Header_d_13.jpg" width="92" height="30" border="0"/></td>
                    <?php } else { ?>
                        <td><a href="library.php?head=13" onClick="redirectPage('library.php?head=13')"><img
                                        src="images/Header_13.jpg" onmouseover="ImgOver(this,'13')"
                                        onmouseout="ImgOut(this,'13')" width="92" height="30" border="0"/></a></td>
                    <?php } ?>
                    <!--
        <?php if ($_REQUEST['head'] == 14) { ?>
        <td><img src="images/Header_d_14.jpg" width="58" height="30" border="0" /></td>
        <?php } else { ?>
        <td><a href="#" target="_blank"><img src="images/Header_14.jpg" onmouseover="ImgOver(this,'14')" onmouseout="ImgOut(this,'14')" width="58" height="30" border="0" /></a></td>
        <?php } ?>
-->

                    <td><img src="images/head.png" width="130" height="30" border="0"/></td>

                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td><img src="images/TaiHing_11.jpg" width="994" height="16" alt=""></td>
    </tr>
</table>
<!--
<map name="Map" id="Map"><area shape="rect" coords="836,42,981,115" href="http://www.taihingroast.com/" target="_blank" alt="�ӿ�����" />
	<area shape="rect" coords="170,0,834,126" href="index.php" alt="�ӿ����p��" />
	<area shape="rect" coords="5,27,166,101" href="http://intranet.taihingroast.com.cn" target="_blank" />
</map>
-->
