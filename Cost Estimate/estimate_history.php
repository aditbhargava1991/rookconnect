<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('cost_estimate');
error_reporting(0);
?>
<script type="text/javascript">

</script>

</head>
<body>

<div class="container">
	<div class="row">

        <h1>Cost Estimate History</h1>

        <?php
        $estimateid = $_GET['estimateid'];
        $result_est = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT history FROM cost_estimate WHERE estimateid='$estimateid'"));

        echo $result_est['history'];

        ?>

	</div>
</div>