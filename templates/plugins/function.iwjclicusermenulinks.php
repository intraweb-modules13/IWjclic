<?php
function smarty_function_iwjclicusermenulinks($params, &$smarty)
{
	$dom = ZLanguage::getModuleDomain('IWjclic');
	// set some defaults
	if (!isset($params['start'])) {
		$params['start'] = '[';
	}
	if (!isset($params['end'])) {
		$params['end'] = ']';
	}
	if (!isset($params['seperator'])) {
		$params['seperator'] = '|';
	}
	if (!isset($params['class'])) {
		$params['class'] = 'pn-menuitem-title';
	}

	$jclicusermenulinks = "<span class=\"" . $params['class'] . "\">" . $params['start'] . " ";

	if (SecurityUtil::checkPermission('IWjclic::', "::", ACCESS_ADD)) {
		$jclicusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWjclic', 'user', 'main')) . "\">" . __('Activities I have assigned ',$dom) . "</a> " . $params['seperator'];
	}

	if (SecurityUtil::checkPermission('IWjclic::', "::", ACCESS_READ)) {
		$jclicusermenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWjclic', 'user', 'assigned')) . "\">" . __('Activities assigned',$dom) . "</a> ";
	}

	if (SecurityUtil::checkPermission('IWjclic::', "::", ACCESS_ADD)) {
		$jclicusermenulinks .= $params['seperator'] . " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWjclic', 'user', 'assig')) . "\">" . __('Assign activities',$dom) . "</a> ";
	}




/*[ Home page | View assignments | Correct assignments | Assign assessment ]*/


	$jclicusermenulinks .= $params['end'] . "</span>\n";

	return $jclicusermenulinks;
}
