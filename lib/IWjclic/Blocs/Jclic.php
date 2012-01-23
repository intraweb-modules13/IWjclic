<?php
// $Id: jclic.php
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html

/**
 * jclic block
 * 
 * The jclic block list the jclic activities where the user can access
 *
 * Purpose of file:  Create a block with the forms where the user can access
 *
 * @package      Intraweb_Modules
 * @subpackage   IWjclic
 * @version      $Id: jcic.php
 * @author       Albert Pérez Monfort
 * @link         http://phobos.xtec.cat/intraweb  The Intraweb Project Home Page
 * @copyright    Copyright (C) 2004 by the Intraweb Project Team
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */ 

function IWjclic_jclicblock_init()
{
    SecurityUtil::registerPermissionSchema("IWjclic:jclicblock:", "Block title::");
}

function IWjclic_jclicblock_info()
{
	$dom = ZLanguage::getModuleDomain('IWjclic');
    return array('text_type' => 'Jclic',
					'module' => 'IWjclic',
					'text_type_long' => __('Display the list of activities you can access the user', $dom),
					'allow_multiple' => true,
					'form_content' => false,
					'form_refresh' => false,
					'show_preview' => true );
}

/**
 * Show the list of activies where the user can access
 * @autor:	Albert Pérez Monfort
 * return:	The list of activities
*/
function IWjclic_jclicblock_display($blockinfo)
{
	// Security check
	if (!SecurityUtil::checkPermission*(0, "IWjclic:jclicblock:", $blockinfo['title']."::", ACCESS_READ)) { 
		return; 
	} 

	// Check if the module is available and user is lock in
	if(!ModUtil::available('IWjclic') || !UserUtil::isLoggedIn()){
		return;
	}

	$uid = (UserUtil::isLoggedIn()) ? UserUtil::getVar('uid') : '-1';

	//get the headlines saved in the user vars. It is renovate every 10 minutes
	$sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
	$exists = ModUtil::apiFunc('IWmain', 'user', 'userVarExists', array('name' => 'jclicBlock',
																		'module' => 'IWjclic',
																		'uid' => $uid,
																		'sv' => $sv));

	if($exists){
		$sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
		$s = ModUtil::func('IWmain', 'user', 'userGetVar', array('uid' => $uid,
																'name' => 'jclicBlock',
																'module' => 'IWjclic',
																'sv' => $sv,
																'nult' => true));
		$blockinfo['content'] = $s;
		return BlockUtil::themesideblock($blockinfo);
	}


	//get the activities that the user has assigned to other users
	$activitiesAssigned = ModUtil::apiFunc('IWjclic', 'user', 'getAllActivitiesAssigned');

	$sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
	$allGroups = ModUtil::func('IWmain', 'user', 'getAllGroups', array('sv' => $sv));
	
	foreach($allGroups as $group){
		$allGroupsArray[$group['id']] = $group['name'];
	}

	foreach($activitiesAssigned as $activity){
		//get assigned groups
		$groups= ModUtil::apiFunc('IWjclic', 'user', 'getGroups', array('jid' => $activity['jid']));
		
		$groupsString = '';
		
		foreach($groups as $group){
			$groupsString .= $allGroupsArray[$group['group_id']].'<br />';
		}
		
		$jclicTeacher_array[] = array('name' => $activity['name'],
										'jid' => $activity['jid']);	
	}	

	//get the activities that the user have got assigned 
	$activities = ModUtil::apiFunc('IWjclic', 'user', 'getAllActivities');

	foreach($activities as $activity){
		$content = ModUtil::func('IWjclic', 'user', 'getActivityContent', array('jid' => $activity['jid']));
		if($content['state'] == 1){
			$jclicStudent_array[] = $content;
		}
	}	

	// Create output object
	$view = Zikula_View::getInstance('IWjclic',false);
	$view -> assign('jclicTeacher',$jclicTeacher_array);
	$view -> assign('jclicStudent',$jclicStudent_array);

	$s = $view -> fetch('IWjclic_block_jclic.htm');
	
	//Copy the block information into user vars

	$sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
	ModUtil::func('IWmain', 'user', 'userSetVar', array('uid' => $uid,
														'name' => 'jclicBlock',
														'module' => 'IWjclic',
														'sv' => $sv,
														'value' => $s,
														'lifetime' => '950'));

	// Populate block info and pass to theme
	$blockinfo['content'] = $s;

	return BlockUtil::themesideblock($blockinfo);
}
