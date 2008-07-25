<?php
################################################################################
##              -= YOU MAY NOT REMOVE OR CHANGE THIS NOTICE =-                 #
## --------------------------------------------------------------------------- #
##  PHP DataGrid version 4.2.6 (31.05.2008)                                    #
##  Author & developer:     Leumas Naypoka <leumas.a@gmail.com>                #
##  Developers:             Zewa           <http://www.softic.at>              #
##                          Fcallez        <http://www.innovavirtual.org>      #
##  Lisence:    GNU GPL                                                        #
##  Site:       http://phpbuilder.blogspot.com                                 #
##  Copyright:  Leumas Naypoka (c) 2006-2008. All rights reserved.             #
##                                                                             #
##  Additional modules (embedded):                                             #
##  -- openWYSIWYG 1.01 (free cross-browser)            http://openWebWare.com #
##  -- PEAR::DB 1.7.11 (PHP Ext. & Application Repository) http://pear.php.net #
##  -- JS AFV 1.0.3 (JS Auto From Validator)    http://phpbuilder.blogspot.com #
##  -- overLIB 4.21 (JS library)            http://www.bosrup.com/web/overlib/ #
##  -- FPDF v.1.53 (PDF files generator)                   http://www.fpdf.org #
##  -- JsCalendar v.1.0 (DHTML/JavaScript Calendar)     http://www.dynarch.com #
##  -- AutoSuggest v.2.1.3 (AJAX autocomplete) http://www.brandspankingnew.net #
##                                                                             #
################################################################################
## +---------------------------------------------------------------------------+
## | 1. Creating & Calling:                                                    |
## +---------------------------------------------------------------------------+
##  *** define a relative (virtual) path to datagrid.class.php file and "pear"
##  *** directory (relatively to the current file)
##  *** RELATIVE PATH ONLY ***
//
//  define ("DATAGRID_DIR", "");                     /* Ex.: "datagrid/" */
//  define ("PEAR_DIR", "pear/");                    /* Ex.: "datagrid/pear/" */
//
//  require_once(DATAGRID_DIR.'datagrid.class.php');
//  require_once(PEAR_DIR.'PEAR.php');
//  require_once(PEAR_DIR.'DB.php');
##
##  *** creating variables that we need for database connection
//  $DB_USER='name';            /* usually like this: prefix_name             */
//  $DB_PASS='';                /* must be already enscrypted (recommended)   */
//  $DB_HOST='localhost';       /* usually localhost                          */
//  $DB_NAME='dbName';          /* usually like this: prefix_dbName           */
//
//  ob_start();
##  *** (example of ODBC connection string)
##  *** $result_conn = $db_conn->connect(DB::parseDSN('odbc://root:12345@test_db'));
##  *** (example of Oracle connection string)
##  *** $result_conn = $db_conn->connect(DB::parseDSN('oci8://root:12345@localhost:1521/mydatabase));
##  *** (example of PostgreSQL connection string)
##  *** $result_conn = $db_conn->connect(DB::parseDSN('pgsql://root:12345@localhost/mydatabase));
##  === (Examples of connections to other db types see in "docs/pear/" folder)
//  $db_conn = DB::factory('mysql');  /* don't forget to change on appropriate db type */
//  $result_conn = $db_conn->connect(DB::parseDSN('mysql://'.$DB_USER.':'.$DB_PASS.'@'.$DB_HOST.'/'.$DB_NAME));
//  if(DB::isError($result_conn)){ die($result_conn->getDebugInfo()); }
##  *** put a primary key on the first place
//  $sql = "SELECT primary_key, field_1, field_2 ... FROM tableName ;";
##  *** set encoding and collation (default: utf8/utf8_unicode_ci)
/// $dg_encoding = "utf8";
/// $dg_collation = "utf8_unicode_ci";
/// $dgrid->setEncoding($dg_encoding, $dg_collation);
##  *** set needed options and create a new class instance
//  $debug_mode = false;        /* display SQL statements while processing */
//  $messaging = true;          /* display system messages on a screen */
//  $unique_prefix = "abc_";    /* prevent overlays - must be started with a letter */
//  $dgrid = new DataGrid($debug_mode, $messaging, $unique_prefix, DATAGRID_DIR);
##  *** set data source with needed options
//  $default_order_field = "field_name_1 [, field_name_2...]";
//  $default_order_type = "ASC|DESC [, ASC|DESC...]";
//  $dgrid->dataSource($db_conn, $sql, $default_order_field, $default_order_type);
##
##
## +---------------------------------------------------------------------------+
## | 2. General Settings:                                                      |
## +---------------------------------------------------------------------------+
##  *** set interface language (default - English)
##  *** (en) - English     (de) - German     (se) - Swedish   (hr) - Bosnian/Croatian
##  *** (hu) - Hungarian   (es) - Espanol    (ca) - Catala    (fr) - Francais
##  *** (nl) - Netherlands/"Vlaams"(Flemish) (it) - Italiano  (pl) - Polish
##  *** (ch) - Chinese     (sr) - Serbian    (bg) - Bulgarian (pb) - Brazilian Portuguese
##  *** (ar) - Arabic      (tr) - Turkish    (cz) - Czech     (ro/ro_utf8) - Romanian
/// $dg_language = "en";
/// $dgrid->setInterfaceLang($dg_language);
##  *** set direction: "ltr" or "rtr" (default - "ltr")
/// $direction = "ltr";
/// $dgrid->setDirection($direction);
##  *** set layouts: "0" - tabular(horizontal) - default, "1" - columnar(vertical), "2" - customized
/// $layouts = array("view"=>"0", "edit"=>"1", "details"=>"1", "filter"=>"1");
/// $dgrid->setLayouts($layouts);
/// $details_template = "<table><tr><td>{field_name_1}</td><td>{field_name_2}</td></tr>...</table>";
/// $dgrid->setTemplates("","",$details_template);
##  *** set modes for operations ("type" => "link|button|image")
##  *** "byFieldValue"=>"fieldName" - make the field to be a link to edit mode page
/// $modes = array(
///     "add"	  =>array("view"=>true, "edit"=>false, "type"=>"link"),
///     "edit"	  =>array("view"=>true, "edit"=>true,  "type"=>"link", "byFieldValue"=>""),
///     "cancel"  =>array("view"=>true, "edit"=>true,  "type"=>"link"),
///     "details" =>array("view"=>true, "edit"=>false, "type"=>"link"),
///     "delete"  =>array("view"=>true, "edit"=>true,  "type"=>"image")
/// );
/// $dgrid->setModes($modes);
##  *** allow scrolling on datagrid
/// $scrolling_option = false;
/// $dgrid->allowScrollingSettings($scrolling_option);
##  *** set scrolling settings (optional)
/// $scrolling_width = "90%";
/// $scrolling_height = "100%";
/// $dgrid->setScrollingSettings($scrolling_width, $scrolling_height);
##  *** allow mulirow operations
//  $multirow_option = true;
//  $dgrid->allowMultirowOperations($multirow_option);
/// $multirow_operations = array(
///     "delete"  => array("view"=>true),
///     "details" => array("view"=>true),
///     "my_operation_name" => array("view"=>true, "flag_name"=>"my_flag_name", "flag_value"=>"my_flag_value", "tooltip"=>"Do something with selected", "image"=>"image.gif")
/// );
/// $dgrid->setMultirowOperations($multirow_operations);
##  *** set CSS class for datagrid
##  *** "default" or "blue" or "gray" or "green" or "pink" or your own css file
/// $css_class = "default";
/// $dgrid->setCssClass($css_class);
##  *** set variables that used to get access to the page (like: my_page.php?act=34&id=56 etc.)
/// $http_get_vars = array("act", "id");
/// $dgrid->setHttpGetVars($http_get_vars);
##  *** set other datagrid/s unique prefixes (if you use few datagrids on one page)
##  *** format (in which mode to allow processing of another datagrids)
##  *** array("unique_prefix"=>array("view"=>true|false, "edit"=>true|false, "details"=>true|false));
/// $anotherDatagrids = array("abcd_"=>array("view"=>true, "edit"=>true, "details"=>true));
/// $dgrid->setAnotherDatagrids($anotherDatagrids);
##  *** set DataGrid caption
/// $dg_caption = "My Favorite Lovely PHP DataGrid";
/// $dgrid->setCaption($dg_caption);
##
##
## +---------------------------------------------------------------------------+
## | 3. Printing & Exporting Settings:                                         |
## +---------------------------------------------------------------------------+
##  *** set printing option: true(default) or false
/// $printing_option = true;
/// $dgrid->allowPrinting($printing_option);
##  *** set exporting option: true(default) or false and relative (virtual) path
##  *** to export directory (relatively to datagrid.class.php file).
##  *** Ex.: "" - if we use current datagrid folder
/// $exporting_option = true;
/// $exporting_directory = "";
/// $dgrid->allowExporting($exporting_option, $exporting_directory);
/// $exporting_types = array("excel"=>"true", "pdf"=>"true", "xml"=>"true");
/// $dgrid->allowExportingTypes($exporting_types);
##
##
## +---------------------------------------------------------------------------+
## | 4. Sorting & Paging Settings:                                             |
## +---------------------------------------------------------------------------+
##  *** set sorting option: true(default) or false
/// $sorting_option = true;
/// $dgrid->allowSorting($sorting_option);
##  *** set paging option: true(default) or false
/// $paging_option = true;
/// $rows_numeration = false;
/// $numeration_sign = "N #";
/// $dgrid->allowPaging($paging_option, $rows_numeration, $numeration_sign);
##  *** set paging settings
/// $bottom_paging = array("results"=>true, "results_align"=>"left", "pages"=>true, "pages_align"=>"center", "page_size"=>true, "page_size_align"=>"right");
/// $top_paging = array("results"=>true, "results_align"=>"left", "pages"=>true, "pages_align"=>"center", "page_size"=>true, "page_size_align"=>"right");
//  $pages_array = array("10"=>"10", "25"=>"25", "50"=>"50", "100"=>"100", "250"=>"250", "500"=>"500", "1000"=>"1000");
/// $default_page_size = 10;
/// $paging_arrows = array("first"=>"|&lt;&lt;", "previous"=>"&lt;&lt;", "next"=>"&gt;&gt;", "last"=>"&gt;&gt;|");
/// $dgrid->setPagingSettings($bottom_paging, $top_paging, $pages_array, $default_page_size, $paging_arrows);
##
##
## +---------------------------------------------------------------------------+
## | 5. Filter Settings:                                                       |
## +---------------------------------------------------------------------------+
##  *** set filtering option: true or false(default)
/// $filtering_option = true;
/// $show_search_type = true;
/// $dgrid->allowFiltering($filtering_option, $show_search_type);
##  *** set aditional filtering settings
##  *** tips: use "," (comma) if you want to make search by some words, for ex.: hello, bye, hi
/// $fill_from_array = array("0"=>"No", "1"=>"Yes");  /* as "value"=>"option" */
/// $filtering_fields = array(
///     "Caption_1"=>array("type"=>"textbox", "table"=>"tableName_1", "field"=>"fieldName_1|,fieldName_2", "show_operator"=>false|true, "default_operator"=>"=|<|>|like|%like|like%|%like%|not like", "case_sensitive"=>false|true, "comparison_type"=>"string|numeric|binary", "width"=>"", "on_js_event"=>""),
///     "Caption_2"=>array("type"=>"textbox", "autocomplete"=>"false|true", "handler"=>"modules/autosuggest/test.php", "maxresults"=>"12", "shownoresults"=>"false|true", "table"=>"tableName_1", "field"=>"fieldName_1|,fieldName_2", "show_operator"=>false|true, "default_operator"=>"=|<|>|like|%like|like%|%like%|not like", "case_sensitive"=>false|true, "comparison_type"=>"string|numeric|binary", "width"=>"", "on_js_event"=>""),
///     "Caption_3"=>array("type"=>"dropdownlist", "order"=>"ASC|DESC", "table"=>"tableName_2", "field"=>"fieldName_2", "source"=>"self"|$fill_from_array, "show_operator"=>false|true, "default_operator"=>"=|<|>|like|%like|like%|%like%|not like", "case_sensitive"=>false|true, "comparison_type"=>"string|numeric|binary", "width"=>"", "on_js_event"=>""),
///     "Caption_4"=>array("type"=>"calendar", "table"=>"tableName_3", "field"=>"fieldName_3", "show_operator"=>false|true, "default_operator"=>"=|<|>|like|%like|like%|%like%|not like", "case_sensitive"=>false|true, "comparison_type"=>"string|numeric|binary", "width"=>"", "on_js_event"=>""),
/// );
/// $dgrid->setFieldsFiltering($filtering_fields);
##
##
## +---------------------------------------------------------------------------+
## | 6. View Mode Settings:                                                    |
## +---------------------------------------------------------------------------+
##  *** set view mode table properties
/// $vm_table_properties = array("width"=>"90%");
/// $dgrid->setViewModeTableProperties($vm_table_properties);
##  *** set columns in view mode
##  *** Ex.: "on_js_event"=>"onclick='alert(\"Yes!!!\");'"
##  ***      "barchart" : number format in SELECT SQL must be equal with number format in max_value
/// $vm_colimns = array(
///     "FieldName_1"=>array("header"=>"Name_A", "type"=>"label",      "align"=>"left", "width"=>"X%|Xpx", "wrap"=>"wrap|nowrap", "text_length"=>"-1", "tooltip"=>true|false, "tooltip_type"=>"floating|simple", "case"=>"normal|upper|lower", "summarize"=>"true|false", "sort_by"=>"", "visible"=>"true", "on_js_event"=>""),
///     "FieldName_2"=>array("header"=>"Name_B", "type"=>"image",      "align"=>"left", "width"=>"X%|Xpx", "wrap"=>"wrap|nowrap", "text_length"=>"-1", "tooltip"=>true|false, "tooltip_type"=>"floating|simple", "case"=>"normal|upper|lower", "summarize"=>"true|false", "sort_by"=>"", "visible"=>"true", "on_js_event"=>"", "target_path"=>"uploads/", "default"=>"default_image.ext", "image_width"=>"50px", "image_height"=>"30px"),
///     "FieldName_3"=>array("header"=>"Name_C", "type"=>"linktoview", "align"=>"left", "width"=>"X%|Xpx", "wrap"=>"wrap|nowrap", "text_length"=>"-1", "tooltip"=>true|false, "tooltip_type"=>"floating|simple", "case"=>"normal|upper|lower", "summarize"=>"true|false", "sort_by"=>"", "visible"=>"true", "on_js_event"=>""),
///     "FieldName_3"=>array("header"=>"Name_C", "type"=>"linktoedit", "align"=>"left", "width"=>"X%|Xpx", "wrap"=>"wrap|nowrap", "text_length"=>"-1", "tooltip"=>true|false, "tooltip_type"=>"floating|simple", "case"=>"normal|upper|lower", "summarize"=>"true|false", "sort_by"=>"", "visible"=>"true", "on_js_event"=>""),
///     "FieldName_4"=>array("header"=>"Name_D", "type"=>"link",       "align"=>"left", "width"=>"X%|Xpx", "wrap"=>"wrap|nowrap", "text_length"=>"-1", "tooltip"=>true|false, "tooltip_type"=>"floating|simple", "case"=>"normal|upper|lower", "summarize"=>"true|false", "sort_by"=>"", "visible"=>"true", "on_js_event"=>"", "field_key"=>"field_name_0"|"field_key_1"=>"field_name_1"|..., "field_data"=>"field_name_2", "rel"=>"", "title"=>"", "target"=>"_new", "href"=>"{0}"),
///     "FieldName_5"=>array("header"=>"Name_E", "type"=>"link",       "align"=>"left", "width"=>"X%|Xpx", "wrap"=>"wrap|nowrap", "text_length"=>"-1", "tooltip"=>true|false, "tooltip_type"=>"floating|simple", "case"=>"normal|upper|lower", "summarize"=>"true|false", "sort_by"=>"", "visible"=>"true", "on_js_event"=>"", "field_key"=>"field_name_0"|"field_key_1"=>"field_name_1"|..., "field_data"=>"field_name_2", "rel"=>"", "title"=>"", "target"=>"_new", "href"=>"mailto:{0}"),
///     "FieldName_6"=>array("header"=>"Name_F", "type"=>"link",       "align"=>"left", "width"=>"X%|Xpx", "wrap"=>"wrap|nowrap", "text_length"=>"-1", "tooltip"=>true|false, "tooltip_type"=>"floating|simple", "case"=>"normal|upper|lower", "summarize"=>"true|false", "sort_by"=>"", "visible"=>"true", "on_js_event"=>"", "field_key"=>"field_name_0"|"field_key_1"=>"field_name_1"|..., "field_data"=>"field_name_2", "rel"=>"", "title"=>"", "target"=>"_new", "href"=>"http://mydomain.com?act={0}&act={1}&code=ABC"),
///     "FieldName_7"=>array("header"=>"Name_G", "type"=>"password",   "align"=>"left", "width"=>"X%|Xpx", "wrap"=>"wrap|nowrap", "text_length"=>"-1", "tooltip"=>true|false, "tooltip_type"=>"floating|simple", "case"=>"normal|upper|lower", "summarize"=>"true|false", "sort_by"=>"", "visible"=>"true", "on_js_event"=>""),
///     "FieldName_8"=>array("header"=>"Name_H", "type"=>"barchart",   "align"=>"left", "width"=>"X%|Xpx", "wrap"=>"wrap|nowrap", "text_length"=>"-1", "tooltip"=>true|false, "tooltip_type"=>"floating|simple", "case"=>"normal|upper|lower", "summarize"=>"true|false", "sort_by"=>"", "visible"=>"true", "on_js_event"=>"", "field"=>"field_name", "maximum_value"=>"value"),
/// );
/// $dgrid->setColumnsInViewMode($vm_colimns);
##  *** set auto-genereted columns in view mode
//  $auto_column_in_view_mode = false;
//  $dgrid->setAutoColumnsInViewMode($auto_column_in_view_mode);
##
##
## +---------------------------------------------------------------------------+
## | 7. Add/Edit/Details Mode Settings:                                        |
## +---------------------------------------------------------------------------+
##  *** set add/edit mode table properties
/// $em_table_properties = array("width"=>"70%");
/// $dgrid->setEditModeTableProperties($em_table_properties);
##  *** set details mode table properties
/// $dm_table_properties = array("width"=>"70%");
/// $dgrid->setDetailsModeTableProperties($dm_table_properties);
##  ***  set settings for add/edit/details modes
//  $table_name  = "table_name";
//  $primary_key = "primary_key";
//  $condition   = "table_name.field = ".$_REQUEST['abc_rid'];
//  $dgrid->setTableEdit($table_name, $primary_key, $condition);
##  *** set columns in edit mode
##  *** first letter:  r - required, s - simple (not required)
##  *** second letter: t - text(including datetime), n - numeric, a - alphanumeric,
##                     e - email, f - float, y - any, l - login name, z - zipcode,
##                     p - password, i - integer, v - verified, c - checkbox, u - URL
##  *** third letter (optional):
##          for numbers: s - signed, u - unsigned, p - positive, n - negative
##          for strings: u - upper,  l - lower,    n - normal,   y - any
##  *** Ex.: "on_js_event"=>"onclick='alert(\"Yes!!!\");'"
##  *** Ex.: type = textbox|textarea|label|date(yyyy-mm-dd)|datedmy(dd-mm-yyyy)|datetime(yyyy-mm-dd hh:mm:ss)|datetimedmy(dd-mm-yyyy hh:mm:ss)|time(hh:mm:ss)|image|password|enum|print|checkbox
##  *** make sure your WYSIWYG dir has 777 permissions
/// $fill_from_array = array("0"=>"No", "1"=>"Yes", "2"=>"Don't know", "3"=>"My be"); /* as "value"=>"option" */
/// $em_columns = array(
///     "FieldName_1"  =>array("header"=>"Name_A", "type"=>"textbox",   "req_type"=>"rt", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>""),
///     "FieldName_2"  =>array("header"=>"Name_B", "type"=>"textarea",  "req_type"=>"rt", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>"", "edit_type"=>"simple|wysiwyg", "resizable"=>"false", "rows"=>"7", "cols"=>"50"),
///     "FieldName_3"  =>array("header"=>"Name_C", "type"=>"label",     "req_type"=>"rt", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>""),
///     "FieldName_4"  =>array("header"=>"Name_D", "type"=>"date",      "req_type"=>"rt", "width"=>"187px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>"", "calendar_type"=>"popup|floating"),
///     "FieldName_5"  =>array("header"=>"Name_E", "type"=>"datetime",  "req_type"=>"st", "width"=>"187px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>"", "calendar_type"=>"popup|floating"),
///     "FieldName_6"  =>array("header"=>"Name_F", "type"=>"time",      "req_type"=>"st", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>""),
///     "FieldName_7"  =>array("header"=>"Name_G", "type"=>"image",     "req_type"=>"st", "width"=>"220px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>"", "target_path"=>"uploads/", "max_file_size"=>"100000|100K|10M|1G", "image_width"=>"Xpx", "image_height"=>"Ypx", "file_name"=>"Image_Name", "host"=>"local|remote"),
///     "FieldName_8"  =>array("header"=>"Name_H", "type"=>"password",  "req_type"=>"rp", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>""),
///     "FieldName_9"  =>array("header"=>"Name_I", "type"=>"enum",      "req_type"=>"st", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>"", "source"=>"self"|$fill_from_array, "view_type"=>"dropdownlist(default)|radiobutton", "radiobuttons_alignment"=>"horizontal|vertical", "multiple"=>false, "multiple_size"=>"4"),
///     "FieldName_10" =>array("header"=>"Name_J", "type"=>"print",     "req_type"=>"st", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>""),
///     "FieldName_11" =>array("header"=>"Name_K", "type"=>"checkbox",  "req_type"=>"st", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>"", "true_value"=>1, "false_value"=>0),
///     "FieldName_12" =>array("header"=>"Name_L", "type"=>"file",      "req_type"=>"st", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>"", "target_path"=>"uploads/", "max_file_size"=>"100000|100K|10M|1G", "file_name"=>"File_Name", "host"=>"local|remote"),
///     "FieldName_13" =>array("header"=>"Name_M", "type"=>"link",      "req_type"=>"st", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "unique"=>false, "unique_condition"=>"", "visible"=>"true", "on_js_event"=>"", "field_key"=>"field_name_0"|"field_key_1"=>"field_name_1"|..., "field_data"=>"field_name_2", "target"=>"_new", "href"=>"http://mydomain.com?act={0}&act={1}&code=ABC"),
///     "FieldName_14" =>array("header"=>"",       "type"=>"hidden",    "req_type"=>"st", "default"=>"default_value", "visible"=>"true", "unique"=>false|true),
///     "validator"    =>array("header"=>"Name_N", "type"=>"validator", "req_type"=>"rv", "width"=>"210px", "title"=>"", "readonly"=>false, "maxlength"=>"-1", "default"=>"", "visible"=>"true", "on_js_event"=>"", "for_field"=>"", "validation_type"=>"password|email"),
///     "delimiter"    =>array("inner_html"=>"<br />"),
/// );
/// $dgrid->setColumnsInEditMode($em_columns);
##  *** set auto-genereted columns in edit mode
//  $auto_column_in_edit_mode = false;
//  $dgrid->setAutoColumnsInEditMode($auto_column_in_edit_mode);
##  *** set foreign keys for add/edit/details modes (if there are linked tables)
##  *** Ex.: "condition"=>"TableName_1.FieldName > 'a' AND TableName_1.FieldName < 'c'"
##  *** Ex.: "on_js_event"=>"onclick='alert(\"Yes!!!\");'"
/// $foreign_keys = array(
///     "ForeignKey_1"=>array("table"=>"TableName_1", "field_key"=>"FieldKey_1", "field_name"=>"FieldName_1", "view_type"=>"dropdownlist(default)|radiobutton|textbox", "radiobuttons_alignment"=>"horizontal|vertical", "condition"=>"", "order_by_field"=>"My_Field_Name", "order_type"=>"ASC|DESC", "on_js_event"=>""),
///     "ForeignKey_2"=>array("table"=>"TableName_2", "field_key"=>"FieldKey_2", "field_name"=>"FieldName_2", "view_type"=>"dropdownlist(default)|radiobutton|textbox", "radiobuttons_alignment"=>"horizontal|vertical", "condition"=>"", "order_by_field"=>"My_Field_Name", "order_type"=>"ASC|DESC", "on_js_event"=>"")
/// );
/// $dgrid->setForeignKeysEdit($foreign_keys);
##
##
## +---------------------------------------------------------------------------+
## | 8. Bind the DataGrid:                                                     |
## +---------------------------------------------------------------------------+
##  *** bind the DataGrid and draw it on the screen
//  $dgrid->bind();
//  ob_end_flush();
##
################################################################################

////////////////////////////////////////////////////////////////////////////////
//
// Not documented:
// -----------------------------------------------------------------------------
// Property : first_field_focus_allowed   = true|false;
//  --//--  : hide_grid_before_serach     = true|false;  /* put it before bind() method */
//  --//--  : draw_add_button_separately  = true|false;
//  --//--  : "pre_addition"=>"" and "post_addition"=>"" attributes in view mode for labels and in add/edit/details modes for textboxes, checkboxes
//  --//--  : "autocomplete"=>"on|off" attribute for textboxes in add/edit modes (default - "on")
//  --//--  : mode_after_update           = ""|"edit";
//  --//--  : "on_item_created"=>"function_name" attributes in view/add/edit/details modes for customized work with field value
//                                        function_name must defined with 1 parameter, that will get filed data. Ex.: function_name($field_value){ ... }
//
// Method   : executeSql()
//            use it after dataSource() method only (after the using dataSource() need to be recalled)
//    		  $dSet = $dgrid->executeSql("SELECT * FROM tblPresidents WHERE tblPresidents.CountryID = ".$_GET['f_rid']."");
//    		  while($row = $dSet->fetchRow()){
//        	    for($c = 0; ($c < $dSet->numCols()); $c++){ echo $row[$c]." "; }
//        	    echo "<br />";
//    		  }
//  --//--  : selectSqlItem()
//            $presidents = $dgrid->selectSqlItem("SELECT COUNT(tblPresidents.presidentID) FROM tblPresidents WHERE tblPresidents.CountryID = ".$_GET['f_rid']."");
//  --//--  : allowHighlighting(true|false);
//  --//--  : setJsErrorsDisplayStyle("all"|"each");
//  --//--  : getNextId();
//  --//--  : getCurrentId();
//  --//--  : setHeadersInColumnarLayout("Field Name", "Field Value");
//  --//--  : setDgMessages("add", "update", "delete");
//
// Feature  : onSubmitMyCheck
//      	<script type='text/javascript'>
//            function unique_prefix_onSubmitMyCheck(){
//              return true;
//      	}
//      	</script>
//  --//--  : "on_js_event"=>"onchange='formAction(\"\", \"\", \"".$dgrid->unique_prefix."\", \"".$dgrid->HTTP_URL."\", \"".$_SERVER['QUERY_STRING']."\")'"
//  --//--  : bind(true|false) - draw DataGrid on the screen on not
//
////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////
//
// Tricks:
// -----------------------------------------------------------------------------
// 1. Set default value, that disappears on focus:
//      "default"=>"http://www.website.com", "on_js_event"=>"onBlur='if(this.value == \"\") this.value = \"http://www.website.com\"; this.style.color=\"#f68d6f\";' onClick='if(this.value==\"http://www.website.com\") this.value=\"\"; this.style.color=\"#000000\";'",
//
// 2. Set uniquie value for uploading image:
//      "file_name"=>"img_".((isset($_GET['prfx_mode']) && ($_GET['prfx_mode'] == "add")) ? $dgrid->getNextId() : $dgrid->getCurrentId())
//
// 3. Make auto-submition for filtering fileds
//      "on_js_event"=>"onchange='document.getElementById(\"...prefix..._ff_onSUBMIT_FILTER\").click();'"
//
// 4. Make a field text colored according to condition
//      if (product='flooring',CONCAT('<SPAN style=\"background-color:yellow\">',product,'</SPAN>'),product) as ProductColored,
//
////////////////////////////////////////////////////////////////////////////////


Class DataGrid
{
    //==========================================================================
    // Data Members
    //==========================================================================
    // unique prefixes ---------------------------------------------------------
    public $unique_prefix;
    public $unique_random_prefix;

    // directory ---------------------------------------------------------------
    public $directory;
    public $inc_dir;

    // language ----------------------------------------------------------------
    public $lang_name;
    public $lang;

    // caption -----------------------------------------------------------------
    public $caption;

    // rows and columns data members -------------------------------------------
    public $rows;
    public $row_lower;
    public $row_upper;
    public $columns;
    public $col_lower;
    public $col_upper;

    // http get vars -----------------------------------------------------------
    public $http;
    public $port;
    public $server_name;
    public $HTTP_URL;
    public $http_get_vars;
    public $another_datagrids;

    // data source -------------------------------------------------------------
    public $db_handler;
    public $sql;
    public $sql_view;
    public $sql_group_by;
    public $data_set;

    // signs -------------------------------------------------------------------
    public $amp;
    public $nbsp;

    // encoding & direction ----------------------------------------------------
    public $encoding;
    public $collation;
    public $direction;

    // layout style ------------------------------------------------------------
    public $layouts;
    public $layout_type;

    // templates ---------------------------------------------------------------
    public $templates;

    // paging variables --------------------------------------------------------
    public $pages_total;
    public $page_current;
    public $default_page_size;
    public $req_page_size;
    public $paging_allowed;
    public $rows_numeration;
    public $numeration_sign;
    public $lower_paging;
    public $upper_paging;
    public $pages_array;
    public $first_arrow;
    public $previous_arrow;
    public $next_arrow;
    public $last_arrow;
    public $limit_start;
    public $limit_size;
    public $rows_total;

    // sorting variables -------------------------------------------------------
    public $sort_field;
    public $sort_type;
    public $default_sort_field;
    public $default_sort_type;
    public $sorting_allowed;
    public $sql_sort;

    // filtering variables -----------------------------------------------------
    public $filtering_allowed;
    public $show_search_type;
    public $filter_fields;
    public $hide_display;

    // columns style parameters ------------------------------------------------
    public $wrap;

    // css style ---------------------------------------------------------------
    public $row_highlighting_allowed;
    public $css_class;
    public $rowColor;

    // table style parameters --------------------------------------------------
    public $tblAlign;
    public $tblWidth;
    public $tblBorder;
    public $tblBorderColor;
    public $tblCellSpacing;
    public $tblCellPadding;

    // datagrid modes ----------------------------------------------------------
    public $modes;
    public $mode_after_update;
    public $mode;
    protected $rid;
    public $rids;
    public $tbl_name;
    public $primary_key;
    public $condition;
    public $foreign_keys_array;
    public $columns_view_mode;
    public $columns_edit_mode;
    public $sorted_columns;
    public $draw_add_button_separately;

    // printing & exporting ----------------------------------------------------
    public $printing_allowed;
    public $exporting_allowed;
    public $exporting_directory;
    protected $exporting_types;

    // debug mode --------------------------------------------------------------
    public $debug;
    public $start_time;
    public $end_time;

    // message -----------------------------------------------------------------
    public $act_msg;
    public $messaging;
    public $is_error;
    public $errors;
    public $is_warning;
    public $warnings;
    public $dg_messages;

    // browser & system types --------------------------------------------------
    public $platform;
    public $browser_name;
    public $browser_version;

    // scrolling ---------------------------------------------------------------
    public $scrolling_option;
    public $scrolling_width;
    public $scrolling_height;

    // header names ------------------------------------------------------------
    public $field_header;
    public $field_value_header;

    // hide --------------------------------------------------------------------
    public $hide_grid_before_serach;

    // summarize ---------------------------------------------------------------
    public $summarize_columns;

    // multirow ----------------------------------------------------------------
    public $multirow_allowed;
    public $multi_rows;
    public $multirow_operations_array;

    // first field focus -------------------------------------------------------
    public $first_field_focus_allowed;

    // javascript errors display style -----------------------------------------
    public $js_validation_errors;

    //==========================================================================
    // Member Functions
    //==========================================================================

    //--------------------------------------------------------------------------
    // default constructor
    //--------------------------------------------------------------------------
    function __construct($debug_mode = false, $messaging = true, $unique_prefix = "", $datagrid_dir = "datagrid/", $inc_dir = "datagrid/"){
        // start calculating running time of a script
        $this->start_time = 0;
        $this->end_time = 0;
        if($debug_mode == true){
            $this->start_time = $this->getFormattedMicrotime();
        }

        // unique prefixes -----------------------------------------------------
        $this->setUniquePrefix($unique_prefix);

        // directory -----------------------------------------------------------
        $this->directory = $datagrid_dir;
        $this->inc_dir   = $inc_dir;

        // language ------------------------------------------------------------
        $this->lang_name = "en";
        $this->lang = array();
        $this->lang['total'] = "Total";
        $this->lang['wrong_parameter_error'] = "Wrong parameter in [<b>_FIELD_</b>]: _VALUE_";

        // caption -------------------------------------------------------------
        $this->caption = "";

        // rows and columns data members ---------------------------------------
        $this->http = $this->getProtocol();
        $this->port = $this->getPort();
        $this->server_name = $this->getServername();
        $this->HTTP_URL = str_replace("///", "//", $this->http.$this->server_name.$this->port.str_replace('?'.$_SERVER['QUERY_STRING'],"",$_SERVER['REQUEST_URI']));

        // http get vars -------------------------------------------------------
        $this->http_get_vars = "";
        $this->another_datagrids = "";

        // css style  ----------------------------------------------------------
        $this->row_highlighting_allowed = true;
        $this->css_class = "default";
        $this->rowColor = array();

        // signs ---------------------------------------------------------------
        $this->amp = "&amp;";
        $this->nbsp = ""; //&nbsp;

        $this->rows = 0;
        $this->row_lower = 0;
        $this->row_upper = 0;
        $this->columns = 0;
        $this->col_lower = 0;
        $this->col_upper = 0;

        // encoding & direction ------------------------------------------------
        $this->encoding = "utf8";
        $this->collation = "utf8_unicode_ci";
        $this->direction = "ltr";

        $this->layouts['view']   = "0";
        $this->layouts['edit']   = "1";
        $this->layouts['filter'] = "1";
        $this->layouts['show']   = "1";
        $this->layout_type = "view";

        // templates -----------------------------------------------------------
        $this->templates['view'] = "";
        $this->templates['edit'] = "";
        $this->templates['show'] = "";

        $this->pages_total = 0;
        $this->page_current = 0;
        $this->pages_array = array("10"=>"10", "25"=>"25", "50"=>"50", "100"=>"100", "250"=>"250", "500"=>"500", "1000"=>"1000");
        $this->first_arrow    = "|&lt;&lt;";
        $this->previous_arrow = "&lt;&lt;";
        $this->next_arrow     = "&gt;&gt;";
        $this->last_arrow     = "&gt;&gt;|";
        $this->default_page_size = 10;
        $this->req_page_size = 10;
        $this->paging_allowed = true;
        $this->rows_numeration = false;
        $this->numeration_sign = "N #";
        $this->lower_paging['results'] = false;
        $this->lower_paging['results_align'] = "left";
        $this->lower_paging['pages'] = false;
        $this->lower_paging['pages_align'] = "center";
        $this->lower_paging['page_size'] = false;
        $this->lower_paging['page_size_align'] = "right";
        $this->upper_paging['results'] = false;
        $this->upper_paging['results_align'] = "left";
        $this->upper_paging['pages'] = false;
        $this->upper_paging['pages_align'] = "center";
        $this->upper_paging['page_size'] = false;
        $this->upper_paging['page_size_align'] = "right";
        $this->limit_start = 0;
        $this->limit_size = $this->req_page_size;
        $this->rows_total = 0;

        $this->sort_field = "";
        $this->sort_field_by = "";
        $this->sort_type = "";
        $this->default_sort_field = array();
        $this->default_sort_type = array();
        $this->sorting_allowed = true;
        $this->sql_view = "";
        $this->sql_group_by = "";
        $this->sql = "";
        $this->sql_sort = "";

        $this->filtering_allowed = false;
        $this->show_search_type = true;
        $this->filter_fields = array();
        $this->hide_display = "";

        $this->tblAlign['view'] = "center";         $this->tblAlign['edit'] = "center";         $this->tblAlign['details'] = "center";
        $this->tblWidth['view'] = "90%";            $this->tblWidth['edit'] = "70%";            $this->tblWidth['details'] = "60%";
        $this->tblBorder['view'] = "1";             $this->tblBorder['edit'] = "1";             $this->tblBorder['details'] = "1";
        $this->tblBorderColor['view'] = "#000000";  $this->tblBorderColor['edit'] = "#000000";  $this->tblBorderColor['details'] = "#000000";
        $this->tblCellSpacing['view'] = "0";        $this->tblCellSpacing['edit'] = "0";        $this->tblCellSpacing['details'] = "0";
        $this->tblCellPadding['view'] = "0";        $this->tblCellPadding['edit'] = "0";        $this->tblCellPadding['details'] = "0";

        // datagrid modes ------------------------------------------------------
        $this->modes["add"]     = array("view"=>true, "edit"=>false, "type"=>"link");
        $this->modes["edit"]    = array("view"=>true, "edit"=>true,  "type"=>"link", "byFieldValue"=>"");
        $this->modes["cancel"]  = array("view"=>true, "edit"=>true,  "type"=>"link");
        $this->modes["details"] = array("view"=>true, "edit"=>false, "type"=>"link");
        $this->modes["delete"]  = array("view"=>true, "edit"=>true,  "type"=>"image");

        $this->draw_add_button_separately = false;

        $this->mode = "view";
        $this->mode_after_update = "";
        $this->rid = "";
        $this->rids = "";
        $this->tbl_name ="";
        $this->primary_key = 0;
        $this->condition = "";

        $this->foreign_keys_array = array();

        $this->columns_view_mode = array();
        $this->columns_edit_mode = array();
        $this->sorted_columns = array();

        $this->printing_allowed = true;
        $this->exporting_allowed = false;
        $this->exporting_directory = "";
        $this->exporting_types = array("excel"=>"true", "pdf"=>"true", "xml"=>"true");

        $this->wrap = "wrap";

        // scrolling -----------------------------------------------------------
        $this->scrolling_option = false;
        $this->scrolling_width = "90%";
        $this->scrolling_height = "100%";

        // header names --------------------------------------------------------
        $this->field_header = "";
        $this->field_value_header = "";

        // hide ----------------------------------------------------------------
        $this->hide_grid_before_serach = false;

        $this->summarize_columns = array();

        $this->multirow_allowed = false;
        $this->multi_rows = 0;
        $this->multirow_operations_array = array();
        $this->multirow_operations_array['delete'] = array("view"=>true);
        $this->multirow_operations_array['details'] = array("view"=>true);

        $this->first_field_focus_allowed = false;

        // message -------------------------------------------------------------
        $this->act_msg = "";
        $this->debug = (($debug_mode == true) || ($debug_mode == "true")) ? true : false ;
        if($this->debug) error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        $this->messaging = (($messaging == true) || ($messaging == "true")) ? true : false ;
        $this->is_error = false;
        $this->errors = array();
        $this->is_warning = false;
        $this->warnings = array();
        $this->dg_messages = array();
            $this->dg_messages['add'] = "";
            $this->dg_messages['update'] = "";
            $this->dg_messages['delete'] = "";

        // javascript errors display style -------------------------------------
        $this->js_validation_errors = "true";

        // set browser definitions
        $this->setBrowserDefinitions();
    }

    //--------------------------------------------------------------------------
    // class destructor
    //--------------------------------------------------------------------------
    function __destruct()
	{
		// echo 'this object has been destroyed';
    }

    //--------------------------------------------------------------------------
    // set unique names
    //--------------------------------------------------------------------------
    function setUniquePrefix($unique_prefix = ""){
        $this->unique_prefix = $unique_prefix;
        $this->unique_random_prefix = $this->getRandomString("5");
    }

    //--------------------------------------------------------------------------
    // set Http Get Vars
    //--------------------------------------------------------------------------
    function setHttpGetVars($http_get_vars = ""){
        $this->http_get_vars = $http_get_vars;
    }

    //--------------------------------------------------------------------------
    // set Other DataGrids
    //--------------------------------------------------------------------------
    function setAnotherDatagrids($another_datagrids = ""){
        $this->another_datagrids = $another_datagrids;
    }

    //--------------------------------------------------------------------------
    // set Scrolling Settings
    //--------------------------------------------------------------------------
    function allowScrollingSettings($scrolling_option = false){
        $this->scrolling_option = (($scrolling_option == true) || ($scrolling_option == "true")) ? true : false ;
    }

    //--------------------------------------------------------------------------
    // set Scrolling Settings
    //--------------------------------------------------------------------------
    function setScrollingSettings($width="", $height=""){
        if($width != "") $this->scrolling_width = $width;
        if($height != "") $this->scrolling_height = $height;
    }

    //--------------------------------------------------------------------------
    // set css class
    //--------------------------------------------------------------------------
    function setCssClass($class = "default"){
        $this->css_class = $class;
    }


    //--------------------------------------------------------------------------
    // write css class
    //--------------------------------------------------------------------------
    function writeCssClass($class = "default", $type = "embedded"){
        $req_print = $this->getVariable('print');

        if(strtolower($this->css_class) == "green"){
                $this->rowColor[0] = "#ffffff";
                $this->rowColor[1] = "#e4f5ef";
                $this->rowColor[2] = "#ffffff";
                $this->rowColor[3] = "#e4f5ef";
                $this->rowColor[4] = "#d4e5df";
                $this->rowColor[5] = "#d4e5df";
                $this->rowColor[6] = "#c6d7cf"; // header (th main) column
                $this->rowColor[7] = "#d4e5df"; // selected row mouse over lighting
                echo "\n<style>.resizable-textarea .grippie { BACKGROUND: url(".$this->directory."images/common/grippie.png) #ddd no-repeat center 2px; }</style>";
        }else if(strtolower($this->css_class) == "gray") {
                $this->rowColor[0] = "#f9f9f9";
                $this->rowColor[1] = "#f0f0f0";
                $this->rowColor[2] = "#f0f0f0";
                $this->rowColor[3] = "#dedede";
                $this->rowColor[4] = "#FEFFE8";
                $this->rowColor[5] = "#FEFFE8";
                $this->rowColor[6] = "#dedede"; // header (th main) column
                $this->rowColor[7] = "#FEFFE8"; // selected row mouse over lighting
                echo "\n<style>.resizable-textarea .grippie { BACKGROUND: url(".$this->directory."images/common/grippie.png) #ddd no-repeat center 2px; }</style>";
        }else if(strtolower($this->css_class) == "blue"){
                $this->rowColor[0] = "#f7f9fb";
                $this->rowColor[1] = "#ffffff";
                $this->rowColor[2] = "#d9e3f1";
                $this->rowColor[3] = "#e4ecf7";
                $this->rowColor[4] = "#FEFFE8";
                $this->rowColor[5] = "#FEFFE8";
                $this->rowColor[6] = "#cdd9ea"; // header (th main) column
                $this->rowColor[7] = "#FEFFE8"; // selected row mouse over lighting
                echo "\n<style>.resizable-textarea .grippie { BACKGROUND: url(".$this->directory."images/common/grippie.png) #ddd no-repeat center 2px; }</style>";
        }else if(strtolower($this->css_class) == "pink"){
                 $this->rowColor[0] = "#F8F7EF";
                $this->rowColor[1] = "#F8F7EF";
                $this->rowColor[2] = "#333333"; //edit td dark
                $this->rowColor[3] = "#F8F7EF"; //edit td light
                $this->rowColor[4] = "#e4e2d3";
                $this->rowColor[5] = "#E7E3C7";
                $this->rowColor[6] = "#FFCFB4"; // header (th main) column
                $this->rowColor[7] = "#e4e2d3"; // selected row mouse over lighting
                echo "\n<style>.resizable-textarea .grippie { BACKGROUND: url(".$this->directory."images/common/grippie.png) #ddd no-repeat center 2px; }</style>";
        }else{
                $this->rowColor[0] = "#fcfaf6";
                $this->rowColor[1] = "#ffffff";
                $this->rowColor[2] = "#ebeadb"; // dark
                $this->rowColor[3] = "#ebeadb"; // light
                $this->rowColor[4] = "#e2f3fc"; // row mouse over lighting
                $this->rowColor[5] = "#fdfde7"; // on mouse click
                $this->rowColor[6] = "#e2e0cb"; // header (th main) column
                $this->rowColor[7] = "#f9f9e3"; // selected row mouse over lighting
                echo "\n<style>.resizable-textarea .grippie { BACKGROUND: url(".$this->directory."images/common/grippie.png) #eee no-repeat center 2px; }</style>";
        }

        // if we in Print Mode
        if($req_print == true){
            $this->rowColor[0] = "";
            $this->rowColor[1] = "";
            $this->rowColor[2] = ""; // dark
            $this->rowColor[3] = ""; // light
            $this->rowColor[4] = ""; // row mouse over lighting
            $this->rowColor[5] = ""; // on mouse click
            $this->rowColor[6] = ""; // header (th main) column
            $this->rowColor[7] = ""; // selected row mouse over lighting
            echo "\n<!--[if IE]><link rel='stylesheet' type='text/css' href='".$this->directory."css/style_print_IE.css' /><![endif]-->";
            echo "\n<link rel='stylesheet' type='text/css' href='".$this->directory."css/style_print.css' />\n\n";
        }else{
            echo "\n<!--[if IE]><link rel='stylesheet' type='text/css' href='".$this->directory."css/style_".$this->css_class."_IE.css' /><![endif]-->";
            echo "\n<link rel='stylesheet' type='text/css' href='".$this->directory."css/style_".$this->css_class.".css' />\n\n";
        }

    }

    //--------------------------------------------------------------------------
    // set title for datagrid
    //--------------------------------------------------------------------------
    function setCaption($dg_caption = ""){
        $this->caption = $dg_caption;
    }

    //--------------------------------------------------------------------------
    // set data source
    //--------------------------------------------------------------------------
    function dataSource($db_handl, $sql = "", $start_order = "", $start_order_type = ""){
        // clear sql statment
        $sql = str_replace("\n", " ", $sql);    // new row
        $sql = str_replace(chr(13), " ", $sql); // CR sign
        $sql = str_replace(chr(10), " ", $sql); // LF sign
        $sql = str_replace(";", "", $sql);

        // get preliminary Primary Key
        $p_key = explode(" ", $sql);
        $p_key = str_replace(",", "", $p_key[1]);
        $p_key = explode(".", $p_key);
        $this->primary_key = $p_key[count($p_key)-1];

        $req_sort_field = $this->getVariable('sort_field');
        $req_sort_field_by = $this->getVariable('sort_field_by');
        $sort_field = ($req_sort_field_by != "") ? $req_sort_field_by : $req_sort_field ;
        $req_sort_type = $this->getVariable('sort_type');
        $this->db_handler = $db_handl;
        $this->db_handler->setFetchMode(DB_FETCHMODE_ORDERED);

        // handle SELECT SQL statement
        $this->sql_view = $sql;
        if($this->lastSubStrOccurence($this->sql_view, "from ") < $this->lastSubStrOccurence($this->sql_view, "where ")){
            // handle SELECT statment with sub-SELECTs and SELECT without WHERE
            $ind = strpos(strtolower($this->sql_view), "group by");
            if($ind){
                $prefix = substr($sql, 0, $ind);
                $suffix = substr($sql, $ind);
                $this->sql_view = $prefix." ";
                $this->sql_group_by = $suffix;
            }else{
                $this->sql_view .= " WHERE 1=1 ";
            }
        }else if($this->lastSubStrOccurence($this->sql_view, "where ") == ""){
            $this->sql_view .= " WHERE 1=1 ";
        }else{
            $ind = strpos(strtolower($this->sql_view), "group by");
            if($ind){
                $prefix = substr($sql, 0, $ind);
                $suffix = substr($sql, $ind);
                $this->sql_view = $prefix." ";
                $this->sql_group_by = $suffix;
            }
        }
        $this->sql = $this->sql_view.$this->sql_group_by;

        // set default order
        if($start_order != ""){
            $default_sort_field = explode(",", $start_order);
            $default_sort_type = explode(",", $start_order_type);
            for($ind=0; $ind < count($default_sort_field); $ind++){
                $this->default_sort_field[$ind] = trim($default_sort_field[$ind]);
                if(isset($default_sort_type[$ind])){
                    if((strtolower(trim($default_sort_type[$ind])) == "asc") || (strtolower(trim($default_sort_type[$ind])) == "desc")){
                        $this->default_sort_type[$ind] = trim($default_sort_type[$ind]);
                    }else{
                        $this->default_sort_type[$ind] = "ASC";
                        $this->addWarning('$default_order_type', $start_order_type);
                    }
                }else{
                    $this->default_sort_type[$ind] = "ASC";
                }
            }
        }else{
            $this->default_sort_field[0] = "1";
            $this->default_sort_type[0] = "ASC";
        }
        // create ORDER BY part of sql statment
        if($req_sort_field){
            if(!substr_count($this->sql, "ORDER BY")){
                $this->sql_sort = " ORDER BY ".$sort_field." ".$req_sort_type;
            }else{
              $this->sql_sort = " , ".$sort_field." ".$req_sort_type;
            }
        }else if($start_order != ""){
            $this->sql_sort = " ORDER BY ".$this->getOrderByList();
        }else{
            $this->sql_sort = " ORDER BY 1 ASC";
        }

        $this->getDataSet($this->sql_sort);

        // check if the preliminary key is a Primary Key
        if(strtolower($this->getFieldInfo(0, 'type', 1)) != "int"){
            $this->addWarning($this->primary_key, "Check this field carefully, it may be not a Primary Key!");
        }
    }

    //--------------------------------------------------------------------------
    // get DataSet
    //--------------------------------------------------------------------------
    function getDataSet($fsort = "", $limit = "", $mode = ""){
        $this->setEncodingOnDatabase();

        // we need this stupid operation to get a total number of rows in our query
        $this->setTotalNumberRows("", "", $mode);

        if($limit == ""){
            $limit = $this->setSqlLimitByDbType();
            $this->data_set = & $this->db_handler->query($this->setSqlByDbType($this->sql, $fsort, $limit));
        }

        if($this->db_handler->isError($this->data_set) == 1){
            $this->is_error = true;
            $this->addErrors();
        }

        $this->rows = $this->numberRows();
        $this->columns = $this->numberCols();

        if($this->debug){
            echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left' class='".$this->css_class."_class_error_message no_print' style='COLOR: #333333;'><b>search sql (".$this->strToLower($this->lang['total']).": ".$this->rows.") </b>". $this->sql.$fsort." ".$limit."</td></tr></table><br />";
        }

        $this->row_lower = 0;
        $this->row_upper = $this->rows;

        $this->col_lower = 0;
        $this->col_upper = $this->columns;
    }

    //--------------------------------------------------------------------------
    // ger ORDER BY fields list
    //--------------------------------------------------------------------------
    function getOrderByList(){
        $orderByList = "";
        for($ind=0; $ind < count($this->default_sort_field); $ind++){
            if($ind != 0) $orderByList .= ",";
            $orderByList .= " ".$this->default_sort_field[$ind]." ".$this->default_sort_type[$ind];
        }
        return $orderByList;
    }

    //--------------------------------------------------------------------------
    // perform security check from a Hack Attacks
    //--------------------------------------------------------------------------
    function securityCheck(){
        // check rid variable
        $req_rid = $this->getVariable('rid');
        if(eregi("'", $req_rid) || eregi('"', $req_rid) || eregi("%27", $req_rid) || eregi("%22", $req_rid)){
            return false;
        }
        // check query string
        $query_string = strtolower(rawurldecode($_SERVER['QUERY_STRING']));
        $bad_string = array("%20union%20", "/*", "*/union/*", "+union+", "load_file", "outfile", "document.cookie", "onmouse", "<script", "<iframe", "<applet", "<meta", "<style", "<form", "<img", "<body", "<link", "_GLOBALS", "_REQUEST", "_GET", "_POST", "include_path", "prefix", "http://", "https://", "ftp://", "smb://" );
        foreach ($bad_string as $string_value){
            if (strstr($query_string, $string_value )){
                return false;
            }
        }
        return true;
    }

    //--------------------------------------------------------------------------
    // bind data and draw
    //--------------------------------------------------------------------------
    function bind($show = true){
        $this->setInterfaceLang();
        $this->setMediaPrint();
        $this->setCommonJavaScript();

        $req_mode = $this->getVariable('mode');
        $req_rid = $this->getVariable('rid');
        $req_new = $this->getVariable('new');
        $req_page_size = $this->getVariable('page_size');
        $req_sort_field = $this->getVariable('sort_field');
        $req_sort_field_by = $this->getVariable('sort_field_by');
        $sort_field = ($req_sort_field_by != "") ? $req_sort_field_by : $req_sort_field ;
        $req_sort_type = $this->getVariable('sort_type');
        $req_print = $this->getVariable('print');

        // protect datagrid from a Hack Attacks
        if($this->securityCheck()){
            // VIEW mode processing
            if($req_mode == ""){
                $this->getDataSet($this->sql_sort);
                $view_limit = $this->setSqlLimitByDbType("0", $req_page_size);
            }

            // DELETE mode processing
            if(($req_mode == "delete") && ($req_rid != "")){
                $this->rid = $req_rid;
                if($req_print != true){
                    $this->deleteRow($this->rid);
                }
                $this->sql = $this->sql_view;
                $this->getDataSet($this->sql_sort);
                $this->mode = "view";
            }

            // UPDATE mode processing
            if($req_mode == "update"){
                $this->rid = $req_rid;
                if($req_print != true){
                    if($req_new != 1){
                        $this->updateRow($this->rid);
                    }else{
                        $this->addRow();
                        $this->mode_after_update = "";
                    }
                }
                if(($req_new != 1) && ($this->mode_after_update == "edit")){
                    $req_mode = "edit";
                    $this->mode = "edit";
                }else{
                    $this->sql = $this->sql_view;
                    $this->getDataSet($this->sql_sort);
                    $this->mode = "view";
                }
            }

            // EDIT & DETAILS modes processing
            if((($req_mode == "edit") || ($req_mode == "details")) && ($req_rid != "")){
                if($req_new == 1){
                    $this->data_set = $this->db_handler->query($this->sql);
                }
                $this->rid = $req_rid;
                $this->allowSorting(false);
                $this->allowPaging(false);
                $this->sql_sort = " ORDER BY " . $this->primary_key . " DESC";
                if(($this->layouts['view'] == "0") && ($this->layouts['edit'] == "1") && ($req_mode == "details")){
                    $this->rids = explode("-", $this->rid);
                    // if we have more that 1 row selected
                    if(count($this->rids) > 1){
                        $where = "WHERE ".$this->primary_key." IN ('-1' ";
                        foreach ($this->rids as $key){ if($key != "") $where .= ", '".$key."' "; }
                        $where .= ") ";
                        $this->multi_rows = count($this->rids);
                    }else{
                        $where = "WHERE ".$this->primary_key." = '".$req_rid."' ";
                    }
                    if($this->condition != ""){ $where .= " AND ". $this->condition; }
                    $view_limit = $this->setSqlLimitByDbType("0", $req_page_size);
                    //$this->sql_sort = ""; we need this sorted for multi-rows editing
                    $this->sql = "SELECT * FROM $this->tbl_name ".$where;
                }else if(($this->layouts['view'] == "0") && ($this->layouts['edit'] == "1") && ($req_mode == "edit")){
                    $this->rids = explode("-", $this->rid);
                    // if we have more that 1 row selected
                    // mr_1
                    if(count($this->rids) > 1){
                        $where = "WHERE ".$this->primary_key." IN ('-1' ";
                        foreach ($this->rids as $key){ if($key != "") $where .= ", '".$key."' "; }
                        $where .= ") ";
                        $this->multi_rows = count($this->rids);
                    }else{
                        $where = "WHERE ".$this->primary_key." = '".$req_rid."' ";
                    }
                    if($this->condition != ""){ $where .= " AND ". $this->condition; }
                    $view_limit = $this->setSqlLimitByDbType("0", $req_page_size);
                    //$this->sql_sort = ""; we need this sorted for multi-rows editing
                    $this->sql = "SELECT * FROM $this->tbl_name ".$where;
                }else if(($this->layouts['view'] == "0") && ($this->layouts['edit'] == "0") && ($req_mode == "details")){
                    // if we have more that 1 row selected
                    $this->rids = explode("-", $this->rid);
                    if(count($this->rids) > 1){
                        $where = "WHERE ".$this->primary_key." IN ('-1' ";
                        foreach ($this->rids as $key){ if($key != "") $where .= ", '".$key."' "; }
                        $where .= ") ";
                        $this->multi_rows = count($this->rids);
                    }else{
                        $where = "WHERE ".$this->primary_key." = '".$req_rid."' ";
                    }
                    $view_limit = $this->setSqlLimitByDbType("0", $req_page_size);
                    $this->sql_sort = "";
                    $this->sql = "SELECT * FROM $this->tbl_name ".$where;
                }else if(($this->layouts['view'] == "0") && ($this->layouts['edit'] == "0") && ($req_mode == "edit")){
                    $view_limit = "";
                    if($this->condition != ""){
                        $where = "WHERE ". $this->condition;
                    } else {
                        $view_limit = $this->setSqlLimitByDbType("0", $req_page_size);
                        $where = "WHERE ".$this->primary_key." >= '".$req_rid."' ";
                    }
                    if($req_sort_field != "") $this->sql_sort = " ORDER BY " . $sort_field . " " . $req_sort_type;
                    $this->sql = "SELECT * FROM $this->tbl_name ".$where;
                }else{
                    $view_limit = $this->setSqlLimitByDbType("0", $req_page_size);
                    $where = "WHERE ".$this->primary_key." = '".$req_rid."' ";
                    $this->sql = "SELECT * FROM $this->tbl_name ".$where;
                }

                $this->getDataSet($this->sql_sort, $view_limit, $this->mode_after_update);
                if($req_mode == "edit") $this->mode = "edit";
                else $this->mode = "details";
            }

            // CANCEL mode processing
            if($req_mode == "cancel"){
                $this->rid = "";
                $this->sql = $this->sql_view;
                $this->getDataSet($this->sql_sort);
                $this->mode = "view";
            }

            // ADD mode processing
            if($req_mode == "add"){
                $this->mode_after_update = "";
                // we don't need multirow option allowed when we add new record
                $this->multirow_allowed = false;
                if(($this->layouts['view'] == "0") && ($this->layouts['edit'] == "0")){
                    // we need
                    $view_limit = "";
                    if($this->condition != "") $where = " WHERE ". $this->condition;
                    else $where = "";
                    $this->sql = "SELECT * FROM $this->tbl_name ".$where;
                }else{
                    $view_limit = "";
                    $this->sql = "SELECT * FROM $this->tbl_name ";
                }
                $this->sql_sort = " ORDER BY " . $this->primary_key . " DESC";
                $this->getDataSet($this->sql_sort, $view_limit);
                $this->rid = -1;
                $this->allowSorting(false);
                $this->allowPaging(false);
                $this->mode = "edit";
            }
        }else{
            // VIEW mode processing
            if($req_mode == ""){
                $this->getDataSet($this->sql_sort);
                $view_limit = $this->setSqlLimitByDbType("0", $req_page_size);
            }
            if($this->debug == true){
                echo "<br /><center><label class='default_class_error_message'>Wrong parameters were passed! Possible Hack attack!</label></center><br />";
            }else{
                echo "<br /><center><label class='default_class_error_message'>Wrong parameters were passed!</label></center><br />";
            }
        }

        $this->displayErrors();
        $this->displayWarnings();
        $this->displayDataSent();

        if($this->data_set){
            if(($this->mode === "edit") || ($this->mode === "add")){
                $this->layout_type = "edit";
                $this->allowHighlighting(false);
            }else if($this->mode === "details"){
                $this->layout_type = "show";
                $this->allowHighlighting(false);
            }else {
                $this->layout_type = "view";
            }

            // sort columns by mode order
            $this->sortColumns($this->mode);

            if($show == true){
                if($this->layouts[$this->layout_type] == "0"){
                    $this->drawTabular();
                }else if($this->layouts[$this->layout_type] == "1"){
                    $this->drawColumnar();
                }else if($this->layouts[$this->layout_type] == "2"){
                    $this->drawCustomized();
                }else{
                    $this->drawTabular();
                }
            }
        }

        $this->setCommonJavaScriptEnd();
        // finish calculating running time of a script
        if($this->debug == true){
            $this->end_time = $this->getFormattedMicrotime();
            echo "<br><center><label class='default_class_label'>Total running time: ".round((float)$this->end_time - (float)$this->start_time, 6)." sec.</label></center>";
        }
    }

    //--------------------------------------------------------------------------
    // set encoding
    //--------------------------------------------------------------------------
    function setEncoding($dg_encoding = "", $dg_collation = ""){
        $this->encoding = ($dg_encoding != "") ? $dg_encoding : $this->encoding;
        $this->collation = ($dg_collation != "") ? $dg_collation : $this->collation;
    }

    //--------------------------------------------------------------------------
    // set encoding and collation on database
    //--------------------------------------------------------------------------
    function setEncodingOnDatabase(){
        $sql_variables = array(
                'character_set_client'  =>$this->encoding,
                'character_set_server'  =>$this->encoding,
                'character_set_results' =>$this->encoding,
                'character_set_database'=>$this->encoding,
                'character_set_connection'=>$this->encoding,
                'collation_server'      =>$this->collation,
                'collation_database'    =>$this->collation,
                'collation_connection'  =>$this->collation
        );
        foreach($sql_variables as $var => $value){
            $sql = "SET $var=$value;";
            $this->db_handler->query($sql);
        }
    }

    //--------------------------------------------------------------------------
    // set direction
    //--------------------------------------------------------------------------
    function setDirection($direction = "ltr"){
        $this->direction = $direction;
    }

    //--------------------------------------------------------------------------
    // set layouts
    //--------------------------------------------------------------------------
    function setLayouts($layouts = ""){
        $this->layouts['view']   = (isset($layouts['view'])) ? $layouts['view'] : "0";
        $this->layouts['edit']   = (isset($layouts['edit'])) ? $layouts['edit'] : "0";
        $this->layouts['show']   = (isset($layouts['details'])) ? $layouts['details'] : "1";
        $this->layouts['filter'] = (isset($layouts['filter'])) ? $layouts['filter'] : "0";
    }

    //--------------------------------------------------------------------------
    // set templates for customized layouts
    //--------------------------------------------------------------------------
    function setTemplates($view = "", $add_edit = "", $details = ""){
        $this->templates['view'] = $view;
        $this->templates['edit'] = $add_edit;
        $this->templates['show'] = $details;
    }

    //--------------------------------------------------------------------------
    // set paging settings
    //--------------------------------------------------------------------------
    function setPagingSettings($lower=false, $upper=false, $pages_array=false, $default_page_size="", $paging_arrows=""){
        if(($lower == true) || ($lower == "true")){
            if($lower['results']) $this->lower_paging['results'] = $lower['results'];
            if($lower['results_align']) $this->lower_paging['results_align'] = $lower['results_align'];
            if($lower['pages']) $this->lower_paging['pages'] = $lower['pages'];
            if($lower['pages_align']) $this->lower_paging['pages_align'] = $lower['pages_align'];
            if($lower['page_size']) $this->lower_paging['page_size'] = $lower['page_size'];
            if($lower['page_size_align']) $this->lower_paging['page_size_align'] = $lower['page_size_align'];
        }
        if(($upper == true) || ($upper == "true")){
            if($upper['results']) $this->upper_paging['results'] = $upper['results'];
            if($upper['results_align']) $this->upper_paging['results_align'] = $upper['results_align'];
            if($upper['pages']) $this->upper_paging['pages'] = $upper['pages'];
            if($upper['pages_align']) $this->upper_paging['pages_align'] = $upper['pages_align'];
            if($upper['page_size']) $this->upper_paging['page_size'] = $upper['page_size'];
            if($upper['page_size_align']) $this->upper_paging['page_size_align'] = $upper['page_size_align'];
        }
        if($pages_array){
            if(is_array($pages_array) && (count($pages_array) > 0)){
                $first_key = "";
                foreach($pages_array as $key => $val){
                    if($first_key == "") {$first_key = $key;};
                    if (intval($pages_array[$key]) == 0) $pages_array[$key] = 1;
                }
                $this->pages_array = $pages_array;
                $this->req_page_size = ($pages_array[$first_key] > 0) ? $pages_array[$first_key] : $this->req_page_size;
            }
        }
        if(($default_page_size != "") && ($default_page_size > 0)) { $this->default_page_size = $this->req_page_size = $default_page_size; }

        if($paging_arrows != ""){
            if(is_array($paging_arrows) && (count($paging_arrows) > 0)){
                $this->first_arrow    = (isset($paging_arrows["first"])) ? $paging_arrows["first"] : $this->first_arrow;
                $this->previous_arrow = (isset($paging_arrows["previous"])) ? $paging_arrows["previous"] : $this->previous_arrow;
                $this->next_arrow     = (isset($paging_arrows["next"])) ? $paging_arrows["next"] : $this->next_arrow;
                $this->last_arrow     = (isset($paging_arrows["last"])) ? $paging_arrows["last"] : $this->last_arrow;
            }
        }
    }

    function allowPrinting  ($option = true) { $this->printing_allowed  = (($option == true) || ($option == "true")) ? true : false ; }
    function allowExporting ($option = true, $exporting_directory = "") { $this->exporting_allowed = (($option == true) || ($option == "true")) ? true : false ; $this->exporting_directory = $exporting_directory; }
    function allowExportingTypes($exporting_types = ""){
        if(is_array($exporting_types)){
            $this->exporting_types["excel"] = (isset($exporting_types["excel"]) && (($exporting_types["excel"] == true) || ($exporting_types["excel"] == "true"))) ?  true : false;
            $this->exporting_types["pdf"]   = (isset($exporting_types["pdf"]) && (($exporting_types["pdf"] == true) || ($exporting_types["pdf"] == "true"))) ?  true : false;
            $this->exporting_types["xml"]   = (isset($exporting_types["xml"]) && (($exporting_types["xml"] == true) || ($exporting_types["xml"] == "true"))) ?  true : false;
        }
    }
    function allowSorting   ($option = true) { $this->sorting_allowed   = (($option == true) || ($option == "true")) ? true : false ; }
    function allowFiltering ($option = false, $show_search_type = "true"){
        $this->filtering_allowed = (($option == true) || ($option == "true")) ? true : false ;
        $this->show_search_type  = (($show_search_type == true) || ($show_search_type == "true")) ? true : false ;
    }
    function allowPaging($option = true, $rows_numeration = false, $numeration_sign = "N #"){
        $this->paging_allowed = (($option == true) || ($option == "true")) ? true : false ;
        $this->rows_numeration = $rows_numeration;
        $this->numeration_sign = $numeration_sign;
    }
    function allowMultirowOperations($multirow_option = false){
        $this->multirow_allowed = (($multirow_option == true) || ($multirow_option == "true")) ? true : false ;
    }

    //--------------------------------------------------------------------------
    // set multirow operations
    //--------------------------------------------------------------------------
    function setMultirowOperations($multirow_operations = ""){
        if(is_array($multirow_operations)){
            foreach($multirow_operations as $fldName => $fldValue){
                $this->multirow_operations_array[$fldName] = $fldValue;
            }
        }
    }

    //--------------------------------------------------------------------------
    // set fields for filtering
    //--------------------------------------------------------------------------
    function setFieldsFiltering($filter_fields_array = ""){
        $req_selSearchType = $this->getVariable('_ff_selSearchType');
        $req_onSUBMIT_FILTER = $this->getVariable('_ff_onSUBMIT_FILTER');

        if(is_array($filter_fields_array)){
            foreach($filter_fields_array as $fldName => $fldValue){
                $this->filter_fields[$fldName] = $fldValue;
            }
            if($req_onSUBMIT_FILTER != ""){
                $search_type_start = "AND";
                if($req_selSearchType == "0"){
                    $search_type = "AND";
                }else{
                    $search_type = "OR";
                }
                if(!substr_count(strtolower($this->sql_view), "where") && !substr_count(strtolower($this->sql_view), "having")) $this->sql_view .= " WHERE 1=1 ";
                foreach($filter_fields_array as $fldName => $fldValue){
                    $table_field_name = "";
                    $fldValue_fields = str_replace(" ", "", $fldValue['field']);
                    $fldValue_fields = explode(",", $fldValue_fields);
                    foreach($fldValue_fields as $fldValue_field){
                        $table_field_name = $fldValue['table']."_".$fldValue_field;
                        if(isset($_REQUEST[$this->unique_prefix."_ff_".$table_field_name]) && ($_REQUEST[$this->unique_prefix."_ff_".$table_field_name] !== "")){
                            $filter_field_operator =  $table_field_name."_operator";
                            if(isset($fldValue['case_sensitive']) && ($fldValue['case_sensitive'] != true)){
                                $fldTableField = $this->getLcaseFooByDbType()."(".(($fldValue['table'] != "") ? $fldValue['table']."." : "" ).$fldValue_field.")";
                                $fldTableFieldName = $this->strToLower($_REQUEST[$this->unique_prefix."_ff_".$table_field_name]);
                            }else{
                                $fldTableField = (($fldValue['table'] != "") ? $fldValue['table']."." : "" ).$fldValue_field;
                                $fldTableFieldName = $_REQUEST[$this->unique_prefix."_ff_".$table_field_name];
                            }
                            if(isset($fldValue['comparison_type']) && (strtolower($fldValue['comparison_type']) == "numeric")){
                                $left_geresh ="";
                            }else{
                                $left_geresh ="'";
                            }


                            // split by separated words if user splitted them by ","
                            $splitted_fldTableFieldName = split(",",$fldTableFieldName);
                            $separated_word_count = 0;
                            if(count($splitted_fldTableFieldName) > 0) $this->sql_view .= $search_type_start." ( ";
                            foreach($splitted_fldTableFieldName as $separated_word){
                                $separated_word = trim($separated_word);
                                if($separated_word_count > 0 ){ $this->sql_view .= " OR "; }
                                if(isset($_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator])){
                                    if(isset($fldValue['comparison_type']) && (strtolower($fldValue['comparison_type']) == "binary")) $comparison_type = "BINARY";
                                    else $comparison_type ="";
                                    if($_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator] == "like"){
                                        $this->sql_view .= " $fldTableField ".$_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator]." ".$comparison_type." '%".$separated_word."%'";
                                    }else if($_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator] == "like%"){
                                        $this->sql_view .= " $fldTableField ".substr($_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator], 0, 4)." ".$comparison_type." '".$separated_word."%'";
                                    }else if($_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator] == "%like"){
                                        $this->sql_view .= " $fldTableField ".substr($_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator], 1, 4)." ".$comparison_type." '%".$separated_word."'";
                                    }else if($_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator] == "%like%"){
                                        $this->sql_view .= " $fldTableField ".substr($_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator], 1, 4)." ".$comparison_type." '%".$separated_word."%'";
                                    }else{
                                        $this->sql_view .= " $fldTableField ".$_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator]." $left_geresh".$separated_word."$left_geresh ";
                                    }
                                }else{
                                    $this->sql_view .= " $fldTableField = $left_geresh".$separated_word."$left_geresh ";
                                }
                                $separated_word_count++;
                            }
                            if(count($splitted_fldTableFieldName) > 0) $this->sql_view .= " ) ";
                            if($search_type_start !== $search_type){ $search_type_start = $search_type; }
                        }
                    }
                }
                $this->dataSource($this->db_handler, $this->sql_view);
            }
        }
    }

    //--------------------------------------------------------------------------
    // set mode add/edit/cancel/delete
    //--------------------------------------------------------------------------
    function setModes($parameters){
        $this->modes = array();
        if(is_array($parameters)){
            foreach($parameters as $modeName => $modeValue){
                $this->modes[$modeName] = $modeValue;
            }
        }
        $this->mode = "view";
    }

    //--------------------------------------------------------------------------
    // set editing table & primary key Id
    //--------------------------------------------------------------------------
    function setTableEdit($tbl_name, $field_name, $condition = ""){
        $this->tbl_name = $tbl_name;
        $this->primary_key = $field_name;
        $this->condition = $condition;
    }

    //--------------------------------------------------------------------------
    // set set Foreign Keys Editing
    //--------------------------------------------------------------------------
    function setForeignKeysEdit($foreign_keys_array = ""){
        if(is_array($foreign_keys_array)){
            foreach($foreign_keys_array as $fldName => $fldValue){
                $this->foreign_keys_array[$fldName] = $fldValue;
            }
        }
    }

    //--------------------------------------------------------------------------
    // set View Mode Table Properties
    //--------------------------------------------------------------------------
    function setViewModeTableProperties($vmt_properties = ""){
        if(is_array($vmt_properties) && (count($vmt_properties) > 0)){
            if(isset($vmt_properties['width'])) $this->tblWidth['view'] = $vmt_properties['width'];
        }
    }

    //--------------------------------------------------------------------------
    // set Add/Edit/Details Mode Table Properties
    //--------------------------------------------------------------------------
    function setEditModeTableProperties($emt_properties = ""){
        if(is_array($emt_properties) && (count($emt_properties) > 0)){
            if(isset($emt_properties['width'])) $this->tblWidth['edit'] = $emt_properties['width'];
        }
    }

    //--------------------------------------------------------------------------
    // set Details Mode Table Properties
    //--------------------------------------------------------------------------
    function setDetailsModeTableProperties($dmt_properties = ""){
        if(is_array($dmt_properties) && (count($dmt_properties) > 0)){
            if(isset($dmt_properties['width'])) $this->tblWidth['details'] = $dmt_properties['width'];
        }
    }

    //--------------------------------------------------------------------------
    // set Columns in View Mode
    //--------------------------------------------------------------------------
    function setColumnsInViewMode($columns = ""){
        unset($this->columns_view_mode);
        $this->columns_edit_mode = array();
        if(is_array($columns)){
            foreach($columns as $fldName => $fldValue){
                $this->columns_view_mode[$fldName] = $fldValue;
            }
        }
    }

    //--------------------------------------------------------------------------
    // set Auto-Generated Columns in View Mode
    //--------------------------------------------------------------------------
    function setAutoColumnsInViewMode($auto_columns = ""){
        if(($auto_columns == true) || ($auto_columns == "true")){
            unset($this->columns_view_mode);
            if($this->db_handler->isError($this->data_set) == 1){
                $this->is_error = true;
                $this->addErrors();
            }else{
                $fields = $this->data_set->tableInfo();
                for($ind=0; $ind < $this->data_set->numCols(); $ind++){
                    $this->columns_view_mode[$fields[$ind]['name']] =
                        array("header"  =>$fields[$ind]['name'],
                            "type"      =>"label",
                            "align"     =>"left",
                            "width"     =>"210px",
                            "wrap"      =>"wrap",
                            "tooltip"   =>false,
                            "text_length"=>"-1",
                            "case"      =>"normal",
                            "summarize" =>false,
                            "visible"   =>"true"
                        );
                }
            }
        }
    }

    //--------------------------------------------------------------------------
    // set Columns in Add/Edit/Details Mode
    //--------------------------------------------------------------------------
    function setColumnsInEditMode($columns = ""){
        unset($this->columns_edit_mode);
        if(is_array($columns)){
            foreach($columns as $fldName => $fldValue){
                $this->columns_edit_mode[$fldName] = $fldValue;
            }
        }
    }

    //--------------------------------------------------------------------------
    // set Auto-Generated Columns in Add/Edit/Details Mode
    //--------------------------------------------------------------------------
    function setAutoColumnsInEditMode($auto_columns = ""){
        if(($auto_columns == true) || ($auto_columns == "true")){
            unset($this->columns_edit_mode);
            $sql  = " SELECT * FROM ".$this->tbl_name." ";
            $dSet = $this->db_handler->query($sql);
            if($this->db_handler->isError($this->data_set) == 1){
                $this->is_error = true;
                $this->addErrors();
            }else{
                $fields = $dSet->tableInfo();
                for($ind=0; $ind < $dSet->numCols(); $ind++){
                    if($fields[$ind]['name'] != $this->primary_key){
                        // get required simbol
                        $required_simbol = ($this->isFieldRequired($fields[$ind]['name'])) ? "r" : "s";
                        // get field view type & view type
                        $type_view = "texbox";
                        switch (strtolower($fields[$ind]['type'])){
                            case 'int':     // int: TINYINT, SMALLINT, MEDIUMINT, INT, INTEGER, BIGINT, TINY, SHORT, LONG, LONGLONG, INT24
                                $type_simbol = "i"; break;
                            case 'real':    // real: FLOAT, DOUBLE, DECIMAL, NUMERIC
                                $type_simbol = "f"; break;
                            case 'null':    // empty: NULL
                                $type_simbol = "t"; break;
                            case 'string':  // string: CHAR, VARCHAR, TINYTEXT, TEXT, MEDIUMTEXT, LONGTEXT, ENUM, SET, VAR_STRING
                            case 'blob':    // blob: TINYBLOB, MEDIUMBLOB, LONGBLOB, BLOB, TEXT
                            case 'date':    // date: DATE
                            case 'timestamp':    // date: TIMESTAMP
                            case 'year':    // date: YEAR
                            case 'time':    // date: TIME
                                $type_simbol = "t"; break;
                            case 'datetime':    // date: DATETIME
                                $type_view = "datetime";
                                $type_simbol = "t"; break;
                            default:
                                $type_simbol = "t"; break;
                        }
                        // get required-type simbols
                        $req_type_simbols = $required_simbol."".$type_simbol;
                        // get field maxlength
                        $field_maxlength = ($fields[$ind]['len'] <= 0) ? "" : $fields[$ind]['len'];
                        $this->columns_edit_mode[$fields[$ind]['name']] =
                            array("header"  =>$fields[$ind]['name'],
                                "type"      =>"$type_view",
                                "req_type"  =>"$req_type_simbols",
                                "width"     =>"210px",
                                "maxlength" =>"$field_maxlength",
                                "title"     =>$fields[$ind]['name'],
                                "readonly"  =>false,
                                "visible"   =>"true"
                            );
                    };
                }

            }
        }
    }

    //--------------------------------------------------------------------------
    // table drawing functions
    //--------------------------------------------------------------------------
    function showCaption() {
        echo ($this->caption != "") ? "<div class='".$this->css_class."_class_caption'>". $this->caption ."</div><br />".chr(13) : "";
    }

    function tblOpen($style=""){
        if($this->scrolling_option == true) {
            $width = ($this->mode == "view") ?  "100%" : $this->tblWidth[$this->mode];
        }else{
            $width = $this->tblWidth[$this->mode];
        }
        echo "<table dir='".$this->direction."' class='".$this->css_class."_class_table' align='".$this->tblAlign[$this->mode]."' width='".$width."' ".$style.">".chr(13);
        echo $this->tbodyOpen();
    }

    function tblClose(){
        echo $this->tbodyClose();
        echo "</table>".chr(13);
    }

    function scrollDivOpen(){
        if($this->scrolling_option == true){
            echo "<center><div style='TEXT-ALIGN:center; PADDING:0px; WIDTH:".$this->scrolling_width."; HEIGHT:".$this->scrolling_height."; overflow:auto;'>";
            echo chr(13);
        }
    }

    function scrollDivClose(){
        if($this->scrolling_option == true){
            echo "</div></center>"; echo chr(13);
        }
    }

    function hideDivOpen(){
        $req_onSUBMIT_FILTER = $this->getVariable('_ff_onSUBMIT_FILTER');
        if(($this->hide_grid_before_serach == true) && !($req_onSUBMIT_FILTER != "")){
            echo "<div style='display: none;'>"; echo chr(13);
        }
    }

    function hideDivClose(){
        $req_onSUBMIT_FILTER = $this->getVariable('_ff_onSUBMIT_FILTER');
        if(($this->hide_grid_before_serach == true) && !($req_onSUBMIT_FILTER != "")){
            echo "</div>"; echo chr(13);
        }
    }

    function theadOpen() { echo "<thead>".chr(13);  }
    function theadClose(){ echo "</thead>".chr(13); }
    function tbodyOpen() { echo "<tbody>".chr(13);  }
    function tbodyClose(){ echo "</tbody>".chr(13); }
    function tfootOpen() { echo "<tfoot>".chr(13);  }
    function tfootClose(){ echo "</tfoot>".chr(13); }

    function rowOpen($id, $rowColor = "", $height=""){
        $req_print = $this->getVariable('print');
        $text = "<tr class='class_tr' bgcolor='$rowColor' id='".$this->unique_prefix."row_".$id."' ";
        if($height != "") { $text .= "height='".$height."' "; };
        if($req_print != true){
            if($this->row_highlighting_allowed){
                $text .= " onclick=\"onMouseClickRow('".$this->unique_prefix."','".$id."','".$this->rowColor[5]."', '".$this->rowColor[1]."', '".$this->rowColor[0]."');\" ";
                $text .= " onmouseover=\"onMouseOverRow('".$this->unique_prefix."','".$id."','".$this->rowColor[4]."', '".$this->rowColor[7]."');\" ";
                $text .= " onmouseout=\"onMouseOutRow('".$this->unique_prefix."','".$id."','".$rowColor."','".$this->rowColor[5]."');\" ";
            }
        }else{
            $text .= " ";
        }
        $text .= ">".chr(13);
        echo $text;
    }

    function rowClose(){
        echo "</tr>".chr(13);
    }

    function mainColOpen($align='left', $colSpan=0, $wrap='', $width='', $class='', $style=''){
        if($class == '') $class = $this->css_class."_class_th";
        $class_align = ($align == "") ? "" : " class_".$align;
        $wrap = ($wrap == '') ? $this->wrap : $wrap;
        $text = "<th class='".$class.$class_align."' ";
        $text .= " bgColor='".$this->rowColor[6]."'";
        $text .= ($this->mode != "edit") ? " onmouseover=\"bgColor='".$this->rowColor[3]."';\" onmouseout=\"bgColor='".$this->rowColor[6]."';\"" : "";
        $text .= ($width !=='')? " width='$width'" : "";
        $text .= ($colSpan != 0) ? " colspan='$colSpan'" : "";
        $text .= ($wrap != '') ? " $wrap" : "";
        $text .= ($style != '') ? " $style" : "";
        $text .= ">";
        echo $text;
    }

    function mainColClose(){
        echo "</th>".chr(13);
    }

    function colOpen($align='left', $colSpan=0, $wrap='', $bgcolor='', $class_td='', $width='', $style=''){
        if($class_td == '') $class_td = $this->css_class."_class_td";
        $req_print = $this->getVariable('print');
        $wrap = ($wrap == '') ? $this->wrap : $wrap;
        $class_align = ($align == "") ? "" : " class_".$align;
        $text = "<td class='".$class_td.$class_align."' ";
        $text .= ($bgcolor !== '')? " bgcolor='$bgcolor'" : "";
        $text .= ($colSpan != 0) ? " colspan='$colSpan'" : "";
        $text .= ($width !=='')? " width='$width'" : "";
        $text .= ($wrap != '') ? " $wrap" : "";
        $text .= ($style != '') ? " $style" : "";
        $text .= ">";
        echo $text;
    }

    function colClose(){
        echo "</td>".chr(13);
    }

    function emptyRow(){
        $this->rowOpen("","");
        $this->colOpen();$this->colClose();
        $this->rowClose();
    }

    //--------------------------------------------------------------------------
    // draw Control Panel
    //--------------------------------------------------------------------------
    function drawControlPanel(){
        $req_print  = $this->getVariable('print');
        $req_export = $this->getVariable('export');
        $req_mode   = $this->getVariable('mode');
        $myRef_window = $this->unique_prefix."myRef";

        if($this->filtering_allowed || $this->exporting_allowed || $this->printing_allowed){
            $margin_bottom = ($this->layout_type == "edit") ? "margin-bottom: 7px;" : "margin-bottom: 5px;";
            echo "<table border='0' align='center' id='printTbl' style='margin-left: auto; margin-right: auto; $margin_bottom' width='".$this->tblWidth[$this->mode]."' cellspacing='1' cellpadding='1'>";
            echo "<tr>";
            echo "<td align='left'>";
            if($this->mode == "edit"){
                echo "<label class='".$this->css_class."_class_label'>".$this->lang['required_fields_msg']."</label>";
            }
            echo "</td>";
            if($this->filtering_allowed && (($this->mode != "edit") && ($this->mode != "details"))){
                echo "<td align='right' class='class_nowrap' width='20px'>";
                $hide_display = "";
                $unhide_display = "display: none; ";
                if(isset($_COOKIE[$this->unique_prefix.'hide_search'])) {
                    if($_COOKIE[$this->unique_prefix.'hide_search'] == 1){
                        $this->hide_display = "display: none;";
                        $hide_display = "display: none; ";
                        $unhide_display = "";
                    }else{
                        $this->hide_display = "";
                        $hide_display = "";
                        $unhide_display = "display: none; ";
                    }
                }
                if($req_print != true){
                    echo "<a id='".$this->unique_prefix."a_hide' style='cursor:pointer; ".$hide_display."' onClick=\"return hideUnHideFiltering('hide', '".$this->unique_prefix."');\"><img src='".$this->directory."images/".$this->css_class."/search_hide_b.gif' onmouseover='this.src=\"".$this->directory."images/".$this->css_class."/search_hide_r.gif\"' onmouseout='this.src=\"".$this->directory."images/".$this->css_class."/search_hide_b.gif\"' alt='".$this->lang['hide_search']."' title='".$this->lang['hide_search']."' /></a>";
                    echo "<a id='".$this->unique_prefix."a_unhide' style='cursor:pointer; ".$unhide_display."' onClick=\"return hideUnHideFiltering('unhide', '".$this->unique_prefix."');\"><img src='".$this->directory."images/".$this->css_class."/search_unhide_b.gif' onmouseover='this.src=\"".$this->directory."images/".$this->css_class."/search_unhide_r.gif\"' onmouseout='this.src=\"".$this->directory."images/".$this->css_class."/search_unhide_b.gif\"' alt='".$this->lang['unhide_search']."' title='".$this->lang['unhide_search']."' /></a>";
                }
                echo "</td>";
            }
            if($this->exporting_allowed){
                if((($req_export == "") || ($req_print != true)) && ($req_print == "")){
                    if($this->exporting_types["excel"] == true){
                        echo "<td align='right' width='20px'>";
                        echo "<a style='cursor:pointer;' onClick=\"".$myRef_window."=window.open(''+self.location+'".(($_SERVER['QUERY_STRING'] == "")?"?":"&").$this->unique_prefix."export=true&".$this->unique_prefix."export_type=csv','ExportToExcel','left=100,top=100,width=540,height=360,toolbar=0,resizable=0,location=0,scrollbars=1');".$myRef_window.".focus();\" class='".$this->css_class."_class_a'>";
                        echo "<img src='".$this->directory."images/".$this->css_class."/excel_b.gif'  onmouseover='this.src=\"".$this->directory."images/".$this->css_class."/excel_r.gif\"' onmouseout='this.src=\"".$this->directory."images/".$this->css_class."/excel_b.gif\"' alt='".$this->lang['export_to_excel']."' title='".$this->lang['export_to_excel']."' /></a>";
                        echo "</td>";
                    }
                    if($this->exporting_types["pdf"] == true){
                        echo "<td align='right' width='20px'>";
                        echo "<a style='cursor:pointer;' onClick=\"".$myRef_window."=window.open(''+self.location+'".(($_SERVER['QUERY_STRING'] == "")?"?":"&").$this->unique_prefix."export=true&".$this->unique_prefix."export_type=pdf','ExportToPdf','left=100,top=100,width=540,height=360,toolbar=0,resizable=0,location=0,scrollbars=1');".$myRef_window.".focus();\" class='".$this->css_class."_class_a'>";
                        echo "<img src='".$this->directory."images/".$this->css_class."/pdf.jpg'  onmouseover='this.src=\"".$this->directory."images/".$this->css_class."/pdf.jpg\"' onmouseout='this.src=\"".$this->directory."images/".$this->css_class."/pdf.jpg\"' alt='".$this->lang['export_to_pdf']."' title='".$this->lang['export_to_pdf']."' /></a>";
                        echo "</td>";
                    }
                    if($this->exporting_types["xml"] == true){
                        echo "<td align='right' width='20px'>";
                        echo "<a style='cursor:pointer;' onClick=\"".$myRef_window."=window.open(''+self.location+'".(($_SERVER['QUERY_STRING'] == "")?"?":"&").$this->unique_prefix."export=true&".$this->unique_prefix."export_type=xml','ExportToXml','left=100,top=100,width=540,height=360,toolbar=0,resizable=0,location=0,scrollbars=1');".$myRef_window.".focus();\" class='".$this->css_class."_class_a'>";
                        echo "<img src='".$this->directory."images/".$this->css_class."/xml_b.png'  onmouseover='this.src=\"".$this->directory."images/".$this->css_class."/xml_r.png\"' onmouseout='this.src=\"".$this->directory."images/".$this->css_class."/xml_b.png\"' alt='".$this->lang['export_to_xml']."' title='".$this->lang['export_to_xml']."' /></a>";
                        echo "</td>";
                    }
                }else{
                    echo "<td align='right' width='20px'></td>";
                }
            }
            if($this->printing_allowed){
                if(($req_export == "") && ($req_print != true)){
                    echo "<td align='right' width='20px'><a style='cursor:pointer;' onClick=\"".$myRef_window."=window.open(''+self.location+'".(($_SERVER['QUERY_STRING'] == "")?"?":"&").$this->unique_prefix."print=true','PrintableView','left=20,top=20,width=840,height=630,toolbar=0,menubar=0,resizable=0,location=0,scrollbars=1');".$myRef_window.".focus()\" class='".$this->css_class."_class_a'><img src='".$this->directory."images/".$this->css_class."/print_b.gif' onmouseover='this.src=\"".$this->directory."images/".$this->css_class."/print_r.gif\"' onmouseout='this.src=\"".$this->directory."images/".$this->css_class."/print_b.gif\"' alt='".$this->lang['printable_view']."' title='".$this->lang['printable_view']."' /></a></td>";
                }else{
                    echo "<td align='right'><a style='cursor:pointer; ' onClick='window:print();' class='".$this->css_class."_class_a no_print'  title='".$this->lang['print_now_title']."'>".$this->lang['print_now']."</a></td>";
                }
            }
            if($this->filtering_allowed && ($this->mode == "view") && ($req_mode != "update") && ($req_mode != "delete")){
                if($req_print != true){
                    echo "<td align='right' width='20px'><a style='cursor:pointer;' onClick='document.location.href=self.location;' class='".$this->css_class."_class_a'><img src='".$this->directory."images/".$this->css_class."/recycle.gif' alt='".$this->lang['refresh_page']."' title='".$this->lang['refresh_page']."' /></a>";
                }
            }
            echo "</tr>";
            echo "</table>";
        }
    }

    //--------------------------------------------------------------------------
    // Export Dispatcher
    //--------------------------------------------------------------------------
    function exportTo(){
        $req_export  = $this->getVariable('export');
        $export_type = $this->getVariable('export_type');

        if($req_export == true){
            if($export_type == "pdf"){
                $this->exportToPdf();
            }else if($export_type == "xml"){
                $this->exportToXml();
            }else{ // csv
                $this->exportToCsv();
            }
        }
    }

    //--------------------------------------------------------------------------
    // Export to CSV (if you change export file name - change file name length in download.php)
    //--------------------------------------------------------------------------
    function exportToCsv(){
        // Let's make sure the we create the file first
        $this->req_page_size = (isset($_REQUEST[$this->unique_prefix.'page_size']))?$_REQUEST[$this->unique_prefix.'page_size']:$this->req_page_size;
        $fe = fopen(DOC_ROOT.'/'.$this->exporting_directory."export.csv", "w+");
        if($fe){
            $somecontent = "";
            // fields headers
            for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
                // get current column's index (offset)
                $c = $this->sorted_columns[$c_sorted];
                $field_name = $this->getFieldName($c);
                if($this->canViewField($field_name)){
                    $somecontent .= ucfirst($this->getHeaderName($field_name, true));
                    if($c_sorted < count($this->sorted_columns) - 1) $somecontent .= ",";
                }
            }
            $somecontent .= "\n";
            // fields data
            for($r = $this->row_lower; (($r >=0 && $this->row_upper >=0) && ($r < $this->row_upper) && ($r < ($this->row_lower + $this->req_page_size))); $r++){
                $row = $this->data_set->fetchRow();
                for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
                    // get current column's index (offset)
                    $c = $this->sorted_columns[$c_sorted];
                    $field_name = $this->getFieldName($c);
                    if($this->canViewField($field_name)){
                        $somecontent .= str_replace(",", "",$row[$c]);
                        if($c_sorted < count($this->sorted_columns) - 1) $somecontent .= ",";
                    }
                }
                $somecontent .= "\n";
            }

            // write some content to the opened file.
            if (fwrite($fe, $somecontent) == FALSE) {
                echo $this->lang['file_writing_error']." (export.csv)";
                exit;
            }
            fclose($fe);
            echo $this->exportDownloadFile("export.csv");
        }else{
            echo "<label class='".$this->css_class."_class_error_message no_print'>".$this->lang['file_opening_error']."</lable>";
            exit;
        }

    }

    //---------------------------------------------------
    // Export to PDF (if you change export file name - change file name length in download.php)
    //---------------------------------------------------
    function exportToPdf($type = "tabular") {
        // Let's make sure the we create the file first
        $this->req_page_size = (isset($_REQUEST[$this->unique_prefix.'page_size']))?$_REQUEST[$this->unique_prefix.'page_size']:$this->req_page_size;

        $newcontent = array();
        $somecontent = "";

        // fields headers
        for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
            // get current column's index (offset)
            $c = $this->sorted_columns[$c_sorted];
            $field_name = $this->getFieldName($c);
            if($this->canViewField($field_name)){
                $somecontent .= ucfirst($this->getHeaderName($field_name, true));
                if($c_sorted < count($this->sorted_columns) - 1) $somecontent .= "\t";
            }
        }
        $newcontent[] = $somecontent;
        $somecontent = "";

        // fields data
        for($r = $this->row_lower; (($r >=0 && $this->row_upper >=0) && ($r < $this->row_upper) && ($r < ($this->row_lower + $this->req_page_size))); $r++){
            $row = $this->data_set->fetchRow();
            for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
                // get current column's index (offset)
                $c = $this->sorted_columns[$c_sorted];
                $field_name = $this->getFieldName($c);
                if($this->canViewField($field_name)){
                    $somecontent .= str_replace("\t", "",$row[$c]);
                    if($c_sorted < count($this->sorted_columns) - 1) $somecontent .= "\t";
                }
            }
            $somecontent .= "\n";
            $newcontent[] = $somecontent;
            $somecontent = "";
        }

        // write some content to the opened file.
        define('FPDF_FONTPATH', $this->inc_dir.'modules/fpdf/font/');
        include_once($this->inc_dir.'modules/fpdf/fpdf.php');
        $pdf=new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);

        for($i=0;$i<count($newcontent);$i++) {
            $pdf->Text(10,($i*10)+10,$newcontent[$i]);
        }

        $pdf->Output(DOC_ROOT.'/'.$this->exporting_directory."export.pdf", "");

        echo $this->exportDownloadFile("export.pdf");
    }

    //--------------------------------------------------------------------------
    // Export to XML (if you change export file name - change file name length in download.php)
    //--------------------------------------------------------------------------
    function exportToXml(){
        // Let's make sure the we create the file first
        $this->req_page_size = (isset($_REQUEST[$this->unique_prefix.'page_size']))?$_REQUEST[$this->unique_prefix.'page_size']:$this->req_page_size;
        $fe = fopen(DOC_ROOT.'/'.$this->exporting_directory."export.xml", "w+");
        if($fe){
            $somecontent = "<?xml version='1.0' encoding='UTF-8' ?>";
            // fields data
            $somecontent .= "<page>";
            for($r = $this->row_lower; (($r >=0 && $this->row_upper >=0) && ($r < $this->row_upper) && ($r < ($this->row_lower + $this->req_page_size))); $r++){
                $row = $this->data_set->fetchRow();
                $somecontent .= "<row".$r.">";
                for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
                    // get current column's index (offset)
                    $c = $this->sorted_columns[$c_sorted];
                    $field_name = $this->getFieldName($c);
                    if($this->canViewField($field_name)){
                        $header_name = $field_name;
                        $somecontent .= "<".$header_name.">";
                        $somecontent .= $row[$c];
                        $somecontent .= "</".$header_name.">";
                    }
                }
                $somecontent .= "</row".$r.">";
            }
            $somecontent .= "</page>";

            // write somecontent to the opened file.
            if (fwrite($fe, $somecontent) == FALSE) {
                echo $this->lang['file_writing_error']." (export.xml)";
                exit;
            }
            fclose($fe);
            echo $this->exportDownloadFile("export.xml");
        }else{
            echo "<label class='".$this->css_class."_class_error_message no_print'>".$this->lang['file_opening_error']."</lable>";
            exit;
        }
    }

    //--------------------------------------------------------------------------
    // draw filtering
    //--------------------------------------------------------------------------
    function drawFiltering(){
        $req_print = $this->getVariable('print');
        $selSearchType = $this->getVariable("_ff_selSearchType");
        $req_onSUBMIT_FILTER = $this->getVariable('_ff_onSUBMIT_FILTER');
        $cols = 0;

        if($this->filtering_allowed){
            echo "<table id='".$this->unique_prefix."searchset' style='".$this->hide_display."' width='".(($this->browser_name == "Firefox") ? "98%" : "100%" )."' align='center'><tr><td align='center'>\n";
            if($req_print != true){
                echo "<fieldset class='".$this->css_class."_class_fieldset' dir='".$this->direction."' align='".$this->tblAlign[$this->mode]."' style='WIDTH: ".$this->tblWidth['view']."'>\n";
                echo "<legend class='".$this->css_class."_class_legend'>".$this->lang['search_d']."</legend>\n";
            }
            echo "<form name='frmFiltering".$this->unique_prefix."' id='frmFiltering".$this->unique_prefix."' action='' method='GET' style='MARGIN: 10px;'>\n";
            $this->saveHttpGetVars();
            echo "<table class='".$this->css_class."_class_filter_table' border='0' id='filterTbl".$this->unique_prefix."' style='margin-left: auto; margin-right: auto;' width='".$this->tblWidth[$this->mode]."' cellspacing='1' cellpadding='1'>\n";
            if($this->layouts['filter'] == "0") echo "<tr>\n";
            foreach($this->filter_fields as $fldName => $fldValue){
                $field_property_on_js_event = $this->getFieldProperty($fldName, "on_js_event", "filter", "normal");
                $field_property_width = $this->getFieldProperty($fldName, "width", "filter", "normal");
                $field_property_autocomplete = $this->getFieldProperty($fldName, "autocomplete", "filter", "normal");
                $field_property_handler = $this->getFieldProperty($fldName, "handler", "filter", "normal");
                $field_property_maxresults = $this->getFieldProperty($fldName, "maxresults", "filter", "normal");
                $field_property_shownoresults = $this->getFieldProperty($fldName, "shownoresults", "filter", "normal");
                if($field_property_shownoresults == "") $field_property_shownoresults = "false";
                $field_width = ($field_property_width != "") ? "width: ".$field_property_width.";" : "";

                if($this->layouts['filter'] == "1") echo "<tr valign='middle'>\n";
                $fldValue_fields = explode(",", $fldValue['field']);
                $table_field_name = "".$fldValue['table']."_".$fldValue_fields[0];///del $fldValue['field'];
                if(isset($_REQUEST[$this->unique_prefix."_ff_".$table_field_name]) AND ($_REQUEST[$this->unique_prefix."_ff_".$table_field_name] != "")){
                    $filter_field_value = $_REQUEST[$this->unique_prefix."_ff_".$table_field_name];
                }else{
                    $filter_field_value = "";
                }
                $filter_field_operator =  $table_field_name."_operator";
                echo "<td align='";
                if($this->layouts['filter'] == "1"){
                    echo ($this->direction == "rtl")?"left":"right"; echo "' width='50%'>".$fldName."";
                    echo "</td><td>".$this->nbsp."</td><td>";
                    $cols +=3;
                }else if($this->layouts['filter'] == "0"){
                    echo ($this->direction == "rtl")?"center":"center"; echo "' >".$fldName."";
                    echo " ";
                    $cols +=1;
                }else {
                    echo ($this->direction == "rtl")?"left":"right"; echo "' width='50%'>".$fldName."";
                    echo "</td>";
                    echo "<td>".$this->nbsp."</td>";
                    echo "<td>";
                    $cols +=2;
                }
                if(isset($fldValue['show_operator']) && $fldValue['show_operator'] != false){
                    if($req_print != true){
                        if(isset($_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator]) && $_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator] != ""){
                            $filter_operator = $_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator];
                        }else if(isset($fldValue['default_operator']) && $fldValue['default_operator'] != ""){
                            $filter_operator = $fldValue['default_operator'];
                        }else{
                            $filter_operator = "=";
                        }
                        echo "<select class='".$this->css_class."_class_select' name='".$this->unique_prefix."_ff_".$filter_field_operator."' id='".$this->unique_prefix."_ff_".$filter_field_operator."'>";
                        echo "<option value='='"; echo ($filter_operator == "=")? "selected" : ""; echo ">".$this->lang['=']."</option>";
                        echo "<option value='&gt;'"; echo ($filter_operator == ">")? "selected" : ""; echo ">".$this->lang['>']."</option>";
                        echo "<option value='&lt;'"; echo ($filter_operator == "<")? "selected" : ""; echo ">".$this->lang['<']."</option>";
                        echo "<option value='like'"; echo ($filter_operator == "like")? "selected" : ""; echo ">".$this->lang['like']."</option>";
                        echo "<option value='like%'"; echo ($filter_operator == "like%")? "selected" : ""; echo ">".$this->lang['like%']."</option>";
                        echo "<option value='%like'"; echo ($filter_operator == "%like")? "selected" : ""; echo ">".$this->lang['%like']."</option>";
                        echo "<option value='%like%'"; echo ($filter_operator == "%like%")? "selected" : ""; echo ">".$this->lang['%like%']."</option>";
                        echo "<option value='not like'"; echo ($filter_operator == "not like")? "selected" : ""; echo ">".$this->lang['not_like']."</option>";
                        echo "</select>";
                    }else{
                        echo (isset($_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator])) ? "[".$_REQUEST[$this->unique_prefix."_ff_".$filter_field_operator]."]" : "";
                    }
                }else{
                    // set default operator
                    if(isset($fldValue['default_operator']) && $fldValue['default_operator'] != ""){
                        echo "<input type='hidden' name='".$this->unique_prefix."_ff_".$filter_field_operator."' id='".$this->unique_prefix."_ff_".$filter_field_operator."' value='".$fldValue['default_operator']."'>";
                        $filter_operator = $fldValue['default_operator'];
                    }else{
                        echo "<input type='hidden' name='".$this->unique_prefix."_ff_".$filter_field_operator."' id='".$this->unique_prefix."_ff_".$filter_field_operator."' value='='>";
                        $filter_operator = "=";
                    }
                }
                if($this->layouts['filter'] == "1"){
                    echo "</td>\n<td>".$this->nbsp."</td>\n";
                    echo "<td  width='50%' align='"; echo ($this->direction == "rtl")?"right":"left"; echo "'>";
                    $cols +=2;
                }else if($this->layouts['filter'] == "0"){
                    echo "<br />";
                }else {
                    echo "</td>\n<td>".$this->nbsp."</td>\n";
                    echo "<td  width='50%' align='"; echo ($this->direction == "rtl")?"right":"left"; echo "'>";
                    $cols +=2;
                }
                $filter_field_type = (isset($fldValue['type'])) ? $fldValue['type'] : "" ;
                if($req_print != true){
                    switch($filter_field_type){
                        case "textbox":
                            $fldValue_fields = str_replace(" ", "", $fldValue['field']);
                            $fldValue_fields = explode(",", $fldValue_fields);
                            $count = 0;
                            $onchange_filter_field = "";
                            foreach($fldValue_fields as $fldValue_field){
                                if($count++ > 0){ $onchange_filter_field .= "document.getElementById(\"".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."\").value="; }
                            }
                            $count = 0;
                            foreach($fldValue_fields as $fldValue_field){
                                if($count++ == 0){
                                    echo "\n<input class='".$this->css_class."_class_textbox' style='".$field_width."' type='text' value='".$filter_field_value."' name='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."' id='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."' onchange='".$onchange_filter_field."this.value;' ".$field_property_on_js_event." />";
                                    if(($field_property_autocomplete == "true") || ($field_property_autocomplete === true)){
                                        echo "\n<script type='text/javascript'>\n<!--\n";
                                        echo "var options = {";
                                        echo "   script: '".$this->directory.$field_property_handler."?json=true&limit=".intval($field_property_maxresults)."&',";
                                        echo "   varname: 'input',";
                                        echo "   json: true,";
                                        echo "   shownoresults: ".$field_property_shownoresults.",";
                                        echo "   maxresults: ".intval($field_property_maxresults);
                                        //callback: function (obj) { document.getElementById('testid').value = obj.id; }
                                        echo "};";
                                        echo "var as_json = new bsn.AutoSuggest('".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."', options);";
                                        echo "\n//-->\n</script>\n";
                                    }
                                }else{
                                    $filter_field_operator =  $fldValue['table']."_".$fldValue_field."_operator";
                                    echo "\n<input type='hidden' name='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."' id='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."' value='".$filter_field_value."' />";
                                    echo "\n<input type='hidden' name='".$this->unique_prefix."_ff_".$filter_field_operator."' id='".$this->unique_prefix."_ff_".$filter_field_operator."' value='".$filter_operator."'>";
                                }
                            }
                            break;
                        case "dropdownlist":
                            echo "<select class='".$this->css_class."_class_select' style='".$field_width."' name='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue['field']."' id='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue['field']."' ".$field_property_on_js_event.">";
                            echo "<option value=''>-- ".$this->lang['any']." --</option>";
                            if(is_array($fldValue['source'])){
                                foreach($fldValue['source'] as $val => $opt){
                                    echo "<option value='".$val."' ";
                                    if($filter_field_value !== ""){
                                        if($filter_field_value == $val) echo "selected";
                                    }
                                    echo ">".$opt."</option>";
                                }
                            }else{
                                $sql = "SELECT DISTINCT ".$fldValue['field']." FROM ".$fldValue['table']." ORDER BY ".$fldValue['field']." ".(($this->strToLower((isset($fldValue['order']) ? $fldValue['order'] : "")) == "desc")?"DESC":"ASC")." ;";
                                $this->db_handler->setFetchMode(DB_FETCHMODE_ASSOC);
                                $dSet = $this->db_handler->query($sql);
                                while($row = $dSet->fetchRow()){
                                    if($row[$fldValue['field']] === $filter_field_value)
                                        echo "<option selected value='".$row[$fldValue['field']]."'>".$row[$fldValue['field']]."</option>";
                                    else
                                        echo "<option value='".$row[$fldValue['field']]."'>".$row[$fldValue['field']]."</option>";
                                }
                            }
                            echo "</select>";
                            break;
                        case "calendar":
                            $fldValue_fields = str_replace(" ", "", $fldValue['field']);
                            $fldValue_fields = explode(",", $fldValue_fields);
                            $count = 0;
                            $onchange_filter_field = "";
                            foreach($fldValue_fields as $fldValue_field){
                                if($count++ > 0){ $onchange_filter_field .= "document.getElementById(\"".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."\").value="; }
                            }
                            $count = 0;
                            foreach($fldValue_fields as $fldValue_field){
                                if($count++ == 0){
                                    echo "\n<input class='".$this->css_class."_class_textbox' style='".$field_width."' type='text' value='".$filter_field_value."' name='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."' id='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."' onchange='".$onchange_filter_field."this.value;' ".$field_property_on_js_event." />";
                                }else{
                                    $filter_field_operator =  $fldValue['table']."_".$fldValue_field."_operator";
                                    echo "\n<input type='hidden' name='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."' id='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."' value='".$filter_field_value."' />";
                                    echo "\n<input type='hidden' name='".$this->unique_prefix."_ff_".$filter_field_operator."' id='".$this->unique_prefix."_ff_".$filter_field_operator."' value='".$filter_operator."'>";
                                }
                            }
                            echo "<a class='".$this->css_class."_class_a2' title='' href=\"javascript: openCalendar('".$this->directory."','', 'frmFiltering".$this->unique_prefix."', '', '".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue_field."', 'date')\"><img src='".$this->directory."images/".$this->css_class."/cal.gif' border='0' alt='".$this->lang['set_date']."' title='".$this->lang['set_date']."' align='top' style='MARGIN:3px;margin-left:6px;margin-right:6px;' /></a>".$this->nbsp;
                            break;
                        default:
                            echo "\n<input class='".$this->css_class."_class_textbox' type='text' value='".$filter_field_value."' name='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue['field']."' id='".$this->unique_prefix."_ff_".$fldValue['table']."_".$fldValue['field']."' />";
                            break;
                    }
                }else{
                    echo $filter_field_value;
                }
                echo "</td>\n";
                //value='$_POST[$fldValue]'
                if($this->layouts['filter'] == "1") echo "</tr>\n";
            }
            if($this->layouts['filter'] == "0") echo "</tr>\n";
            echo "<tr><td ".(($cols > 0) ? "colspan='".$cols."'" : "")." height='6px' align='center'></td></tr>\n";
            echo "<tr><td ".(($cols > 0) ? "colspan='".$cols."'" : "")." align='center'>";
            if(count($this->filter_fields) > 1){
                if($this->show_search_type){ echo $this->lang['search_type'].":&nbsp;&nbsp;"; }
                if($req_print != true){
                    if($this->show_search_type){
                        echo "<select class='".$this->css_class."_class_select' name='".$this->unique_prefix."_ff_"."selSearchType' id='".$this->unique_prefix."_ff_"."selSearchType'>";
                        echo "<option value='0' "; echo (($selSearchType != "") && ($selSearchType == 0)) ? "selected" : ""; echo ">".$this->lang['and']."</option>";
                        echo "<option value='1' "; echo ($selSearchType == 1) ? "selected" : ""; echo ">".$this->lang['or']."</option>";
                        echo "</select>&nbsp;&nbsp;&nbsp;";
                    }else{
                        echo "<input type='hidden' name='".$this->unique_prefix."_ff_"."selSearchType' id='".$this->unique_prefix."_ff_"."selSearchType' value='0' />";
                    }
                }else{
                    if(($selSearchType != "") && ($selSearchType == 0)){
                        echo "[and]";
                    }else if($selSearchType == 1){
                        echo "[or]";
                    }else {
                        echo "[none]";
                    }
                }
            }
            if($req_print != true){
                if($req_onSUBMIT_FILTER != ""){
                    $curr_url = $this->combineUrl("view", "", "&");
                    $this->setUrlString($curr_url, "", "sorting", "paging");
                    echo "<input class='".$this->css_class."_class_button' type='button' value='".$this->lang['reset']."' onClick='document.location.href=\"".$this->HTTP_URL.$curr_url."\"'>&nbsp;";
                }
                echo "<input class='".$this->css_class."_class_button' type='submit' name='".$this->unique_prefix."_ff_"."onSUBMIT_FILTER' id='".$this->unique_prefix."_ff_"."onSUBMIT_FILTER' value='".$this->lang['search']."'>";
            }
            echo "</td></tr>\n";
            $this->tblClose();
            echo "</form>\n";
            if($req_print != true){
                echo "</fieldset>\n";
            }
            echo "</td></tr></table>\n";
        }
    }

    //--------------------------------------------------------------------------
    // draw in customized layout
    //--------------------------------------------------------------------------
    function drawCustomized(){
        $req_print   = $this->getVariable('print');
        $req_mode    = $this->getVariable('mode');

        $this->writeCssClass();
        $this->exportTo();
        $this->showCaption($this->caption);
        $this->drawControlPanel();

        if(($this->mode != "edit") && ($this->mode != "details")) $this->drawFiltering();
        if(($req_mode !== "add") || ($req_mode == "")) $this->pagingFirstPart();
        $this->displayMessages();
        if($this->paging_allowed) $this->pagingSecondPart($this->upper_paging, false, true, "Upper");
        if($this->row_lower == $this->row_upper) echo "<br />";

        echo "<div id='".$this->unique_random_prefix."loading_image'><br /><table align='center'><tr><td valign='middle'>".$this->lang['loading_data']."</td><td valign='middle'><img src='".$this->directory."images/common/loading.gif' /></table></div>";
        // draw hide DG open div
        $this->hideDivOpen();


        for($r = $this->row_lower; (($r >=0 && $this->row_upper >=0) && ($r < $this->row_upper) && ($r < ($this->row_lower + $this->req_page_size))); $r++){
            // draw column data
            $row = $this->data_set->fetchRow();
            $template = $this->templates[$this->layout_type];
            for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
                // get current column's index (offset)
                $c = $this->sorted_columns[$c_sorted];
                if($this->isForeignKey($this->getFieldName($c))){
                    $template = str_replace("{".$this->getFieldName($c)."}", $this->getForeignKeyInput($row[$this->getFieldOffset($this->primary_key)], $this->getFieldName($c), $row[$c], "view"), $template);
                }else{
                    $template = str_replace("{".$this->getFieldName($c)."}", $this->getFieldValueByType($row[$c], $c, $row), $template);
                }
            }
            echo $template;
        }

        // draw empty table
        if($r == $this->row_lower){ $this->noDataFound(); }
        $this->scrollDivClose();

        if($this->paging_allowed) $this->pagingSecondPart($this->lower_paging, true, true, "Lower");

        // draw hide DG close div
        $this->hideDivClose();
        echo "<script type='text/javascript'>\n<!--\n document.getElementById('".$this->unique_random_prefix."loading_image').style.display='none'; \n//-->\n</script>";

    }

    //--------------------------------------------------------------------------
    // draw in tabular layout
    //--------------------------------------------------------------------------
    function drawTabular(){
        $req_print   = $this->getVariable('print');
        $req_mode    = $this->getVariable('mode');

        $this->writeCssClass();
        $this->exportTo();
        $this->showCaption($this->caption);
        $this->drawControlPanel();

        if($this->mode != "edit") $this->drawFiltering();
        if(($req_mode !== "add") || ($req_mode == "")) $this->pagingFirstPart();
        $this->displayMessages();
        if($this->paging_allowed) $this->pagingSecondPart($this->upper_paging, false, true, "Upper");
        if($this->row_lower == $this->row_upper) echo "<br />";

        //prepare summarize columns array
        foreach ($this->columns_view_mode as $key => $val){
            $field_property_summarize = $this->getFieldProperty($key, "summarize", "view");
            if(($field_property_summarize == "true") || ($field_property_summarize == true)){
                $this->summarize_columns[$key] = 0;
            }
        }

        echo "<div id='".$this->unique_random_prefix."loading_image'><br /><table align='center'><tr><td valign='middle'>".$this->lang['loading_data']."</td><td valign='middle'><img src='".$this->directory."images/common/loading.gif' /></table></div>";
        // draw hide DG open div
        $this->hideDivOpen();

        // draw add link-button cell
        if(isset($this->modes['add'][$this->mode]) && $this->modes['add'][$this->mode] && $this->draw_add_button_separately){
            echo "<table dir='".$this->direction."' border='0' align='".$this->tblAlign[$this->mode]."' width='".$this->tblWidth[$this->mode]."'>";
            echo "<tr>";
            echo "<td align='".(($this->direction == "ltr") ? "left" : "right")."'><b>";
                $curr_url = $this->combineUrl("add", "-1");
                $this->setUrlString($curr_url, "filtering", "sorting", "paging");
                $this->drawModeButton("add", $curr_url, $this->lang['add_new'], $this->lang['add_new_record'], "add.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$curr_url."';\"", false, "", "");
            echo "</b></td>";
            echo "</tr>";
            echo "</table>";
            $this->modes['add'][$this->mode] = false;
        }

        $this->scrollDivOpen();
        $this->tblOpen();

        // *** START DRAWING HEADERS -------------------------------------------
        $this->rowOpen("");

            // draw multi-row checkboxes header
            if(($this->multirow_allowed) && ($this->rows_total > 0)){
                $this->colOpen("center",0,"nowrap",$this->rowColor[0], $this->css_class."_class_td", "26px");
                echo $this->nbsp;
                $this->colClose();
            }

            // draw add link-button cell
            if(isset($this->modes['add'][$this->mode]) && $this->modes['add'][$this->mode]){
                $curr_url = $this->combineUrl("add", "-1");
                $this->setUrlString($curr_url, "filtering", "sorting", "paging");
                $this->mainColOpen("center",0,"nowrap", "1%", $this->css_class."_class_th_normal");
                $this->drawModeButton("add", $curr_url, $this->lang['add_new'], $this->lang['add_new_record'], "add.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$curr_url."';\"", false, "", "");
                $this->mainColClose();
            }else{
                if(isset($this->modes['edit'][$this->mode]) && $this->modes['edit'][$this->mode]){
                    $this->mainColOpen("center",0,"nowrap", "1%", $this->css_class."_class_th_normal"); echo $this->nbsp; $this->mainColClose();
                }
            }

            if(($this->rows_numeration)){
                $this->mainColOpen("center",0,"nowrap", ""); echo $this->numeration_sign; $this->mainColClose();
            }

            // draw column headers in add mode
            if(($this->rid == -1) && ($req_mode == "add")){
                foreach($this->columns_edit_mode as $key => $val){
                    if($this->getFieldProperty($key, "type") != "hidden"){
                        $this->mainColOpen("center",0);
                        echo "<b>".ucfirst($this->getHeaderName($key))."</b>";
                        $this->mainColClose();
                    }
                }
            }else{
                $req_sort_field    = $this->getVariable('sort_field');
                $req_sort_field_by = $this->getVariable('sort_field_by');
                $req_sort_type     = $this->getVariable('sort_type');
                if($req_sort_field){
                    $sort_img = (strtolower($req_sort_type) == "desc") ? $this->directory."images/".$this->css_class."/s_desc.png" : $this->directory."images/".$this->css_class."/s_asc.png" ;
                    $sort_img_back = (strtolower($req_sort_type) == "desc") ? $this->directory."images/".$this->css_class."/s_asc.png" : $this->directory."images/".$this->css_class."/s_desc.png" ;
                    $sort_alt = (strtolower($req_sort_type) == "desc") ? $this->lang['descending'] : $this->lang['ascending'] ;
                }
                if($this->mode === "view"){
                    // draw column headers in view mode
                    for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
                        // get current column's index (offset)
                        $c = $this->sorted_columns[$c_sorted];
                        $field_name = $this->getFieldName($c);

                        $field_property_sort_by = $this->getFieldProperty($field_name, "sort_by", "view");
                        if($field_property_sort_by != ""){
                            $sort_field_by = ($this->getFieldOffset($field_property_sort_by)+1);
                        } else {
                            $sort_field_by = "";
                        };

                        if($this->canViewField($field_name)){
                            $field_property_wrap  = $this->getFieldProperty($field_name, "wrap", "view", "lower", $this->wrap);
                            $field_property_width = $this->getFieldProperty($field_name, "width", "view");

                            if($this->sorting_allowed && ($req_print != true) && $req_sort_field && ($c == ($req_sort_field -1))){ $th_css_class = $this->css_class."_class_th_selected"; } else { $th_css_class = $this->css_class."_class_th" ;};
                            $this->mainColOpen("center", 0, $field_property_wrap, $field_property_width, $th_css_class);
                            if($this->sorting_allowed){
                                $href_string = $this->combineUrl("view");
                                $this->setUrlString($href_string, "filtering", "", "paging");
                                if(isset($_REQUEST[$this->unique_prefix.'sort_type']) && $_REQUEST[$this->unique_prefix.'sort_type'] == "asc") $sort_type="desc";
                                else $sort_type="asc";
                                if($req_print != true){
                                    $href_string .= $this->amp.$this->unique_prefix."sort_field=".($c+1).$this->amp.$this->unique_prefix."sort_field_by=".$sort_field_by.$this->amp.$this->unique_prefix."sort_type=";
                                    // prepare sorting order by field's type
                                    if($req_sort_field && ($c == ($req_sort_field -1))){
                                        $href_string .= $sort_type;
                                    }else{
                                        if($this->isDate($field_name)){ $href_string .= "desc"; }
                                        else{ $href_string .= "asc"; }
                                    }
                                    echo "<nobr><b><a class='".$this->css_class."_class_a_header' href='$href_string' title='".$this->lang['sort']."' ";
                                    if($req_sort_field && ($c == ($req_sort_field -1))){
                                        echo "onmouseover=\"if(document.getElementById('soimg".$c."')){ document.getElementById('soimg".$c."').src='".$sort_img_back."';  }\" ";
                                        echo "onmouseout=\"if(document.getElementById('soimg".$c."')){ document.getElementById('soimg".$c."').src='".$sort_img."';  }\" ";
                                    }
                                    echo ">".ucfirst($this->getHeaderName($field_name))." ";
                                    if($req_sort_field && ($c == ($req_sort_field -1))){
                                        echo $this->nbsp."<img id='soimg".$c."' src='".$sort_img."' alt='".$sort_alt."' title='".$sort_alt."' border='0'>".$this->nbsp;
                                    }
                                    echo "</a></b></nobr>";
                                }else{
                                    echo "<b>".ucfirst($this->getHeaderName($field_name))."</b>";
                                }
                            }else{
                                echo "<b>".ucfirst($this->getHeaderName($field_name))."</b>";
                            }
                            $this->mainColClose();
                        }
                    }//for
                }else if($this->mode === "edit"){
                    foreach($this->columns_edit_mode as $key => $val){
                        if($this->getFieldProperty($key, "type") != "hidden"){
                            if($this->canViewField($key)){
                                $this->mainColOpen("center",0);
                                // alow/disable sorting by headers
                                echo "<b>".ucfirst($this->getHeaderName($key))."</b>";
                                $this->mainColClose();
                            }
                        }
                    }
                }
            }
            if(isset($this->modes['details'][$this->mode]) && $this->modes['details'][$this->mode]){
                $this->mainColOpen("center",0,"nowrap", "10%", $this->css_class."_class_th_normal");echo $this->lang['view'];$this->mainColClose();
            }
            if(isset($this->modes['delete'][$this->mode]) && $this->modes['delete'][$this->mode]){
                $this->mainColOpen("center",0,"nowrap", "10%", $this->css_class."_class_th_normal");echo $this->lang['delete'];$this->mainColClose();
            }
        $this->rowClose();
        // *** END HEADERS -----------------------------------------------------

        //if we add a new row on linked tabular view mode table (mode 0 <-> 0)
        $quick_exit = false;
        if((isset($_REQUEST[$this->unique_prefix.'mode']) && ($_REQUEST[$this->unique_prefix.'mode'] == "add")) && ($this->row_lower == 0) && ($this->row_upper == 0)){
            $this->row_upper = 1;
            $quick_exit = true;
        }

        // *** START DRAWING ROWS ----------------------------------------------
        $first_field_name = "";
        $curr_url = "";
        $c_curr_url = "";
        for($r = $this->row_lower; (($r >=0 && $this->row_upper >=0) && ($r < $this->row_upper) && ($r < ($this->row_lower + $this->req_page_size))); $r++){
            // add new row (ADD MODE)
            if(($r == $this->row_lower) && ($this->rid == -1) && ($req_mode == "add")){
                if($r % 2 == 0){$this->rowOpen($r, $this->rowColor[0]); $main_td_color=$this->rowColor[2];}
                else  {$this->rowOpen($r, $this->rowColor[1]); $main_td_color=$this->rowColor[3];}
                $curr_url = $this->combineUrl("update", -1, $this->amp);
                $this->setUrlString($c_curr_url, "filtering", "sorting", "paging", $this->amp);
                $curr_url .= $c_curr_url;
                $curr_url .= $this->amp.$this->unique_prefix."new=1";
                echo "<form name='".$this->unique_prefix."frmEditRow' id='".$this->unique_prefix."frmEditRow' method='post' action='".$curr_url."'>\n";
                $this->setEditFieldsFormScript($curr_url);
                // draw multi-row empty cell
                if(($this->multirow_allowed) && (!$this->is_error)){$this->colOpen("center",0,"nowrap",$this->rowColor[0], $this->css_class."_class_td");echo $this->nbsp;$this->colClose();}
                $this->colOpen("center",0,"nowrap",$main_td_color, $this->css_class."_class_td_main");
                $this->drawModeButton("edit", "#", $this->lang['create'], $this->lang['create_new_record'], "update.gif", "\"".$this->unique_prefix."sendEditFields(); return false;\"", false, "&nbsp", "");
                $cancel_url = $this->combineUrl("cancel", -1);
                $this->setUrlString($cancel_url, "filtering", "sorting", "paging");
                $cancel_url .= $this->amp.$this->unique_prefix."new=1";
                $this->drawModeButton("cancel", $cancel_url, $this->lang['cancel'], $this->lang['cancel'], "cancel.gif", "\"return ".$this->unique_prefix."verifyCancel('".$cancel_url."'); javascript:document.location.href='".$this->HTTP_URL.$cancel_url."'\"", false, $this->nbsp, "");
                $this->colClose();

                foreach($this->columns_edit_mode as $key => $val){
                    if($this->getFieldProperty($key, "type") != "hidden"){
                        $this->colOpen("left",0,"nowrap");
                        if($this->isForeignKey($key)){
                            echo $this->nbsp.$this->getForeignKeyInput(-1, $key, '-1', "edit").$this->nbsp;
                        }else{
                            echo $this->getFieldValueByType('', 0, '', $key);
                        }
                        $this->colClose();
                    }else{
                        echo $this->getFieldValueByType('', 0, '', $key);
                    }
                }

                if(isset($this->modes['delete']) && $this->modes['delete'][$this->mode]) $this->colOpen("center",0,"nowrap");echo"";$this->colClose();
                echo "</form>";
                $this->rowClose();
            }

            //if we add a new row on linked tabular view mode table (mode 0 <-> 0)
            if($quick_exit == true){
                $this->tblClose();
                echo "<script type='text/javascript'>\n<!--\n document.getElementById('".$this->unique_random_prefix."loading_image').style.display='none'; \n//-->\n</script>";
                if(($this->first_field_focus_allowed) && ($first_field_name != "")) echo "<script type='text/javascript'>\n<!--\n document.forms['".$this->unique_prefix."frmEditRow']".$this->getFieldRequiredType($first_field_name).$first_field_name.".focus(); \n//-->\n</script>";
                return;
            }

            $row = $this->data_set->fetchRow();
            if($r % 2 == 0){$this->rowOpen($r, $this->rowColor[0]); $main_td_color=$this->rowColor[2];}
            else  {$this->rowOpen($r, $this->rowColor[1]); $main_td_color=$this->rowColor[3];}

            // draw multi-row row checkboxes
            if($this->multirow_allowed){
                $this->colOpen("center",0,"nowrap","","");
                if($req_print == true){
                    $disable = "disabled";
                }else{
                    $disable = "";
                }
                echo "<input onclick=\"onMouseClickRow('".$this->unique_prefix."','".$r."', '".$this->rowColor[5]."', '".$this->rowColor[1]."', '".$this->rowColor[0]."')\" type='checkbox' name='".$this->unique_prefix."checkbox_".$r."' id='".$this->unique_prefix."checkbox_".$r."' value='";
                echo ($row[$this->getFieldOffset($this->primary_key)] != -1) ? $row[$this->getFieldOffset($this->primary_key)] : "0" ;
                echo "' ".$disable."/>";
                $this->colClose();
            }

            // draw mode buttons
            if(isset($this->modes['edit'][$this->mode]) && $this->modes['edit'][$this->mode]){
                if(($this->mode == "edit") && (intval($this->rid) == intval($row[$this->getFieldOffset($this->primary_key)]))){
                    $curr_url = $this->combineUrl("update", $row[$this->getFieldOffset($this->primary_key)], $this->amp);
                    $cancel_url = $this->combineUrl("cancel", $row[$this->getFieldOffset($this->primary_key)]);
                    $this->setUrlString($c_curr_url, "filtering", "sorting", "paging", $this->amp);
                    $curr_url .= $c_curr_url;
                    $cancel_url .= $c_curr_url;
                    if(isset($_REQUEST[$this->unique_prefix.'mode']) && $_REQUEST[$this->unique_prefix.'mode'] === "add") { $curr_url .= $this->amp.$this->unique_prefix."new=1"; $cancel_url .= $this->amp.$this->unique_prefix."new=1";}
                    echo "<form name='".$this->unique_prefix."frmEditRow' id='".$this->unique_prefix."frmEditRow' method='post' action='".$curr_url."'>";
                    $this->setEditFieldsFormScript($curr_url);
                    $this->colOpen("center",0,"nowrap",$main_td_color, $this->css_class."_class_td_main");
                    $this->drawModeButton("edit", "#", $this->lang['update'], $this->lang['update_record'], "update.gif", "\"".$this->unique_prefix."sendEditFields(); return false;\"", false, "&nbsp;", "");
                    if(isset($_REQUEST[$this->unique_prefix.'mode']) && $_REQUEST[$this->unique_prefix.'mode'] === "add") {
                        $cancel_url = $this->combineUrl("delete", $row[$this->primary_key]);
                        $this->setUrlString($cancel_url, "filtering", "sorting", "paging");
                        if(isset($this->modes['cancel'][$this->mode]) && $this->modes['cancel'][$this->mode]){
                            $this->drawModeButton("cancel", $cancel_url, $this->lang['cancel'], $this->lang['cancel'], "cancel.gif", "\"return ".$this->unique_prefix."verifyCancel('".$cancel_url."'); javascript:document.location.href='".$this->HTTP_URL.$cancel_url."'\"", false, $this->nbsp, "");
                        }
                    }else{
                        $this->drawModeButton("cancel", $cancel_url, $this->lang['cancel'], $this->lang['cancel'], "cancel.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$cancel_url."'\"", false, $this->nbsp, "");
                    }
                    $this->colClose();
                }else {
                    $row_id = ($this->getFieldOffset($this->primary_key) != "-1") ? $row[$this->getFieldOffset($this->primary_key)] : $this->getFieldOffset($this->primary_key);
                    $curr_url = $this->combineUrl("edit", $row_id);
                    $this->setUrlString($curr_url, "filtering", "sorting", "paging");
                    if(isset($_REQUEST[$this->unique_prefix.'new']) && (isset($_REQUEST[$this->unique_prefix.'new']) == 1)){
                        $curr_url .= $this->amp.$this->unique_prefix."new=1";
                    }
                    if(isset($this->modes['edit'][$this->mode]) && $this->modes['edit'][$this->mode]){
                        // by field Value - link on Edit mode page
                        if (isset($this->modes['edit']['byFieldValue']) && ($this->modes['edit']['byFieldValue'] != "")){
                            if($this->getFieldOffset($this->modes['edit']['byFieldValue']) == "-1"){
                                if($this->debug == true){
                                    $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap",$main_td_color, $this->css_class."_class_td_main");
                                    echo $this->nbsp.$this->lang['wrong_field_name']." - ".$this->modes['edit']['byFieldValue'].$this->nbsp;
                                }else{
                                    $this->colOpen("center",0,"nowrap",$main_td_color, $this->css_class."_class_td_main");
                                    $this->drawModeButton("edit", $curr_url, $this->lang['edit'], $this->lang['edit_record'], "edit.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$curr_url."'\"", false, $this->nbsp, "");
                                }
                            }else{
                                $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap",$main_td_color, $this->css_class."_class_td_main");
                                echo $this->nbsp."<a class='".$this->css_class."_class_a_header' href='$curr_url'>".$row[$this->getFieldOffset($this->modes['edit']['byFieldValue'])]."</a>".$this->nbsp;
                            }
                        }else{
                            $this->colOpen("center",0,"nowrap",$main_td_color, $this->css_class."_class_td_main", "10%");
                            $this->drawModeButton("edit", $curr_url, $this->lang['edit'], $this->lang['edit_record'], "edit.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$curr_url."'\"", false, $this->nbsp, "");
                        }
                        $this->colClose();
                    }
                }

            }else{
                if(isset($this->modes['add'][$this->mode]) && $this->modes['add'][$this->mode]){
                    $this->colOpen("center",0,"nowrap",$this->rowColor[2], $this->css_class."_class_td_main");$this->colClose();
                }
            }

            if($this->rows_numeration){
                $this->colOpen("center",0,"nowrap"); echo "<label class='".$this->css_class."_class_label'>".($r+1)."</label>"; $this->colClose();
            }

            // draw column data
            for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
                // get current column's index (offset)
                $c = $this->sorted_columns[$c_sorted];
                $col_align = $this->getFieldAlign($c, $row);
                $field_property_wrap = $this->getFieldProperty($this->getFieldName($c), "wrap", "view", "lower", $this->wrap);
                if(($this->mode === "view") && ($this->canViewField($this->getFieldName($c)))){
                    if($req_sort_field == $c+1){
                        $this->colOpen($col_align, 0, $field_property_wrap, $this->rowColor[0], $this->css_class."_class_td_selected");
                    }else{
                        $this->colOpen($col_align, 0, $field_property_wrap);
                    }
                    $field_value = $this->getFieldValueByType($row[$c], $c, $row);
                    $field_property_summarize = $this->getFieldProperty($this->getFieldName($c), "summarize", "view");
                    if(($field_property_summarize == "true") || ($field_property_summarize == true)){
                        $this->summarize_columns[$this->getFieldName($c)] += str_replace(",", "", $row[$c]);
                    }
                    echo $field_value;
                    $this->colClose();
                }else if($this->mode === "edit"){
                    if($this->getFieldProperty($this->getFieldName($c), "type") == "hidden"){
                        echo $this->getFieldValueByType('', 0, '', $this->getFieldName($c));
                    }else if($this->canViewField($this->getFieldName($c))){
                        if($first_field_name == "") $first_field_name = $this->getFieldName($c);
                        if(intval($this->rid) === intval($row[$this->getFieldOffset($this->primary_key)])){
                            $this->colOpen("left", 0, $field_property_wrap);
                            if($this->isForeignKey($this->getFieldName($c))){
                                echo $this->nbsp.$this->getForeignKeyInput($row[$this->getFieldOffset($this->primary_key)], $this->getFieldName($c), $row[$c], "edit").$this->nbsp;
                            }else{
                                echo $this->getFieldValueByType($row[$c], $c, $row);
                            }
                            $this->colClose();
                        }else{
                            $this->colOpen($col_align, 0, $field_property_wrap);
                            if($this->isForeignKey($this->getFieldName($c))){
                                echo $this->nbsp.$this->getForeignKeyInput($row[$this->getFieldOffset($this->primary_key)], $this->getFieldName($c), $row[$c],"view").$this->nbsp;
                            }else{
                                echo $this->nbsp.trim($row[$c]).$this->nbsp;
                            }
                            $this->colClose();
                        }
                    }
                }
            }
            $row_id = ($this->getFieldOffset($this->primary_key) != "-1") ? $row[$this->getFieldOffset($this->primary_key)] : $this->getFieldOffset($this->primary_key);
            if(isset($this->modes['details'][$this->mode]) && $this->modes['details'][$this->mode]){
                $curr_url = $this->combineUrl("details", $row_id);
                $this->setUrlString($curr_url, "filtering", "sorting", "paging");
                $this->colOpen("center",0,"nowrap");
                $this->drawModeButton("details", $curr_url, $this->lang['details'], $this->lang['view_details'], "details.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$curr_url."';\"", false, $this->nbsp, "");
                $this->colClose();
            }
            if(isset($this->modes['delete'][$this->mode]) && $this->modes['delete'][$this->mode]){
                $curr_url = $this->combineUrl("delete", $row_id);
                $this->setUrlString($curr_url, "filtering", "sorting", "paging");
                $this->colOpen("center",0,"nowrap");
                $this->drawModeButton("delete", $curr_url, $this->lang['delete'], $this->lang['delete_record'], "delete.gif", "\"return ".$this->unique_prefix."verifyDelete('$curr_url');\"", false, "", "");
                $this->colClose();
            }

            if(($this->mode == "edit") && (intval($this->rid) == intval($row[$this->getFieldOffset($this->primary_key)]))){ echo "</form>"; }
            $this->rowClose();
        }
        // *** END ROWS --------------------------------------------------------


        // draw summarizing row
        if($r != $this->row_lower){ $this->drawSummarizeRow($r); }
        $this->tblClose();

        // draw empty table
        if($r == $this->row_lower){ $this->noDataFound(); }
        $this->scrollDivClose();

        $this->drawMultiRowBar($r, $curr_url);  // draw multi-row row footer cell

        if($this->paging_allowed) $this->pagingSecondPart($this->lower_paging, true, true, "Lower");

        // draw hide DG close div
        $this->hideDivClose();
        echo "<script type='text/javascript'>\n<!--\n document.getElementById('".$this->unique_random_prefix."loading_image').style.display='none'; \n//-->\n</script>";

        if(($this->first_field_focus_allowed) && ($first_field_name != "")) echo "<script type='text/javascript'>\n<!--\n document.".$this->unique_prefix."frmEditRow.".$this->getFieldRequiredType($first_field_name).$first_field_name.".focus(); \n//-->\n</script>";
    }

    //--------------------------------------------------------------------------
    // draw in columnar layout
    //--------------------------------------------------------------------------
    function drawColumnar(){
        $r = ""; //???
        $req_print = $this->getVariable('print');
        $req_mode = ($this->mode_after_update == "") ? $this->getVariable('mode') : $this->mode_after_update;

        $this->writeCssClass();
        $this->exportTo();
        $this->showCaption($this->caption);
        $this->drawControlPanel();

        if((($req_mode !== "add") && ($req_mode !== "details")) || ($req_mode == "")) $this->pagingFirstPart();
        $this->displayMessages();

        if(isset($this->modes['add'][$this->mode]) && $this->modes['add'][$this->mode]){
            $this->tblOpen();
            $this->rowOpen($r, $this->rowColor[0]);
                $curr_url = $this->combineUrl("add", "-1");
                $this->setUrlString($curr_url, "filtering", "sorting", "paging");
                $this->mainColOpen("center",0,"nowrap", "", $this->css_class."_class_th_normal");
                $this->drawModeButton("add", $curr_url, $this->lang['add_new'], $this->lang['add_new'], "add.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$curr_url."';\"", true, "", "");
                $this->mainColClose();
            $this->rowClose();
            $this->tblClose();
        }

        if($this->paging_allowed) $this->pagingSecondPart($this->upper_paging, false, true, "Upper");

        //prepare action url for the form
        $curr_url = $this->combineUrl("update", $this->rid, $this->amp);
        $this->setUrlString($c_curr_url, "filtering", "sorting", "paging", $this->amp);
        $curr_url .= $c_curr_url;
        if($req_mode === "add") {
            $curr_url .= $this->amp.$this->unique_prefix."new=1";
        }

        echo "<div id='".$this->unique_random_prefix."loading_image'><br /><table align='center'><tr><td valign='middle'>".$this->lang['loading_data']."</td><td valign='middle'><img src='".$this->directory."images/common/loading.gif' /></td></tr></table></div>";
        echo "<form name='".$this->unique_prefix."frmEditRow' id='".$this->unique_prefix."frmEditRow' method='post' action='".$curr_url."'>".chr(13);
        $this->tblOpen();
        // draw header
        $this->rowOpen($r);
        $this->mainColOpen("center",0,"nowrap","32%", (($req_print == true) ? $this->css_class."_class_td" : $this->css_class."_class_th")); echo "<b>".(($this->field_header != "") ? $this->field_header : $this->lang['field'])."</b>"; $this->mainColClose();
        $this->mainColOpen("center",0,"nowrap","68%", (($req_print == true) ? $this->css_class."_class_td" : $this->css_class."_class_th")); echo "<b>".(($this->field_value_header != "") ? $this->field_value_header : $this->lang['field_value'])."</b>"; $this->mainColClose();
        $this->rowClose();

        // set number of showing rows on the page
        if(($this->layouts['view'] == "0") && ($this->layouts['edit'] == "1") && ($this->mode == "edit")){
            if($this->multi_rows > 0){
                $this->req_page_size = $this->multi_rows;
            }else{
                $this->req_page_size = 1;
            }
        }else if(($this->layouts['view'] == "0") && ($this->layouts['edit'] == "1") && ($this->mode == "details")){
            if($this->multi_rows > 0){
                $this->req_page_size = $this->multi_rows;
            }else{
                $this->req_page_size = 1;
            }
        }else if(($this->layouts['view'] == "1") && ($this->layouts['edit'] == "1") && ($this->mode == "edit")){
            $this->req_page_size = 1;  // ???
        }else if(($this->layouts['edit'] == "1") && ($this->mode == "details")){
            $this->req_page_size = 1;
        }

        $first_field_name = ""; /* we need it to set a focus on this field */
        // draw rows in ADD MODE
        if($this->rid == -1){
            foreach($this->columns_edit_mode as $key => $val){
                if(($first_field_name == "") && (($this->mode === "edit") || ($this->mode === "add"))) $first_field_name = $key;
                if($r % 2 == 0) $this->rowOpen($r, $this->rowColor[0]);
                else $this->rowOpen($r, $this->rowColor[1]);
                if($key == "delimiter"){
                    $this->colOpen(($this->direction == "rtl")?"right":"left",2,"nowrap");
                        echo $this->getFieldProperty("delimiter", "inner_html");
                    $this->colClose();
                }else if($key == "validator"){
                    $field_property_for_field = $this->getFieldProperty("validator", "for_field");
                    $field_property_header    = $this->getFieldProperty("validator", "header");
                    $field_property_req_type  = $this->getFieldProperty("validator", "req_type");
                    // column's header
                    $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap");
                        echo $this->nbsp;echo "<b>".ucfirst($field_property_header)."</b>";
                    $this->colClose();
                    // column's data
                    $col_align = ($this->direction == "rtl")?"right":"left";
                    $this->colOpen($col_align,0,"nowrap");
                        echo $this->getFieldValueByType('', 0, '', $field_property_for_field, $field_property_req_type);
                    $this->colClose();
                }else if($this->getFieldProperty($key, "type") == "hidden"){
                    echo $this->getFieldValueByType('', 0, '', $key);
                }else{
                    // column's header
                    $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap");
                        echo $this->nbsp;echo "<b>".ucfirst($this->getHeaderName($key))."</b>";
                    $this->colClose();
                    // column's data
                    $col_align = ($this->direction == "rtl")?"right":"left";
                    $this->colOpen($col_align,0,"nowrap");
                    if($this->isForeignKey($key)){
                        echo $this->nbsp.$this->getForeignKeyInput(-1, $key, '-1', "edit").$this->nbsp;
                    }else{
                        echo $this->getFieldValueByType('', 0, '', $key);
                    }
                    $this->colClose();
                }
                $this->rowClose();
            }
        }
        // *** START DRAWING ROWS ----------------------------------------------
        for($r = $this->row_lower; (($this->rid != -1) && ($r < $this->row_upper) && ($r < ($this->row_lower + $this->req_page_size))); $r++){
            $row = $this->data_set->fetchRow();
            // draw column headers
            for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
                // get current column's index (offset)
                $c = $this->sorted_columns[$c_sorted];
                if($r % 2 == 0) $this->rowOpen($r, $this->rowColor[0]);
                else $this->rowOpen($r, $this->rowColor[1]);
                if($this->canViewField($this->getFieldName($c))){
                    if($this->getFieldProperty($this->getFieldName($c), "type") == "hidden"){
                        echo $this->getFieldValueByType('', 0, '', $this->getFieldName($c));
                    }else{
                        if(($first_field_name == "") && (($this->mode === "edit") || ($this->mode === "add"))) $first_field_name = $this->getFieldName($c);

                        // column headers
                        if(($this->mode === "view") && ($this->canViewField($this->getFieldName($c)))){
                            $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap");
                            echo $this->nbsp;echo "<b>".ucfirst($this->getHeaderName($this->getFieldName($c)))."</b>";
                            $this->colClose();
                        }else if(($this->mode === "edit") && ($this->canViewField($this->getFieldName($c)))){
                            $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap");
                            echo $this->nbsp;echo "<b>".ucfirst($this->getHeaderName($this->getFieldName($c)))."</b>";
                            $this->colClose();
                        }else if(($this->mode === "details") && ($this->canViewField($this->getFieldName($c)))){
                            $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap");
                            echo $this->nbsp;echo "<b>".ucfirst($this->getHeaderName($this->getFieldName($c)))."</b>";
                            $this->colClose();
                        }

                        // column data
                        $col_align = ($this->direction == "rtl") ? "right" : "left";
                        if(($this->mode === "view") && ($this->canViewField($this->getFieldName($c)))){
                            $field_property_wrap = $this->getFieldProperty($this->getFieldName($c), "wrap", "view");
                            $this->colOpen($col_align, 0, $field_property_wrap);
                            echo $this->getFieldValueByType($row[$c], $c, $row);
                            $this->colClose();
                        }else if(($this->mode === "details") && ($this->canViewField($this->getFieldName($c)))){
                                $this->colOpen($col_align,0);
                                if($this->isForeignKey($this->getFieldName($c))){
                                    echo $this->getForeignKeyInput($row[$this->getFieldOffset($this->primary_key)], $this->getFieldName($c), $row[$c],"view");
                                }else{
                                    echo $this->getFieldValueByType($row[$c], $c, $row);
                                }
                                $this->colClose();
                        }else if(($this->mode === "edit") && ($this->canViewField($this->getFieldName($c)))){
                                // if we have multi-rows selected
                                // mr_2
                                if($this->multi_rows > 0){
                                    $rid_value = $this->rids[$r];
                                }else{
                                    $rid_value = $this->rid;
                                }
                                $ind = ($this->getFieldOffset($this->primary_key) != -1) ? $this->getFieldOffset($this->primary_key) : 0;
                                if(intval($rid_value) === intval($row[$ind])){
                                        $this->colOpen($col_align,0,"nowrap");
                                        if($this->isForeignKey($this->getFieldName($c))){
                                            echo $this->nbsp.$this->getForeignKeyInput($row[$ind], $this->getFieldName($c), $row[$c], "edit").$this->nbsp;
                                        }else{
                                            echo $this->getFieldValueByType($row[$c], $c, $row);
                                        }
                                        $this->colClose();
                                }else{
                                    $this->colOpen($col_align,0,"nowrap");
                                    if($this->rid == -1){
                                        // add new row
                                        if($this->isForeignKey($this->getFieldName($c))){
                                            echo $this->nbsp.$this->getForeignKeyInput(-1, $this->getFieldName($c), '-1', "edit").$this->nbsp;
                                        }else{
                                            echo $this->getFieldValueByType('', $c, $row);
                                        }
                                    }else{
                                        if($this->isForeignKey($this->getFieldName($c))){
                                            echo $this->nbsp.$this->getForeignKeyInput($row[$this->getFieldOffset($this->primary_key)], $this->getFieldName($c), $row[$c],"view").$this->nbsp;
                                        }else{
                                            echo $this->nbsp.trim($row[$c]).$this->nbsp;
                                        }
                                    }
                                    $this->colClose();
                                }
                        }
                    }
                }else{
                    $ind = 0;
                    foreach($this->columns_edit_mode as $key => $val){
                        if($ind == $c_sorted){
                            if($key == "validator"){ // customized rows (validator)
                                $field_property_for_field = $this->getFieldProperty($key, "for_field");
                                $field_property_header    = $this->getFieldProperty($key, "header");
                                $field_property_req_type  = $this->getFieldProperty($key, "req_type");
                                $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap");
                                    echo $this->nbsp;echo "<b>".ucfirst($field_property_header)."</b>";
                                $this->colClose();
                                $field_property_wrap = $this->getFieldProperty($this->getFieldName($c), "wrap", "view");
                                $this->colOpen($col_align, 0, $field_property_wrap);
                                    echo $this->getFieldValueByType($row[$this->getFieldOffset($field_property_for_field)], $this->getFieldOffset($field_property_for_field), $row, "", $field_property_req_type);
                                $this->colClose();
                            }else if($key == "delimiter"){ // customized rows (delimiter)
                                $this->colOpen("",2,"nowrap");
                                echo $this->getFieldProperty("delimiter", "inner_html");
                                $this->colClose();
                            }
                        }
                        $ind++;
                    }
                }
                $this->rowClose();
            }// for
        }
        // *** END DRAWING ROWS ------------------------------------------------

        $this->tblClose();
        echo "<br />";
        if(($r == $this->row_lower) && ($this->rid != -1)){
            $this->noDataFound();
            echo "<br /><center>";
            if($req_print != ""){
                echo "<span class='".$this->css_class."_class_a'><b>".$this->lang['back']."</b></span>";
            }else{
                echo "<a class='".$this->css_class."_class_a' href='javascript:history.go(-1);'><b>".$this->lang['back']."</b></a>";
            }
            echo "</center>";
        }else{
            $this->tblOpen();
            $this->rowOpen($r, $this->rowColor[1]);
            $this->mainColOpen('left', 0, '', '', (($req_print == true) ? $this->css_class."_class_td_normal" : $this->css_class."_class_th"), "style='BORDER-RIGHT: #d2d0bb 0px solid;'");
            if($this->mode === "details"){
                $cancel_url = $this->combineUrl("cancel", $row[$this->getFieldOffset($this->primary_key)]);
                $this->setUrlString($c_curr_url, "filtering", "sorting", "paging");
                $cancel_url .= $c_curr_url;
                echo "<div style='float:";
                echo ($this->direction == "rtl")?"left":"right";
                if($req_print != ""){
                    echo ";'><span class='".$this->css_class."_class_a'><b>".$this->lang['back']."</b></span></div>";
                }else{
                    echo ";'>";
                    echo $this->drawModeButton("cancel", $cancel_url, $this->lang['back'], $this->lang['back'], "cancel.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$cancel_url."'\"", false, "", "");
                    echo "</div>";
                }
            }else{
                // if not new row
                $ind = ($this->getFieldOffset($this->primary_key) != -1) ? $this->getFieldOffset($this->primary_key) : 0;
                if(($this->rid != -1) && isset($this->modes['delete'][$this->mode]) && $this->modes['delete'][$this->mode]){
                    $curr_url = $this->combineUrl("delete", $row[$ind]);
                    $this->setUrlString($curr_url, "filtering", "sorting", "paging");
                    $this->drawModeButton("delete", $curr_url, $this->lang['delete'], $this->lang['delete_record'], "delete.gif", "\"return ".$this->unique_prefix."verifyDelete('$curr_url');\"", true, "", "");
                }

                if($this->rid != -1){
                    $rid = $row[$ind];
                }else{
                    $rid = -1;
                }
                $curr_url = $this->combineUrl("update", $rid);
                $cancel_url = $this->combineUrl("cancel", $rid);
                $this->setUrlString($c_curr_url, "filtering", "sorting", "paging");
                $cancel_url .= $c_curr_url;
                $curr_url .= $c_curr_url;

                if(isset($this->modes['edit'][$this->mode]) && $this->modes['edit'][$this->mode]){
                    if($req_mode === "add") { $cancel_url .= $this->amp.$this->unique_prefix."new=1";}
                    $this->setEditFieldsFormScript();

                    echo "<div style='float:"; echo ($this->direction == "rtl")?"left":"right"; echo ";'>";
                    if($req_mode === "add") {
                        if($this->rid == -1){
                            $cancel_url = $this->combineUrl("cancel", $rid);
                        }else{
                            $cancel_url = $this->combineUrl("delete", $rid);
                        }
                        $this->setUrlString($cancel_url, "filtering", "sorting", "paging");

                        $this->drawModeButton("cancel", $cancel_url, $this->lang['cancel'], $this->lang['cancel'], "cancel.gif", "\"return ".$this->unique_prefix."verifyCancel('$cancel_url'); javascript:document.location.href='".$this->HTTP_URL.$cancel_url."'\"", false, $this->nbsp, "");
                    }else{
                        $this->drawModeButton("cancel", $cancel_url, $this->lang['cancel'], $this->lang['cancel'], "cancel.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$cancel_url."'\"", false, $this->nbsp, "");
                    }
                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    if($this->rid == -1){ //aaa new record
                       $this->drawModeButton("edit", "#", $this->lang['create'], $this->lang['create_new_record'], "update.gif", "\"".$this->unique_prefix."sendEditFields(); return false;\"", false, $this->nbsp, "");
                    }else{
                       $this->drawModeButton("edit", "#", $this->lang['update'], $this->lang['update_record'], "update.gif", "\"".$this->unique_prefix."sendEditFields(); return false;\"", false, $this->nbsp, "");
                    }
                    echo "</div>";
                }else{
                    if(isset($this->modes['cancel'][$this->mode]) && $this->modes['cancel'][$this->mode]){
                        echo "<div style='float:"; echo ($this->direction == "rtl")?"left":"right"; echo ";'>";
                        $this->drawModeButton("cancel", $cancel_url, $this->lang['back'], $this->lang['back'], "cancel.gif", "\"\"", false, $this->nbsp, "");
                        echo "</div>";
                    }
                }
            }
            $this->mainColClose();
            $this->rowClose();
            $this->tblClose();
        }

        echo "</form>";
        echo "<script type='text/javascript'>\n<!--\n document.getElementById('".$this->unique_random_prefix."loading_image').style.display='none'; \n//-->\n</script>";

        if($this->paging_allowed) $this->pagingSecondPart($this->lower_paging, true, true, "Lower");
        if(($this->first_field_focus_allowed) && ($first_field_name != "")) echo "<script type='text/javascript'>\n<!--\n document.".$this->unique_prefix."frmEditRow.".$this->getFieldRequiredType($first_field_name).$first_field_name.".focus(); \n//-->\n</script>";
    }


    //--------------------------------------------------------------------------
    // draw Multi-Row Bar
    //--------------------------------------------------------------------------
    function drawMultiRowBar($r, $curr_url){
        $req_print = $this->getVariable('print');

        if(($this->multirow_allowed) && ($r != $this->row_lower)){
            echo "<script type='text/javascript'>\n<!--\n
            function ".$this->unique_prefix."verifySelected(param, button_type, flag_name, flag_value){
                if(confirm('Are you sure you want to carry out this operation?')){
                    selected_rows = '&".$this->unique_prefix."rid=';
                    selected_rows_ids = '';
                    found = 0;
                    for(i=".$this->row_lower."; i < ".$this->row_upper."; i++){
                        if(document.getElementById(\"".$this->unique_prefix."checkbox_\"+i).checked == true){
                            if(found == 1){ selected_rows_ids += '-'; }
                            selected_rows_ids += document.getElementById(\"".$this->unique_prefix."checkbox_\"+i).value;
                            found = 1;
                        }
                    }
                    if(found){
                        document_location_href = param+selected_rows+selected_rows_ids;
                        if(flag_name != ''){
                            found = (document_location_href.indexOf(flag_name) != -1);
                            if(!found){
                                document_location_href += '&'+flag_name+'='+flag_value;
                            }
                        }
                        document.location.href = document_location_href;
                    }else{
                        alert('You need to select one or more rows to carry out this operation!');
                        return false;
                    }
                }
            };
            function ".$this->unique_prefix."setCheckboxes(check){
                if(check){
                    for(i=".$this->row_lower."; i < ".$this->row_upper."; i++){
                        document.getElementById('".$this->unique_prefix."checkbox_'+i).checked = true;
                        document.getElementById('".$this->unique_prefix."row_'+i).style.background = '".$this->rowColor[5]."';
                    }
                }else{
                    for(i=".$this->row_lower."; i < ".$this->row_upper."; i++){
                        document.getElementById('".$this->unique_prefix."checkbox_'+i).checked = false;
                        if((i % 2) == 0) row_color_back = '".$this->rowColor[0]."';
                        else row_color_back = '".$this->rowColor[1]."';
                        document.getElementById('".$this->unique_prefix."row_'+i).style.background = row_color_back;
                    }
                }
            }
            \n//-->\n</script>
            ";
            echo "<table dir='".$this->direction."' border='0' align='".$this->tblAlign[$this->mode]."' width='".$this->tblWidth[$this->mode]."'>";
            echo "<tr>";
            echo "<td align='left'>";
            echo "<table border='0' align='left'>
                    <tr>
                        <td align='left' valign='center' class='class_nowrap'>";
                            $count = 0;
                            foreach($this->multirow_operations_array as $key => $val){
                                if($this->multirow_operations_array[$key]['view']) $count++;
                            }
                            if($count > 0){
                                echo "<img style='PADDING:0px; MARGIN:0px;' src='".$this->directory."images/".$this->css_class."/arrow_ltr.png' border='0' width='38' height='22' alt='".$this->lang['with_selected'].":' title='".$this->lang['with_selected'].":' />";
                                    if(!$req_print){
                                        echo "<a class='".$this->css_class."_class_a' href='javascript:void(0);' onClick='".$this->unique_prefix."setCheckboxes(true); return false;'>".$this->lang['check_all']."</a>
                                        &nbsp;/&nbsp;
                                        <a class='".$this->css_class."_class_a' href='javascript:void(0);' onClick='".$this->unique_prefix."setCheckboxes(false); return false;'>".$this->lang['uncheck_all']."</a>";
                                    }else{
                                        echo "<a class='".$this->css_class."_class_label'>".$this->lang['check_all']."</label>
                                        &nbsp;/&nbsp;
                                        <a class='".$this->css_class."_class_label'>".$this->lang['uncheck_all']."</label>";
                                    }
                                echo "
                                    &nbsp;&nbsp;&nbsp;
                                    <label class='".$this->css_class."_class_label'><i>".$this->lang['with_selected'].":</i></label>&nbsp;&nbsp;
                                    </td>
                                    <td align='left' valign='bottom' >";
                                foreach($this->multirow_operations_array as $key => $val){
                                    if($this->multirow_operations_array[$key]['view']){
                                        echo "&nbsp;";
                                        $curr_url = $this->combineUrl($key, "");
                                        $flag_name = isset($val['flag_name']) ? $val['flag_name'] : "";
                                        $flag_value = isset($val['flag_value']) ? $val['flag_value'] : "";
                                        $tooltip = isset($val['tooltip']) ? $val['tooltip'] : $this->lang[$key.'_selected'];
                                        $image = isset($val['image']) ? $val['image'] : $key.".gif" ;
                                        $this->setUrlString($curr_url, "filtering", "sorting", "paging");
                                        $this->drawModeButton($key, $curr_url, $tooltip, $tooltip, $image, "\"return ".$this->unique_prefix."verifySelected('$curr_url', '$key', '$flag_name', '$flag_value');\"", false, "", "image");
                                        echo "&nbsp;";
                                    }
                                }
                            }
            echo "
                        </td>
                    </tr>
               </table>
            ";
            echo "</td>";
            echo "</tr>";
            echo "</table>";
        }
    }

    //--------------------------------------------------------------------------
    // draw Summarize Row
    //--------------------------------------------------------------------------
    function drawSummarizeRow($r){
        if(count($this->summarize_columns) > 0){
            $this->rowOpen("", $this->rowColor[0]);
            // draw multi-row row footer cell
            if($this->multirow_allowed){
                $this->colOpen("center",0,"nowrap","","");
                echo $this->nbsp;
                $this->colClose();
            }

            // draw column headers in view mode
            for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
                // get current column's index (offset)
                $c = $this->sorted_columns[$c_sorted];
                if($c_sorted == $this->col_lower){
                    if((isset($this->modes['add'][$this->mode]) && $this->modes['add'][$this->mode]) ||
                       (isset($this->modes['edit'][$this->mode]) && $this->modes['edit'][$this->mode]))
                    {
                        $this->colOpen("center",0,"nowrap",$this->rowColor[2], $this->css_class."_class_td_main");
                        echo "<a class='".$this->css_class."_class_a'><b>".$this->lang['total'].":</b></a>";
                        $this->colClose();
                    }
                    if($this->rows_numeration){
                       $this->colOpen("center",0,"nowrap"); echo ""; $this->colClose();
                    }
                }
                if($this->canViewField($this->getFieldName($c))){
                    $this->colOpen("right",0,"nowrap");
                    $field_property_summarize = $this->getFieldProperty($this->getFieldName($c), "summarize", "view");
                    if(($field_property_summarize == "true") || ($field_property_summarize == true)){
                        echo $this->nbsp."=".$this->nbsp."<a class='".$this->css_class."_class_a'><b>".number_format($this->summarize_columns[$this->getFieldName($c)], 2)."</b></a>";
                    }
                    $this->colClose();
                }

            }
            if((isset($this->modes['details'][$this->mode]) && $this->modes['details'][$this->mode])){
                $this->colOpen("right",0,"nowrap");$this->colClose();
            }
            if((isset($this->modes['delete'][$this->mode]) && $this->modes['delete'][$this->mode])){
                $this->colOpen("right",0,"nowrap");$this->colClose();
            }
            $this->rowClose();
        }
    }

    //--------------------------------------------------------------------------
    // sort columns by mode order
    //--------------------------------------------------------------------------
    function sortColumns($mode = ""){
        if($mode == "view"){
            foreach($this->columns_view_mode as $fldName => $fldValue){
                $this->sorted_columns[] = $this->getFieldOffset($fldName);
            }
        }else if(($mode == "edit") || ($mode == "details")){
            if(isset($this->columns_edit_mode) && is_array($this->columns_edit_mode)){
                foreach($this->columns_edit_mode as $fldName => $fldValue){
                    $this->sorted_columns[] = $this->getFieldOffset($fldName);
                }
            }
        }
    }

    //--------------------------------------------------------------------------
    // add error to array of errors
    //--------------------------------------------------------------------------
    function addErrors($dSet = ""){
        if($this->debug){
            if($dSet == "") $dSet = $this->data_set;
            $this->errors[] = $dSet->getDebugInfo();
        }
    }

    //--------------------------------------------------------------------------
    // add warning to array of warnings
    //--------------------------------------------------------------------------
    function addWarning($warning_field, $warning_value){
        if($this->debug){
            $warning = str_replace('_FIELD_', $warning_field, $this->lang['wrong_parameter_error']);
            $warning = str_replace('_VALUE_', $warning_value, $warning);
            $this->warnings[] = $warning;
        }
    }

    //--------------------------------------------------------------------------
    // display warnings
    //--------------------------------------------------------------------------
    function displayWarnings(){
        if($this->debug){
            $count = 0;
            if(count($this->warnings) > 0){
                echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left'>";
                echo "<font class='".$this->css_class."_class_error_message no_print'><u><b>".$this->lang['warnings']."</b></u>:</font><br /><br />";
                foreach($this->warnings as $key){
                    echo "<font class='".$this->css_class."_class_error_message no_print'>".(++$count).") $key</font><br />";
                }
                echo "<br />";
                echo "</td></tr></table>";
            }
        }
    }

    //--------------------------------------------------------------------------
    // display errors
    //--------------------------------------------------------------------------
    function displayErrors(){
        if($this->debug){
            $count = 0;
            if(count($this->errors) > 0){
                echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left'>";
                echo "<font class='".$this->css_class."_class_error_message no_print'><u><b>".$this->lang['errors']."</b></u>:</font><br /><br />";
                foreach($this->errors as $key){
                    echo "<font class='".$this->css_class."_class_error_message no_print'>".(++$count).") </font>";
                    echo "<font class='".$this->css_class."_class_label'>".substr($key, 0, strpos($key, "["))."</font><br />";
                    echo "<font class='".$this->css_class."_class_error_message no_print'>".stristr($key, "[")."</font><br /><br />";
                }
                echo "<br />";
                echo "</td></tr></table>";
            }
        }
    }

    //--------------------------------------------------------------------------
    // draw data sent by POST and GET
    //--------------------------------------------------------------------------
    function displayDataSent(){
        if($this->debug){
            print_r("<font class='".$this->css_class."_class_ok_message no_print'><b>POST</b>: ");
            print_r($_POST);
            print_r("</font><br /><br />");
            print_r("<font class='".$this->css_class."_class_ok_message no_print'><b>GET</b>: ");
            print_r($_GET);
            print_r("</font><br /><br />");
        }
    }

    //--------------------------------------------------------------------------
    // draw messages
    //--------------------------------------------------------------------------
    function displayMessages(){
        if($this->messaging && $this->act_msg){
            $css_class = "".$this->css_class."_class_ok_message";
            if($this->is_error) $css_class= "".$this->css_class."_class_error_message no_print";
            if($this->is_warning) $css_class= "".$this->css_class."_class_error_message no_print";
            echo "<div style='margin-top:10px;margin-bottom:10px;'><center><font class='".$css_class."'>".$this->act_msg."</font></center></div>";
            $this->act_msg = "";
        }
    }

    //--------------------------------------------------------------------------
    // save Http Get Vars
    //--------------------------------------------------------------------------
    function saveHttpGetVars(){
        if(is_array($this->http_get_vars) && (count($this->http_get_vars) > 0)){
            foreach($this->http_get_vars as $key){
                echo "<input type='hidden' name='".$key."' id='".$key."' value='".((isset($_REQUEST[$key]))?$_REQUEST[$key]:"")."'>";
            }
        }
        echo "<input type='hidden' name='".$this->unique_prefix."page_size'     id='".$this->unique_prefix."page_size'     value='".((isset($_REQUEST[$this->unique_prefix.'page_size']))?$_REQUEST[$this->unique_prefix.'page_size']:$this->req_page_size)."'>\n";
        echo "<input type='hidden' name='".$this->unique_prefix."sort_field'    id='".$this->unique_prefix."sort_field'    value='".((isset($_REQUEST[$this->unique_prefix.'sort_field']))?$_REQUEST[$this->unique_prefix.'sort_field']:"")."'>\n";
        echo "<input type='hidden' name='".$this->unique_prefix."sort_field_by' id='".$this->unique_prefix."sort_field_by' value='".((isset($_REQUEST[$this->unique_prefix.'sort_field_by']))?$_REQUEST[$this->unique_prefix.'sort_field_by']:"")."'>\n";
        echo "<input type='hidden' name='".$this->unique_prefix."sort_type'     id='".$this->unique_prefix."sort_type'     value='".((isset($_REQUEST[$this->unique_prefix.'sort_type']))?$_REQUEST[$this->unique_prefix.'sort_type']:"")."'>\n";

        // get URL vars from another  DG
        if(is_array($this->another_datagrids) && (count($this->another_datagrids) > 0)){
            foreach($this->another_datagrids as $key => $val){
                if($val[$this->mode] == true){
                    foreach($_REQUEST as $r_key => $r_val){
                        if(strstr($r_key, $key)){ // ."_ff_"
                           echo "<input type='hidden' name='".$r_key."' id='".$r_key."' value='".((isset($_REQUEST[$r_key]))?$_REQUEST[$r_key]:"")."'>\n";
                        }
                    }
                }
            }
        }

    }

    //--------------------------------------------------------------------------
    // combine url
    //--------------------------------------------------------------------------
    function combineUrl($mode, $rid="", $amp=""){
        $amp = ($amp != "") ? $amp : $this->amp;
        $ind = 0;
        if(is_array($this->http_get_vars) && (count($this->http_get_vars) > 0)){
            foreach($this->http_get_vars as $key){
                if($ind == 0){ $a_url = "?"; $ind = 1; }
                else $a_url .= $amp;
                $a_url .= $key."=".((isset($_REQUEST[$key]))?$_REQUEST[$key]:"");
            }
        }
        if($ind == 0) $a_url = "?".$this->unique_prefix."mode=".$mode."";
        else $a_url .= $amp.$this->unique_prefix."mode=".$mode."";
        if($rid !== "") $a_url .= $amp.$this->unique_prefix."rid=".$rid;

        // get URL vars from another  DG
        if(is_array($this->another_datagrids) && (count($this->another_datagrids) > 0)){
            foreach($this->another_datagrids as $key => $val){
                if($val[$this->mode] == true){
                    $a_url .= $amp.$key."mode=".((isset($_REQUEST[$key.'mode']))?$_REQUEST[$key.'mode']:"");
                    $a_url .= $amp.$key."rid=".((isset($_REQUEST[$key.'rid']))?$_REQUEST[$key.'rid']:"");
                    $a_url .= $amp.$key."sort_field=".((isset($_REQUEST[$key.'sort_field']))?$_REQUEST[$key.'sort_field']:"");
                    $a_url .= $amp.$key."sort_field_by=".((isset($_REQUEST[$key.'sort_field_by']))?$_REQUEST[$key.'sort_field_by']:"");
                    $a_url .= $amp.$key."sort_type=".((isset($_REQUEST[$key.'sort_type']))?$_REQUEST[$key.'sort_type']:"");
                    $a_url .= $amp.$key."page_size=".((isset($_REQUEST[$key.'page_size']))?$_REQUEST[$key.'page_size']:"");
                    $a_url .= $amp.$key."p=".((isset($_REQUEST[$key.'p']))?$_REQUEST[$key.'p']:"");
                    foreach($_REQUEST as $r_key => $r_val){
                        if(strstr($r_key, $key."_ff_")){
                           //d echo  $r_key."=".$_REQUEST[$r_key]."<br />";
                           $a_url .= $amp.$r_key."=".((isset($_REQUEST[$r_key]))?$_REQUEST[$r_key]:"");
                        }
                    }
                }
            }
        }
        return $a_url;
    }

    //--------------------------------------------------------------------------
    // set SQL limit
    //--------------------------------------------------------------------------
    function setSqlLimit(){
        $req_page_num  = "";
        $req_page_size = $this->getVariable('page_size');
        $req_p = $this->getVariable('p');
        if($req_page_size != "") $this->req_page_size = $req_page_size;
        if($req_p != "") $req_page_num  = $req_p;
        if(is_numeric($req_page_num)){
            if($req_page_num > 0) $this->page_current = $req_page_num;
            else $this->page_current = 1;
        }else{
            $this->page_current = 1;
        }

        // if there was deleted a last rows from a last page
        if(intval($this->rows_total) <= intval(($this->page_current - 1) * $this->req_page_size)){
            if($this->page_current > 1){
                $this->page_current--;
                $_REQUEST[$this->unique_prefix.'p'] = $this->page_current;
            }
        }

        $this->limit_start = ($this->page_current - 1) * $this->req_page_size;
        $this->limit_size = $this->req_page_size;
    }

    //--------------------------------------------------------------------------
    // set SQL limit by DB type
    //--------------------------------------------------------------------------
    function setSqlLimitByDbType($limit_start="", $limit_size=""){
        $this->setSqlLimit();
        if($limit_start == "") $limit_start = $this->limit_start;
        if($limit_size == "") $limit_size = $this->limit_size;
        $limit_string = "";
        switch($this->db_handler->phptype){
            case "oci8":    // oracle
                $limit_string = "AND (rownum > ".$limit_start." AND rownum <= ".intval($limit_start + $limit_size).") ";
                break;
            case "mssql":   // mssql
                $limit_string = "AND RowNumber > ".$limit_start." AND RowNumber < ".intval($limit_start + $limit_size).") ";
                break;
            case "pgsql":   // Postgresql
                $limit_string = "OFFSET ".$limit_start." LIMIT ".$limit_size." ";
                break;
            case "mysql":   // mysql and others
            default:
                $limit_string = "LIMIT ".$limit_start.", ".$limit_size." ";
                break;
        }
        return $limit_string;
    }

    //--------------------------------------------------------------------------
    // set real escape string by DB type
    //--------------------------------------------------------------------------
    function setRealEscapeStringByDbType($field_value = ""){
        switch($this->db_handler->phptype){
            case "mysql":   // mysql
                return mysql_real_escape_string($field_value);  break;
            default:
                return $field_value;  break;
        }
    }

    //--------------------------------------------------------------------------
    // set SQL by DB type
    //--------------------------------------------------------------------------
    function setSqlByDbType($sql="", $order_by="", $limit=""){
        $sql_string = "";
        switch($this->db_handler->phptype){
            case "oci8":    // oracle
                if($limit != ""){
                    preg_match_all("/\d+/",$limit,$matches);
                    $limit_start = $matches[0][0];
                    $limit_size = $matches[0][1]-$limit_start;
                    $sql_string = $this->db_handler->modifyLimitQuery($sql." ".$order_by, $limit_start, $limit_size);
                    if($this->debug) echo "<table><tr><td><b>Oracle sql: </b>".$sql_string."</td></tr></table><br>";
                }else{
                    $sql_string = $sql." ".$order_by;
                }
                break;
            case "mssql":   // mssql
                //d (10.01.2008) $sql_string = "SELECT ".$limit." ".substr($sql, strpos("select", $this->strToLower($sql))+7, strlen($sql))." ".$order_by;
                $from_index = strpos($this->strToLower($sql), "from ");
                $prefix = substr($sql, 0, $from_index);
                $suffix = substr($sql, $from_index);
                $sql_string = $prefix.", Row_Number() over (ORDER BY ".$this->primary_key.") AS RowNumber ".$suffix;
                $sql_string += " ".$limit." ".$order_by;
                break;
            case "mysql":   // mysql and others
            default:
                $sql_string = $sql." ".$order_by." ".$limit;
                break;
        }
        return $sql_string;
    }

    //--------------------------------------------------------------------------
    // get LCASE function name by DB type
    //--------------------------------------------------------------------------
    function getLcaseFooByDbType(){
        $lcase_name = "";
        switch($this->db_handler->phptype){
            case "oci8":    // oracle
                $lcase_name = "lower";  break;
            case "mssql":   // mssql
                $lcase_name = "LCASE";  break;
            case "pgsql":   // pgsql
                $lcase_name = "lower";  break;
            case "mysql":   // mysql and others
            default:
                $lcase_name = "LCASE";  break;
        }
        return $lcase_name;
    }

    //--------------------------------------------------------------------------
    // paging function - part 1
    //--------------------------------------------------------------------------
    function pagingFirstPart(){
        // (1) if we got a wrong number of page -> set page=1
        $req_page_num  = "";
        $req_page_size = $this->getVariable('page_size');
        $req_p = $this->getVariable('p');
        if(($req_page_size != "") && ($req_page_size != 0)) $this->req_page_size = $req_page_size;
        if($req_p != "") $req_page_num  = $req_p;

        if(is_numeric($req_page_num)){
            if($req_page_num > 0) $this->page_current = $req_page_num;
            else $this->page_current = 1;
        }else{
            $this->page_current = 1;
        }
        // (2) set pages_total & page_current vars for paging
        if($this->rows_total > 0){
            if(is_float($this->rows_total / $this->req_page_size))
                $this->pages_total = intval(($this->rows_total / $this->req_page_size) + 1);
            else
                $this->pages_total = intval($this->rows_total / $this->req_page_size);
        }else{
            $this->pages_total = 0;
        }
        if($this->page_current > $this->pages_total) $this->page_current = $this->pages_total;
    }

    //--------------------------------------------------------------------------
    // paging function - part 2
    //--------------------------------------------------------------------------
    function pagingSecondPart($lu_paging=false, $upper_br, $lower_br, $type="1"){
        // (4) display paging line
        $req_print = $this->getVariable('print');
        if($req_print != true) {$a_tag = "a";} else {$a_tag = "span";};
        $text = "";

        if($this->pages_total >= 1){
            $href_string = $this->combineUrl("view", "", "&");
            $this->setUrlString($href_string, "filtering", "sorting", "");
            $text .= "\n<script type='text/javascript'>\n<!--\n";
            $text .= "function ".$this->unique_prefix."setPageSize".$type."(){document.location.href = '$href_string&".$this->unique_prefix."page_size='+document.frmPaging$this->unique_prefix".$type.".page_size".$type.".value;}";
            $text .= "\n//-->\n</script>\n";
            $href_string .= $this->amp.$this->unique_prefix."page_size=".$this->req_page_size;
            $text .= "<form name='frmPaging$this->unique_prefix".$type."' id='frmPaging$this->unique_prefix".$type."' action='' style='MARGIN:0px; PADDING:5px; '>";
            if($lu_paging['results'] || $lu_paging['pages'] || $lu_paging['page_size']){
                if($upper_br) $text .= "";  //<br />
            }
            $text .= "<table class='".$this->css_class."_class_paging_table' dir='".$this->direction."' align='".$this->tblAlign[$this->mode]."' width='".$this->tblWidth[$this->mode]."' border='0' >";
            $text .= "<tr><td align='".$lu_paging['results_align']."' class='class_nowrap'>";
            if($lu_paging['results']){
                $text .= "&nbsp;".$this->lang['results'].":&nbsp;";
                if(($this->page_current * $this->req_page_size) <= $this->rows_total) $total = ($this->page_current * $this->req_page_size);
                else $total = $this->rows_total;
                $text .= ($this->page_current * $this->req_page_size - $this->req_page_size + 1)." - ".$total;
                $text .= "&nbsp;".$this->lang['of']."&nbsp;";
                $text .= $this->rows_total;
                $text .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }
            $text .= "</td><td align='".$lu_paging['pages_align']."' class='class_nowrap'>";
            if($lu_paging['pages']){
                $text .= "&nbsp;".$this->lang['pages'].":&nbsp;";
                $href_prev1 = $href_prev2 = $href_first = "";
                if($this->page_current > 1){
                    $href_prev1 = "href='$href_string".$this->amp.$this->unique_prefix."p=".($this->page_current - 1)."'";
                    $href_prev2 = "href='$href_string".$this->amp.$this->unique_prefix."p=".$this->page_current."'";
                    $href_first = "href='$href_string".$this->amp.$this->unique_prefix."p=1'";
                }
                $text .= "&nbsp;<".$a_tag." title='".$this->lang['first']."' class='".$this->css_class."_class_a' style='TEXT-DECORATION: none;' ".$href_first.">".$this->first_arrow."</".$a_tag.">";
                if($this->page_current > 1) $text .= "&nbsp;<".$a_tag." class='".$this->css_class."_class_a' style='TEXT-DECORATION: none;' title='".$this->lang['previous']."' ".$href_prev1.">".$this->previous_arrow."</".$a_tag.">";
                else $text .= "&nbsp;<".$a_tag." class='".$this->css_class."_class_a' style='TEXT-DECORATION: none;' title='".$this->lang['previous']."' ".$href_prev2.">".$this->previous_arrow."</".$a_tag.">";
                $text .= "&nbsp;";
                $low_window_ind = $this->page_current - 3;
                $high_window_ind = $this->page_current + 3;
                if($low_window_ind > 1){ $start_index = $low_window_ind; $text .= "..."; }
                else $start_index = 1;
                if($high_window_ind < $this->pages_total) $end_index = $high_window_ind;
                else $end_index = $this->pages_total;
                for($ind=$start_index; $ind <= $end_index; $ind++){
                    if($ind == $this->page_current) $text .= "&nbsp;<".$a_tag." class='".$this->css_class."_class_a' style='TEXT-DECORATION: underline;' title='".$this->lang['current']."' href='$href_string".$this->amp.$this->unique_prefix."p=".$ind."'><b><u>" . $ind . "</u></b></".$a_tag.">";
                    else $text .= "&nbsp;<".$a_tag." class='".$this->css_class."_class_a' style='TEXT-DECORATION: none;' href='$href_string".$this->amp.$this->unique_prefix."p=".$ind."'>".$ind."</".$a_tag.">";
                    if($ind < $this->pages_total) $text .= ",&nbsp;";
                    else $text .= "&nbsp;";
                }
                if($high_window_ind < $this->pages_total) $text .= "...";
                $href_next1 = $href_next2 = $href_last = "";
                if($this->page_current < $this->pages_total){
                    $href_next1 = "href='$href_string".$this->amp.$this->unique_prefix."p=".($this->page_current + 1)."'";
                    $href_next2 = "href='$href_string".$this->amp.$this->unique_prefix."p=".$this->page_current."'";
                    $href_last  = "href='$href_string".$this->amp.$this->unique_prefix."p=".$this->pages_total."'";
                }
                if($this->page_current < $this->pages_total) $text .= "&nbsp;<".$a_tag." class='".$this->css_class."_class_a' style='TEXT-DECORATION: none;' title='".$this->lang['next']."' ".$href_next1.">".$this->next_arrow."</".$a_tag.">";
                else $text .= "&nbsp;<".$a_tag." class='".$this->css_class."_class_a' style='TEXT-DECORATION: none;' title='".$this->lang['next']."' ".$href_next2.">".$this->next_arrow."</".$a_tag.">";
                $text .= "&nbsp;<".$a_tag." class='".$this->css_class."_class_a' style='TEXT-DECORATION: none;' title='".$this->lang['last']."' ".$href_last.">".$this->last_arrow."</".$a_tag.">";
            }
            $text .= "</td><td align='".$lu_paging['page_size_align']."' class='class_nowrap'>";
            if($lu_paging['page_size']){
                $text .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                $text .= "&nbsp;".$this->lang['page_size'].":&nbsp;";
                $text .= $this->drawDropDownList('page_size'.$type, 'setPageSize'.$type.'()', $this->pages_array, $this->req_page_size);
            }
            $text .= "</td></tr>";
            $text .= "</table>";
            $text .= "</form>";
            if($lu_paging['results'] || $lu_paging['pages'] || $lu_paging['page_size']){
                if($lower_br) $text .= ""; //<br />
            }
            echo $text;
        }else{
            echo "<br /><br />";
        }
    }

    //--------------------------------------------------------------------------
    // function - set total number of rows in query
    //--------------------------------------------------------------------------
    function setTotalNumberRows($fsort = "", $limit = "", $mode = ""){
        $req_mode = ($mode == "") ? $this->getVariable('mode') : $mode;
        if(($req_mode == "edit") || ($req_mode == "details")){
            // we need this stupid operation to get a total number of rows in our query
            $this->data_set = & $this->db_handler->query($this->setSqlByDbType($this->sql, $fsort, $limit));
            $this->rows_total = $this->numberRows();
        }else{
            $temp_sql = $this->setSqlByDbType($this->sql, "", "");
            $from_pos = $this->lastSubStrOccurence($temp_sql, "from ");
            $new_sql = "SELECT count(*) as cnt FROM ".substr($temp_sql, (int)(strlen($temp_sql) - $from_pos), (int)$from_pos);
            $this->data_set = & $this->db_handler->query($new_sql);
            if($this->db_handler->isError($this->data_set) == 1){
                $this->rows_total = 0;
            }else{
                $row = $this->data_set->fetchRow();
                $this->rows_total = $row[0];
            }
        }
    }

    //--------------------------------------------------------------------------
    // function - number rows
    //--------------------------------------------------------------------------
    function numberRows(){
        if($this->db_handler->isError($this->data_set)){
            return 0;
        }else{
            return $this->data_set->numRows();
        }
    }

    //--------------------------------------------------------------------------
    // function - number columns
    //--------------------------------------------------------------------------
    function numberCols(){
        if($this->db_handler->isError($this->data_set)){
            return 0;
        }else{
            return $this->data_set->numCols();
        }
    }

    //--------------------------------------------------------------------------
    // function - no data found
    //--------------------------------------------------------------------------
    function noDataFound(){
        $this->tblOpen();
        $this->rowOpen(0, $this->rowColor[0]);
            $add_column = 0;
            if((isset($this->modes['add'][$this->mode]) && ($this->modes['add'][$this->mode])) ||
               (isset($this->modes['edit'][$this->mode]) && ($this->modes['edit'][$this->mode]))
              ) $add_column += 1;
            if(isset($this->mode['delete']) && $this->mode['delete']) $add_column += 1;
            $this->colOpen("center", (count($this->sorted_columns) + $add_column),"");
                if($this->is_error){
                    echo "<br /><span class='".$this->css_class."_class_error_message no_print'>".$this->lang['no_data_found_error']."<br />&nbsp;";
                    if(!$this->debug){ echo "<br />".$this->lang['turn_on_debug_mode']."<br />&nbsp;"; }
                    echo "</span>";
                }else{
                    echo "<br />".$this->lang['no_data_found']."<br />&nbsp;";
                }
            $this->colClose();
        $this->rowClose();
        $this->tblClose();
    }

    //--------------------------------------------------------------------------
    // delete row
    //--------------------------------------------------------------------------
    function deleteRow($rid){
        // security check
        if(($this->modes["delete"]["view"] == false) && ($this->modes["delete"]["edit"] == false)){
            $this->is_warning = true;
            if($this->debug){
                $this->act_msg = $this->lang['delete_record_blocked'];
            }else{
                $this->act_msg = $this->lang['deleting_operation_uncompleted'];
            }
            return false;
        }

        $this->rids = explode("-", $rid);
        $sql = "DELETE FROM $this->tbl_name WHERE $this->primary_key IN ('-1' ";
        foreach ($this->rids as $key){
            $sql .= ", '".$key."' ";
        }
        $sql .= ");";
        $this->db_handler->query($sql);
        $affectedRows = $this->db_handler->affectedRows();
        if($affectedRows > 0){
            $this->act_msg = ($this->dg_messages['delete'] != "") ? $this->dg_messages['delete'] : $this->lang['deleting_operation_completed'];
        }else{
            $this->is_warning = true;
            $this->act_msg = $this->lang['deleting_operation_uncompleted'];
        }
        if($this->debug) echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left' class='".$this->css_class."_class_error_message no_print' style='COLOR: #333333;'><b>delete sql (".$this->strToLower($this->lang['total']).": ".$affectedRows.") </b>".$sql."</td></tr></table><br />";
        if($this->debug) $this->act_msg .= " ".$this->lang['record_n']." ".$this->rid;
    }

    //--------------------------------------------------------------------------
    // update row
    //--------------------------------------------------------------------------
    function updateRow($rid){
        // security check
        if(($this->modes["edit"]["view"] == false) && ($this->modes["edit"]["edit"] == false)){
            $this->is_warning = true;
            if($this->debug){
                $this->act_msg = $this->lang['update_record_blocked'];
            }else{
                $this->act_msg = $this->lang['updating_operation_uncompleted'];
            }
            return false;
        }

        $unique_field_found = false;
        $field_header = "";
        $field_count = "0";

        // check for unique fields
        foreach($this->columns_edit_mode as $fldName => $fldValue){
            if(($fldName != "") && ($this->getFieldProperty($fldName, "unique") == true)){
                $field_prefix = "syy";
                if(isset($fldValue['req_type'])){
                    if(strlen(trim($fldValue['req_type'])) == 3){ $field_prefix = $fldValue['req_type']; }
                    else if(strlen(trim($fldValue['req_type'])) == 2){ $field_prefix = $fldValue['req_type']."y"; }
                }
                $field_header =     (isset($fldValue['header'])) ? $fldValue['header'] : $fldName;
                $unique_condition = (isset($fldValue['unique_condition'])) ? trim($fldValue['unique_condition']) : "" ;
                $field_value =      (isset($_POST[$field_prefix.$fldName])) ? $_POST[$field_prefix.$fldName] : "";
                $sql = "SELECT COUNT(*) as count FROM $this->tbl_name WHERE $this->primary_key <> '$rid' AND $fldName = '$field_value'";
                if($unique_condition != "") $sql .= " AND ".$unique_condition." ";
                if(($field_count = $this->selectSqlItem($sql)) > 0){
                    $unique_field_found = true;
                    break;
                }
            }
        }
        // create update statment
        if(!$unique_field_found){
            $sql = "UPDATE $this->tbl_name SET ";
                $ind = 0;
                $this->addCheckBoxesValues();
                $max = count($_POST);
                foreach($_POST as $fldName => $fldValue){
                    // update all fields, excepting uploading fields
                    if(!strpos($fldName, "file_act")){
                        $fldName = substr($fldName, 3, strlen($fldName));
                        $fldValue = $this->isDatePrepare($fldName,$fldValue);
                        if(!$this->isReadonly($fldName) && !$this->isValidator($fldName)){
                            if (intval($ind) >= 1) $sql .= ", ";
                            if($this->isText($fldName)){
                                $fldValue_new = $fldValue;
                                if(is_array($fldValue)){    // it was a multiple enum
                                    $count = 0; $fldValue_new = "";
                                    foreach ($fldValue as $val){ if($count++ > 0) $fldValue_new .= ","; $fldValue_new .= $val; }
                                }
                                $sql .= "$fldName = '".$this->setRealEscapeStringByDbType($fldValue_new)."' ";
                            }else{
                                $sql .= (trim($fldValue) != "") ? "$fldName = $fldValue " : "$fldName = 0 ";
                            }
                            //if ((intval($ind) === 0) && ($max > 1)) $sql .= ", ";
                            $ind++;
                        }
                    }
                }
            $sql .= " WHERE $this->primary_key = '$rid' ";
            $this->db_handler->query($sql);
            $affectedRows = $this->db_handler->affectedRows();
            if($affectedRows >= 0){
                $this->act_msg = ($this->dg_messages['update'] != "") ? $this->dg_messages['update'] : $this->lang['updating_operation_completed'];
            }else{
                $this->is_warning = true;
                $this->act_msg = $this->lang['updating_operation_uncompleted'];
            }
            if($this->debug) echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left' class='".$this->css_class."_class_error_message no_print' style='COLOR: #333333;'><b>update sql (".$this->strToLower($this->lang['total']).": ".$affectedRows.") </b>".$sql."</td></tr></table><br />";
        }else{
            $this->is_warning = true;
            $this->act_msg = str_replace("_FIELD_", $field_header, $this->lang['unique_field_error']);
            if($this->debug) echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left' class='".$this->css_class."_class_error_message no_print' style='COLOR: #333333;'><b>select sql (".$this->strToLower($this->lang['total']).": ".$field_count.") </b>".$sql."</td></tr></table><br />";
        }
        if($this->debug) $this->act_msg .= " ".$this->lang['record_n']." ".$this->rid;
    }

    //--------------------------------------------------------------------------
    // add row
    //--------------------------------------------------------------------------
    function addRow(){
        // security check
        if(($this->modes["add"]["view"] == false) && ($this->modes["add"]["edit"] == false)){
            $this->is_warning = true;
            if($this->debug){
                $this->act_msg = $this->lang['add_new_record_blocked'];
            }else{
                $this->act_msg = $this->lang['adding_operation_uncompleted'];
            }
            return false;
        }

        $unique_field_found = false;
        $field_header = "";
        $field_count = "0";

        // check for unique fields
        foreach($this->columns_edit_mode as $fldName => $fldValue){
            if(($fldName != "") && ($this->getFieldProperty($fldName, "unique") == true)){
                $field_prefix = "syy";
                if(isset($fldValue['req_type'])){
                    if(strlen(trim($fldValue['req_type'])) == 3){ $field_prefix = $fldValue['req_type']; }
                    else if(strlen(trim($fldValue['req_type'])) == 2){ $field_prefix = $fldValue['req_type']."y"; }
                }
                $field_header =     (isset($fldValue['header'])) ? $fldValue['header'] : $fldName;
                $unique_condition = (isset($fldValue['unique_condition'])) ? trim($fldValue['unique_condition']) : "" ;
                $field_value =      (isset($_POST[$field_prefix.$fldName])) ? $_POST[$field_prefix.$fldName] : "";
                $sql = "SELECT COUNT(*) as count FROM $this->tbl_name WHERE $fldName = '$field_value' ";
                if($unique_condition != "") $sql .= " AND ".$unique_condition." ";
                if(($field_count = $this->selectSqlItem($sql)) > 0){
                    $unique_field_found = true;
                    break;
                }
            }
        }
        // create insert statment
        if(!$unique_field_found){
            $this->addCheckBoxesValues();
            $sql = "INSERT INTO $this->tbl_name (";
                $ind = 0;
                $max = count($_POST);
                foreach($_POST as $fldName => $fldValue){
                    $ind ++;
                    // all fields, excepting uploading fields
                    if(!strpos($fldName, "file_act")){
                        if(!$this->isValidator($fldName)){
                            $fldName = substr($fldName, 3, strlen($fldName));
                            $sql .= "$fldName";
                            if (intval($ind) < intval($max) ) $sql .= ", ";
                        }
                    }
                }
            $sql .= ") VALUES (";
                $ind = 0;
                $max = count($_POST);
                foreach($_POST as $fldName => $fldValue){
                    $ind ++;
                    // all fields, excepting uploading fields
                    if(!strpos($fldName, "file_act")){
                        $fldName = substr($fldName, 3, strlen($fldName));
                        $fldValue = $this->isDatePrepare($fldName,$fldValue);
                        if(!$this->isValidator($fldName)){
                            if($this->isText($fldName)) {
                                if($fldValue != ""){
                                    $fldValue_new = $fldValue;
                                    if(is_array($fldValue)){    // it was a multiple enum
                                        $count = 0; $fldValue_new = "";
                                        foreach ($fldValue as $val){ if($count++ > 0) $fldValue_new .= ","; $fldValue_new .= $val; }
                                    }
                                    $sql .=  "'".$this->setRealEscapeStringByDbType($fldValue_new)."'";
                                }else if($this->isFieldRequired($fldName)){
                                    $sql .= "' '";
                                }else{
                                    $sql .= "''";
                                }
                            }else{
                                if(trim($fldValue) != ""){
                                    $sql .=  $fldValue;
                                }else if($this->isFieldRequired($fldName)){
                                    $sql .= '0';
                                }else{
                                    $sql .= 'NULL';
                                }
                            }
                            if (intval($ind) < intval($max) ) $sql .= ", ";
                        }
                    }
                }
            $sql .= ") ";
            $dSet = $this->db_handler->query($sql);

            $affectedRows = $this->db_handler->affectedRows();
            if($this->debug) echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left'><b>insert sql (".$this->strToLower($this->lang['total']).": ".$affectedRows.") </b>".$sql."</td></tr></table><br />";

            if($affectedRows > 0){
                $this->act_msg = ($this->dg_messages['add'] != "") ? $this->dg_messages['add'] : $this->lang['adding_operation_completed'];
                $res = $this->db_handler->query("SELECT MAX(".$this->primary_key.") as maximal_row FROM ".$this->tbl_name." ");
                $row = $res->fetchRow();
                $this->rid = $row[0];
                if($this->debug){
                    $this->act_msg .= " ".$this->lang['record_n']." ".$this->rid;
                }
            }else{
                $this->is_warning = true;
                $this->act_msg = $this->lang['adding_operation_uncompleted'];
                if($this->db_handler->isError($dSet) == 1){
                    $this->is_error = true;
                    $this->addErrors($dSet);
                }
            }
        }else{
            $this->is_warning = true;
            $this->act_msg = str_replace("_FIELD_", $field_header, $this->lang['unique_field_error']);
            if($this->debug) echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left'><b>select sql (".$this->strToLower($this->lang['total']).": ".$field_count.") </b>".$sql."</td></tr></table><br />";
        }

        $this->sql = "SELECT * FROM $this->tbl_name ";
        $fsort = " ORDER BY " . $this->primary_key . " DESC";
        $this->getDataSet($fsort);
    }

    //--------------------------------------------------------------------------
    // get field offset
    //--------------------------------------------------------------------------
    function getFieldOffset($field_name){
        if(!$this->is_error){
            $fields = $this->data_set->tableInfo();
            for($ind=0; $ind < $this->data_set->numCols(); $ind++){
                if($fields[$ind]['name'] === $field_name) return $ind;
            }
        }
        return -1;
    }

    //--------------------------------------------------------------------------
    // is field required
    //--------------------------------------------------------------------------
    function isFieldRequired($field){
        if(!$this->is_error){
            $fields = $this->data_set->tableInfo();
            if($this->getFieldOffset($field) != -1){
                $flags = $fields[$this->getFieldOffset($field)]['flags'];
                //echo $fields[$this->getFieldOffset($field)]['type']." ".$flags."<br />";
                if(strstr(strtolower($flags), "not_null")){
                    return true;
                }
            }
        }
        return false;
    }

    //--------------------------------------------------------------------------
    // get field info
    //--------------------------------------------------------------------------
    function getFieldInfo($field, $parameter='', $type=0){
        if(!$this->is_error){
            $fields = $this->data_set->tableInfo();
            if($type == 0){
                if($this->getFieldOffset($field) != -1)
                   return $fields[$this->getFieldOffset($field)][$parameter];
                else
                   return '';
            }else{
                return $fields[$field][$parameter];
            }
        }
        return -1;
    }

    //--------------------------------------------------------------------------
    // set Datetime field in right format (dd-mm-yyyy)|(yyyy-mm-dd)
    //--------------------------------------------------------------------------
    function isDatePrepare($field_name, $fldValue){
        $field_property_type = $this->getFieldProperty($field_name, "type");
        switch ($field_property_type){
            case 'date':        // date: DATE
            case 'datetime':    // date: DATE
                break;
            case 'datetimedmy': // date: DATETIME
                $time1   = substr(trim($fldValue), 10, 9);
                $year1   = substr(trim($fldValue), 6, 4);
                $month1  = substr(trim($fldValue), 3, 2);
                $day1    = substr(trim($fldValue), 0, 2);
                $fldValue   = $year1."-".$month1."-".$day1." ".$time1;
                break;
            case 'datedmy':    // date: DATE
                $year1   = substr(trim($fldValue), 6, 4);
                $month1  = substr(trim($fldValue), 3, 2);
                $day1    = substr(trim($fldValue), 0, 2);
                $fldValue   = $year1."-".$month1."-".$day1;
                break;
            default:
                break;
        }
        return $fldValue;
    }

    //--------------------------------------------------------------------------
    // check if field type needs ''(text) or not (numeric...)
    //--------------------------------------------------------------------------
    function isText($field_name){
        $field_type = $this->getFieldInfo($field_name, 'type', 0);
        $result = true;
        switch (strtolower($field_type)){
            case 'int':     // int: TINYINT, SMALLINT, MEDIUMINT, INT, INTEGER, BIGINT, TINY, SHORT, LONG, LONGLONG, INT24
            case 'real':    // real: FLOAT, DOUBLE, DECIMAL, NUMERIC
            case 'null':    // empty: NULL
                $result = false; break;
            case 'string':  // string: CHAR, VARCHAR, TINYTEXT, TEXT, MEDIUMTEXT, LONGTEXT, ENUM, SET, VAR_STRING
            case 'blob':    // blob: TINYBLOB, MEDIUMBLOB, LONGBLOB, BLOB, TEXT
            case 'date':    // date: DATE
            case 'timestamp':    // date: TIMESTAMP
            case 'year':    // date: YEAR
            case 'time':    // date: TIME
            case 'datetime':    // date: DATETIME
                $result = true; break;
            default:
                $result = true; break;
        }
        return $result;
    }

    //--------------------------------------------------------------------------
    // check if field type is a date/time type
    //--------------------------------------------------------------------------
    function isDate($field_name){
        $field_type = $this->getFieldInfo($field_name, 'type', 0);
        $result = false;
        switch (strtolower($field_type)){
            case 'date':    // date: DATE
            case 'timestamp':    // date: TIMESTAMP
            case 'year':    // date: YEAR
            case 'time':    // date: TIME
            case 'datetime':    // date: DATETIME
                $result = true; break;
            default:
                $result = false; break;
        }
        return $result;
    }

    //--------------------------------------------------------------------------
    // check if a field is readonly
    //--------------------------------------------------------------------------
    function isReadonly($field_name){
        $field_property_readonly = $this->getFieldProperty($field_name, "readonly");
        if($field_property_readonly == true){
            return true;
        }else{
            return false;
        }
    }

    //--------------------------------------------------------------------------
    // check if a field is validator
    //--------------------------------------------------------------------------
    function isValidator($field_name){
        $field_property_type = $this->getFieldProperty($field_name, "type");
        if($field_property_type == "validator"){
            return true;
        }else{
            return false;
        }
    }

    //--------------------------------------------------------------------------
    // check if a field is a foreign key
    //--------------------------------------------------------------------------
    function isForeignKey($field_name){
        if(array_key_exists($field_name, $this->foreign_keys_array)){
           return true;
        }
        return false;
    }

    //--------------------------------------------------------------------------
    // get foreign key input
    //--------------------------------------------------------------------------
    function getForeignKeyInput($keyFieldValue, $fk_field_name, $fk_field_value, $mode="edit"){
        $req_mode = $this->getVariable('mode');

        // check if foreign key field is readonly or disabled
        $readonly = "";
        $disabled = "";
        $field_property_readonly = $this->getFieldProperty($fk_field_name, "readonly");
        $field_property_radiobuttons_alignment = "horizontal";
        if(isset($this->foreign_keys_array[$fk_field_name]['radiobuttons_alignment']) && (strtolower($this->foreign_keys_array[$fk_field_name]['radiobuttons_alignment']) == "vertical")){
            $field_property_radiobuttons_alignment = "vertical";
        }
        if($req_mode == "edit"){
            if($field_property_readonly == true){
                $disabled = "disabled"; //$readonly = "readonly='readonly'";
            }
        }

        $sql  = " SELECT ".$fk_field_name;
        $sql .= " FROM ".$this->tbl_name;
        $sql .= " WHERE ".$this->primary_key." = '".$keyFieldValue."' ";
        $this->db_handler->setFetchMode(DB_FETCHMODE_ASSOC);
        $dSet = $this->db_handler->query($sql);
        if($dSet->numRows() > 0){
            $row = $dSet->fetchRow();
            $kFieldValue = $row[$fk_field_name];
        }else{
            $kFieldValue = -1;
        }
        // select from outer table
        $sql  = " SELECT ".$this->foreign_keys_array[$fk_field_name]['field_key'].",".$this->foreign_keys_array[$fk_field_name]['field_name'].", ".$this->foreign_keys_array[$fk_field_name]['table'].".* ";
        $sql .= " FROM ".$this->foreign_keys_array[$fk_field_name]['table'] ;
        $sql .= " WHERE 1=1 ";
        if($mode !== "edit"){
            $sql .= " AND " .$this->foreign_keys_array[$fk_field_name]['field_key']."='".$kFieldValue."'";
        }
        if(isset($this->foreign_keys_array[$fk_field_name]['condition']) && ($this->foreign_keys_array[$fk_field_name]['condition'] != "")){
            $sql .= " AND " .$this->foreign_keys_array[$fk_field_name]['condition'];
        }
        // define sorting order
        if(isset($this->foreign_keys_array[$fk_field_name]['order_by_field']) && ($this->foreign_keys_array[$fk_field_name]['order_by_field'] != "")){
            $order_by_field = $this->foreign_keys_array[$fk_field_name]['order_by_field'];
        }else{
            $order_by_field = $this->foreign_keys_array[$fk_field_name]['field_key'];
        }
        // define sorting type
        if(isset($this->foreign_keys_array[$fk_field_name]['order_type']) && ($this->foreign_keys_array[$fk_field_name]['order_type'] != "")){
            $order_type = $this->foreign_keys_array[$fk_field_name]['order_type'];
        }else{
            $order_type = "ASC";
        }
        $sql .= " ORDER BY ".$order_by_field." ".$order_type;

        $dSet = $this->db_handler->query($sql);

        if($this->db_handler->isError($dSet) == 1){
            $this->is_error = true;
            $this->addErrors($dSet);
        }

        if($this->debug){
            if($this->db_handler->isError($dSet) == 1){
                $num_rows = 0;
            }else{
                $num_rows = $dSet->numRows();
            }
            echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left' wrap><b>search sql (".$this->strToLower($this->lang['total']).": ".$num_rows.") </b>". $sql."</td></tr></table><br />";
        }

        if($mode === "edit"){
            // save entered values from fields in add/edit modes
            $req_field_value = $this->getVariable($this->getFieldRequiredType($fk_field_name).$fk_field_name, false, "post");

            $on_js_event = (isset($this->foreign_keys_array[$fk_field_name]['on_js_event'])) ? $this->foreign_keys_array[$fk_field_name]['on_js_event'] : "";
            $view_type = (isset($this->foreign_keys_array[$fk_field_name]['view_type'])) ? $this->foreign_keys_array[$fk_field_name]['view_type'] : "";
            if($view_type == "textbox"){ //'view_type"=>"textbox"
                while($row = $dSet->fetchRow()){
                    if($row[$this->foreign_keys_array[$fk_field_name]['field_key']] === $kFieldValue){
                        $kFieldValue = $row[$this->foreign_keys_array[$fk_field_name]['field_name']];
                        $kFieldValue = str_replace('"', "&quot;", $kFieldValue); // double quotation mark
                        $kFieldValue = str_replace("'", "&#039;", $kFieldValue); // single quotation mark
                    }
                }
                return "<input class='".$this->css_class."_class_textbox' type='text' title='".$this->getFieldTitle($fk_field_name)."' id='".$this->getFieldRequiredType($fk_field_name).$fk_field_name."' name='".$this->getFieldRequiredType($fk_field_name).$fk_field_name."' value='".$kFieldValue."' $disabled ".$on_js_event.">";
            }else if($view_type == "radiobutton"){ //'view_type"=>"radiobutton"
                return $this->drawRadioButtons($this->getFieldRequiredType($fk_field_name).$fk_field_name, $fk_field_name, $dSet, $kFieldValue, 'field_key', 'field_name', $disabled, $on_js_event, $field_property_radiobuttons_alignment);
            }else { //'view_type"=>"dropdownlist" - default
                $req_field_name = $this->getVariable($this->getFieldRequiredType($fk_field_name).$fk_field_name, false, "post");
                if($req_mode == "add"){
                    if($req_field_name != "") $req_field_value = $req_field_value;
                    else $req_field_value = $this->getFieldProperty($fk_field_name, "default");
                }else {
                    if($req_field_name != "") $req_field_value = $req_field_value;
                    else $req_field_value = $fk_field_value;
                }
                return $this->drawDropDownList($this->getFieldRequiredType($fk_field_name).$fk_field_name, '', $dSet, $req_field_value, $fk_field_name, 'field_key', 'field_name', $disabled, $on_js_event);
            }
        }else{
            $row = $dSet->fetchRow();
            $ff_name = $this->foreign_keys_array[$fk_field_name]['field_name'];
            if (eregi(" as ", strtolower($ff_name))) $ff_name = substr($ff_name, strpos(strtolower($ff_name), " as ")+4);
            return $this->nbsp.$row[$ff_name].$this->nbsp;
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // URL string creating
    ////////////////////////////////////////////////////////////////////////////
    //--------------------------------------------------------------------------
    // setUrl
    //--------------------------------------------------------------------------
    function setUrlString(&$curr_url, $filtering = "", $sorting = "", $paging ="", $amp=""){
        $amp = ($amp != "") ? $amp : $this->amp;
        if($filtering != "") $this->setUrlStringFiltering($curr_url, $amp);
        if($sorting != "") $this->setUrlStringSorting($curr_url, $amp);
        if($paging != "") $this->setUrlStringPaging($curr_url, $amp);
    }

    //--------------------------------------------------------------------------
    // setUrlString Filtering
    //--------------------------------------------------------------------------
    function setUrlStringFiltering(&$curr_url, $amp=""){
        $amp = ($amp != "") ? $amp : $this->amp;
        $req_onSUBMIT_FILTER = $this->getVariable('_ff_onSUBMIT_FILTER');

        if($this->filtering_allowed){
            foreach($this->filter_fields as $fldField){
                $table_field_name = "".$fldField['table']."_".$fldField['field'];
                if(isset($_REQUEST[$this->unique_prefix."_ff_".$table_field_name]) AND ($_REQUEST[$this->unique_prefix."_ff_".$table_field_name] != "")){
                    $curr_url .= $amp.$this->unique_prefix."_ff_".$fldField['table'].'_'.$fldField['field']."=".urlencode($_REQUEST[$this->unique_prefix."_ff_".$table_field_name])."";
                }
                $table_field_name_operator = "".$fldField['table']."_".$fldField['field']."_operator";
                if(isset($_REQUEST[$this->unique_prefix."_ff_".$table_field_name_operator]) AND ($_REQUEST[$this->unique_prefix."_ff_".$table_field_name_operator] != "")){
                    $curr_url .= $amp.$this->unique_prefix."_ff_".$fldField['table'].'_'.$fldField['field']."_operator=".urlencode($_REQUEST[$this->unique_prefix."_ff_".$table_field_name_operator])."";
                }
            }
            if(isset($_REQUEST[$this->unique_prefix."_ff_".'selSearchType']) && (trim($_REQUEST[$this->unique_prefix."_ff_".'selSearchType']) != ""))
                $curr_url .= $amp.$this->unique_prefix."_ff_"."selSearchType=".urlencode($_REQUEST[$this->unique_prefix."_ff_".'selSearchType'])."";
            if($req_onSUBMIT_FILTER != "")
                $curr_url .= $amp.$this->unique_prefix."_ff_"."onSUBMIT_FILTER=search";
        }
    }

    //--------------------------------------------------------------------------
    // setUrlString Sorting
    //--------------------------------------------------------------------------
    function setUrlStringSorting(&$curr_url, $amp=""){
        $amp = ($amp != "") ? $amp : $this->amp;
        $sort_field = $this->getVariable('sort_field');
        $sort_field_by = $this->getVariable('sort_field_by');
        $sort_type = $this->getVariable('sort_type');
        if($sort_field != "") {
           $this->sort_field = $sort_field;
           $this->sort_field_by = $sort_field_by;
           $curr_url .= $amp.$this->unique_prefix."sort_field=".$this->sort_field.$amp.$this->unique_prefix."sort_field_by=".$this->sort_field_by;
        }else {
            if(!is_numeric($this->default_sort_field[0])){ $this->default_sort_field[0] = $this->getFieldOffset($this->default_sort_field[0]) + 1; }
            $curr_url .= $amp.$this->unique_prefix."sort_field=".$this->default_sort_field[0];
        }; // make pKey
        if($sort_type != "") {
            $this->sort_type = $sort_type;
            $curr_url .= $amp.$this->unique_prefix."sort_type=".$this->sort_type;
        } else {
            $curr_url .= $amp.$this->unique_prefix."sort_type=".$this->default_sort_type[0];
        };
    }

    //--------------------------------------------------------------------------
    // setUrlString Pading
    //--------------------------------------------------------------------------
    function setUrlStringPaging(&$curr_url, $amp=""){
        $amp = ($amp != "") ? $amp : $this->amp;
        $page_size = $this->getVariable('page_size');
        $p = $this->getVariable('p');
        if($this->layouts['edit'] == "0"){
            if($page_size != ""){
                $this->req_page_size = $page_size;
                $curr_url .= $amp.$this->unique_prefix."page_size=".$this->req_page_size;
            }else{
                $curr_url .= $amp.$this->unique_prefix."page_size=".$this->req_page_size;
            }
        }else{
            if($this->mode === "view"){
                $curr_url .= $amp.$this->unique_prefix."page_size=".$this->req_page_size;
            }else{
                if($page_size != ""){
                    $this->req_page_size = $page_size;
                }else{
                    if($this->mode == "edit"){
                        $this->req_page_size = $this->default_page_size;
                    }
                }
                $curr_url .= $amp.$this->unique_prefix."page_size=".$this->req_page_size;
            }
        }
        if($p != "") {
            $this->page_current = $p;
            $curr_url .= $amp.$this->unique_prefix."p=".$this->page_current;
        }else {
            $this->page_current = 1;
            $curr_url .= $amp.$this->unique_prefix."p=1";
        };
    }

    ////////////////////////////////////////////////////////////////////////////
    // View & Edit mode methods
    ////////////////////////////////////////////////////////////////////////////
    //--------------------------------------------------------------------------
    // get enum values
    //--------------------------------------------------------------------------
    function getEnumValues( $table , $field ){
        $enum_array = "";
        $query = " SHOW COLUMNS FROM $table LIKE '$field' ";
        $this->db_handler->setFetchMode(DB_FETCHMODE_ORDERED);
        $result = $this->db_handler->query($query);
        if($row = $result->fetchRow()){
            // extract the values, the values are enclosed in single quotes and separated by commas
            $regex = "/'(.*?)'/";
            preg_match_all( $regex , $row[1], $enum_array );
            $temp_enum_fields = $enum_array[1];
            $enum_fields = array();
            foreach($temp_enum_fields as $key => $val){
                $enum_fields[$val] = $val;
            }
            return $enum_fields ;
        }else{
            return array();
        }
    }

    //--------------------------------------------------------------------------
    // check if field exists & can be viewed
    //--------------------------------------------------------------------------
    function canViewField($field_name){
        $field_property_visible =  $this->getFieldProperty($field_name, "visible", $this->mode, "lower", "true");
        if($this->mode === "view"){
            if(array_key_exists($field_name, $this->columns_view_mode) && ($field_property_visible == true)) return true;
        }else if($this->mode === "edit"){
            if(array_key_exists($field_name, $this->columns_edit_mode) && ($field_property_visible == true)) return true;
        }else if($this->mode === "details"){
            if(array_key_exists($field_name, $this->columns_edit_mode) && ($field_property_visible == true)) return true;
        }
        return false;
    }
    //--------------------------------------------------------------------------
    // check if field exists & can be viewed
    //--------------------------------------------------------------------------
    function getHeaderName($field_name, $force_simple = false){
        $force_simple = (($force_simple == true) || ($force_simple == "true")) ? true : false ;
        $field_property_header = $this->getFieldProperty($field_name, "header", $this->mode, "normal");
        if($this->mode === "view"){
            if(array_key_exists($field_name, $this->columns_view_mode) && ($field_property_header != "")){
                return $field_property_header;
            }
        }else if($this->mode === "edit"){
            if(array_key_exists($field_name, $this->columns_edit_mode) && ($field_property_header != "")){
                if((substr($this->getFieldRequiredType($field_name), 0, 1) == "r") && (!$force_simple)){
                    return "<font color='#cd0000'>*</font> ".ucfirst($field_property_header);
                }else{
                    return $field_property_header;
                }
            }
        }else if($this->mode === "details"){
            if(array_key_exists($field_name, $this->columns_edit_mode) && ($field_property_header != "")){
                return $field_property_header;
            }
        }
        return $field_name;
    }

    //--------------------------------------------------------------------------
    // Returns field name
    //--------------------------------------------------------------------------
    function getFieldName($field_offset){
        if(!$this->is_error){
            $fields = $this->data_set->tableInfo();
            $field_name = isset($fields[$field_offset]['name']) ? $fields[$field_offset]['name'] : "";
            if($field_name) return $field_name;
        }
        return $field_offset;
    }

    //--------------------------------------------------------------------------
    // get Field Required Type
    //--------------------------------------------------------------------------
    function getFieldRequiredType($field_name, $validator=false){
        $field_prefix = "syy";
        $field_req_type = trim($this->getFieldProperty($field_name, "req_type"));
        if($field_req_type != ""){
            if(strlen($field_req_type) == 3){ $field_prefix = $field_req_type; }
            else if(strlen($field_req_type) == 2){
                $field_prefix = $field_req_type."y";
            }
        }
        if($validator) $field_prefix[1] = "v";
        return $field_prefix;
    }

    //--------------------------------------------------------------------------
    // get Field Property
    //--------------------------------------------------------------------------
    function getFieldProperty($field_name, $property_name, $mode = "edit", $case = "lower", $return_value = ""){
        switch($mode){
            case "view":
                if(isset($this->columns_view_mode[$field_name][$property_name])){
                    if($case === "lower") {
                        $return_value =  strtolower($this->columns_view_mode[$field_name][$property_name]);
                    } else {
                        $return_value = $this->columns_view_mode[$field_name][$property_name];
                    }
                }
                break;
            case "filter":
                if(isset($this->filter_fields[$field_name][$property_name])){
                    if($case === "lower") {
                        $return_value =  strtolower($this->filter_fields[$field_name][$property_name]);
                    } else {
                        $return_value = $this->filter_fields[$field_name][$property_name];
                    }
                }
                break;
            case "details":
            case "edit":
            default:
                if(isset($this->columns_edit_mode[$field_name][$property_name])){
                    if($case === "lower") {
                        if(is_array($this->columns_edit_mode[$field_name][$property_name])){
                            return $this->columns_edit_mode[$field_name][$property_name];
                        }else{
                            $return_value = strtolower($this->columns_edit_mode[$field_name][$property_name]);
                        }
                    } else {
                        $return_value = $this->columns_edit_mode[$field_name][$property_name];
                    }
                }
                break;
        }
        if($return_value == "true"){
            $return_value = true;
        }else if($return_value == "false"){
            $return_value = false;
        }
        return $return_value;
    }

    //--------------------------------------------------------------------------
    // get Field Title
    //--------------------------------------------------------------------------
    function getFieldTitle($field_name, $mode="edit"){
        $field_title = $this->getFieldProperty($field_name, "title", $mode, "");
        if($field_title === ""){
            $field_header = $this->getFieldProperty($field_name, "header", $mode);
            if($field_header === ""){
                return $field_name;
            }else{
                return str_replace("'", "&#039;", $field_header);
            }
        }else{
            return $field_title;
        }
    }

    //--------------------------------------------------------------------------
    // get Field Alignment
    //--------------------------------------------------------------------------
    function getFieldAlign($ind, $row, $mode="view"){
        $field_name = $this->getFieldName($ind);
        $field_align = $this->getFieldProperty($field_name, "align", $mode);
        if(($mode == "view") && ($field_align != "")){
            return $field_align;
        }else if(($mode != "view") && ($field_align != "")){
            return $field_align;
        }else{
            if(isset($row[$ind]) && $this->isText($field_name)){
                return ($this->direction == "ltr")?"left":"right";
            }else{
                return ($this->direction == "ltr")?"right":"left";
            }
        }
    }

    //--------------------------------------------------------------------------
    // get Field Value By Type
    //--------------------------------------------------------------------------
    function getFieldValueByType($field_value, $ind, $row, $field_name="", $m_field_req_type=""){
        // Un-quote string quoted by mysql_real_escape_string()
        if(get_magic_quotes_gpc()) {
            if(ini_get('magic_quotes_sybase')) {
                $field_value = str_replace("''", "'", $field_value);
            } else {
                $field_value = stripslashes($field_value);
            }
        }

        $req_print = $this->getVariable('print');
        $req_mode = $this->getVariable('mode');

        if($field_name == "") $field_name = $this->getFieldName($ind);
        // -= VIEW MODE =-
        if($this->mode === "view"){
            if(array_key_exists($field_name, $this->columns_view_mode)){

                $fp_tooltip = $this->getFieldProperty($field_name, "tooltip", "view");
                $fp_tooltip_type = $this->getFieldProperty($field_name, "tooltip_type", "view");
                $field_property_pre_addition   = $this->getFieldProperty($field_name, "pre_addition", "view");
                $field_property_post_addition  = $this->getFieldProperty($field_name, "post_addition", "view");
                $field_property_on_item_created = $this->getFieldProperty($field_name, "on_item_created", "view");
                $field_property_text_length    = $this->getFieldProperty($field_name, "text_length", "view");
                $field_property_type           = $this->getFieldProperty($field_name, "type", "view");
                $field_property_case           = $this->getFieldProperty($field_name, "case", "view");
                $field_property_on_js_event    = $this->getFieldProperty($field_name, "on_js_event", "view", "normal");

                // customized working with field value
                if(function_exists($field_property_on_item_created)) $field_value = $field_property_on_item_created($field_value);

                $title = "";
                if(($field_property_text_length != "-1") && ($field_property_text_length != "") && ($field_value != "")){
                    if((strlen($field_value)) > $field_property_text_length){
                        $field_value = str_replace('"', "&quot;", $field_value); // double quotation mark
                        $field_value = str_replace("'", "&#039;", $field_value); // single quotation mark
                        $field_value = str_replace(chr(13), "", $field_value);   // CR sign
                        $field_value = str_replace(chr(10), " ", $field_value);  // LF sign
                        if($req_print != true){
                            if(($fp_tooltip == true) || ($fp_tooltip == "true")){
                                if($fp_tooltip_type == "floating"){
                                    $title = " onmouseover=\"return overlib('".$field_value."');\" onmouseout='return nd();' style='cursor: help;'";
                                }else{
                                    $title = "title='".$field_value."' style='cursor: help;'";
                                }
                            }
                        }
                        $field_value = substr($field_value, 0, $field_property_text_length)."...";
                    }
                }

                $field_type = ($field_property_type == "") ? "label" : $field_property_type;
                // format case of field value
                if($field_property_case == ""){
                    $field_value = $field_value;
                }else if(strtolower($field_property_case) == "upper"){
                    $field_value = strtoupper($field_value);
                }else if(strtolower($field_property_case) == "lower") {
                    $field_value = $this->strToLower($field_value);
                }
                if($req_print == true){ $field_type = "label"; }
                $on_js_event = $field_property_on_js_event;

                switch($field_type){
                    case "barchart":
                        $field_property_field = $this->getFieldProperty($field_name, "field", "view");
                        $field_property_maximum_value = $this->getFieldProperty($field_name, "maximum_value", "view");
                        if(($field_property_field != "") && ($this->getFieldOffset($field_property_field) != -1)) $field_value = $row[$this->getFieldOffset($field_property_field)];
                        $barchart_result ="
                        <table width='110px;' bgcolor='#dddddd' height='10px' align='center' cellpadding='0' cellspacing='0' ".$on_js_event.">
                        <tr title='".$field_value."'>
                        <td style='FONT-SIZE:9px;' align='center' width='".($field_value/$field_property_maximum_value * 100)."px' bgcolor='#999999' class='class_nowrap'>
                        ".(($field_value > 0) ? $field_value : "")."
                        </td>
                        <td style='FONT-SIZE:9px;' width='".(100 - ($field_value/$field_property_maximum_value * 100))."px' align='center' class='class_nowrap'>
                        ".(($field_value == 0) ? $field_value : "")."
                        </td>
                        </tr>
                        </table>";
                        return $barchart_result;
                        break;
                    case "image":
                        $field_property_align        = $this->getFieldProperty($field_name, "align", "view", "lower", "center");
                        $field_property_target_path  = $this->getFieldProperty($field_name, "target_path", "view");
                        $field_property_image_width  = $this->getFieldProperty($field_name, "image_width", "view", "lower", "50px");
                        $field_property_image_height = $this->getFieldProperty($field_name, "image_height", "view", "lower", "30px");
                        $field_property_default      = $this->getFieldProperty($field_name, "default", "view", "normal");
                        if($field_property_default != ""){
                            if(file_exists(trim($field_property_default))){
                                $img_default = "<img src='".$field_property_default."' width='".$field_property_image_width."' height='".$field_property_image_height."' alt='' title='' ".$on_js_event." />";
                            }else{
                                $img_default = "<span class='".$this->css_class."_class_label' ".$on_js_event.">".$this->lang['no_image']."</span>";
                            }
                        }else{
                            $img_default = "";
                        }
                        if((trim($field_value) !== "") && file_exists($field_property_target_path.trim($field_value))){
                            return $this->nbsp."<img src='".$field_property_target_path.trim($field_value)."' border='1' width='".$field_property_image_width."' height='".$field_property_image_height."' align='middle' ".$on_js_event." />".$this->nbsp;
                        }else{
                            return "<table align='".$field_property_align."' style='BORDER: solid 0px #000000;' width='".$field_property_image_width."' height='".$field_property_image_height."'><tr><td align='".$field_property_align."'>".$img_default."</td></tr></table>";
                        }
                        break;
                    case "label":
                        if((trim($field_value) != "")
                            // we need this for right handling wysiwyg editor values
                            && (trim($this->strToLower($field_value)) !== "<pre></pre>")
                            && (trim($this->strToLower($field_value)) !== "<pre>".$this->nbsp."</pre>")
                            && (trim($this->strToLower($field_value)) !== "<p></p>")
                            && (trim($this->strToLower($field_value)) !== "<p>".$this->nbsp."</p>")){
                            return $field_property_pre_addition.$this->nbsp."<label class='".$this->css_class."_class_label' ".$title." ".$on_js_event.">".trim($field_value)."</label>".$this->nbsp.$field_property_post_addition;
                        }else{
                            return "&nbsp;&nbsp;";
                        }
                        break;
                    case "link":
                    case "linkbutton":
                        $field_property_field_data = $this->getFieldProperty($field_name, "field_data", "view", "normal");
                        $field_property_rel        = $this->getFieldProperty($field_name, "rel", "view");
                        $field_property_href       = $this->getFieldProperty($field_name, "href", "view");
                        $field_property_target     = $this->getFieldProperty($field_name, "target", "view");

                        if($field_property_field_data != ""){
                            $rel = ($field_property_rel != "") ? "rel=".$field_property_rel : "";
                            $title = $this->getFieldTitle($field_name, "view");
                            $href = $field_property_href;
                            foreach ($this->columns_view_mode[$field_name] as $search_field_key => $search_field_value){
                                if(substr($search_field_key, 0, 9) == "field_key"){
                                    $field_number = intval(substr($search_field_key, 10, strlen($search_field_key) - 10));
                                    $field_inner = ($this->getFieldOffset($search_field_value) != "-1") ? $row[$this->getFieldOffset($search_field_value)] : "";
                                    if(strpos($field_property_href, "{".$field_number."}") >= 0){
                                        $href = str_replace("{".$field_number."}", $field_inner, $href);
                                    }
                                }
                            }
                            // remove unexpected 'http://'s
                            if(strstr($field_property_href, "http://") != ""){
                                $href = str_replace("http://", "", $href);
                                $href = "http://".$href;
                            }
                            return $this->nbsp."<a class='".$this->css_class."_class_a2' href='".$href."' target='".$field_property_target."' ".$rel." title='".$title."' ".$on_js_event.">".trim($row[$this->getFieldOffset($field_property_field_data)])."</a>".$this->nbsp;
                        }else{
                            return $this->nbsp;
                        }
                        break;
                    case "linktoview";
                        $curr_url = $this->combineUrl("details", intval($row[(($this->getFieldOffset($this->primary_key) != -1) ? $this->getFieldOffset($this->primary_key) : 0)]));
                        $this->setUrlStringPaging($curr_url);
                        $this->setUrlStringSorting($curr_url);
                        $this->setUrlStringFiltering($curr_url);
                        return $this->nbsp."<a class='".$this->css_class."_class_a' href='$curr_url' ".$title." ".$on_js_event.">".trim($field_value)."</a>".$this->nbsp;
                        break;
                    case "linktoedit";
                        $curr_url = $this->combineUrl("edit", $row[(($this->getFieldOffset($this->primary_key) != -1) ? $this->getFieldOffset($this->primary_key) : 0)]);
                        $this->setUrlStringPaging($curr_url);
                        $this->setUrlStringSorting($curr_url);
                        $this->setUrlStringFiltering($curr_url);
                        return $this->nbsp."<a class='".$this->css_class."_class_a' href='$curr_url' ".$title." ".$on_js_event.">".trim($field_value)."</a>".$this->nbsp;
                        break;
                    case "password":
                        return $this->nbsp."<label class='".$this->css_class."_class_label' ".$title." ".$on_js_event.">******</label>".$this->nbsp;
                        break;
                    default:
                        return $this->nbsp."<label class='".$this->css_class."_class_label' ".$title." ".$on_js_event.">".trim($field_value)."</label>".$this->nbsp; break;
                }
            }
        // -= ADD / EDIT / DETAILS MODE =-
        }else if(($this->mode === "edit") || ($this->mode === "details")){

            if(array_key_exists($field_name, $this->columns_edit_mode)){
                $field_property_maxlength       = $this->getFieldProperty($field_name, "maxlength");
                $field_property_type            = $this->getFieldProperty($field_name, "type");
                $field_property_req_type        = ($m_field_req_type != "") ? $m_field_req_type : $this->getFieldProperty($field_name, "req_type");
                $field_property_width           = $this->getFieldProperty($field_name, "width");
                $field_property_readonly        = $this->getFieldProperty($field_name, "readonly");
                $field_property_default         = $this->getFieldProperty($field_name, "default", "edit", "normal");
                $field_property_on_js_event     = $this->getFieldProperty($field_name, "on_js_event", "edit", "normal");
                $field_property_calendar_type   = $this->getFieldProperty($field_name, "calendar_type");
                $field_property_pre_addition    = $this->getFieldProperty($field_name, "pre_addition");
                $field_property_post_addition   = $this->getFieldProperty($field_name, "post_addition");
                $field_property_on_item_created = $this->getFieldProperty($field_name, "on_item_created", "edit");
                $field_property_autocomplete    = $this->getFieldProperty($field_name, "autocomplete");
                if($field_property_autocomplete == "off") $autocomplete = "autocomplete='off'"; else $autocomplete = "";

                // customized working with field value
                if(function_exists($field_property_on_item_created)) $field_value = $field_property_on_item_created($field_value);

                // detect maxlength for the current field
                $field_maxlength = $this->getFieldInfo($field_name, 'len', 0);
                if($field_maxlength <= 0) $field_maxlength = "";
                else $field_maxlength = "maxlength='".$field_maxlength."'";
                if($field_property_maxlength == ""){
                    if(!$this->isText($field_name)){ $field_maxlength = "maxlength='50'"; }
                }else{
                    if(($field_property_maxlength != "-1") && ($field_property_maxlength != "")){
                        $field_maxlength = "maxlength='".$field_property_maxlength."'";
                    }
                }
                // detect field's type
                if($field_property_type == ""){ $field_type = "label"; } else $field_type = $field_property_type;
                // get required prefix for a field
                $field_req_type = $field_property_req_type;
                if(strlen(trim($field_req_type)) == 3){ $field_req_type = $field_req_type; }
                else if(strlen(trim($field_req_type)) == 2){ $field_req_type = $field_req_type."y"; }
                else { $field_req_type = "syy"; }
                // detect field's width
                if($field_property_width != "") $field_width = "style='width:".$field_property_width.";'"; else $field_width = "";
                // detect field's readonly property
                if($field_property_readonly == true) { $readonly = "readonly='readonly'"; $disabled = "disabled"; }
                else { $readonly = ""; $disabled = ""; }
                if($req_print == true){ $field_type = "print"; }
                // get default value of field
                if(($req_mode == "add") || ($field_type == "hidden")){
                    if($field_property_default != "") { $field_value = $field_property_default; }
                    ///d 4.02.08 && ($field_property_default != 0)
                }
                $on_js_event = $field_property_on_js_event;

                if ($this->mode === "edit"){
                    // save entered values from fields in add/edit modes
                    $req_field_value = $this->getVariable($field_req_type.$field_name, false, "post");
                    if($req_field_value != "") $field_value = $req_field_value;
                    switch($field_type){
                        case "checkbox":
                            $checked = "";
                            $field_property_true_value = $this->getFieldProperty($field_name, "true_value");
                            $field_property_false_value = $this->getFieldProperty($field_name, "false_value");
                            if(($field_property_true_value != "") && ($field_property_false_value != "")){
                                if($field_value == $field_property_true_value){
                                    $checked = "checked";
                                }
                            }
                            echo $field_property_pre_addition.$this->nbsp."<input class='".$this->css_class."_class_checkbox' type='checkbox' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' title='".$this->getFieldTitle($field_name)."' value='".trim($field_value)."' ".$checked." ".$readonly." ".$on_js_event.">".$this->nbsp.$field_property_post_addition;
                            break;
                        case "date":
                            return $this->drawCalendarButton($field_name, $field_type, "Y-m-d", $field_value, $field_property_pre_addition, $field_property_post_addition, $field_width, $field_maxlength, $on_js_event, $readonly, $field_property_calendar_type);
                            break;
                        case "datedmy":
                            return $this->drawCalendarButton($field_name, $field_type, "d-m-Y", $field_value, $field_property_pre_addition, $field_property_post_addition, $field_width, $field_maxlength, $on_js_event, $readonly, $field_property_calendar_type);
                            break;
                        case "datetime":
                            return $this->drawCalendarButton($field_name, $field_type, "Y-m-d H:i:s", $field_value, $field_property_pre_addition, $field_property_post_addition, $field_width, $field_maxlength, $on_js_event, $readonly, $field_property_calendar_type);
                            break;
                        case "datetimedmy":
                            return $this->drawCalendarButton($field_name, $field_type, "d-m-Y H:i:s", $field_value, $field_property_pre_addition, $field_property_post_addition, $field_width, $field_maxlength, $on_js_event, $readonly, $field_property_calendar_type);
                            break;
                        case "enum":
                            $ret_enum = "";
                            $field_property_view_type = $this->getFieldProperty($field_name, "view_type");
                            $field_property_radiobuttons_alignment = $this->getFieldProperty($field_name, "radiobuttons_alignment");
                            if($this->getFieldProperty($field_name, "multiple") == true){ $enum_multiple = true; } else { $enum_multiple = false; }
                            $field_property_multiple_size = $this->getFieldProperty($field_name, "multiple_size", "edit", "lower", "4");
                            switch($field_property_view_type){
                                case "radiobutton":
                                    if(is_array($this->columns_edit_mode[$field_name]["source"])){  // don't remove columns_edit_mode
                                        $ret_enum .= $this->nbsp.$this->drawRadioButtons($this->getFieldRequiredType($field_name).$field_name, $field_name, $this->columns_edit_mode[$field_name]["source"], $field_value, 'source', "", $disabled, $on_js_event, $field_property_radiobuttons_alignment).$this->nbsp;
                                    }else{
                                        $ret_enum .= $this->nbsp.$this->drawRadioButtons($this->getFieldRequiredType($field_name).$field_name, $field_name, $this->getEnumValues($this->tbl_name, $field_name), $field_value, 'source', "", $disabled, $on_js_event, $field_property_radiobuttons_alignment).$this->nbsp;
                                    }
                                    break;
                                case "dropdownlist":
                                default:
                                    if(is_array($this->columns_edit_mode[$field_name]["source"])){  // don't remove columns_edit_mode
                                        $ret_enum .= $this->nbsp.$this->drawDropDownList($this->getFieldRequiredType($field_name).$field_name, '', $this->columns_edit_mode[$field_name]["source"], $field_value, "", "", "", $disabled, $on_js_event, $enum_multiple, $field_property_multiple_size).$this->nbsp;
                                    }else{
                                        $ret_enum .= $this->nbsp.$this->drawDropDownList($this->getFieldRequiredType($field_name).$field_name, '', $this->getEnumValues($this->tbl_name, $field_name), trim($field_value), "", "", "", $disabled, $on_js_event, $enum_multiple, $field_property_multiple_size).$this->nbsp;
                                    }
                                    break;
                            }
                            return $ret_enum;
                            break;
                        case "hidden":
                            $ret_hidden  ="<input type='hidden' id='".$this->getFieldRequiredType($field_name).$field_name."' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' value='".trim($field_value)."' />";
                            return $ret_hidden;
                            break;
                        case "image":
                        case "file":
                            $ret_file = "";
                            $file = false;
                            $file_error_msg = "";
                            $file_name_view = $field_value;
                            $file_act = $this->getVariable('file_act');
                            $file_id = $this->getVariable('file_id');
                            $rid = $this->getVariable('rid');
                            // where the file is going to be placed
                            $field_property_target_path   = $this->getFieldProperty($field_name, "target_path");
                            $field_property_file_name     = $this->getFieldProperty($field_name, "file_name");
                            $field_property_image_width   = $this->getFieldProperty($field_name, "image_width", "edit", "lower", "120px");
                            $field_property_image_height  = $this->getFieldProperty($field_name, "image_height", "edit", "lower", "90px");
                            $field_property_max_file_size = $this->getFieldProperty($field_name, "max_file_size");

                            if($this->getFieldProperty($field_name, "host") == "remote"){
                                // *** upload file from url (remote host)
                                $ret_file = "";
                                if(trim($field_value) == ""){
                                    if(($file_act == "upload") && ($file_id == $field_name)){
                                        $file_error_msg = $this->lang['file_uploading_error'];
                                        $file = false;
                                    }
                                }else{
                                    if(($file_act == "remove") && ($file_id == $field_name)){
                                        $sql = "UPDATE $this->tbl_name SET ".$field_name." = '' WHERE $this->primary_key = '".$rid."' ";
                                        $this->db_handler->query($sql);
                                        // delete file from target path
                                        if(file_exists($field_property_target_path.$field_value)){ unlink($field_property_target_path.$field_value); }
                                        else{ $file_error_msg = $this->lang['file_deleting_error']; }
                                        $file = false;
                                    }else if(($file_act == "upload") && ($file_id == $field_name)){
                                        if($downloaded_file = fopen($field_value, "r")){
                                            $content = fread($downloaded_file, $this->getRemoteFileSize($field_value));
                                            // get file name from url
                                            $field_value = strrev($field_value);
                                            $last_slash = strlen($field_value) - strpos($field_value,'/');
                                            $field_value = strrev($field_value);
                                            if($last_slash) { $field_value = substr($field_value,$last_slash); }
                                            if($field_property_file_name != ""){
                                                $file_name_view = $field_property_file_name.strchr(basename($field_value),".");
                                                $field_value = $file_name_view;
                                            }
                                            if($uploaded_file = fopen($field_property_target_path.$field_value, "w")){
                                                if(!fwrite($uploaded_file, $content)){
                                                    $file_error_msg = $this->lang['file_writing_error'];
                                                    $file = false;
                                                }else{
                                                    //echo "eee";
                                                    $sql = "UPDATE $this->tbl_name SET ".$field_name;
                                                    $sql .= " = '".$field_value."' ";
                                                    $sql .= " WHERE $this->primary_key= '".$rid."' ";
                                                    $this->db_handler->query($sql);
                                                    $file = true;
                                                    fclose($uploaded_file);
                                                }
                                            }
                                            fclose($downloaded_file);
                                        }else{
                                            $file_error_msg = $this->lang['file_uploading_error'];
                                        }
                                    }else{
                                        $file = true;
                                    }
                                }
                                // if there is a file (uploaded or exists)
                                if($file == true){
                                    //echo $target_path.$field_value;
                                    if(strlen($field_value) > 40){
                                        $str_start = strlen($field_value) - 40;
                                        $str_prefix = "...";
                                    }else{
                                        $str_start = 0;
                                        $str_prefix = "";
                                    }
                                    //$ret_file .= "<input type='hidden' name='".$this->unique_prefix."file_act' id='".$this->unique_prefix."file_act' value='remove' />";
                                    $ret_file .= "<table><tr valign='middle'><td align='center'>";
                                    if($field_type == "image"){
                                        list($f_width, $f_height, $f_type, $f_attr) = getimagesize($field_property_target_path.$field_value);
                                        $f_size = number_format((filesize($field_property_target_path.$field_value)/1024),2,".",",")." Kb";
                                        $ret_file .= $this->nbsp."<img src='".$field_property_target_path.$field_value."' height='".$field_property_image_height."' width='".$field_property_image_width."' title='$field_value ($f_width x $f_height - $f_size)' alt='$field_value'/>".$this->nbsp;
                                    }else{
                                        $ret_file .= $this->nbsp.$str_prefix.substr($file_name_view, $str_start, 40).$this->nbsp;
                                    }
                                    if($field_type == "image") $ret_file .= "<br />";
                                    else $ret_file .= "&nbsp;&nbsp;";
                                    if($readonly != ""){
                                        $ret_file .= $this->nbsp."[<a class='".$this->css_class."_class_a' href='' onclick='formAction(\"remove\", \"".$field_name."\", \"".$this->unique_prefix."\", \"".$this->HTTP_URL."\", \"".$_SERVER['QUERY_STRING']."\"); return false;'><b>".$this->lang['remove']."</b></a>]".$this->nbsp;
                                    }
                                    $ret_file .= "</td></tr></table>";
                                    $ret_file .= "<input type='hidden' value='$field_value' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' />";
                                }else{
                                    //$ret_file .= "<input type='hidden' name='".$this->unique_prefix."file_act' id='".$this->unique_prefix."file_act' value='upload' />";
                                    if($file_error_msg != "") $ret_file .= $this->nbsp."<label class='".$this->css_class."_class_error_message no_print'>".$file_error_msg."</label><br />";
                                    $ret_file .= $this->nbsp."<input type='textbox' class='".$this->css_class."_class_textbox' ".$field_width." title='".$this->getFieldTitle($field_name)."' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' ".$disabled." ".$on_js_event.">&nbsp;&nbsp;";
                                    $ret_file .= "[<a class='".$this->css_class."_class_a' ".(($disabled == "disabled") ? "" : "style='cursor: pointer;' onclick='formAction(\"upload\", \"".$field_name."\", \"".$this->unique_prefix."\", \"".$this->HTTP_URL."\", \"".$_SERVER['QUERY_STRING']."\"); return false;'")."><b>".$this->lang['upload']."</b></a>]".$this->nbsp;
                                }
                                return $ret_file;

                            }else{
                                // *** upload file from local machine
                                $ret_file = "";
                                if(trim($field_value) == ""){
                                    $file = true;
                                    $file_name = $this->getFieldRequiredType($field_name).$field_name;
                                    if((count($_FILES) > 0) && ($file_id == $field_name)){
                                        if (isset($_FILES[$file_name]["error"]) && ($_FILES[$file_name]["error"] > 0)){
                                            $file_error_msg = $this->lang['file_uploading_error'];
                                            if($this->debug){ $file_error_msg .= "Error: ".$_FILES[$file_name]["error"]; }
                                            $file = false;
                                        }else{
                                            // check file's max size
                                            if($field_property_max_file_size != ""){
                                                $max_file_size = $field_property_max_file_size;
                                                if (!is_numeric($max_file_size)) {
                                                    if (strpos($max_file_size, 'm') !== false)
                                                        $max_file_size = intval($max_file_size)*1024*1024;
                                                    elseif (strpos($max_file_size, 'k') !== false)
                                                        $max_file_size = intval($max_file_size)*1024;
                                                    elseif (strpos($max_file_size, 'g') !== false)
                                                        $max_file_size = intval($max_file_size)*1024*1024*1024;
                                                }

                                                if(isset($_FILES[$file_name]["size"]) && ($_FILES[$file_name]["size"] > $max_file_size)){
                                                   $file = false;
                                                   $file_error_msg = $this->lang['file_invalid file_size'].": ".number_format(($_FILES[$file_name]["size"]/1024),2,".",",")." Kb (".$this->lang['max'].". ".number_format(($max_file_size/1024),2,".",",")." Kb) ";
                                                }
                                            }
                                        }
                                        if($file == true){
                                            // create a directory for uploading, if it was not.
                                            if (!file_exists($field_property_target_path)) { mkdir($field_property_target_path,0744); }
                                            // add the original filename to our target path. Result is "uploads/filename.extension"
                                            if($field_property_file_name != ""){
                                                $target_path_full = $field_property_target_path . $field_property_file_name.strchr(basename($_FILES[$file_name]['name']),".");
                                            }else{
                                                $target_path_full = $field_property_target_path . (isset($_FILES[$file_name]['name']) ? basename($_FILES[$file_name]['name']) : "") ;
                                            }
                                            if(isset($_FILES[$file_name]['tmp_name'])){
                                                if(move_uploaded_file($_FILES[$file_name]['tmp_name'], $target_path_full)) {
                                                    chmod($target_path_full, 0644);
                                                    $sql = "UPDATE $this->tbl_name SET ".$field_name;
                                                    if($field_property_file_name != ""){
                                                        $file_name_view = $field_property_file_name.strchr(basename($_FILES[$file_name]['name']),".");
                                                        $field_value = $field_property_file_name.strchr(basename($_FILES[$file_name]['name']),".");
                                                        $sql .= " = '".$field_property_file_name.strchr(basename($_FILES[$file_name]['name']),".")."'";
                                                    }else{
                                                        $file_name_view = $_FILES[$file_name]['name'];
                                                        $field_value = $_FILES[$file_name]['name'];
                                                        $sql .= " = '".$_FILES[$file_name]['name']."' ";
                                                    }
                                                    $sql .= " WHERE $this->primary_key= '".$rid."' ";
                                                    $dSet = $this->db_handler->query($sql);
                                                    if(($this->debug) && ($this->db_handler->isError($dSet) == 1)){
                                                        $this->is_error = true;
                                                        $this->addErrors($dSet);
                                                    }
                                                    $file = true;
                                                } else{
                                                    $file_error_msg = $this->lang['file_uploading_error'];
                                                    $file = false;
                                                }
                                            }else{
                                                $file = false;
                                            }
                                        }
                                    }else{
                                        $file = false;
                                    }
                                }else{
                                    if(($file_act == "remove") && ($file_id == $field_name)){
                                        $sql = "UPDATE $this->tbl_name SET ".$field_name." = '' WHERE $this->primary_key = '".$rid."' ";
                                        $this->db_handler->query($sql);
                                        // delete file from target path
                                        if(file_exists($field_property_target_path.$field_value)){
                                            unlink($field_property_target_path.$field_value);
                                        }else{
                                            $file_error_msg = $this->lang['file_deleting_error'];
                                        }
                                        $file = false;
                                    }else{
                                        $file = true;
                                    }
                                }
                                // if there is a file (uploaded or exists)
                                if($file == true){
                                    if(strlen($field_value) > 40){
                                        $str_start = strlen($field_value) - 40;
                                        $str_prefix = "...";
                                    }else{
                                        $str_start = 0;
                                        $str_prefix = "";
                                    }
                                    //$ret_file .= "<input type='hidden' name='".$this->unique_prefix."file_act' id='".$this->unique_prefix."file_act' value='remove' />";
                                    $ret_file .= "<table><tr valign='middle'><td align='center'>";
                                    if($field_type == "image"){
                                        $f_width = $f_height = $f_size = 0;
                                        if(file_exists($field_property_target_path.$field_value)){
                                            list($f_width, $f_height, $f_type, $f_attr) = getimagesize($field_property_target_path.$field_value);
                                            $f_size = number_format((filesize($field_property_target_path.$field_value)/1024),2,".",",")." Kb";
                                        }else{
                                            $ret_file .= $this->nbsp."<label class='".$this->css_class."_class_error_message no_print'>".$this->lang['file_uploading_error']."</label><br />";
                                        }
                                        $ret_file .= $this->nbsp."<img src='".$field_property_target_path.$field_value."' height='".$field_property_image_height."' width='".$field_property_image_width."' title='$field_value ($f_width x $f_height - $f_size)' alt='$field_value'/>".$this->nbsp;
                                    }else{
                                        $ret_file .= $this->nbsp.$str_prefix.substr($file_name_view, $str_start, 40).$this->nbsp;
                                    }
                                    if($field_type == "image") $ret_file .= "<br />";
                                    else $ret_file .= "&nbsp;&nbsp;";
                                    if($readonly != ""){
                                        $ret_file .= $this->nbsp."[<a class='".$this->css_class."_class_a' href='javascript:void(0);' onclick='formAction(\"remove\", \"".$field_name."\", \"".$this->unique_prefix."\", \"".$this->HTTP_URL."\", \"".$_SERVER['QUERY_STRING']."\"); return false;'><b>".$this->lang['remove']."</b></a>]".$this->nbsp;
                                    }
                                    $ret_file .= "</td></tr></table>";
                                    $ret_file .= "<input type='hidden' value='$field_value' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' />";
                                }else{
                                    //$ret_file .= "<input type='hidden' name='".$this->unique_prefix."file_act' id='".$this->unique_prefix."file_act' value='upload' />";
                                    if($file_error_msg != "") $ret_file .= $this->nbsp."<label class='".$this->css_class."_class_error_message no_print'>".$file_error_msg."</label><br />";
                                    $ret_file .= $this->nbsp."<input type='file' class='".$this->css_class."_class_textbox' ".$field_width." title='".$this->getFieldTitle($field_name)."' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' ".$disabled." ".$on_js_event.">&nbsp;&nbsp;";
                                    $ret_file .= "[<a class='".$this->css_class."_class_a' ".(($disabled == "disabled") ? "" : "style='cursor: pointer;' onclick='formAction(\"upload\", \"".$field_name."\", \"".$this->unique_prefix."\", \"".$this->HTTP_URL."\", \"".$_SERVER['QUERY_STRING']."\"); return false;'")."><b>".$this->lang['upload']."</b></a>]".$this->nbsp;
                                }
                                return $ret_file;
                            }
                            break;
                        case "label":
                            return $this->nbsp."<label class='".$this->css_class."_class_textbox' ".$field_width." ".$on_js_event.">".trim($field_value)."</label>".$this->nbsp;
                            break;
                        case "link":
                            return $this->nbsp."<input type='text' class='".$this->css_class."_class_textbox' ".$field_width." title='".$this->getFieldTitle($field_name)."' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' value='".trim($field_value)."' $field_maxlength $readonly ".$on_js_event.">".$this->nbsp;
                            break;
                        case "password":
                            return $this->nbsp."<input type='password' class='".$this->css_class."_class_textbox' ".$field_width." title='".$this->getFieldTitle($field_name)."' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' value='".trim($field_value)."' $field_maxlength $readonly ".$on_js_event.">".$this->nbsp;
                            break;
                        case "print":
                            return $this->nbsp."<label class='".$this->css_class."_class_label' ".$field_width.">".trim($field_value)."</label>".$this->nbsp;
                            break;
                        case "textarea":
                            $field_value = str_replace('"', "&quot;", $field_value); // double quotation mark
                            $field_value = str_replace("'", "&#039;", $field_value); // single quotation mark
                            $resizable           = $field_property_resizable = $this->getFieldProperty($field_name, "resizable", "edit", "lower", "false");
                            $field_rows          = $field_property_rows      = $this->getFieldProperty($field_name, "rows", "edit", "lower", "3");
                            $field_cols          = $field_property_cols      = $this->getFieldProperty($field_name, "cols", "edit", "lower", "23");
                            $field_edit_type     = $field_property_edit_type = $this->getFieldProperty($field_name, "edit_type");
                            $field_wysiwyg_width = $field_property_width     = $this->getFieldProperty($field_name, "width", "edit", "lower", "0");

                            $field_id = $this->getFieldRequiredType($field_name).$field_name;
                            if(strtolower($field_edit_type) == "wysiwyg") $field_maxlength = "";
                            if(($resizable == true) || ($resizable == "true")) { $field_class = "class='resizable'"; } else { $field_class = ""; };
                            $texarea  = $this->nbsp."<textarea ".$field_class." id='".$field_id."' name='".$field_id."' title='".$this->getFieldTitle($field_name)."' rows='".$field_rows."' cols='".$field_cols."' ".$field_maxlength." ".$field_width." ".$readonly." ".$on_js_event." >".trim($field_value)."</textarea>".$this->nbsp;
                            if((strtolower($this->browser_name) != "netscape") && strtolower($field_edit_type) == "wysiwyg"){
                                $texarea .= $this->nbsp."\n<script type='text/javascript'>\n";
                                $texarea .= "<!--\n";
                                $texarea .= "wysiwygWidth = ".((intval($field_wysiwyg_width) > ((9.4)*$field_cols)) ? intval($field_wysiwyg_width) : ((9.4)*$field_cols)).";";
                                $texarea .= "wysiwygHeight = ".(21*$field_rows).";";
                                $texarea .= "generate_wysiwyg('".$this->getFieldRequiredType($field_name).$field_name."'); \n";
                                $texarea .= "//-->\n";
                                $texarea .= "</script>\n";
                            }
                            return $texarea;
                            break;
                        case "textbox":
                            $field_value = str_replace('"', "&quot;", $field_value); // double quotation mark
                            $field_value = str_replace("'", "&#039;", $field_value); // single quotation mark
                            return $field_property_pre_addition.$this->nbsp."<input class='".$this->css_class."_class_textbox' ".$field_width." type='text' title='".$this->getFieldTitle($field_name)."' name='".$field_req_type.$field_name."' id='".$field_req_type.$field_name."' value='".trim($field_value)."' ".$field_maxlength." ".$readonly." ".$on_js_event." ".$autocomplete.">".$this->nbsp.$field_property_post_addition;
                            break;
                        case "time":
                            $ret_date  = $this->nbsp."<input class='".$this->css_class."_class_textbox' ".$field_width." readonly type='text' title='".$this->getFieldTitle($field_name)."' id='".$this->getFieldRequiredType($field_name).$field_name."' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' value='".trim($field_value)."' $field_maxlength ".$on_js_event.">";
                            if($field_property_calendar_type == "floating"){
                                if(!$readonly) $ret_date .= "<img id='img".$this->getFieldRequiredType($field_name).$field_name."' src='".$this->directory."images/".$this->css_class."/cal.gif' border='0' alt='".$this->lang['set_date']."' title='".$this->lang['set_date']."' align='top' style='cursor:pointer;margin:3px;margin-left:6px;margin-right:6px;' /></a>\n"."<script type='text/javascript'>Calendar.setup({inputField : '".$this->getFieldRequiredType($field_name).$field_name."', ifFormat : '%H:%M:%S', showsTime : true, button : 'img".$this->getFieldRequiredType($field_name).$field_name."'});</script>".$this->nbsp;
                            }else{
                                if(!$readonly) $ret_date .= "<a class='".$this->css_class."_class_a2' title='".$this->getFieldTitle($field_name)."' href=\"javascript: openCalendar('".$this->directory."','', '".$this->unique_prefix."frmEditRow', '$field_req_type', '".$field_name."', '$field_type')\"><img src='".$this->directory."images/".$this->css_class."/cal.gif' border='0' alt='".$this->lang['set_date']."' title='".$this->lang['set_date']."' align='top' style='MARGIN:3px;margin-left:6px;margin-right:6px;' /></a>".$this->nbsp;
                            }
                            if(!$readonly) $ret_date .= "<a class='".$this->css_class."_class_a2' style='cursor: pointer;' onClick='document.getElementById(\"".$this->getFieldRequiredType($field_name).$field_name."\").value=\"".date("H:i:s")."\"'>[".date("H:i:s")."]</a>";
                            if((!$readonly) && (substr($this->getFieldRequiredType($field_name), 0, 1) == "s")) $ret_date .= "&nbsp;<a class='".$this->css_class."_class_a2'  style='cursor: pointer;' onClick='document.getElementById(\"".$this->getFieldRequiredType($field_name).$field_name."\").value=\"\"' title='".$this->lang['clear']."'>[".$this->lang['clear']."]</a>";
                            return $ret_date;
                            break;
                        case "validator":
                            $field_property_for_field = $this->getFieldProperty($field_name, "for_field");
                            $field_property_validation_type = $this->getFieldProperty($field_name, "validation_type");
                            $field_req_type           = $this->getFieldRequiredType($field_property_for_field, true);
                            if($field_property_validation_type == "password"){ $validator_field_type = "password"; } else { $validator_field_type = "text"; }
                            return $this->nbsp."<input type='".$validator_field_type."' class='".$this->css_class."_class_textbox' ".$field_width." title='".$this->getFieldTitle($field_name)."' name='".$field_req_type.$field_property_for_field."' id='".$field_req_type.$field_property_for_field."' value='' $field_maxlength $readonly ".$on_js_event.">".$this->nbsp;
                            break;
                        default:
                            return $this->nbsp."<input type='text' class='".$this->css_class."_class_textbox' ".$field_width." title='".$this->getFieldTitle($field_name)."' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' value='".trim($field_value)."' $field_maxlength $readonly ".$on_js_event.">".$this->nbsp;
                            break;
                    }
                }else if ($this->mode === "details"){
                    switch($field_type){
                        case "checkbox":
                            return $this->nbsp.(($field_value == 1) ? $this->lang['yes'] : $this->lang['no']).$this->nbsp;
                            break;
                        case "date":
                            return $this->nbsp.substr(trim($field_value), 0, 10).$this->nbsp;
                            break;
                        case "datedmy":
                            return $this->nbsp.$this->myDate($field_value, "datedmy").$this->nbsp;
                            break;
                        case "datetime":
                            return $this->nbsp.trim($field_value).$this->nbsp;
                            break;
                        case "datetimedmy":
                            return $this->nbsp.$this->myDate($field_value, "datetimedmy").$this->nbsp;
                            break;
                        case "enum":
                            // don't remove columns_edit_mode
                            if(isset($this->columns_edit_mode[$field_name]['source']) && is_array($this->columns_edit_mode[$field_name]['source'])){
                                foreach($this->columns_edit_mode[$field_name]['source'] as $val => $opt){
                                    if($field_value == $val) return $this->nbsp.trim($opt).$this->nbsp;
                                }
                            }
                            return $this->nbsp.trim($field_value).$this->nbsp;
                            break;
                        case "hidden":
                            return "";  break;
                        case "image":
                            $field_property_target_path   = $this->getFieldProperty($field_name, "target_path");
                            $field_property_image_width   = $this->getFieldProperty($field_name, "image_width", "edit", "lower", "50px");
                            $field_property_image_height  = $this->getFieldProperty($field_name, "image_height", "edit", "lower", "30px");
                            $field_property_default       = $this->getFieldProperty($field_name, "default", "edit", "normal");
                            $img_default     = (($field_property_default != "") && file_exists($field_property_target_path.trim($field_property_default))) ? "<img src='".$field_property_target_path.$field_property_default."' width='".$field_property_image_width."' height='".$field_property_image_height."' alt='' title='' />" : "<span class='".$this->css_class."_class_label'>".$this->lang['no_image']."</span>";
                            if((trim($field_value) !== "") && file_exists($field_property_target_path.trim($field_value))){
                                return $this->nbsp."<img src='".$field_property_target_path.trim($field_value)."' border='1' width='".$field_property_image_width."' height='".$field_property_image_height."' align='middle' />".$this->nbsp;
                            }else{
                                return "<table style='BORDER: solid 1px #000000;' width='".$field_property_image_width."' height='".$field_property_image_height."'><tr><td align='center'>".$img_default."</td></tr></table>";
                            }
                            break;
                        case "label":
                            return $this->nbsp.trim($field_value)."";
                            break;
                        case "link":
                            $field_property_field_data = $this->getFieldProperty($field_name, "field_data", "details", "normal");
                            if($field_property_field_data != ""){
                                $href_inner   = $field_property_href   = $this->getFieldProperty($field_name, "href");
                                $field_property_target = $this->getFieldProperty($field_name, "target");
                                $on_js_event  = $field_property_on_js_event = $this->getFieldProperty($field_name, "on_js_event", "details", "normal");

                                foreach ($this->columns_edit_mode[$field_name] as $search_field_key => $search_field_value){
                                    if(substr($search_field_key, 0, 9) == "field_key"){
                                        $field_number = intval(substr($search_field_key, 10, strlen($search_field_key) - 10));
                                        $field_inner = $row[$this->getFieldOffset($search_field_value)];
                                        if(strpos($href_inner, "{".$field_number."}") >= 0){
                                            $href = str_replace("{".$field_number."}", $field_inner, $href_inner);
                                        }
                                    }
                                }
                                // remove unexpected 'http://'s
                                if(strstr($href_inner, "http://") != ""){
                                    $href = str_replace("http://", "", $href);
                                    $href = "http://".$href;
                                }
                                $link_value = ($this->getFieldOffset($field_property_field_data) != "-1") ? trim($row[$this->getFieldOffset($field_property_field_data)]) : "";
                                return $this->nbsp."<a class='".$this->css_class."_class_a2' href='".$href."' target='".$field_property_target."' ".$on_js_event.">".$link_value."</a>".$this->nbsp;
                            }else{
                                return $this->nbsp;
                            }
                            break;
                        case "password":
                            return $this->nbsp."<label class='".$this->css_class."_class_label'>******</label>".$this->nbsp;
                            break;
                        case "print":
                            return $this->nbsp."<label class='".$this->css_class."_class_label' ".$field_width.">".trim($field_value)."</label>".$this->nbsp;
                            break;
                        case "textarea":
                        case "textbox":
                            return $field_property_pre_addition.$this->nbsp.trim($field_value).$field_property_post_addition;
                            break;
                        case "validator":
                            return "";  break;
                        default:
                            return $this->nbsp.trim($field_value)."";
                            break;
                    }
                }
            }
        }
        return false;
    }


    //--------------------------------------------------------------------------
    // add Check Boxes Values
    //--------------------------------------------------------------------------
    function addCheckBoxesValues(){
        foreach($this->columns_edit_mode as $itemName => $itemValue){
            if(isset($itemValue['type']) && $itemValue['type'] == "checkbox"){
                $found = false;
                foreach($_POST as $i => $v){
                    if($i == $this->getFieldRequiredType($itemName).$itemName){
                        $found = true;
                    }
                }
                if(!$found){
                    $_POST[$this->getFieldRequiredType($itemName).$itemName] = $itemValue['false_value'];
                }else{
                    $_POST[$this->getFieldRequiredType($itemName).$itemName] = $itemValue['true_value'];
                }
            }
        }
    }

    //--------------------------------------------------------------------------
    // get $_REQUEST variable
    //--------------------------------------------------------------------------
    function getVariable($var = "", $prefix = true, $method = "request"){
        $prefix = (($prefix == true) || ($prefix == "true")) ? true : false;
        $unique_prefix = ($prefix) ? $this->unique_prefix : "" ;
        $unique_prefix_var = (isset($_GET[$unique_prefix.$var])) ? $_GET[$unique_prefix.$var] : "0";

        // check for possible hack attack
        $max_page_size = intval(max($this->pages_array));
        if(($var == "page_size") && (intval($unique_prefix_var) > intval($max_page_size))) {
            return $max_page_size;
        }

        switch($method){
            case "get":
                return isset($_GET[$unique_prefix.$var]) ? $_GET[$unique_prefix.$var] : "";
                break;
            case "post":
                return isset($_POST[$unique_prefix.$var]) ? $_POST[$unique_prefix.$var] : "";
                break;
            default:
                return isset($_REQUEST[$unique_prefix.$var]) ? $_REQUEST[$unique_prefix.$var] : "";
                break;
        }
    }

    //--------------------------------------------------------------------------
    // draw RadioButtons
    //--------------------------------------------------------------------------
    function drawRadioButtons($tag_name, $field_name, &$select_array, $compare = "", $sub_field_value="", $sub_field_name="", $disabled="", $on_js_event="", $radiobuttons_alignment=""){
        $req_print = $this->getVariable('print');
        $break_by = ($radiobuttons_alignment == "vertical") ? "<br />" : "";
        $text = "";
        if($req_print != true){
            if($on_js_event !="") $text .= "<span ".$on_js_event.">";
            if(is_object($select_array)){
                while($row = $select_array->fetchRow()){
                    if(strtolower($row[$this->foreign_keys_array[$field_name][$sub_field_value]]) == strtolower($compare))
                        $text .= "<input class='".$this->css_class."_class_radiobutton' type='radio' title='".$this->getFieldTitle($field_name)."' name='".$tag_name."' id='".$tag_name."' value='".$row[$this->foreign_keys_array[$field_name][$sub_field_value]]."' checked ".$disabled.">".$row[$this->foreign_keys_array[$field_name][$sub_field_name]].$this->nbsp.$break_by;
                    else
                        $text .= "<input class='".$this->css_class."_class_radiobutton' type='radio' title='".$this->getFieldTitle($field_name)."' name='".$tag_name."' id='".$tag_name."' value='".$row[$this->foreign_keys_array[$field_name][$sub_field_value]]."'  ".$disabled.">".$row[$this->foreign_keys_array[$field_name][$sub_field_name]].$this->nbsp.$break_by;
                }
            }else{
                foreach($select_array as $key => $val){
                    if(strtolower($key) == strtolower($compare)){
                        $text .= "<input class='".$this->css_class."_class_radiobutton' type='radio' id='".$tag_name."' name='".$tag_name."' value='".$key."' title='".$this->getFieldTitle($field_name)."' checked  ".$disabled.">".$val."&nbsp;".$break_by;
                    }else{
                        $text .= "<input class='".$this->css_class."_class_radiobutton' type='radio' id='".$tag_name."' name='".$tag_name."' value='".$key."' title='".$this->getFieldTitle($field_name)."'  ".$disabled.">".$val."&nbsp;".$break_by;
                    }
                }
            }
            if($on_js_event !="") $text .= "</span>";
        }else{
            if(is_object($select_array)){
                $found = 0;
                while(($row = $select_array->fetchRow()) && (!$found)){
                    if(strtolower($row[$this->foreign_keys_array[$field_name][$sub_field_value]]) == strtolower($compare)){
                        $text .= "<span ".$on_js_event.">".$row[$this->foreign_keys_array[$field_name][$sub_field_name]]."</span>";
                        $found = 1;
                    }
                }
                if($found == 0) $text .= "<span ".$on_js_event.">none</span>";
            }else{
                $text = $compare;
            }
        }
        return $text;
    }

    //--------------------------------------------------------------------------
    // draw drop-down list
    //--------------------------------------------------------------------------
    function drawDropDownList($tag_name, $foo_name, &$select_array, $compare = "", $field_name="", $sub_field_value="", $sub_field_name="", $disabled="", $on_js_event="", $multiple=false, $multiple_size="4"){
        $req_print = $this->getVariable('print');
        $text = "";
        $multiple_parameters = ($multiple) ? $multiple_parameters = "multiple size='".$multiple_size."'" : "";
        $tag_id = $tag_name;
        $tag_name = ($multiple) ? $tag_name = $tag_name."[]" : $tag_name;
        if($req_print != true){
            if(is_object($select_array)){
                $text = "<select class='".$this->css_class."_class_select' name='".$tag_name."' id='".$tag_id."' title='".$this->getFieldTitle($field_name)."' ".(($foo_name != "") ? "onChange='".$this->unique_prefix.$foo_name."'" : "")." ".$disabled." ".$on_js_event." ".$multiple_parameters.">";
                $text .= "<option value=''>-- ".$this->lang['select']." --</option>";
                if($this->db_handler->isError($select_array) != 1){
                    while($row = $select_array->fetchRow()){
                        $ff_name = $this->foreign_keys_array[$field_name][$sub_field_name];
                        if(eregi(" as ", strtolower($ff_name))) $ff_name = substr($ff_name, strpos(strtolower($ff_name), " as ")+4);
                        if(strtolower($row[$this->foreign_keys_array[$field_name][$sub_field_value]]) == strtolower($compare))
                            $text .= "<option selected value='".$row[$this->foreign_keys_array[$field_name][$sub_field_value]]."'>".$row[$ff_name]."</option>";
                        else
                            $text .= "<option value='".$row[$this->foreign_keys_array[$field_name][$sub_field_value]]."'>".$row[$ff_name]."</option>";
                    }
                }
            }else{
                if(!is_array($compare)){ $splitted_compare = split(",",$compare); }else{ $splitted_compare = $compare; }
                $text = "<select class='".$this->css_class."_class_select' name='".$tag_name."' id='".$tag_id."' ".(($foo_name != "") ? "onChange='".$this->unique_prefix.$foo_name."'" : "")." ".$disabled." ".$on_js_event." ".$multiple_parameters.">";
                foreach($select_array as $key => $val){
                    $selected = "";
                    if(count($splitted_compare) > 1){
                        foreach($splitted_compare as $spl_val){
                            if($spl_val == $key) {$selected = "selected"; break; }
                        }
                    }else{
                        $selected = ((strtolower($compare) == strtolower($key)) ? "selected" : "");
                    }
                    $text .= "<option ".$selected." value='".$key."'>".$val."</option>";
                }
            }
            $text .= "</select>";
        }else{
            if(is_object($select_array)){
                $found = 0;
                while(($row = $select_array->fetchRow()) && (!$found)){
                    if(strtolower($row[$this->foreign_keys_array[$field_name][$sub_field_value]]) == strtolower($compare)){
                        $text .= "<span>".$row[$this->foreign_keys_array[$field_name][$sub_field_name]]."</span>";
                        $found = 1;
                    }
                }
                if($found == 0) $text .= "<span>none</span>";
            }else{
                $text = $compare;
            }
        }
        return $text;
    }

    //--------------------------------------------------------------------------
    // draw Mode Button
    //--------------------------------------------------------------------------
    function drawModeButton($mode, $mode_url, $botton_name, $alt_name, $image_file, $onClick, $div_align=false, $nbsp="", $type=""){
        $req_print = $this->getVariable('print');
        if($type == ""){
            $mode_type = (isset($this->modes[$mode]['type'])) ? $this->modes[$mode]['type'] : "";
        }else{
            $mode_type = $type;
        }
        if(!$this->is_error){
            if($req_print != true){
                switch($mode_type){
                    case "button":
                        echo $nbsp."<input class='".$this->css_class."_class_button' type='button' ";
                        if($div_align){ echo "style='float: "; echo ($this->direction == "rtl")?"right":"left"; echo "' "; }
                        echo "onClick=$onClick value='".$botton_name."' />".$nbsp;
                        break;
                    case "image":
                        if($div_align){ echo "<div style='float:"; echo ($this->direction == "rtl")?"right":"left"; echo ";'>"; }
                        echo $nbsp."<img style='cursor:pointer;' align='middle' onClick=".$onClick." src='".$this->directory."images/".$this->css_class."/".$image_file."' alt='$alt_name' title='$alt_name' />".$nbsp;
                        if($div_align) echo "</div>";
                        break;
                    default:
                        if($div_align){ echo "<div style='float:"; echo ($this->direction == "rtl")?"right":"left"; echo ";'>"; }
                        echo $nbsp."<a class='".$this->css_class."_class_a".(($mode == "add") ? "_header" : "")."' href='$mode_url' onClick=".$onClick." title='$alt_name'>".$botton_name."</a>".$nbsp;
                        if($div_align) echo "</div>";
                        break;
                }
            }else{
                switch($mode_type){
                    case "button":
                        echo "<span ";
                        if($div_align){ echo "style='float: "; echo ($this->direction == "rtl")?"right":"left"; echo "' "; }
                        echo ">".$botton_name."</span>";
                        break;
                    case "image":
                        if($div_align){ echo "<div style='float:"; echo ($this->direction == "rtl")?"right":"left"; echo ";'>"; }
                        echo "<img align='middle' src='".$this->directory."images/".$this->css_class."/".$image_file."' readonly />";
                        if($div_align) echo "</div>";
                        break;
                    default:
                        if($div_align){ echo "<div style='float:"; echo ($this->direction == "rtl")?"right":"left"; echo ";'>"; }
                        echo $nbsp."<span class='".$this->css_class."_class_a' >".$botton_name."</span>".$nbsp;
                        if($div_align) echo "</div>";
                        break;
                }
            }
        }
    }

    //--------------------------------------------------------------------------
    // set Common JavaScript
    //--------------------------------------------------------------------------
    function setCommonJavaScript(){
        $req_mode = $this->getVariable('mode');
        $req_new = $this->getVariable('new');
        // change mode after update
        if(($req_mode == "update") && ($req_new != 1) && ($this->mode_after_update == "edit")){
            $req_mode = $this->mode_after_update;
        }
        echo "\n<!-- This script was generated by datagrid.class.php v.4.2.6 (http://phpbuilder.blogspot.com) -->";

        // set common JavaScript
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$this->directory.'scripts/dg.js')) {
            echo "<label class='".$this->css_class."_class_error_message no_print'>Cannot find file: <b>".$this->directory."scripts/dg.js</b>. Check if this file exists and you use a correct path!</label><br /><br />";
        }else{
            echo "\n<script type='text/javascript' src='".$this->directory."scripts/dg.js'></script>";
        }

        if(($req_mode == "add") || ($req_mode == "edit")){
            // include calendar script (floating), if needed
            if ($this->datetimeFieldExists("floating")) {
                // set calendar JS
                echo "<style type='text/css'>@import url(".$this->directory."modules/jscalendar/skins/aqua/theme.css);</style>\n";
                //<!-- import the calendar script -->
                echo "<script type='text/javascript' src='".$this->directory."modules/jscalendar/calendar.js'></script>"."\n";
                //<!-- import the language module -->
                echo "<script type='text/javascript' src='".$this->directory."modules/jscalendar/lang/calendar-".$this->getLangAbbrForCalendar().".js'></script>"."\n";
                //<!-- the following script defines the Calendar.setup helper function, which makes
                //adding a calendar a matter of 1 or 2 lines of code. -->
                echo "<script type='text/javascript' src='".$this->directory."modules/jscalendar/calendar-setup.js'></script>\n";
            }
            // include form checking script, if needed
            if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$this->directory.'modules/jsafv/form.scripts.js')) {
                echo "<label class='".$this->css_class."_class_error_message no_print'>Cannot find file: <b>".$this->directory."modules/jsafv/form.scripts.js</b>. Check if this file exists and you use a correct path!</label><br /><br />";
            }else{
                echo "\n<script type='text/javascript' src='".$this->directory."modules/jsafv/lang/jsafv-".$this->lang_name.".js'></script>";
                echo "\n<script type='text/javascript' src='".$this->directory."modules/jsafv/form.scripts.js'></script>";
            }
            // include resizable textarea script, if needed
            if ($this->resizableFieldExists()) {
                if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$this->directory.'scripts/resize.js')) {
                    echo "<label class='".$this->css_class."_class_error_message no_print'>Cannot find file: <b>".$this->directory."scripts/resize.js</b>. Check if this file exists and you use a correct path!</label><br /><br />";
                }else{
                    echo "\n<script type='text/javascript' src='".$this->directory."scripts/resize.js'></script>";
                }
            }
            // include WYSIWYG script, if needed
            if ($this->wysiwygFieldExists()) {
                // set WYSIWYG
                echo "\n<script type='text/javascript'>\n";
                echo "<!--\n";
                echo "imagesDir = '".$this->directory."modules/wysiwyg/icons/';\n";  // Images Directory
                echo "cssDir = '".$this->directory."modules/wysiwyg/styles/';\n";    // CSS Directory
                echo "popupsDir = '".$this->directory."modules/wysiwyg/popups/';\n"; // Popups Directory
                echo "//-->\n";
                echo "</script>";
                echo "\n<script type='text/javascript' src='".$this->directory."modules/wysiwyg/wysiwyg.js'></script>";
            }
            // set verify JS functions
            if(isset($this->modes['cancel'][$this->mode]) && $this->modes['cancel'][$this->mode]){
                echo "\n<script type='text/javascript'>\n<!--";
                echo "\n function ".$this->unique_prefix."verifyCancel(param){if(confirm(\"".$this->lang['cancel_creating_new_record']."\")){document.location.href=param;} else { return false;}};";
                echo "//-->\n</script>\n";
            }
        }else{ // view mode
            // include autosuggest.js file and other for AutoSuggestion
            if ($this->autosuggestFieldExists()){
                echo "\n<script type='text/javascript' src='".$this->directory."modules/autosuggest/js/bsn.AutoSuggest_2.1.3.js'></script>";
                echo "\n<link rel='stylesheet' href='".$this->directory."modules/autosuggest/css/autosuggest_inquisitor.css' type='text/css' media='screen' charset='utf-8' />";
            }
        }
        // include overlib.js file for floating tooltips
        if ($this->floatingToolTipsFieldExists()) {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$this->directory.'modules/overlib/overlib.js')) {
                echo "<label class='".$this->css_class."_class_error_message no_print'>Cannot find file: <b>".$this->directory."modules/overlib/overlib.js</b>. Check if this file exists and you use a correct path!</label><br /><br />";
            }else{
                echo "\n<script type='text/javascript' src='".$this->directory."modules/overlib/overlib.js'></script>";
            }
        }
        // include highlight.js file for rows highlighting
        if($this->row_highlighting_allowed){
            if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$this->directory.'scripts/highlight.js')) {
                echo "<label class='".$this->css_class."_class_error_message no_print'>Cannot find file: <b>".$this->directory."scripts/highlight.js</b>. Check if this file exists and you use a correct path!</label><br /><br />";
            }else{
                echo "\n<script type='text/javascript' src='".$this->directory."scripts/highlight.js'></script>";
            }
        }
    }

    function setCommonJavaScriptEnd(){
        echo "<script type='text/javascript'>\n<!--\n";
        // set verify JS functions
        if(isset($this->modes['delete'][$this->mode]) && $this->modes['delete'][$this->mode]){
            echo "\nfunction ".$this->unique_prefix."verifyDelete(param){if(confirm(\"".$this->lang['delete_this_record']."\")){document.location.href=param;} else { window.event.cancelBubble = true; return false;}};";
        }
        echo "\n//-->\n";
        echo "</script>";
    }

    function setMediaPrint(){
        echo "\n<style type='text\css'> @media print { .no_print {DISPLAY: none! important};  }</style>";
    }

    //--------------------------------------------------------------------------
    // set Edit Fields Form Script
    //--------------------------------------------------------------------------
    function setEditFieldsFormScript($url=""){
        echo "<script type='text/javascript'>\n";
        echo "<!--\n";
        //echo $url;
        //document.".$this->unique_prefix."frmEditRow.action ='".str_replace($this->amp,'&',$url)."';
        echo "function ".$this->unique_prefix."sendEditFields(){
            if(window.".$this->unique_prefix."onSubmitMyCheck){ if(!".$this->unique_prefix."onSubmitMyCheck()){ return false; } }
        ";
        // two different parts of code to find & save wysiwyg editor data
        if($this->browser_name == "Firefox"){
            echo "
                elements = document.getElementsByTagName('*');
                for (var idx = 0; idx < elements.length; idx++) {
                    node = elements.item(idx);
                    field_name = node.getAttribute('name');
                    field_type = node.getAttribute('type');
                    // check file or image fields
                    if(field_type == 'file'){
                        if(document.getElementById(field_name).value != ''){
                           alert('You need to upload file or image before update! Click on Upload link.');
                           return false;
                        }
                    }
                    field_full_name = 'wysiwyg' + field_name;
                    if(document.getElementById(field_full_name)){
                        document.getElementById(field_name).value = document.getElementById(field_full_name).contentWindow.document.body.innerHTML;
                        if((document.getElementById(field_name).value == '<p>&nbsp;</p>') || (document.getElementById(field_name).value == '<p> </p>') || (document.getElementById(field_name).value == '&lt;p&gt;&nbsp;&lt;/p&gt;')){
                            document.getElementById(field_name).value = '';
                        }
                    }
                }
            ";
        }else{ // "MSIE" or other
            echo "
                for (var idx=0; idx < document.".$this->unique_prefix."frmEditRow.length; idx++) {
                    field_name = ".$this->unique_prefix."frmEditRow.elements.item(idx).name;
                    field_type = ".$this->unique_prefix."frmEditRow.elements.item(idx).type;
                    // check file or image fields
                    if(field_type == 'file'){
                        if(document.getElementById(field_name).value != ''){
                           alert('You need to upload file or image before update! Click on Upload link.');
                           return false;
                        }
                    }
                    field_full_name = 'wysiwyg' + field_name;
                    if(document.getElementById(field_full_name)){
                        document.getElementById(field_name).value = document.getElementById(field_full_name).contentWindow.document.body.innerHTML;
                        if((document.getElementById(field_name).value == '<P>&nbsp;</P>') || (document.getElementById(field_name).value == '&lt;P&gt;&nbsp;&lt;/P&gt;')){
                            document.getElementById(field_name).value = '';
                        }
                    }
                };
            ";
        };
        echo "
            if(onSubmitCheck(document.".$this->unique_prefix."frmEditRow, ".$this->js_validation_errors.")){
                document.".$this->unique_prefix."frmEditRow.submit();
            }else{
                return false;
            }
        }";
        echo "\n//-->\n";
        echo "</script>\n";
    }

    //--------------------------------------------------------------------------
    // return date format
    //--------------------------------------------------------------------------
    function myDate($field_value, $type="datedmy"){
        $ret_date = "";
        if($type == "datedmy"){
            if (substr(trim($field_value), 4, 1) == "-"){
                $year1 = substr(trim($field_value), 0, 4);
                $month1 = substr(trim($field_value), 5, 2);
                $day1 = substr(trim($field_value), 8, 2);
                if($day1 != ""){ $ret_date = $day1."-".$month1."-".$year1; }
            }else{
                $year1 = substr(trim($field_value), 6, 4);
                $month1 = substr(trim($field_value), 3, 2);
                $day1 = substr(trim($field_value), 0, 2);
                if($day1 != ""){ $ret_date = $day1."-".$month1."-".$year1; }
            }
        }else if($type == "datetimedmy"){
            if (substr(trim($field_value), 4, 1) == "-"){
                $year1 = substr(trim($field_value), 0, 4);
                $month1 = substr(trim($field_value), 5, 2);
                $day1 = substr(trim($field_value), 8, 2);
                $time1 = substr(trim($field_value), 11, 2);
                $time2 = substr(trim($field_value), 14, 2);
                $time3 = substr(trim($field_value), 17, 2);
                if($day1 != ""){ $ret_date = $day1."-".$month1."-".$year1." ".$time1.":".$time2.":".$time3; }
            }else{
                $year1 = substr(trim($field_value), 6, 4);
                $month1 = substr(trim($field_value), 3, 2);
                $day1 = substr(trim($field_value), 0, 2);
                $time1 = substr(trim($field_value), 11, 2);
                $time2 = substr(trim($field_value), 14, 2);
                $time3 = substr(trim($field_value), 17, 2);
                if($day1 != ""){ $ret_date = $day1."-".$month1."-".$year1." ".$time1.":".$time2.":".$time3; }
            }
        }else{
            $ret_date = $field_value;
        }
        return $ret_date;
    }

    ////////////////////////////////////////////////////////////////////////////
    //
    // Non documented
    //
    ////////////////////////////////////////////////////////////////////////////
    function allowHighlighting($option = true){ $this->row_highlighting_allowed = (($option == true) || ($option == "true")) ? true : false ; }

    //--------------------------------------------------------------------------
    // get current Id
    //--------------------------------------------------------------------------
    function getCurrentId(){
        return ($this->rid != "") ? $this->rid : $this->getVariable('rid');
    }

    //--------------------------------------------------------------------------
    // get Next Id
    //--------------------------------------------------------------------------
    function getNextId(){
        if(isset($this->db_handler)){
            // need to be declined if creating new row was cancelied
            // return $this->db_handler->nextId("'".$this->tbl_name."'");
            $sql  = " SELECT MAX(".$this->primary_key.") as max_id FROM ".$this->tbl_name." ";
            $dSet = $this->db_handler->query($sql);
            if($row = $dSet->fetchRow()){
                return $row[0]+1;
            }
        }else{
            return "-1";
        }
    }

    //--------------------------------------------------------------------------
    // set messages
    //--------------------------------------------------------------------------
    function setDgMessages($add_message = "", $update_message = "", $delete_message = ""){
        $this->dg_messages['add'] = $add_message;
        $this->dg_messages['update'] = $update_message;
        $this->dg_messages['delete'] = $delete_message;
    }

    //--------------------------------------------------------------------------
    // set header names in columnar layout
    //--------------------------------------------------------------------------
    function setHeadersInColumnarLayout($field_header = "", $field_value_header = ""){
        $this->field_header = $field_header;
        $this->field_value_header = $field_value_header;
    }

    //--------------------------------------------------------------------------
    // set javascript errors display style
    //--------------------------------------------------------------------------
    function setJsErrorsDisplayStyle($display_style = "all"){
        $this->js_validation_errors = ($display_style == "all") ? "true" : "false";
    }

    //--------------------------------------------------------------------------
    // selectSqlItem - return the first field after executing custom SELECT SQL statement
    //--------------------------------------------------------------------------
    function selectSqlItem($sql = ""){
        $dataField = "";
        if($this->db_handler){
            if($sql != ""){
                $this->setEncodingOnDatabase();
                $this->db_handler->setFetchMode(DB_FETCHMODE_ORDERED);
                $dataSet = & $this->db_handler->query($sql);
                if($dataSet->numCols() > 0){
                   $row = $dataSet->fetchRow();
                   $dataField = $row[0];
                }
                if($this->debug){
                    if($this->db_handler->isError($dataSet) == 1){ $debugInfo = "<tr><td>".$dataSet->getDebugInfo()."</td></tr>"; } else { $debugInfo = ""; };
                    echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left'><label class='".$this->css_class."_class_label'><b>select sql (".$this->strToLower($this->lang['total']).": ".$dataSet->numCols().") </b>".$sql."</label></td></tr>".$debugInfo."</table><br />";
                }
            }
        }else{
            $this->addWarning('selectSqlItem() method', 'This method must be called after dataSource() method only!');
        }
        return $dataField;
    }

    //--------------------------------------------------------------------------
    // executeSql - return dataSet after executing custom SQL statement
    //--------------------------------------------------------------------------
    function executeSQL($sql = ""){
        $dataSet = "";
        if($this->db_handler){
            if($sql != ""){
                $this->setEncodingOnDatabase();
                $dataSet = & $this->db_handler->query($sql);
            }
            if($this->debug){
                if($this->db_handler->isError($dataSet) == 1){ $debugInfo = "<tr><td>".$dataSet->getDebugInfo()."</td></tr>"; } else { $debugInfo = ""; };
                echo "<table width='".$this->tblWidth[$this->mode]."'><tr><td align='left'><label class='".$this->css_class."_class_label'><b>sql: </b>".$sql."</label></td></tr>".$debugInfo."</table><br />";
            }
        }else{
            $this->addWarning('executeSql() method', 'This method must be called after dataSource() method only!');
        }
        return $dataSet;
    }

    ////////////////////////////////////////////////////////////////////////////
    //
    // Auxiliary methods
    // -------------------------------------------------------------------------
    ////////////////////////////////////////////////////////////////////////////
    function prepareFileOperations($key){
        $files = array();
        if(is_array($this->columns_edit_mode)){
            foreach($this->columns_edit_mode as $fldName => $fldParam){
                foreach($fldParam as $key => $val){
                    if(($val === "image") || ($val === "file")){
                        $file = array();
                        $file['file_name'] = $fldParam['file_name'];
                        $file['target_path'] = $fldParam['target_path'];
                        $files[] = $file;
                        break;
                    }
                }
            }
        }
        print_r($files);
    }

    function drawCalendarButton($field_name, $field_type, $datetime_format="Y-m-d", $field_value="", $field_property_pre_addition="", $field_property_post_addition="", $field_width="", $field_maxlength="", $on_js_event="", $readonly=false, $field_property_calendar_type = "popup"){
        if($datetime_format == "Y-m-d"){
            $if_format = "%Y-%m-%d";          $show_time = false;
        }else if($datetime_format == "d-m-Y"){
            $if_format = "%d-%m-%Y";          $show_time = false;
        }else if($datetime_format == "Y-m-d H:i:s"){
            $if_format = "%Y-%m-%d %H:%M:%S"; $show_time = true;
        }else if($datetime_format == "d-m-Y H:i:s"){
            $if_format = "%d-%m-%Y %H:%M:%S"; $show_time = true;
        }else{
            $if_format = "%Y-%m-%d";          $show_time = false;
        }
        $ret_date  = $this->nbsp."<input class='".$this->css_class."_class_textbox' ".$field_width." readonly type='text' title='".$this->getFieldTitle($field_name)."' name='".$this->getFieldRequiredType($field_name).$field_name."' id='".$this->getFieldRequiredType($field_name).$field_name."' value='".trim($field_value)."' $field_maxlength ".$on_js_event.">";
        if($field_property_calendar_type == "floating"){
            if(!$readonly) $ret_date .= "<img id='img".$this->getFieldRequiredType($field_name).$field_name."' src='".$this->directory."images/".$this->css_class."/cal.gif' border='0' alt='".$this->lang['set_date']."' title='".$this->lang['set_date']."' align='top' style='cursor:pointer;margin:3px;margin-left:6px;margin-right:6px;' /></a>".$this->nbsp."<script type='text/javascript'>Calendar.setup({inputField : '".$this->getFieldRequiredType($field_name).$field_name."', ifFormat : '".$if_format."', showsTime : ".$show_time.", button : 'img".$this->getFieldRequiredType($field_name).$field_name."'});</script>";
        }else{
            if(!$readonly) $ret_date .= "<a class='".$this->css_class."_class_a2' title='".$this->getFieldTitle($field_name)."' href=\"javascript: openCalendar('".$this->directory."','', '".$this->unique_prefix."frmEditRow', '".$this->getFieldRequiredType($field_name)."', '".$field_name."', '$field_type')\"><img src='".$this->directory."images/".$this->css_class."/cal.gif' border='0' alt='".$this->lang['set_date']."' title='".$this->lang['set_date']."' align='top' style='MARGIN:3px;margin-left:6px;margin-right:6px;' /></a>".$this->nbsp;
        }
        if(!$readonly) $ret_date .= "<a class='".$this->css_class."_class_a2' style='cursor: pointer;' onClick='document.getElementById(\"".$this->getFieldRequiredType($field_name).$field_name."\").value=\"".date($datetime_format)."\"'>[".date($datetime_format)."]</a>";
        if((!$readonly) && (substr($this->getFieldRequiredType($field_name), 0, 1) == "s")) $ret_date .= "&nbsp;<a class='".$this->css_class."_class_a2'  style='cursor: pointer;' onClick='document.getElementById(\"".$this->getFieldRequiredType($field_name).$field_name."\").value=\"\"' title='".$this->lang['clear']."'>[".$this->lang['clear']."]</a>";
        return $field_property_pre_addition.$ret_date.$field_property_post_addition;
    }

    //--------------------------------------------------------------------------
    // get Formatted Microtime
    //--------------------------------------------------------------------------
    function getFormattedMicrotime(){
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }

    //--------------------------------------------------------------------------
    // download export file
    //--------------------------------------------------------------------------
    function exportDownloadFile($file_name){
        return "<script type='text/javascript'>\n<!--\n ".
        "if(confirm('Do you want to export datagrid content into [".$file_name."] file?')){ ".
        " document.write('".str_replace("_FILE_", $file_name, $this->lang['export_message'])."'); ".
        " document.location.href = '".$this->directory."scripts/download.php?dir=".$this->exporting_directory."&file=".$file_name."'; ".
        "} else {".
        " window.close();".
        "}".
        "\n//-->\n</script>";
    }

    //--------------------------------------------------------------------------
    // overloaded php function strtolower
    //--------------------------------------------------------------------------
    function strToLower($str){
        if($this->lang_name == "en"){
            return strtolower($str);
        }else{
            return mb_strtolower($str, mb_detect_encoding($str));
        }
    }

    //--------------------------------------------------------------------------
    // check if there is any resizable field
    //--------------------------------------------------------------------------
    function resizableFieldExists(){
        if(is_array($this->columns_edit_mode)){
            foreach($this->columns_edit_mode as $fldName => $fldValue){
                foreach($fldValue as $key => $val){
                    if(($key == "resizable") && (($val == true) || ($val == "true"))) return true;
                }
            }
        }
        return false;
    }

    //--------------------------------------------------------------------------
    // check if there is any autosuggest (autocomplete) field exists (in filter)
    //--------------------------------------------------------------------------
    function autosuggestFieldExists(){
        if(is_array($this->filter_fields)){
            foreach($this->filter_fields as $fldName => $fldValue){
                foreach($fldValue as $key => $val){
                    if(($key == "autocomplete") && (($val == true) || ($val == "true"))) return true;
                }
            }
        }
        return false;
    }

    //--------------------------------------------------------------------------
    // check if there is any datetime field
    //--------------------------------------------------------------------------
    function datetimeFieldExists($cal_type = ""){
        $found_field_type = false;
        $found_calendar_type = false;
        if(is_array($this->columns_edit_mode)){
            foreach($this->columns_edit_mode as $fldName => $fldValue){
                foreach($fldValue as $key => $val){
                    if($key == "type"){
                        if(($val == "date") || ($val == "datedmy") || ($val == "datetime") || ($val == "datetimedmy") || ($val == "time")){
                            $found_field_type = true;
                        }
                    }
                    if($key == "calendar_type"){
                        if(strtolower($val) == $cal_type){
                            $found_calendar_type = true;
                        }
                    }
                    if($found_field_type && $found_calendar_type) return true;
                }
            }
        }
        return false;
    }

    //--------------------------------------------------------------------------
    // check if there is any field with floating tool tips
    //--------------------------------------------------------------------------
    function floatingToolTipsFieldExists(){
        $tooltip_allowed = false;
        if(isset($this->columns_view_mode)){
            foreach($this->columns_view_mode as $fldName => $fldValue){
                foreach($fldValue as $key => $val){
                    if(($key == "tooltip") && (($val == true) || ($val == "true"))){ $tooltip_allowed = true; }
                    if($tooltip_allowed && ($key == "tooltip_type") && (strtolower($val) == "floating")) { return true; }
                }
            }
        }
        return false;
    }

    //--------------------------------------------------------------------------
    // check if there is any wysiwyg field
    //--------------------------------------------------------------------------
    function wysiwygFieldExists(){
        if(is_array($this->columns_edit_mode)){
            foreach($this->columns_edit_mode as $fldName => $fldValue){
                foreach($fldValue as $key => $val){
                    if(($key == "edit_type") && (strtolower($val) == "wysiwyg")) return true;
                }
            }
        }
        return false;
    }

    //--------------------------------------------------------------------------
    // get size of the remote file
    //--------------------------------------------------------------------------
    function getRemoteFileSize($url, $user = "", $pw = ""){
        ob_start();
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        if(!empty($user) && !empty($pw))	{
            $headers = array('Authorization: Basic ' .  base64_encode("$user:$pw"));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $ok = curl_exec($ch);
        curl_close($ch);
        $head = ob_get_contents();
        ob_end_clean();
        $regex = '/Content-Length:\s([0-9].+?)\s/';
        $count = preg_match($regex, $head, $matches);
        return isset($matches[1]) ? $matches[1] : "unknown";
    }

    //--------------------------------------------------------------------------
    // get http port
    //--------------------------------------------------------------------------
    function getPort(){
        $port = "";
        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != "80"){
            $port = ":".$_SERVER['SERVER_PORT'];
        }
        return $port;
    }

    //--------------------------------------------------------------------------
    // get Protocol (http/s)
    //--------------------------------------------------------------------------
    function getProtocol(){
        $protocol = "http://";
        if((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != "off")) ||
            strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 5)) == "https"){
            $protocol = "https://";
        }
        return $protocol;
    }

    //--------------------------------------------------------------------------
    // get Server Name
    //--------------------------------------------------------------------------
    function getServerName(){
        $server = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : "";
        $colon = strpos($server,':');
        if ($colon > 0 && $colon < strlen($server)){
            $server = substr($server, 0, $colon);
        }
        return $server;
    }

    //--------------------------------------------------------------------------
    // return last substring occurence
    //--------------------------------------------------------------------------
    function lastSubStrOccurence($string, $substring){
        $string = str_replace("\t", " ", $string);
        $string = str_replace("\n", " ", $string);
        $string = strrev(strtolower($string));
        $substring = strrev(strtolower($substring));
        return strpos($string, $substring);
    }

    //--------------------------------------------------------------------------
    // gets random string
    //--------------------------------------------------------------------------
    function getRandomString($length = 20) {
        $template = "1234567890abcdefghijklmnopqrstuvwxyz";
        settype($template, "string");
        settype($length, "integer");
        settype($rndstring, "string");
        settype($a, "integer");
        settype($b, "integer");

        for ($a = 0; $a <= $length; $a++) {
            $b = rand(0, strlen($template) - 1);
            $rndstring .= $template[$b];
        }
        return $rndstring;
    }

    //--------------------------------------------------------------------------
    // set browser definitions
    //--------------------------------------------------------------------------
    function setBrowserDefinitions(){
        $bd = array();

        $agent = $_SERVER['HTTP_USER_AGENT'];
        // initialize properties
        $bd['platform'] = "Windows";
        $bd['browser'] = "MSIE";
        $bd['version'] = "6.0";

        // find operating system
        if (eregi("win", $agent))       $bd['platform'] = "Windows";
        elseif (eregi("mac", $agent))   $bd['platform'] = "MacIntosh";
        elseif (eregi("linux", $agent)) $bd['platform'] = "Linux";
        elseif (eregi("OS/2", $agent))  $bd['platform'] = "OS/2";
        elseif (eregi("BeOS", $agent))  $bd['platform'] = "BeOS";

        // test for Opera
        if (eregi("opera",$agent)){
            $val = stristr($agent, "opera");
            if (eregi("/", $val)){
                $val = explode("/",$val); $bd['browser'] = $val[0]; $val = explode(" ",$val[1]); $bd['version'] = $val[0];
            }else{
                $val = explode(" ",stristr($val,"opera")); $bd['browser'] = $val[0]; $bd['version'] = $val[1];
            }
        // test for MS Internet Explorer version 1
        }elseif(eregi("microsoft internet explorer", $agent)){
            $bd['browser'] = "MSIE"; $bd['version'] = "1.0"; $var = stristr($agent, "/");
            if (ereg("308|425|426|474|0b1", $var)) $bd['version'] = "1.5";
        // test for MS Internet Explorer
        }elseif(eregi("msie",$agent) && !eregi("opera",$agent)){
            $val = explode(" ",stristr($agent,"msie")); $bd['browser'] = $val[0]; $bd['version'] = $val[1];
        // test for MS Pocket Internet Explorer
        }elseif(eregi("mspie",$agent) || eregi('pocket', $agent)){
            $val = explode(" ",stristr($agent,"mspie")); $bd['browser'] = "MSPIE"; $bd['platform'] = "WindowsCE";
            if (eregi("mspie", $agent))
                $bd['version'] = $val[1];
            else {
                $val = explode("/",$agent);     $bd['version'] = $val[1];
            }
        // test for Firebird
        }elseif(eregi("firebird", $agent)){
            $bd['browser']="Firebird"; $val = stristr($agent, "Firebird"); $val = explode("/",$val); $bd['version'] = $val[1];
        // test for Firefox
        }elseif(eregi("Firefox", $agent)){
            $bd['browser']="Firefox"; $val = stristr($agent, "Firefox"); $val = explode("/",$val); $bd['version'] = $val[1];
        // test for Mozilla Alpha/Beta Versions
        }elseif(eregi("mozilla",$agent) && eregi("rv:[0-9].[0-9][a-b]",$agent) && !eregi("netscape",$agent)){
            $bd['browser'] = "Mozilla"; $val = explode(" ",stristr($agent,"rv:")); eregi("rv:[0-9].[0-9][a-b]",$agent,$val); $bd['version'] = str_replace("rv:","",$val[0]);
        // test for Mozilla Stable Versions
        }elseif(eregi("mozilla",$agent) && eregi("rv:[0-9]\.[0-9]",$agent) && !eregi("netscape",$agent)){
            $bd['browser'] = "Mozilla"; $val = explode(" ",stristr($agent,"rv:")); eregi("rv:[0-9]\.[0-9]\.[0-9]",$agent,$val); $bd['version'] = str_replace("rv:","",$val[0]);
        // remaining two tests are for Netscape
        }elseif(eregi("netscape",$agent)){
            $val = explode(" ",stristr($agent,"netscape")); $val = explode("/",$val[0]); $bd['browser'] = $val[0]; $bd['version'] = $val[1];
        }elseif(eregi("mozilla",$agent) && !eregi("rv:[0-9]\.[0-9]\.[0-9]",$agent)){
            $val = explode(" ",stristr($agent,"mozilla")); $val = explode("/",$val[0]); $bd['browser'] = "Netscape"; $bd['version'] = $val[1];
        }
        // clean up extraneous garbage that may be in the name
        $bd['browser'] = ereg_replace("[^a-z,A-Z]", "", $bd['browser']);
        $bd['version'] = ereg_replace("[^0-9,.,a-z,A-Z]", "", $bd['version']);

        $this->browser_name     = $bd['browser'];
        $this->browser_version  = $bd['version'];
        $this->platform         = $bd['platform'];
    }

    //--------------------------------------------------------------------------
    // get Language Abbreviation for Calendar
    //--------------------------------------------------------------------------
    function getLangAbbrForCalendar(){
        $return_abbrv = "en";
        switch($this->lang_name){
            case "ar": $return_abbrv = "en"; break; // Arabic
            case "hr": $return_abbrv = "hr"; break; // Bosnian/Croatian
            case "bg": $return_abbrv = "bg"; break; // Bulgarian
            case "pb": $return_abbrv = "pt"; break; // Brazilian Portuguese
            case "ca": $return_abbrv = "ca"; break; // Catala
            case "ch": $return_abbrv = "cn"; break; // Chinese
            case "cz": $return_abbrv = "cs"; break; // Czech
            case "es": $return_abbrv = "es"; break; // Espanol
            case "fr": $return_abbrv = "fr"; break; // Francais
            case "de": $return_abbrv = "de"; break; // German
            case "hu": $return_abbrv = "hu"; break; // Hungarian
            case "it": $return_abbrv = "it"; break; // Italiano
            case "nl": $return_abbrv = "nl"; break; // Netherlands/"Vlaams"(Flemish)
            case "pl": $return_abbrv = "pl"; break; // Polish
            case "ro_utf8":
            case "ro": $return_abbrv = "ro"; break; // Romanian
            case "sr": $return_abbrv = "en"; break; // Serbian
            case "se": $return_abbrv = "sv"; break; // Swedish
            case "tr": $return_abbrv = "tr"; break; // Turkish
            case "en":
            default:
                $return_abbrv = "en"; break;
        }
        return $return_abbrv;
    }

    //--------------------------------------------------------------------------
    // set Language
    //--------------------------------------------------------------------------
    function setInterfaceLang($lang_name = ""){
        $default_language = false;
        if(($lang_name == "ru_utf8") || ($lang_name != "") && (strlen($lang_name) == 2)){ $this->lang_name = $lang_name; }
        if (file_exists($this->inc_dir.'languages/'.$this->lang_name.'.php')) {
            include_once($this->inc_dir.'languages/'.$this->lang_name.'.php');
            if(function_exists('setLanguage')){
                $this->lang = setLanguage();
            }else{
                if($this->debug){ echo "<label class='".$this->css_class."_class_error_message no_print'>Your language interface option is turned on, but the system was failed to open correctly stream: <b>'".$this->inc_dir."languages/lang.php'</b>. <br />The structure of the file is corrupted or invalid. Please check it or return the language option to default value: <b>'en'</b>!</label><br />"; }
                $default_language = true;
            }
    	}else{
            if((strtolower($lang_name) != "en") && ($this->debug)){
                echo "<label class='".$this->css_class."_class_error_message no_print'>Your language interface option is turned on, but the system was failed to open stream: <b>'".$this->inc_dir."languages/".$lang_name.".php'</b>. <br />No such file or directory. Please check it or return the language option to default value: <b>'en'</b>!</label><br />";
            }
            $default_language = true;
    	}

        if($default_language){
            $this->lang['='] = "=";  // "equal";
            $this->lang['>'] = ">";  // "bigger";
            $this->lang['<'] = "<";  // "smaller";
            $this->lang['add'] = "Add";
            $this->lang['add_new'] = "+ Add New";
            $this->lang['add_new_record'] = "Add new record";
            $this->lang['add_new_record_blocked'] = "Security check: attempt of adding a new record! Check your settings, the operation is not allowed!";
            $this->lang['adding_operation_completed'] = "The adding operation completed successfully!";
            $this->lang['adding_operation_uncompleted'] = "The adding operation uncompleted!";
            $this->lang['and'] = "and";
            $this->lang['any'] = "any";
            $this->lang['ascending'] = "Ascending";
            $this->lang['back'] = "Back";
            $this->lang['cancel'] = "Cancel";
            $this->lang['cancel_creating_new_record'] = "Are you sure you want to cancel creating new record?";
            $this->lang['check_all'] = "Check All";
            $this->lang['clear'] = "Clear";
            $this->lang['create'] = "Create";
            $this->lang['create_new_record'] = "Create new record";
            $this->lang['current'] = "current";
            $this->lang['delete'] = "Delete";
            $this->lang['delete_record'] = "Delete record";
            $this->lang['delete_record_blocked'] = "Security check: attempt of deleting a record! Check your settings, the operation is not allowed!";
            $this->lang['delete_selected'] = "Delete selected";
            $this->lang['delete_selected_records'] = "Are you sure you want to delete the selected records?";
            $this->lang['delete_this_record'] = "Are you sure you want to delete this record?";
            $this->lang['deleting_operation_completed'] = "The deleting operation completed successfully!";
            $this->lang['deleting_operation_uncompleted'] = "The deleting operation uncompleted!";
            $this->lang['descending'] = "Descending";
            $this->lang['details'] = "Details";
            $this->lang['details_selected'] = "View selected";
            $this->lang['edit'] = "Edit";
            $this->lang['edit_selected'] = "Edit selected";
            $this->lang['edit_record'] = "Edit record";
            $this->lang['edit_selected_records'] = "Are you sure you want to edit the selected records?";
            $this->lang['errors'] = "Errors";
            $this->lang['export_to_excel'] = "Export to Excel";
            $this->lang['export_to_pdf'] = "Export to PDF";
            $this->lang['export_to_xml'] = "Export to XML";
            $this->lang['export_message'] = "<label class='".$this->css_class."_class_label'>The file _FILE_ is ready. After you finish downloading,</label> <a class='".$this->css_class."_class_error_message no_print' href='javascript: window.close();'>close this window</a>.";
            $this->lang['field'] = "Field";
            $this->lang['field_value'] = "Field Value";
            $this->lang['file_find_error'] = "Cannot find file: <b>_FILE_</b>. <br />Check if this file exists and you use a correct path!";
            $this->lang['file_opening_error'] = "Cannot open a file. Check your permissions.";
            $this->lang['file_writing_error'] = "Cannot write to file. Check writing permissions.";
            $this->lang['file_invalid file_size'] = "Invalid file size: ";
            $this->lang['file_uploading_error'] = "There was an error while uploading, please try again!";
            $this->lang['file_deleting_error'] = "There was an error while deleting!";
            $this->lang['first'] = "first";
            $this->lang['handle_selected_records'] = "Are you sure you want to handle the selected records?";
            $this->lang['hide_search'] = "Hide Search";
            $this->lang['last'] = "last";
            $this->lang['like'] = "like";
            $this->lang['like%'] = "like%";  // "begins with";
            $this->lang['%like'] = "%like";  // "ends with";
            $this->lang['%like%'] = "%like%";  // "ends with";
            $this->lang['loading_data'] = "loading data...";
            $this->lang['max'] = "max";
            $this->lang['next'] = "next";
            $this->lang['no'] = "No";
            $this->lang['no_data_found'] = "No data found";
            $this->lang['no_data_found_error'] = "No data found! Please, check carefully your code syntax!<br />It may be case sensitive or there are some unexpected symbols.";
            $this->lang['no_image'] = "No Image";
            $this->lang['not_like'] = "not like";
            $this->lang['of'] = "of";
            $this->lang['or'] = "or";
            $this->lang['pages'] = "Pages";
            $this->lang['page_size'] = "Page size";
            $this->lang['previous'] = "previous";
            $this->lang['printable_view'] = "Printable View";
            $this->lang['print_now'] = "Print Now";
            $this->lang['print_now_title'] = "Click here to print this page";
            $this->lang['record_n'] = "Record # ";
            $this->lang['refresh_page'] = "Refresh Page";
            $this->lang['remove'] = "Remove";
            $this->lang['reset'] = "Reset";
            $this->lang['results'] = "Results";
            $this->lang['required_fields_msg'] = "<font color='#cd0000'>*</font> Items marked with an asterisk are required";
            $this->lang['search'] = "Search";
            $this->lang['search_d'] = "Search"; // (description)
            $this->lang['search_type'] = "Search type";
            $this->lang['select'] = "select";
            $this->lang['set_date'] = "Set date";
            $this->lang['sort'] = "Sort";
            $this->lang['total'] = "Total";
            $this->lang['turn_on_debug_mode'] = "For more information, turn on debug mode.";
            $this->lang['uncheck_all'] = "Uncheck All";
            $this->lang['unhide_search'] = "Unhide Search";
            $this->lang['unique_field_error'] = "The field _FIELD_ allows only unique values - please reenter!";
            $this->lang['update'] = "Update";
            $this->lang['update_record'] = "Update record";
            $this->lang['update_record_blocked'] = "Security check: attempt of updating a record! Check your settings, the operation is not allowed!";
            $this->lang['updating_operation_completed'] = "The updating operation completed successfully!";
            $this->lang['updating_operation_uncompleted'] = "The updating operation uncompleted!";
            $this->lang['upload'] = "Upload";
            $this->lang['view'] = "View";
            $this->lang['view_details'] = "View details";
            $this->lang['warnings'] = "Warnings";
            $this->lang['with_selected'] = "With selected";
            $this->lang['wrong_field_name'] = "Wrong field name";
            $this->lang['wrong_parameter_error'] = "Wrong parameter in [<b>_FIELD_</b>]: _VALUE_";
            $this->lang['yes'] = "Yes";
        }
    }

}// end class

?>