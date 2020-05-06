<table border="1" align="left" cellpadding="2" cellspacing="2">
    <tr bgcolor="#CCFFFF">
        <td align="center"><strong>#</strong></td>
        <td align="center"><strong>分店</strong></td>
        <?php foreach ($aryMenu as $key => $menu) { ?>
            <?php if (strpos($key, "-") === false) { ?>
                <?php $name = ($menu[chr_reportName] == "") ? $menu[chr_name] : $menu[chr_reportName]; ?>
                <td style="width:200px;" align="center"><strong><?= $name ?><br>(<?= $menu[chr_no] ?>)</strong></td>
            <?php } ?>
        <?php } ?>
    </tr>

    <?php $count = 1; ?>
    <?php foreach ($product as $key => $value) { ?>
        <?php if (in_array($value[shop_id], $aryDisplayShop)) { ?>
            <?php $bg = ($count % 2 == 0) ? "#EEEEEE" : "#FFFFFF" ?>
            <tr bgcolor="<?= $bg ?>">
                <td align="center"><span class="style3"><?= $count ?></span></td>
                <td align="center">
		<span class="style3">
			<?= $key ?>
		</span>
                </td>
                <?php foreach ($aryMenu as $mkey => $menu) { ?>
                    <?php if (strpos($mkey, "-") === false) { ?>
                        <td align="center"><span class="style6">&nbsp;<?= $value[$menu[chr_no]] ?>&nbsp;</span></td>
                    <?php } ?>
                <?php } ?>
            </tr>
            <?php $count++; ?>
        <?php } ?>
    <?php } ?>
    <tr bgcolor="#CCFFFF">
        <td align="center" width="3%"><strong>#</strong></td>
        <td align="center" width="15%"><strong>分店</strong></td>
        <?php foreach ($aryMenu as $mkey => $menu) { ?>
            <?php if (strpos($mkey, "-") === false) { ?>
                <?php $name = ($menu[chr_reportName] == "") ? $menu[chr_name] : $menu[chr_reportName]; ?>
                <td align="center"><strong><?= $name ?><br>(<?= $menu[chr_no] ?>)</strong></td>
            <?php } ?>
        <?php } ?>
    </tr>
    <tr>
        <td colspan="2" align="right" bgcolor="#FFFFCC">總數</td>
        <?php foreach ($aryMenu as $mkey => $menu) { ?>
            <?php if (strpos($mkey, "-") === false) { ?>
                <td align="center" bgcolor="#FFFFCC">&nbsp;<span class="style6"><?= $total[$menu[chr_no]] + 0 ?></span>&nbsp;
                </td>
            <?php } ?>
        <?php } ?>
    </tr>
</table>