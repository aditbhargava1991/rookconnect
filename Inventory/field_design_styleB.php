<!DOCTYPE html>
<?php 
$style_settings = $_GET['style_settings'];

$color = "#000000";
$units = "12";
$page_ori = "Portrait";

$file_name = "";

$font_heading_size = "12";
$font_heading_type = "";
$font_heading = "times";

$font_main_heading_size = "12";
$font_main_heading_type = "";
$font_main_heading = "times";

$font_main_body_size = "12";
$font_main_body_type = "";
$font_main_body = "times";

$font_footer_size = "12";
$font_footer_type = "";
$font_footer = "times";

$pdf_header_logo = "";
$pdf_footer_logo = "";
$pdf_header_logo_align = "C";
$pdf_footer_logo_align = "C";

$margin_left = "10px";
$margin_right = "10px";
$margin_top = "10px";
$margin_header = "10px";
$margin_bottom = "10px";

$heading_color = "#000000";
$main_body_color = "#000000";
$main_heading_color = "#000000";
$footer_color = "#000000";

$footer_text = "Test Footer";
$header_text = "Test Header";

$select_pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "select * from inventory_pdf_setting where style = '$style_settings'"));
if(!empty($select_pdf_settings)) {
    $file_name = $select_pdf_settings['file_name'];

    $font_heading_size = $select_pdf_settings['font_size'];
    $font_heading_type = $select_pdf_settings['font_type'];
    $font_heading = $select_pdf_settings['font'];

    $font_main_heading_size = $select_pdf_settings['font_size'];
    $font_main_heading_type = $select_pdf_settings['font_type'];
    $font_main_heading = $select_pdf_settings['font'];

    $font_main_body_size = $select_pdf_settings['font_size'];
    $font_main_body_type = $select_pdf_settings['font_type'];
    $font_main_body = $select_pdf_settings['font'];

    $font_footer_size = $select_pdf_settings['font_size'];
    $font_footer_type = $select_pdf_settings['font_type'];
    $font_footer = $select_pdf_settings['font'];

    $pdf_header_logo = $select_pdf_settings['pdf_logo'];

    $pdf_size = $select_pdf_settings['pdf_size'];
    $page_ori = $select_pdf_settings['page_ori'];
    $units = $select_pdf_settings['units'];
    $margin_left = $select_pdf_settings['left_margin'];
    $margin_right = $select_pdf_settings['right_margin'];
    $margin_top = $select_pdf_settings['top_margin'];
    $margin_header = $select_pdf_settings['header_margin'];
    $margin_bottom = $select_pdf_settings['bottom_margin'];

    $heading_color = $select_pdf_settings['pdf_color'];
    $main_body_color = $select_pdf_settings['pdf_color'];
    $main_heading_color = $select_pdf_settings['pdf_color'];
    $footer_color = $select_pdf_settings['pdf_color'];
}

$select_header_pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "select * from inventory_pdf_setting where style = '$style_settings' AND setting_type = 'header'"));
if(!empty($select_header_pdf_settings)) {
    $file_name = $select_header_pdf_settings['file_name'];

    $font_heading_size = $select_header_pdf_settings['font_size'];
    $font_heading_type = $select_header_pdf_settings['font_type'];
    $font_heading = $select_header_pdf_settings['font'];

    $pdf_header_logo = $select_header_pdf_settings['pdf_logo'];
    $pdf_header_logo_align = $select_header_pdf_settings['alignment'];

    $heading_color = $select_header_pdf_settings['pdf_color'];
    $header_text = $select_header_pdf_settings['text'];
}

$select_footer_pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "select * from inventory_pdf_setting where style = '$style_settings' AND setting_type = 'footer'"));
if(!empty($select_footer_pdf_settings)) {
    $file_name = $select_footer_pdf_settings['file_name'];

    $font_footer_size = $select_footer_pdf_settings['font_size'];
    $font_footer_type = $select_footer_pdf_settings['font_type'];
    $font_footer = $select_footer_pdf_settings['font'];

    $pdf_footer_logo = $select_footer_pdf_settings['pdf_logo'];
    $pdf_footer_logo_align = $select_footer_pdf_settings['alignment'];

    $footer_color = $select_footer_pdf_settings['pdf_color'];
    $footer_text = $select_footer_pdf_settings['text'];
}

$select_main_pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "select * from inventory_pdf_setting where style = '$style_settings' AND setting_type = 'main'"));
if(!empty($select_main_pdf_settings)) {

    $font_main_heading_size = $select_main_pdf_settings['font_size'];
    $font_main_heading_type = $select_main_pdf_settings['font_type'];
    $font_main_heading = $select_main_pdf_settings['font'];

    $font_main_body_size = $select_main_pdf_settings['font_body_size'];
    $font_main_body_type = $select_main_pdf_settings['font_body_type'];
    $font_main_body = $select_main_pdf_settings['font_body'];

    $main_body_color = $select_main_pdf_settings['pdf_body_color'];
    $main_heading_color = $select_main_pdf_settings['pdf_color'];
}

if($pdf_header_logo_align == "C") {
	$pdf_header_logo_align = 'style="display: inline; position: relative; left: calc(50% - 75px)"';
	$header_align = 'right';
} else if($pdf_header_logo_align == "L") {
	$pdf_header_logo_align = 'class="pull-left"';
	$header_align = 'right';
} else {
	$pdf_header_logo_align = 'class="pull-right"';
	$header_align = 'left';
}

if($pdf_footer_logo_align == "C") {
	$pdf_footer_logo_align = 'style="display: inline; position: relative; left: calc(50% - 75px)"';
	$footer_align = 'right';
} else if($pdf_footer_logo_align == "L") {
	$pdf_footer_logo_align = 'class="pull-left"';
	$footer_align = 'right';
} else {
	$pdf_footer_logo_align = 'class="pull-right"';
	$footer_align = 'left';
}
?>

<h3 style="margin-left:10px"><?php echo "Design Style"; ?></h3>
<div class="clearfix"></div>
<div style="margin:10px;padding:25px;height:auto" class="sidebar">
	<h4>Choose Design Style</h4>
	<a href="?type=pdfstyling&style_settings=design_styleA"><canvas id="myCanvas" width="130" height="120"></canvas></a>
	<a href="?type=pdfstyling&style_settings=design_styleB"><canvas id="myCanvas1" width="130" height="120"></canvas></a>
	<a href="?type=pdfstyling&style_settings=design_styleC"><canvas id="myCanvas2" width="130" height="120"></canvas></a>
	<hr>
	<div style="padding:50px;">
		<span class="pull-<?= $header_align ?>" style="color:<?= $heading_color ?>;font-size:<?= $font_heading_size ?>px;font-family:<?= $font_heading ?>;font-style:<?= $font_heading_type ?>;"><?= $header_text ?></span>
		<?php if(!empty($pdf_header_logo)) { ?>
			<span <?= $pdf_header_logo_align ?>><img src="download/<?php echo $pdf_header_logo; ?>" width="150" height="120"/></span>
		<?php } ?>
		<div class="clearfix"></div>
		<br>
		<table border="1" style="font-size:<?php echo $font_main_body_size; ?>px;font-style:<?php echo $font_main_body_type; ?>;font-family:<?php echo $font_main_body; ?>;color:<?php echo $main_body_color; ?>;border:3px solid black;width:100%">
			<tr style="font-size:<?php echo $font_main_heading_size; ?>px;font-style:<?php echo $font_main_heading_type; ?>;font-family:<?php echo $font_main_heading; ?>;color:<?php echo $main_heading_color; ?>">
				<td>Code</td>
				<td>Tab</td>
				<td>Name</td>
				<td>Unit Price</td>
				<td>Final Retail Price</td>
			</tr>
			<?php for($i = 0; $i < 20; $i++) { ?>
				<tr>
					<td>Test Code</td>
					<td>Test Tab</td>
					<td>Test Name</td>
					<td>$100.00</td>
					<td>$150.00</td>
				</tr>
			<?php } ?>
		</table>
		<br>
		<span class="pull-<?= $footer_align ?>" style="color:<?= $footer_color ?>;font-size:<?= $font_footer_size ?>px;font-family:<?= $font_footer ?>;font-style:<?= $font_footer_type ?>;"><?= $footer_text ?></span>
		<?php if(!empty($pdf_footer_logo)) { ?>
			<span <?= $pdf_footer_logo_align ?>><img src="download/<?php echo $pdf_footer_logo; ?>" width="150" height="120"/></span>
		<?php } ?>
		<div class="clearfix"></div>
		<br><br>
		<span class="pull-right">
			<a href="?style_settings=<?php echo $style_settings; ?>&settings=pdf_setting"><button class="btn brand-btn hide-titles-mob" type="button" value="PDF Setting for Style <?php echo substr($style_settings, -1); ?>" >PDF Setting for <?php echo substr($style_settings, -1); ?></button></a>
		</span>
		<br><br>
	</div>
</div>
<script>
var style_set = '<?php echo $style_settings; ?>';
var style_colorA = "#ebebeb";
var text_colorA = "black";
var style_colorB = "#ebebeb";
var text_colorB = "black";
var style_colorC = "#ebebeb";
var text_colorC = "black";
if(style_set == 'design_styleA') {
	style_colorA = "#39C";
	text_colorA = "white";
}
if(style_set == 'design_styleB') {
	style_colorB = "#39C";
	text_colorB = "white";
}
if(style_set == 'design_styleC') {
	style_colorC = "#39C";
	text_colorC = "white";
}

var c = document.getElementById("myCanvas");
var ctx = c.getContext("2d");
ctx.fillStyle = style_colorA;
ctx.fillRect(20, 20, 100, 100);
ctx.fillStyle = text_colorA;
ctx.font = "40pt Helvetica";
ctx.fillText("A", 50, 90);
ctx.stroke();

var c = document.getElementById("myCanvas1");
var ctx = c.getContext("2d");
ctx.fillStyle = style_colorB;
ctx.fillRect(20, 20, 100, 100);
ctx.fillStyle = text_colorB;
ctx.font = "40pt Helvetica";
ctx.fillText("B", 50, 90);
ctx.stroke();

var c = document.getElementById("myCanvas2");
var ctx = c.getContext("2d");
ctx.fillStyle = style_colorC;
ctx.fillRect(20, 20, 100, 100);
ctx.fillStyle = text_colorC;
ctx.font = "40pt Helvetica";
ctx.fillText("C", 50, 90);
ctx.stroke();

</script> 
