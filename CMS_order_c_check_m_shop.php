
<table border="1" cellpadding="2" cellspacing="2" width="100%">
	<tr bgcolor="#CCFFFF">
		<td align="center" width="3%"><strong>#</strong></td>
		<td align="center" width="5%"><strong>產品編號</strong></td>
		<td align="center" width="10%"><strong>產品名稱</strong></td>
		<td bgcolor="#FFFFCC" align="center" width="<?=$width?>"><strong>Total</strong></td>
		<?php foreach ($aryDisplayShop as $key => $value) { ?>
		<?php if ($product[$value][txt_name] != "") { ?>
		<td align="center" width="<?=$width?>%"><strong><?=$product[$value][ename]?><br><?=$product[$value][txt_name]?></strong></td>
		<?php } ?>
		<?php } ?>
	</tr>
	
	<?php $count = 1; ?>
	<?php if($report[int_hide] == 1){ ?>
	<?php foreach($aryMenu as $key => $value){ ?>
	<?php if(array_key_exists ($key, $total) ){ //判斷總數是否0?>
	<?php $bg = ($count%2==0) ? "#EEEEEE" : "#FFFFFF" ?>
	<tr bgcolor="<?=$bg?>">
		<td align="center"><span class="style3"> <?=$count?> </span></td>
		<td align="center"><span class="style3"> <?=$aryMenu[$key][chr_no]?> </span></td>
		<?php $name = ($aryMenu[$key][chr_reportName] == "") ? $aryMenu[$key][chr_name] : $aryMenu[$key][chr_reportName]; ?>
		<td align="center"><span class="style3"> <?=$name?> </span></td>
        <td align="center" bgcolor="#FFFFCC">
            <span class="style6">&nbsp;<?=$total[$aryMenu[$key][chr_no]]?>&nbsp;</span>
        </td>
		<?php foreach ($aryDisplayShop as $shop) { ?>
		<?php if ($product[$shop][txt_name] != "") { ?>
		<td align="center"><span class="style6">&nbsp;
			<?=$product[$shop][$aryMenu[$key][chr_no]]?>
		&nbsp;</span></td>
		<?php } ?>
		<?php } ?>

	</tr>
	<?php $count++; ?>
	<?php } ?>
	<?php } ?>
	<?php }else{ ?>
	<?php foreach($aryMenu as $key => $value){ ?>
	<?php if(strpos ($key, "-") === false) {?>
	<?php $bg = ($count%2==0) ? "#EEEEEE" : "#FFFFFF" ?>
	<tr bgcolor="<?=$bg?>">
		<td align="center"><span class="style3"> <?=$count?> </span></td>
		<td align="center"><span class="style3"> <?=$aryMenu[$key][chr_no]?> </span></td>
		<td align="center"><span class="style3"> <?=$aryMenu[$key][chr_name]?> </span></td>
		<td align="center" bgcolor="#FFFFCC">
			<span class="style6">&nbsp;<?=$total[$aryMenu[$key][chr_no]] + 0?>&nbsp;</span>
		</td>
		<?php foreach ($aryDisplayShop as $shop) { ?>
		<?php if ($product[$shop][txt_name] != "") { ?>
		<td align="center" width="<?=$width?>"><span class="style6">&nbsp;
			<?=$product[$shop][$aryMenu[$key][chr_no]]?>
		&nbsp;</span></td>
		<?php } ?>
		<?php } ?>
	</tr>
	<?php $count++; ?>
	<?php } ?>
	<?php } ?>
	<?php } ?>
</table>