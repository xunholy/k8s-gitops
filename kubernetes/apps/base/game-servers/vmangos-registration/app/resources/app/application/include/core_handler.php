<?php
/**
 * VMaNGOS Registration Portal - Core Handler
 **/

$core_config['salt_field'] = 'salt';
$core_config['verifier_field'] = 'verifier';

if($config['server_core'] == 5) { // CMangos
	$core_config['salt_field'] = 's';
	$core_config['verifier_field'] = 'v';
}
