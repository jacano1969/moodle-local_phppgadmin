<?php

	/**
	 * Manage functions in a database
	 *
	 * $Id: functions.php,v 1.3 2002/09/10 18:46:25 xzilla Exp $
	 */

	// Include application functions
	include_once('../conf/config.inc.php');
	
	$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';
	if (!isset($msg)) $msg = '';
	$PHP_SELF = $_SERVER['PHP_SELF'];
	
	/** 
	 * Function to save after editing a function
	 */
	function doSaveEdit() {
		global $localData;
		
		$status = $localData->setView($_POST['view'], $_POST['formDefinition']);
		if ($status == 0)
			doProperties('Function updated.');
		else
			doEdit('Function update failed.');
	}
	
	/**
	 * Function to allow editing of a Function
	 */
	function doEdit($msg = '') {
		global $data, $localData, $misc;
		global $PHP_SELF, $strName, $strDefinition;
		
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": Views: ", htmlspecialchars($_REQUEST['view']), ": Edit</h2>\n";
		$misc->printMsg($msg);
		
		$viewdata = &$localData->getView($_REQUEST['view']);
		
		if ($viewdata->recordCount() > 0) {
			echo "<form action=\"$PHP_SELF\" method=post>\n";
			echo "<table width=100%>\n";
			echo "<tr><th class=data>{$strName}</th></tr>\n";
			echo "<tr><td class=data1>", htmlspecialchars($viewdata->f[$data->vwFields['vwname']]), "</td></tr>\n";
			echo "<tr><th class=data>{$strDefinition}</th></tr>\n";
			echo "<tr><td class=data1><textarea style=\"width:100%;\" rows=20 cols=50 name=formDefinition wrap=virtual>", 
				htmlspecialchars($viewdata->f[$data->vwFields['vwdef']]), "</textarea></td></tr>\n";
			echo "</table>\n";
			echo "<input type=hidden name=action value=save_edit>\n";
			echo "<input type=hidden name=view value=\"", htmlspecialchars($_REQUEST['view']), "\">\n";
			echo "<input type=hidden name=database value=\"", htmlspecialchars($_REQUEST['database']), "\">\n";
			echo "<input type=submit value=Save> <input type=reset>\n";
			echo "</form>\n";
		}
		else echo "<p>No data.</p>\n";
		
		echo "<p><a class=navlink href=\"$PHP_SELF?database=", urlencode($_REQUEST['database']), "\">Show All Views</a> |\n";
		echo "<a class=navlink href=\"$PHP_SELF?action=properties&database=", urlencode($_REQUEST['database']), "&view=", 
			urlencode($_REQUEST['view']), "\">Properties</a></p>\n";
	}
	
	/**
	 * Show read only properties for a function
			pc.oid,
					proname, 
					lanname as language,
					format_type(prorettype, NULL) as return_type,
					prosrc as source,
					probin as binary,
					oidvectortypes(pc.proargtypes) AS arguments



	 */
	function doProperties($msg = '') {
		global $data, $localData, $misc;
		global $PHP_SELF, $strFunctions, $strArguments, $strReturns, $strActions, $strNoFunctions, $strDefinition, $strLanguage;
	
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": Functions: ", htmlspecialchars($_REQUEST['function']), ": Properties</h2>\n";
		$misc->printMsg($msg);
		
		$funcdata = &$localData->getFunction($_REQUEST['function_oid']);
		
		if ($funcdata->recordCount() > 0) {
			echo "<table width=90%>\n";
			echo "<tr><th class=data>{$strFunctions}</th>\n";
			echo "<th class=data>{$strArguments}</th>\n";
			echo "<th class=data>{$strReturns}</th>\n";
			echo "<th class=data>{$strLanguage}</th></tr>\n";
			echo "<tr><td class=data1>", htmlspecialchars($funcdata->f[$data->fnFields['fnname']]), "</td>\n";
			echo "<td class=data1>", htmlspecialchars($funcdata->f[$data->fnFields['fnarguments']]), "</td>\n";
			echo "<td class=data1>", htmlspecialchars($funcdata->f[$data->fnFields['fnreturns']]), "</td>\n";
			echo "<td class=data1>", htmlspecialchars($funcdata->f[$data->fnFields['fnlang']]), "</td></tr>\n";
			echo "<tr><th class=data colspan=4>{$strDefinition}</th></tr>\n";
			echo "<tr><td class=data1 colspan=4>", nl2br(htmlspecialchars($funcdata->f[$data->fnFields['fndef']])), "</td></tr>\n";
			echo "</table>\n";
		}
		else echo "<p>No data.</p>\n";
		
		echo "<p><a class=navlink href=\"$PHP_SELF?database=", urlencode($_REQUEST['database']), "\">Show All Functions</a> |\n";
		echo "<a class=navlink href=\"$PHP_SELF?action=edit&database=", urlencode($_REQUEST['database']), "&function=", 
			urlencode($_REQUEST['function']), "\">Edit</a></p>\n";
	}
	
	/**
	 * Show confirmation of drop and perform actual drop
	 */
	function doDrop($confirm) {
		global $localData, $database;
		global $PHP_SELF;

		if ($confirm) { 
			echo "<h2>", htmlspecialchars($_REQUEST['database']), ": Functions: ", htmlspecialchars($_REQUEST['function']), ": Drop</h2>\n";
			
			echo "<p>Are you sure you want to drop the function \"", htmlspecialchars($_REQUEST['function']), "\"?</p>\n";
			
			echo "<form action=\"$PHP_SELF\" method=\"post\">\n";
			echo "<input type=hidden name=action value=drop>\n";
			echo "<input type=hidden name=function value=\"", htmlspecialchars($_REQUEST['function']), "\">\n";
			echo "<input type=hidden name=database value=\"", htmlspecialchars($_REQUEST['database']), "\">\n";
			echo "<input type=submit name=choice value=\"Yes\"> <input type=submit name=choice value=\"No\">\n";
			echo "</form>\n";
		}
		else {
			$status = $localData->dropFunction($_POST['function']);
			if ($status == 0)
				doDefault('Function dropped.');
			else
				doDefault('Function drop failed.');
		}
		
	}
	
	/**
	 * Displays a screen where they can enter a new view
	 */
	function doCreate($msg = '') {
		global $data, $localData, $misc;
		global $PHP_SELF, $strName, $strDefinition;
		
		if (!isset($_POST['formFunc'])) $_POST['formFunc'] = '';
		if (!isset($_POST['formDefinition'])) $_POST['formDefinition'] = '';
		
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": Views: Create View</h2>\n";
		$misc->printMsg($msg);
		
		echo "<form action=\"$PHP_SELF\" method=post>\n";
		echo "<table width=100%>\n";
		echo "<tr><th class=data>{$strName}</th></tr>\n";
		echo "<tr><td class=data1><input name=formView size={$data->_maxNameLen} maxlength={$data->_maxNameLen} value=\"", 
			htmlspecialchars($_POST['formView']), "\"></td></tr>\n";
		echo "<tr><th class=data>{$strDefinition}</th></tr>\n";
		echo "<tr><td class=data1><textarea style=\"width:100%;\" rows=20 cols=50 name=formDefinition wrap=virtual>", 
			htmlspecialchars($_POST['formDefinition']), "</textarea></td></tr>\n";
		echo "</table>\n";
		echo "<input type=hidden name=action value=save_create>\n";
		echo "<input type=hidden name=database value=\"", htmlspecialchars($_REQUEST['database']), "\">\n";
		echo "<input type=submit value=Save> <input type=reset>\n";
		echo "</form>\n";
		
		echo "<p><a class=navlink href=\"$PHP_SELF?database=", urlencode($_REQUEST['database']), "\">Show All Views</a></p>\n";
	}
	
	/**
	 * Actually creates the new view in the database
	 */
	function doSaveCreate() {
		global $localData, $strViewNeedsName, $strViewNeedsDef;
		
		// Check that they've given a name and a definition
		if ($_POST['formView'] == '') doCreate($strViewNeedsName);
		elseif ($_POST['formDefinition'] == '') doCreate($strViewNeedsDef);
		else {		 
			$status = $localData->createView($_POST['formView'], $_POST['formDefinition']);
			if ($status == 0)
				doDefault('View created.');
			else
				doCreate('View creation failed.');
		}
	}	

	/**
	 * Show default list of views in the database
	 */
	function doDefault($msg = '') {
		global $data, $localData, $misc, $database, $func;
		global $PHP_SELF, $strFunctions, $strArguments, $strReturns, $strActions, $strNoFunctions;
		
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": Functions</h2>\n";
		$misc->printMsg($msg);
		
		$funcs = &$localData->getFunctions();
		
		if ($funcs->recordCount() > 0) {
			echo "<table>\n";
			echo "<tr><th class=data>{$strFunctions}</th><th class=data>{$strReturns}</th><th class=data>{$strArguments}</th><th colspan=3 class=data>{$strActions}</th>\n";
			$i = 0;
			while (!$funcs->EOF) {

				$func_full = $funcs->f[$data->fnFields['fnname']] . "(". $funcs->f[$data->fnFields['fnarguments']] .")";

				$id = (($i % 2) == 0 ? '1' : '2');
				echo "<tr><td class=data{$id}>", htmlspecialchars($funcs->f[$data->fnFields['fnname']]), "</td>\n";
				echo "<td class=data{$id}>", htmlspecialchars($funcs->f[$data->fnFields['fnreturns']]), "</td>\n";
				echo "<td class=data{$id}>", htmlspecialchars($funcs->f[$data->fnFields['fnarguments']]), "</td>\n";
				echo "<td class=opbutton{$id}><a href=\"$PHP_SELF?action=properties&database=", 
					htmlspecialchars($_REQUEST['database']), "&function=", urlencode($func_full), "&function_oid=", $funcs->f[$data->fnFields['fnoid']], "\">Properties</a></td>\n";
				echo "<td class=opbutton{$id}>Edit</td>\n";
				echo "<td class=opbutton{$id}><a href=\"$PHP_SELF?action=confirm_drop&database=", 
					htmlspecialchars($_REQUEST['database']), "&function=", urlencode($func_full), "&function_oid=", $funcs->f[$data->fnFields['fnoid']], "\">Drop</a></td>\n";
				echo "</tr>\n";
				$funcs->moveNext();
				$i++;
			}

			echo "</table>\n";
		}
		else {
			echo "<p>{$strNoFunctions}</p>\n";
		}
		
		echo "<p><a class=navlink href=\"$PHP_SELF?action=create&database=", urlencode($_REQUEST['database']), "\">Create Function</a></p>\n";

	}

	echo "<html>\n";
	echo "<body>\n";
	
	switch ($action) {
		case 'save_create':
			doSaveCreate();
			break;
		case 'create':
			doCreate();
			break;
		case 'drop':
			if ($_POST['choice'] == 'Yes') doDrop(false);
			else doDefault();
			break;
		case 'confirm_drop':
			doDrop(true);
			break;			
		case 'save_edit':
			doSaveEdit();
			break;
		case 'edit':
			doEdit();
			break;
		case 'properties':
			doProperties();
			break;
		case 'browse':
			// @@ Not yet implemented
		default:
			doDefault();
			break;
	}	

	echo "</body>\n";
	echo "</html>\n";
	
?>