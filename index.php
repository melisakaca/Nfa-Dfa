<?php
include 'resources.php'; 
include 'logic.php';

$ex = examples();


$i = 0;


//resource meret nga file resource te cilin e kemi ber include
if ($_POST) {
	$i = $_POST['resource'];
	$nfa = $ex[$i];
	$return['nfa'] = $nfa['graph'];
	$return['dfa'] = nfa_to_dfa($nfa);
	die(json_encode($return));
}

$nfa = $ex[$i];
$dfa = nfa_to_dfa($nfa);

?>
<!--i gjigh kodi me poshfe sherben si view per te afishuar 3 llojet e shembullit te grafit--->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Konvetimi AFJD NE AFD</title>
    <link type="text/css" rel="stylesheet" href="css/style.css" />
</head>

<body>
    <p>Zgjidh grafin:</p>
    <select id="change">
        <option value="0">Shembull 1 </option>
        <option value="2">Shembull 2</option>
    </select>
    <p>Grafi fillestar AFJD </p>
    <ul class="display_list" id="show_nfa">
        <li><?php
			$nfa = show_simple($nfa['graph']);
			echo '<ul class="graph">';
			foreach ($nfa as $key => $value) {
				echo '<li>' . $key . '</li><li>	=> ' . $value . '</li>';
			}
			echo '</ul>';
			?>
        </li>
        <li><img src="images/nfa<?php echo $i ?>.jpg"></li>
    </ul>
    <br>
    <p> Konvertimi ne AFD </p>
    <ul class="display_list" id="show_dfa">
        <li><?php
			echo '<ul class="graph">';
			foreach ($dfa as $key => $value) {
				echo '<li>' . $key . '</li><li>	=> ' . $value . '</li>';
			}
			echo '</ul>';
			?>
        </li>
        <li><img src="images/dfa<?php echo $i ?>.jpg"></li>
    </ul>

    <script src="lib/jquery-2.1.0.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>

