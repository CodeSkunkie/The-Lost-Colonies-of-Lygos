<?php

	$this->require_login();
	
	require(WEBROOT .'classes/Colony_Building.php');
	require(WEBROOT .'classes/Resource_Bundle.php');
	
	$colony_id = clean_text($_GET['colony_id']);
	
	$this->data['buildings'] = array();
	$bldg_qry = $Mysql->query("SELECT * FROM `buildings`
	WHERE `colony_id` = '". $colony_id ."'");
	while ( $bldg_row = $bldg_qry->fetch_assoc() )
	{
		$bldg_class_name = Colony_Building::type2classname($bldg_row['type']);
		require(WEBROOT .'classes/'. $bldg_class_name .'.php');
		$building = new $bldg_class_name($bldg_row);
		$this->data['buildings'][] = $building;
		$this->data['update_cost'][] = $building->upgrade_cost();
	}
	
?>