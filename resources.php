<?php
//deklarimi i epsilon
$EP = 'epsylon';


function examples(){

	
	//shenbuj te grafeve sipas funksioneve deri ne 3nf
	$nfa0['graph'] = ["S.F.0.a" =>[1,2],					  
					  "F.1.2.a"=>[1,2],					  					  
					  "2.b"=>[1,3],
					  "3.a"=>[1,2],					  
					];
	
	$nfa1['graph'] = ["S.0.a"=>[1,2,3],
					  "S.0.b"=>[2,3],
					  "F.1.a"=>[1,2],
					  "F.1.b"=>[2,3],					  
					  "2.b"=>[2,3,4],
					  "3.a"=>[4],
					  "3.b"=>[2,3,4],					  
					];

	$nfa2['graph'] = ["S.0.a"=>[2,4],
					 	"S.0.b"=>[1,2,4],
					 	"1.a"=>[0,3],
					 	"1.b"=>[0,3],					 	
					 	"2.b"=>[0,3],
					 	"3.a"=>[2,4],
					 	"3.b"=>[1,2,4],					 	
					];

	/*
			inicializmi i grafit
	*/
	$nfa3['graph'] = ["S.A.0"=>['B','G'],
						"S.A.EP"=>['C'],
					    "S.A.1"=>['D'],
					    "B.EP"=>['C'], 
					    "C.1"=>['B'],
					    "C.0"=>['F'],
					    "D.1"=>['A'],
					    "D.EP"=>['F'],
					    "G.1"=>['F']
					];
	$nfa3['finish'] = ['F'];

	
	$nfa = [$nfa0, $nfa1, $nfa2, $nfa3];

	return $nfa;
}
//cunksioni i cili sherben per te ehfaqur grafet ne baZE te variablave dhe vektoreve te percaktuar me lart
function show_simple($nfa){
	$show_nfa = [];
	foreach ($nfa as $key => $value) {
		if(!isset($show_nfa[$key])){
			$show_nfa[$key] = implode(',',$value);
		}
	}
	return $show_nfa;
}
?>