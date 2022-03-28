<?php

//PArametrat iniciliazues
//nfa esht variabel global qe sherben per iniciLiximin e grafit
function get_initial($nfa){
	$start = $end = $alphabet = $all_keys = $dfa_possible = [];//mer si shembull ge gjith rastet aflanumerike per te afishhar grafin,ne rrastin tone 3 raste

	if(isset($nfa['finish'])) $end = $nfa['finish'];//me anen e nje coll isset kontrollon nq jan plotesuar rastet dhe ka arritur ne gjendjen finish
	//dhe ne momentin qe jemi finish afishojme grafin
	$nfa = $nfa['graph'];

	foreach ($nfa as $key => $connected) {//bejm connection e grafit per 3 llojet duke i percaktuar vet key 
		$key_el = explode(".", $key);

		//incializojme real key duke bredhur grafin 
		$key_real = $key_el[count($key_el)-2];
		if(!in_array($key_el[count($key_el)-1], $alphabet)) $alphabet[] = $key_el[count($key_el)-1];

		
		//ne switch kontrollojme llojin e conection fe gafit
		//nqs elementi esht tek,3 ath jemi ne grafin fillestar
		//nqs key kalon ne cift elementesh ath kalojm ne fund te grafit
		switch(count($key_el)){
			case '3':
					//Elementi fillestar ose perfundimtar
					if($key_el[0]=='S' && !in_array($key_el[1], $start))
						$start[] = $key_el[1];
					elseif ($key_el[0]=='F' && !in_array($key_el[1], $end))
						$end[] = $key_el[1];

					break;
			case '4':
					if(!in_array($key_el[2], $start))
						$start[] = $key_el[2];

					if(!in_array($key_el[2], $end))
						$end[] = $key_el[2];

					break;
		}
		//Marrim gjendjet  nga nfa
		if(!in_array($key_real, $all_keys))
			$all_keys[] = $key_real;

		//Marrim elementin fillestar from nfa
		if(count($dfa_possible) == 0){
			$dfa_possible[] = $key_real;
			$i = true;
		}
	}

	return array($start, $end, $alphabet, $all_keys, $dfa_possible, $i);
}

//Heqim epsilonet nese ekzistojne 
function remove_epsylon($nfa){
	$start = $end = $alphabet = $all_keys = $dfa_possible = []; 
	// merr te gjitha rastet e mundshme affabetike dhe filim dhe ne fund tper te percaktuar dfa


	list($start, $end, $alphabet, $all_keys, $dfa_possible, $i) = get_initial($nfa);
	$nfa = $nfa['graph'];

	//Nese eksiztojne kalime me epsilon
	if(in_array('EP', $alphabet)){
		foreach ($nfa as $key => $value) {
			$key_el = explode(".", $key);
			//marrim gjendjet
			$key_real = $key_el[count($key_el)-2];
			$key_alpha = $key_el[count($key_el)-1];

			//percaktojme kalimin me epsilon
			if($key_alpha=='EP') {
				//nese eshte kalimi fillestar procedojme
				if(in_array($key_real, $start)){

					//Shto s ose F nese eshte starti ose finishi
					if(in_array($key_real, $end)) $key_real = 'F.'.$key_real;
					$key_real = 'S.'.$key_real;

					// kontrollon cdo gjendje qe kalon me epsilon
					foreach($value as $val){
						//lidh cdo shkroje te alfabetit dhe kontrollo
					
						foreach ($alphabet as $alpha) {
							$mod_key_real = $key_real.'.'.$alpha;
							$mod_val = $val.'.'.$alpha;
							//shtojme s ose f nese eshte start ose finish
							if(in_array($val, $end)) $val = 'F.'.$val;
							if(in_array($val, $start)) $val = 'S.'.$val;
							//kontrollon nfa dhe merr cdo kalim
							foreach($nfa as $n_key=>$n_val){
								if($n_key==$mod_val){
									
									$not_exist = true;
									foreach ($nfa as $o_key => $o_value) {
										//kontrollon nese ekziston
										if($o_key == $mod_key_real){
											$not_exist = false;
											$nfa[$mod_key_real] = array_unique(array_merge($n_val, $o_value));
										}
									}
									
									if($not_exist){
										$nfa[$mod_key_real] = $n_val;
									}
								}
							}
						}
					}
				}else{
					//nese nuk eshte start shkon pas dhe lidhet me gjendjen e meparshme
					foreach ($nfa as $n_key => &$n_value) {
						if(in_array($key_real, $n_value)){
							$n_value = array_merge($n_value, $value);
						}
					}
				}

				//heq epsilonin
				unset($nfa[$key]);
			}
		}

		//modifkon alfabetin per te hequr epsilonin
		if(($key = array_search("EP", $alphabet)) !== false) {
		    unset($alphabet[$key]);
		}
	}
	return array($nfa, $start, $end, $alphabet, $all_keys, $dfa_possible, $i);
}


//shton kalime nese mungojne
function add_missing_branches($dfa, $alphabet, $start, $end, $w_field){
	$passed = [];
	
	foreach ($start as $st) {
	
		if(in_array($st, $end))
			$st = 'F.'.$st;
		$st = 'S.'.$st;

		foreach ($alphabet as $alpha) {
			$m_st = $st;
			$m_st = $m_st.'.'.$alpha;
			if(!isset($dfa[$m_st]))
				$dfa[$m_st] = 'W';
		}
	}
	foreach ($dfa as $key => $connected) {
		if(!in_array($connected, $passed)){
			$passed[]=$connected;
			if($connected!='W'){

				$is_start = $is_finish = false;
				$conn_vals = explode(',', $connected);

				foreach ($conn_vals as $conn_v) {
					//vendos S nese eshte start
					if(in_array($conn_v, $start)){
						$is_start = true;
						break;
					}
				}
				foreach ($conn_vals as $conn_v) {
					//vendos F nese eshte finish
					if(in_array($conn_v, $end)){
						$is_finish = true;
						break;
					}
				}
				if($is_finish) $connected = 'F.'.$connected;
				if($is_start)  $connected = 'S.'.$connected;

				$m_conn_vals = $connected;
				foreach ($alphabet as $alpha) {
					$m_conn_vals = $connected.'.'.$alpha;
					if(!isset($dfa[$m_conn_vals])){
						$dfa[$m_conn_vals] = 'W';
						$w_field = true;
					}
				}
			}
		}
	}
	return ['dfa'=>$dfa, 'w_field'=>$w_field];
}

//Konvertimi I Nfa ne dfa
function nfa_to_dfa($nfa){

	$dfa = $start = $end = $alphabet = $all_keys = $dfa_possible = $dfa_history = [];
	$w_field = false; 

	//Zevendesimi i epsilon 
	//merr te gjithe elementet e dfa
	list($nfa, $start, $end, $alphabet, $all_keys, $dfa_possible, $i) = remove_epsylon($nfa);

	//ndodh konvertimi
	while($i){
		
		//array me pozicionet
		$poss_arr = array();
		
		$look_element = array_shift($dfa_possible);

		//ruan elementet qe mos kete dublikime
		if(!in_array($look_element, $dfa_history)) $dfa_history[] = $look_element;

		
		//kontrollojme pozicionin e gjendjes s f
		if(strpos($look_element, ',') > 0){
			$poss_arr = explode(",", $look_element);
		}else{
			$poss_arr[0] = $look_element;
		}

		foreach($poss_arr as $simple){
			foreach ($nfa as $key => $connected) {
				$key_el = explode(".", $key);
				
				$key_real = $key_el[count($key_el)-2];
				$key_alpha = $key_el[count($key_el)-1];

				if($key_real.".".$key_alpha == $simple.".".$key_alpha){

					$dfa_key = $look_element.".".$key_alpha;

					//kontrollojme start dhe finish
					foreach($poss_arr as $elF){
						if(in_array($elF, $end)){
							$dfa_key = "F.".$dfa_key;
							break;
						}
					}
					foreach($poss_arr as $elS){
						if(in_array($elS, $start)){
							$dfa_key = "S.".$dfa_key;
							break;
						}
					}

					$dfa_value = implode(',', $connected)!='' ? implode(',', $connected) : 'W';

					//konyrollohen vlerat
					if(!in_array($dfa_value, $dfa_possible) &&
					   !in_array($dfa_value, $dfa_history) &&
					   $dfa_value!='W')
							$dfa_possible[] = $dfa_value;

				
					$add_new = true;
					foreach($dfa as $key=>$value){
						if($key == $dfa_key){
							if($value == 'W') 			$dfa[$key] = $dfa_value;
							elseif($dfa_value == 'W')	$dfa[$key] = $value;
							elseif($value != $dfa_value) {
								//hiqin dublikimet
								$value = strpos($value, ',') > -1 ? explode(',', $value) : (array)$value;
								$dfa_value = strpos($dfa_value, ',') > -1 ? explode(',', $dfa_value) : (array)$dfa_value;
								$merged_value = implode(array_unique(array_merge($value, $dfa_value)));

								//updatohet vlera
								$dfa[$key] = $merged_value;

								//vlerat pasardhese
								if(!in_array($merged_value, $dfa_possible) &&
								   !in_array($merged_value, $dfa_history))
										$dfa_possible[] = $merged_value;
							}

							$add_new = false;
							break;
						}
					}

					//shtohen nese nuk ekzistonte
					if($add_new) {
						if($dfa_value == 'W') $w_field = true;
						$dfa[$dfa_key] = $dfa_value;
					}
				}
			}
		}

		if(count($dfa_possible)	== 0)	$i = false;
	}

	//Kontrollon dfa e konvertuar nese mungon ndonje gjendje
	$return_missing = add_missing_branches($dfa, $alphabet, $start, $end, $w_field);
	$dfa = $return_missing['dfa'];
	$w_field = $return_missing['w_field'];

	//shtohet W
	if($w_field){
		foreach ($alphabet as $alpha) {
			$dfa['W.'.$alpha] = 'W';
		}
	}


	return $dfa;

} 
