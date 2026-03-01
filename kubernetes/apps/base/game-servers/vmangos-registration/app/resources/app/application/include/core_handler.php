<?php
/**
 * @author Amin Mahmoudi (MasterkinG)
 * @copyright    Copyright (c) 2019 - 2024, MasterkinG32. (https://masterking32.com)
 * @link    https://masterking32.com
 **/

$core_config['salt_field'] = 'salt';
$core_config['verifier_field'] = 'verifier';

if($config['server_core'] == 5) { // CMangos
	$core_config['salt_field'] = 's';
	$core_config['verifier_field'] = 'v';
}
