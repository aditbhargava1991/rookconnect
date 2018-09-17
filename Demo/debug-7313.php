<?php
    include_once('../include.php');

    if(!$dbc2) {
		echo '<h3>Second database not configured. Please configure the second database before you compare configurations.</h3>';
	} else {
        $tables_config = mysqli_query($dbc, "SELECT table_name FROM information_schema.tables WHERE table_type = 'base table' AND table_schema='demo_rookconnect_db' AND table_name LIKE '%_config%'");
        $SrNo = 1;
        while($resTables = mysqli_fetch_assoc($tables_config)) {
            $table_config = $resTables['table_name'];
            echo $SrNo. " - ". $table_config."<br>";
            $SrNo++;
        }
    }

?>