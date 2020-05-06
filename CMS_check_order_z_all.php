<?php
include("connect.inc");
session_start();
require('pdf/FPDF/chinese.php');
header('Content-Type: text/html; charset=big5');

class PDF_chinese_2 extends PDF_chinese
{
    var $deliDate_;
    var $ordDate_;

    function SetValue($deliDate, $ordDate)
    {
        $this->deliDate_ = $deliDate;
        $this->ordDate_ = $ordDate;
    }

    //Page header
    function Header()
    {
        $this->SetFont('Big5', 'U', 26);
        $this->MultiCell(0, 15, "貨倉 綜合訂購單", 0, 'C');
        $this->SetFont('Big5', '', 26);
        $this->Cell(100, 20, "送貨日期：" . $this->deliDate_, '0', 0, 'L', $fill);
        $this->SetFont('Big5', 'B', 14);
        $this->Cell(90, 20, "落單日期：" . $this->ordDate_, '0', 0, 'R', $fill);
        $this->Ln();
    }

    //Page footer
    function Footer()
    {
        //Position at 0 cm from top
        $this->SetY(0);
        //Arial italic 8
        $this->SetFont('Arial', 'I', 13);
        //Page number
        $this->Cell(170, 8, '', 0, 0, 'L');
        $this->Cell(25, 8, 'Page ' . $this->PageNo() . '/{nb}', 1, 0, 'R');
    }

    function orderHeaderWidth()
    {
        $w = array(15, 70, 40, 65);
        return $w;
    }

    function orderHeader()
    {
        $header = array('項目', '貨品', '編號', '數量');
        return $header;
    }
}

function order_z_dept($day, $time)
{
    require("connect.inc");
    if ($GLOBALS['debugMode']) {
        $testOrderDate = "2017-07-17";
    }

    $PDFPath = "order\\";

    $timestamp = gettimeofday("sec");
    $timestamp2 = gettimeofday("sec") + (60 * 60 * 24 * (1 + $day));

    $orderdate = DATE('Y-m-d', $timestamp);
    $delidate = DATE('Y-m-d', $timestamp2);
    $fill = false;
    //Next PO
    $sql_po_no = "SELECT * FROM tbl_order_po_no";
    $result_po_no = mysqli_query($con, $sql_po_no) or die($result_po_no);
    $record_po_no = mysqli_fetch_array($result_po_no);

    $po_pref = $record_po_no['chr_prefix'];
    $po_no = $record_po_no['int_po_no'];


    $sql = <<<EOT
 SELECT
	T4.int_id as user_id,
    T1.int_id as product_id,
	T5.chr_code as shop_code,
	T5.chr_name as shop_name, 
    T5.chr_tel as shop_tel,
    T5.chr_fax as shop_fax,
    T5.chr_address as shop_address,
    T1.chr_name as item_name,
    T1.chr_no as item_code,
    T3.chr_name as cat_name,
    T2.chr_name as group_name,
    T0.int_qty as qty,
    T6.chr_name as unit_name,
	T2.int_id as group_id
FROM tbl_order_z_dept T0
	LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id
	LEFT JOIN tbl_order_z_group T2 ON T1.int_group = T2.int_id
	LEFT JOIN tbl_order_z_cat T3 ON T2.int_cat = T3.int_id
    LEFT JOIN tbl_user T4 ON T0.int_user = T4.int_id
    LEFT JOIN tbl_district T5 ON T4.int_district = T5.int_id
    LEFT JOIN tbl_order_z_unit T6 ON T1.int_unit = T6.int_id
WHERE T0.status IN (1)
    AND DATE(order_date) = CURDATE()
	AND T0.chr_phase = '$day'
	AND T1.chr_cuttime = '$time'
ORDER BY T3.int_id, T2.int_id, T0.int_user, T1.int_sort;
EOT;
//die($sql);
    $result = mysqli_query($con, $sql) or die($sql);

    /*
     *  $printpage = Supplier = ONE PDF
     *  $orderuser = Shop = ONE order
     *  $subcount = Order Item Count of Order
     */
    $printpage = 1;
    $subcount = 1;
    $orderuser = "";
    while ($record = mysqli_fetch_array($result)) {
        $user_id = $record[user_id];
        $user[$user_id]['shop_code'] = str($record['shop_code']);
        $user[$user_id]['shop_name'] = str($record['shop_name']);
        $user[$user_id]['shop_tel'] = str($record['shop_tel']);
        $user[$user_id]['shop_fax'] = str($record['shop_fax']);
        $user[$user_id]['shop_address'] = str($record['shop_address']);

        $product_id = $record[product_id];
        $product['product_name'] = str($record['item_name']);
        $product['product_code'] = str($record['item_code']);
        $product['unit_name'] = str($record['unit_name']);
        $product['qty'] = str($record['qty']);

        $order_key = str("$record[cat_name] - $record[group_name]");
        $order[$order_key][$user_id][$product_id] = $product;
    }
    echo "<PRE>";
    print_r($order);
    echo "</PRE>";
    $subcount = 1;
    foreach ($order as $dept => $u) {
        $pdf = startNewPDF($day, $dept);
        $printpage = $dept;
        $filename = date('Y_n_j', $timestamp) . "_" . $time . "_" . substr($printpage, 0, 4) . "_" . $day . ".pdf";
        //$filename = date('Y_n_j',$timestamp)."_".$day.".pdf";
        $subcount = 1;
        foreach ($u as $userid => $item) {
            writeOrderHeader($pdf, $day, $time, $user[$userid], $po_pref, $po_no);

            $po_no = $po_no + 1;
            $sql_po = "UPDATE tbl_order_po_no SET int_po_no = " . ($po_no);
            $result_po = mysqli_query($con, $sql_po) or die($sql_po);

            $fill = false;
            $subcount = 1;
            foreach ($item as $itemid => $i) {
                $itemID[] = $itemid;
                writeOrderRecord($pdf, $i, $subcount, $fill);
            }
            $tempID = join(',', $itemID);
//		$sql  = "UPDATE tbl_order_z_dept ";
//		$sql .= "SET status = 98, chr_po_no = '".($po_no - 1)."' ";
//		$sql .= "WHERE DATE(order_date) = CURDATE() AND chr_phase = '$day' AND status IN(1) AND int_user = '$userid' AND int_product IN($tempID); ";
//		mysqli_query($con, $sql) or die($sql);


        }
        //
        savePDF($pdf, $filename, $PDFPath);
    }
}

/*
while($record = mysqli_fetch_array($result)){
  print_r($record);
  die('1');
//END and Save OLD PDF
//Start NEW PDF
if ($printpage <> $record['suppName']) {
	//END and SAVE OLD PDF
	if ($printpage <> 1) {
		$filename = date('Y_n_j',$timestamp)."_".$time."_".$printpage."_".$day."_m.pdf";
		savePDF($pdf, $filename, $PDFPath, $faxFilePath);
	}
  
	//START NEW PDF
	$pdf = startNewPDF($day, $record);
	$printpage = $record['suppName'];
	$faxnumber = $record['suppFax'];
	$emailaddress = $record['suppEmail'];
	if($pdf->LGD_deli)
		$emailaddress . ";LGD@taihingroast.com";
	$emailname = $record['suppName'];
	
	//PRINT FIRST ORDER HEADER
	$orderuser = writeOrderHeader($pdf, $day, $time, $tools, $record, $po_pref, $po_no);
	$subcount = 1;
}

//PRINT ORDER HEADER
if ($orderuser <> $record['shopName']) {
  $orderuser = writeOrderHeader($pdf, $day, $time, $tools, $record, $po_pref, $po_no);
  $subcount = 1;
}

//Data loading
writeOrderRecord($pdf, $record, $subcount);
} // END SQL WHILE

//SAVE LAST PDF
if(mysqli_num_rows($result) > 0) {
$filename = date('Y_n_j',$timestamp)."_".$time."_".$printpage."_".$day."_m.pdf";
savePDF($pdf, $filename, $PDFPath, $faxFilePath);
}
*/
function set_magic_quotes_runtime($new_setting)
{
    return true;
}

function str($str)
{
    //return iconv('utf-8', 'big5', $str);
    return mb_convert_encoding($str, "BIG5", "UTF-8");
}

function startNewPDF($day, $dept)
{

    $fill = false;
    $today = gettimeofday("sec");
    $deli = gettimeofday("sec") + (60 * 60 * 24 * (1 + $day));
    $week = array('日', '一', '二', '三', '四', '五', '六');

    $pdf = new PDF_chinese_2();
    $pdf->AliasNbPages();
    $pdf->AddBig5Font();
    $pdf->SetAutoPageBreak(1, 1);

    $deli_date = date('j/n/Y', $deli) . "(" . $week[date('w', $deli)] . ")";
    $curr_date = date('j/n/Y g:i a', $today);
    $pdf->SetValue($deli_date, $curr_date);

    $pdf->AddPage();
    $pdf->SetFillColor(207, 207, 207);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(.3);

    // PDF Header
    $pdf->SetFont('Big5', 'U', 16);
    $pdf->MultiCell(0, 8, $dept, 0, "L");
    $pdf->SetFont('Big5', '', 12);
    /*
    $pdf->Cell(50,8,"收件者：".$record['suppContact'],'0',0,'L',$fill);
    $pdf->Cell(50,8,"TEL：".$record['suppTel'],'0',0,'L',$fill);
    $pdf->Cell(50,8,"FAX：".$record['suppFax'],'0',0,'L',$fill);
    $pdf->Cell(40,8,"供應商編號：".$record['suppSAP'],'0',0,'L',$fill);
    $pdf->Ln();
    $pdf->Cell(100,8,"Email：".TRIM($record['suppEmail']),'0',0,'L',$fill);
    $pdf->Cell(90,8,"SMS：".TRIM($record['suppSMS']),'0',0,'L',$fill);
    */
    $pdf->Ln();

    //Order Field Header
    $header = $pdf->orderHeader();
    $w = $pdf->orderHeaderWidth();
    for ($i = 0; $i < count($header); $i++)
        $pdf->Cell($w[$i], 8, $header[$i], 1, 0, 'C', $fill);
    $pdf->Ln();

    echo $GLOBALS['var'];

    return $pdf;
}

function writeOrderHeader($pdf, $day, $time, $shop, $po_prefix, $po_no)
{
    $pdf->Ln();
    $pdf->SetFont('Big5', 'B', 12);

    $orderHeader = "#" . $shop['shop_code'] . " " . $shop['shop_name'];
    $orderHeader .= "(PO#$po_prefix" . str_pad($po_no, 6, '0', STR_PAD_LEFT) . ")";
    $orderHeader .= "- 電話:" . $shop['shop_tel'];
    $pdf->MultiCell(0, 7, $orderHeader, 0, "L");

    $orderHeader = "  地址:" . $shop['shop_address'];
    $pdf->MultiCell(0, 7, $orderHeader, 0, "L");
}

function writeOrderRecord($pdf, $item, &$subcount, &$fill)
{
    $w = $pdf->orderHeaderWidth();

    $pdf->SetFont('Big5', 'B', 12);
    $pdf->Cell($w[0], 7, $subcount, '1', 0, 'C', $fill);
    $pdf->SetFont('Big5', 'B', 10);
    $pdf->Cell($w[1], 7, $item['product_name'], '1', 0, 'C', $fill);
    $pdf->SetFont('Big5', 'B', 12);
    $pdf->Cell($w[2], 7, $item['product_code'], '1', 0, 'C', $fill);
    $pdf->SetFont('Big5', 'B', 11);
    $pdf->Cell($w[3], 7, round($item['qty'], 2) . " " . $item['unit_name'], '1', 0, 'C', $fill);
    $pdf->Ln();
    $subcount += 1;
    $fill = !$fill;
}

function savePDF($pdf, $filename, $PDFPath)
{
    $pdf->SetFont('Big5', 'B', 26);
    $pdf->MultiCell(0, 14, "- 完 -", 0, 'C');
    $pdf->SetFont('Big5', 'B', 12);
    $pdf->MultiCell(0, 6, "", 0, 'C');
    $pdf->SetFont('Big5', 'B', 16);
    $pdf->MultiCell(0, 6, "此乃自動發出之傳真，如能如期送貨，無需回覆", 0, 'C');
    $pdf->MultiCell(0, 6, "如有任何問題，請聯絡落單分店或本公司採購部", 0, 'C');

    $pdf->Output($PDFPath . $filename, 'F');
}


?>