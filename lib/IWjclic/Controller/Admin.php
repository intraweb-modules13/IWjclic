<?php
/**
 * Show the main configurable parameters needed to configurate the module jclic
 * @author:     Albert PÃ©rez Monfort (aperezm@xtec.cat)

 * @return:	The form with needed to change the parameters
*/
function IWjclic_admin_main()
{
	$dom = ZLanguage::getModuleDomain('IWjclic');
	// Security check
	if (!SecurityUtil::checkPermission('IWjclic::', "::", ACCESS_ADMIN)) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}

	$jclicUpdatedFiles = ModUtil::getVar('IWjclic','jclicUpdatedFiles');
	$jclicJarBase = ModUtil::getVar('IWjclic','jclicJarBase');
	$timeLap = ModUtil::getVar('IWjclic','timeLap');
	$groupsProAssign = ModUtil::getVar('IWjclic','groupsProAssign');

	// Create output object
	$view = Zikula_View::getInstance('IWjclic',false);

	$sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
	$groups = ModUtil::func('IWmain', 'user', 'getAllGroups', array('sv' => $sv,
																	'less' => ModUtil::getVar('iw_myrole', 'rolegroup')));
																	
	foreach($groups as $group){
		$checked = false;
		
		if(strpos($groupsProAssign,'$'.$group['id'].'$') != false){
			$checked = true;	
		}
		
		$groupsArray[] = array('id' => $group['id'],
								'name' => $group['name'],
								'checked' => $checked);
	}

	if(!file_exists(ModUtil::getVar('IWmain', 'documentRoot').'/'.$jclicUpdatedFiles) || $jclicUpdatedFiles == ''){
		$view -> assign('noFolder', true);
	}else{
		if(!is_writeable(ModUtil::getVar('IWmain', 'documentRoot').'/'.$jclicUpdatedFiles)){
			$view -> assign('noWriteable', true);
		}
	}

	$view -> assign('jclicJarBase', $jclicJarBase);
	$view -> assign('timeLap', $timeLap);
	$view -> assign('jclicUpdatedFiles', $jclicUpdatedFiles);
	$view -> assign('groupsArray', $groupsArray);
	
	return $view -> fetch('IWjclic_admin_main.htm');
}

/**
 * Show the information about the module
 * @author:     Albert PÃ©rez Monfort (aperezm@xtec.cat)
 * @return:	The information about this module
*/
function IWjclic_admin_module()
{
	$dom = ZLanguage::getModuleDomain('IWjclic');
	// Create output object
	$view = Zikula_View::getInstance('IWjclic',false);

	$module = ModUtil::func('IWmain', 'user', 'module_info', array('module_name' => 'IWjclic',
																'type' => 'admin'));
																
	$view -> assign('module', $module);
	return $view -> fetch('IWjclic_admin_module.htm');
}

/**
 * Update the module configuration
 * @author:     Albert PÃ©rez Monfort (aperezm@xtec.cat)
 * @param:	Configuration values
 * @return:	The form with needed to change the parameters
*/
function IWjclic_admin_updateConf($args)
{
	$dom = ZLanguage::getModuleDomain('IWjclic');
	$jclicJarBase = FormUtil::getPassedValue('jclicJarBase', isset($args['jclicJarBase']) ? $args['jclicJarBase'] : null, 'POST');
	$timeLap = FormUtil::getPassedValue('timeLap', isset($args['timeLap']) ? $args['timeLap'] : null, 'POST');
	$groups = FormUtil::getPassedValue('groups', isset($args['groups']) ? $args['groups'] : null, 'POST');
	$jclicUpdatedFiles = FormUtil::getPassedValue('jclicUpdatedFiles', isset($args['jclicUpdatedFiles']) ? $args['jclicUpdatedFiles'] : null, 'POST');
	
	// Security check
	if (!SecurityUtil::checkPermission('IWjclic::', "::", ACCESS_ADMIN)) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}

	// Confirm authorisation code
	if (!SecurityUtil::confirmAuthKey()) {
		return LogUtil::registerAuthidError (ModUtil::url('IWjclic', 'admin', 'main'));
	}
	
	$groupsString = '$';
	foreach($groups as $group){
		$groupsString .= '$'.$group.'$';
	}

	ModUtil::setVar('IWjclic','jclicUpdatedFiles', $jclicUpdatedFiles);
	ModUtil::setVar('IWjclic','jclicJarBase', $jclicJarBase);
	ModUtil::setVar('IWjclic','timeLap', $timeLap);
	ModUtil::setVar('IWjclic','groupsProAssign', $groupsString);

	LogUtil::registerStatus (__('The module configuration has changed', $dom));

	return System::redirect(ModUtil::url('IWjclic', 'admin', 'main'));
}
