<?php

$character = $eve->getCharacter('90423421') ;
//echo json_encode($character->getCharacterSheet()) ;

//$character->loadMailBodies(array('ids' => array('306700306', '306676108', '306725618'))) ;
/*$character->loadCharacterSheet() ;
$character->loadAccountBalance() ;
$character->loadAssetList() ;
$character->loadMarketOrders() ;*/
$character->load(array(
					array('CharacterSheet'), 
					array('AccountBalance'), 
					array(
						'MailBodies', 
						array(
							'ids' => array(
									'306700306', 
									'306676108', 
									'306725618'
									)
						)
					)
				)) ;


			function print_ar($array, $count=0) {
			    $i=0;
			    $tab ='';
			    while($i != $count) {
			        $i++;
			        $tab .= "&nbsp;&nbsp;|&nbsp;&nbsp;";
			    }
			    foreach($array as $key=>$value){
			        if(is_array($value) OR is_object($value)){
			            echo $tab."[<strong><u>$key</u></strong>]<br />";
			            $count++;
			            print_ar($value, $count);
			            $count--;
			        }
			        else{
			            $tab2 = substr($tab, 0, -12);
			            echo "$tab2~ $key: <strong>$value</strong><br />";
			        }
			        $k++;
			    }
			    $count--;
			}

draw_tree($character) ;

//print_r($character) ;

//echo $character->skills[3300]->skillpoints ;

//echo json_encode($character) ;
