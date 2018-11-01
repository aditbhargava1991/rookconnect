<?php
$dashboard_layout = get_config($dbc, 'documents_all_dashboard_layout');
$documents_all_tabs = array_filter(explode(',',get_config($dbc, 'documents_all_tabs')));
$documents_all_tiles = array_filter(explode(',',get_config($dbc, 'documents_all_tiles')));

$document_tabs = [];
foreach ($documents_all_tabs as $documents_all_tab) {
	$document_tabs[config_safe_str($documents_all_tab)] = $documents_all_tab;
}

if(!empty($_GET['tile_name'])) {
	$tile_name = $_GET['tile_name'];
	$edit_access = vuaed_visible_function($dbc, 'documents_all_'.$_GET['tile_name']);
	$config_access = config_visible_function($dbc, 'documents_all_'.$_GET['tile_name']);
	foreach($documents_all_tiles as $documents_all_tile) {
		if($_GET['tile_name'] == config_safe_str($documents_all_tile)) {
			switch($_GET['tile_name']) {
				case 'client_documents':
					$document_tabs = ['client_documents' => 'Client Documents'];
					$_GET['tab'] = 'client_documents';
					break;
				case 'staff_documents':
					$document_tabs = ['staff_documents' => 'Staff Documents'];
					$_GET['tab'] = 'staff_documents';
					break;
				case 'internal_documents':
					$document_tabs = ['internal_documents' => 'Internal Documents'];
					$_GET['tab'] = 'internal_documents';
					break;
				case 'marketing_material':
					$document_tabs = ['marketing_material' => 'Marketing Materials'];
					$_GET['tab'] = 'marketing_material';
					break;
				default:
					$document_tabs = [$_GET['tile_name'] => $documents_all_tile];
					$_GET['tab'] = $_GET['tile_name'];
			}
		}
	}
} else {
	$tile_name = '';
	$edit_access = vuaed_visible_function($dbc, 'documents_all');
	$config_access = config_visible_function($dbc, 'documents_all');
	foreach($document_tabs as $type => $type_name) {
		if(!check_subtab_persmission($dbc, 'documents_all', ROLE, $type_name)) {
			unset($document_tabs[$type]);
		} else if(empty($_GET['tab']) && $dashboard_layout != 'tile') {
			$_GET['tab'] = $type;
		}
	}
}

$tab = $_GET['tab'];
switch($tab) {
	case 'client_documents':
		$tab_title =  'Client Documents';
		$tab_type = 'Client Document';
		$tab_table = 'client_documents';
		$tab_table_type = 'client_documents_type';
		$tab_table_category = 'category';
		$custom_tab_query = '';
		break;
	case 'staff_documents';
		$tab_title = 'Staff Documents';
		$tab_type = 'Staff Document';
		$tab_table = 'staff_documents';
		$tab_table_type = 'staff_documents_type';
		$tab_table_category = 'category';
		$custom_tab_query = '';
		break;
	case 'internal_documents':
		$tab_title = 'Internal Documents';
		$tab_type = 'Internal Document';
		$tab_table = 'internal_documents';
		$tab_table_type = 'internal_documents_type';
		$tab_table_category = 'category';
		$custom_tab_query = '';
		break;
	case 'marketing_material':
		$tab_title = 'Marketing Materials';
		$tab_type = 'Marketing Material';
		$tab_table = 'marketing_material';
		$tab_table_type = 'marketing_material_type';
		$tab_table_category = 'category';
		$custom_tab_query = '';
		break;
	default:
		$tab_title = $document_tabs[$tab];
		$tab_type = $document_tabs[$tab];
		$tab_table = 'custom_documents';
		$tab_table_type = 'custom_documents_type';
		$tab_table_category = 'category';
		$custom_tab_query = " AND `tab_name` = '".config_safe_str($tab_type)."'";
}
?>