<?php
/*
Customer Listing
*/
include ('include.php');
?>
<script type="text/javascript">
   function handleClick(sel) {

    var stagee = sel.value;
	var contactid = $('.contacterid').val();

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "ajax_all.php?fill=styler_configuration&contactid="+contactid+"&value="+stagee,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});

}

function sortTable(){
    var tbl = document.getElementById("caltbl").tBodies[0];
    var store = [];
    for(var i=0, len=tbl.rows.length; i<len; i++){
        var row = tbl.rows[i];
        var sortnr = parseFloat(row.cells[0].textContent || row.cells[0].innerText);
        if(!isNaN(sortnr)) store.push([sortnr, row]);
    }
    store.sort(function(x,y){
        return x[0] - y[0];
    });
    for(var i=0, len=store.length; i<len; i++){
        tbl.appendChild(store[i][1]);
    }
    store = null;
}
sortTable();
</script>
</head>
<body>
<?php include_once ('navigation.php');
checkAuthorised();
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

        <?php include('Settings/settings_navigation.php'); ?>
        <br><br>
		<div id="">
        <?php
	$contactidfortile = $_SESSION['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactidfortile'");
    while($row = mysqli_fetch_assoc($result)) {
		$software_config = $row['software_styler_choice'];
    }
        ?>
		<!-- If you change anything here, it should also be changed in the login_page_style.php, header.php, admin_software_config.php as well. -->
        <table class='table table-bordered' id="caltbl">
            <tr class='hidden-sm '>
                <th  data-title="Software Style">Software Style</th>
                <th  data-title="Activation">
                Activation</th>
            </tr>
			<tr>
				<td>Default</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == '') { echo "checked"; } ?> value=''></td>
			</tr>
			<tr>
				<td>Black</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'washt') { echo "checked"; } ?> value='washt'></td>
			</tr>
			<tr>
				<td>Black & Orange</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackorange') { echo "checked"; } ?> value='blackorange'></td>
			</tr>
			<tr>
				<td>Black & Purple</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackpurple') { echo "checked"; } ?> value='blackpurple'></td>
			</tr>
			<tr>
				<td>Black & Red</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackred') { echo "checked"; } ?> value='blackred'></td>
			</tr>
			<tr>
				<td>Black & Turquoise</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'turq') { echo "checked"; } ?> value='turq'></td>
			</tr>
			<tr>
				<td>Black Neon (Blue)</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackneon') { echo "checked"; } ?> value='blackneon'></td>
			</tr>
			<tr>
				<td>Black Neon (Red)</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackneonred') { echo "checked"; } ?> value='blackneonred'></td>
			</tr>
			<tr>
				<td>Break the Barrier</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'btb') { echo "checked"; } ?> value='btb'></td>
			</tr>
			<tr>
				<td>Chrome</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'chrome') { echo "checked"; } ?> value='chrome'></td>
			</tr>
			<tr>
				<td>Clinic Ace</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'bgw') { echo "checked"; } ?> value='bgw'></td>
			</tr>
			<tr>
				<td>Cosmic</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'cosmos') { echo "checked"; } ?> value='cosmos'></td>
			</tr>
			<tr>
				<td>Cotton Candy</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'purp') { echo "checked"; } ?> value='purp'></td>
			</tr>
			<tr>
				<td>Flowers</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'flowers') { echo "checked"; } ?> value='flowers'></td>
			</tr>
			<tr>
				<td>Fresh Focus Media</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'ffm') { echo "checked"; } ?> value='ffm'></td>
			</tr>
			<tr>
				<td>FFM White</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'ffmwhite') { echo "checked"; } ?> value='ffmwhite'></td>
			</tr>
			<tr>
				<td>Green</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'green') { echo "checked"; } ?> value='green'></td>
			</tr>
			<tr>
				<td>Green & Grey</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'silver') { echo "checked"; } ?> value='silver'></td>
			</tr>
			<tr>
				<td>Leopard Print</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'leo') { echo "checked"; } ?> value='leo'></td>
			</tr>
			<tr>
				<td>Navy</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'navy') { echo "checked"; } ?> value='navy'></td>
			</tr>
			<tr>
				<td>Polka Dots</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'polka') { echo "checked"; } ?> value='polka'></td>
			</tr>
			<tr>
				<td>Precision Workflow (Black)</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'bwr') { echo "checked"; } ?> value='bwr'></td>
			</tr>
			<tr>
				<td>Precision Workflow (White)</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'swr') { echo "checked"; } ?> value='swr'></td>
			</tr>
			<tr>
				<td>ROOK Connect</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blw') { echo "checked"; } ?> value='blw'></td>
			</tr>
			<tr>
				<td>Smiley Faces</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'happy') { echo "checked"; } ?> value='happy'></td>
			</tr>

        </table>

<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>
		</div>
        </div>
    </div>
</div>
<?php include ('footer.php'); ?>