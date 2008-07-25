<?php

class DataGridCruises extends DataGrid
{
  
    // Inheritance of DataGridCruises
    
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
                        //if($div_align){ echo "<div style='float:"; echo ($this->direction == "rtl")?"right":"left"; echo ";'>"; }
                        echo $nbsp."<img style='cursor:pointer;' onClick=".$onClick." src='".$this->directory."images/".$this->css_class."/extention_a/".$image_file."' alt='$alt_name' title='$alt_name' />".$nbsp;
                        //if($div_align) echo "</div>"; 
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
                        echo "<img align='middle' src='".$this->directory."images/".$this->css_class."/extention_a/".$image_file."' readonly />";
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

        //if(($this->paging_allowed) && ($this->upper_paging != "")) $this->pagingSecondPart($this->upper_paging, false, true, "Upper");
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
        #if(isset($this->modes['add'][$this->mode]) && $this->modes['add'][$this->mode] && $this->draw_add_button_separately){                    
        #    echo "<table dir='".$this->direction."' border='0' align='".$this->tblAlign[$this->mode]."' width='".$this->tblWidth[$this->mode]."'>";
        #    echo "<tr>";
        #    echo "<td align='".(($this->direction == "ltr") ? "left" : "right")."'><b>";
        #        $curr_url = $this->combineUrl("add", "-1");
        #        $this->setUrlString($curr_url, "filtering", "sorting", "paging");        
        #        $this->drawModeButton("add", $curr_url, $this->lang['add_new'], $this->lang['add_new_record'], "add.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$curr_url."';\"", false, "", "");                        
        #    echo "</b></td>";
        #    echo "</tr>";
        #    echo "</table>";
        #    $this->modes['add'][$this->mode] = false;
        #}

        $this->scrollDivOpen();

        //echo "<table  border='0' align='center' width='90%'>";
        //echo "<tr><td style='BACKGROUND: url(images/tblheadrightbg.gif) no-repeat right top;'>";

        //echo "<div style='width:600px; padding'>";
        echo "<form name='".$this->unique_prefix."frmTabulerViewMode' id='".$this->unique_prefix."frmTabulerViewMode' action=''>";
        //$this->tblOpen();
        echo "<table dir='ltr' class='gray_class_table' align='center' width='".$this->tblWidth[$this->mode]."' style='border-top:0px;border-right:0px;border-left:0px;'>";
        //style='BACKGROUND: url(images/tblheadrightbg.gif) no-repeat right top;'
        
        
        // *** START DRAWING HEADERS -------------------------------------------
        $this->rowOpen("");

            // draw multi-row checkboxes header
            if(($this->multirow_allowed) && ($this->rows_total > 0)){                
                $this->colOpen("center",0,"nowrap",$this->rowColor[0], $this->css_class."_class_td", "26px");
                echo $this->nbsp;
                $this->colClose();
            }            

            #// draw add link-button cell
            #if(isset($this->modes['add'][$this->mode]) && $this->modes['add'][$this->mode]){            
            #    $curr_url = $this->combineUrl("add", "-1");
            #    $this->setUrlString($curr_url, "filtering", "sorting", "paging");
            #    $this->mainColOpen("center",0,"nowrap", "1%", $this->css_class."_class_th_normal");
            #    $this->drawModeButton("add", $curr_url, $this->lang['add_new'], $this->lang['add_new_record'], "add.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$curr_url."';\"", false, "", "");                        
            #    $this->mainColClose();
            #}else{            
            #    if(isset($this->modes['edit'][$this->mode]) && $this->modes['edit'][$this->mode]){
            #        $this->mainColOpen("center",0,"nowrap", "1%", $this->css_class."_class_th_normal"); echo $this->nbsp; $this->mainColClose();                
            #    }
            #}
    
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

                            if($c_sorted == $this->col_lower){
                                //--- bof first column
                                //$this->mainColOpen("center", 0, $field_property_wrap, $field_property_width, $th_css_class);                                
                                echo "<th align='left' valign='top' onmouseover=\"bgColor='#dedede';\" onmouseout=\"bgColor='#dedede';\" style='width:13px; BACKGROUND: url(".$this->directory."/images/".$this->css_class."/header_background.gif) repeat-x left bottom; BORDER-BOTTOM:1px solid #d0d0d0; padding:0px; margin:0px;'>";
                                echo "<img src='".$this->directory."/images/".$this->css_class."/extention_a/tblheadleftbg.gif' style='margin:0 auto; border:0px;' />";
                                echo "</th>";
                                //--- eof first column
                                $this->mainColOpen("center", 0, $field_property_wrap, $field_property_width, $th_css_class, 'style=border-left:0px;');
                            }
                            else{
                                $this->mainColOpen("center", 0, $field_property_wrap, $field_property_width, $th_css_class);                                
                            }

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
                                    //echo "<table border=1 align='center' style='width:50%; height:15px;'><tr><td>";
                                    //echo "<div style='margin:0 auto; text-align:center; width:80%; background-color:#ff0000;'>";
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
                                    //echo "</td></tr></table>";
                                    //echo "</div>";
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
            //if(isset($this->modes['delete'][$this->mode]) && $this->modes['delete'][$this->mode]){
            //    $this->mainColOpen("center",0,"nowrap", "10%", $this->css_class."_class_th_normal");echo $this->lang['delete'];$this->mainColClose();
            //}
            
            //--- bof last column
            echo "<th align='center' class='gray_class_th' width='50px' valign='middle' style='BACKGROUND: url(".$this->directory."/images/".$this->css_class."/header_background.gif) repeat-x right bottom; BORDER:0px;  BORDER-BOTTOM:1px solid #d0d0d0; padding:0px; margin:0px; FONT-SIZE:12px; COLOR: #fafafa; '>"; 
            echo "Select";
            $this->mainColClose();
            echo "<th align='right' width='13px' valign='top' style='BACKGROUND: url(".$this->directory."/images/".$this->css_class."/header_background.gif) repeat-x right bottom; BORDER-BOTTOM:1px solid #d0d0d0; BORDER-LEFT:0px;  padding:0px; margin:0px;'>"; 
            echo "<img src='".$this->directory."/images/".$this->css_class."/extention_a/tblheadrightbg.gif' style='border:0px; align:right;' />";
            $this->mainColClose();
            //--- eof last column
            
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
                
                //if(isset($this->modes['delete']) && $this->modes['delete'][$this->mode]) $this->colOpen("center",0,"nowrap");echo"";$this->colClose();                
                echo "</form>"; 
                $this->rowClose();                
            }
                            
            //if we add a new row on linked tabular view mode table (mode 0 <-> 0) 
            if($quick_exit == true){
                $this->tblClose();
                echo "<script>document.getElementById('".$this->unique_random_prefix."loading_image').style.display='none';</script>";                
                if(($this->first_field_focus_allowed) && ($first_field_name != "")) echo "<script type='text/javascript'>\n<!--\n document.".$this->unique_prefix."frmEditRow.".$this->getFieldRequiredType($first_field_name).$first_field_name.".focus(); \n//-->\n</script>";                
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
                #if(isset($this->modes['add'][$this->mode]) && $this->modes['add'][$this->mode]){                    
                #    $this->colOpen("center",0,"nowrap",$this->rowColor[2], $this->css_class."_class_td_main");$this->colClose();                    
                #}
            }
            
            if($this->rows_numeration){
                $this->colOpen("center",0,"nowrap");
                echo "<label class='".$this->css_class."_class_label'>".($r+1)."</label>";
                $this->colClose();
            }

            // draw column data
            for($c_sorted = $this->col_lower; $c_sorted < count($this->sorted_columns); $c_sorted++){
                // get current column's index (offset)
                $c = $this->sorted_columns[$c_sorted];
                $col_align = $this->getFieldAlign($c, $row);
                $field_property_wrap = $this->getFieldProperty($this->getFieldName($c), "wrap", "view", "lower", $this->wrap);
                if(($this->mode === "view") && ($this->canViewField($this->getFieldName($c)))){                    
                    if($c_sorted == $this->col_lower){
                        //--- bof first column
                        echo "<td class='gray_class_td' style='BORDER-RIGHT:0px solid #d0d0d0; '></td>";
                        $this->colOpen($col_align, 0, $field_property_wrap, "", "", "", "style='border-left:0px;'");                        
                        //--- eof first column
                    }else{
                        if($req_sort_field == $c+1){
                            $this->colOpen($col_align, 0, $field_property_wrap, $this->rowColor[0], $this->css_class."_class_td_selected");
                        }else{
                            $this->colOpen($col_align, 0, $field_property_wrap);
                        }
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
            #if(isset($this->modes['delete'][$this->mode]) && $this->modes['delete'][$this->mode]){
            #    $curr_url = $this->combineUrl("delete", $row_id);
            #    $this->setUrlString($curr_url, "filtering", "sorting", "paging");                                                                            
            #    $this->colOpen("center",0,"nowrap");
            #    $this->drawModeButton("delete", $curr_url, $this->lang['delete'], $this->lang['delete_record'], "delete.gif", "\"return ".$this->unique_prefix."verifyDelete('$curr_url');\"", false, "", "");                        
            #    $this->colClose();
            #}
            if($this->mode == "view"){
                $this->colOpen("center",0,"nowrap", "", "", "", "style='BORDER-RIGHT:0px solid #d0d0d0;'");
                echo "<input type='radio' name='rid_radio' value='".$row_id."' />";
                $this->colClose();
                echo "<td class='gray_class_td' style='BORDER-LEFT:0px solid #d0d0d0; '></td>";
            }            

            if(($this->mode == "edit") && (intval($this->rid) == intval($row[$this->getFieldOffset($this->primary_key)]))){ echo "</form>"; }
            $this->rowClose();
        }
        // *** END ROWS --------------------------------------------------------        
       
        
        // draw summarizing row
        if($r != $this->row_lower){ $this->drawSummarizeRow($r); }         
        $this->tblClose();
        echo "</form>";        
        
        // draw empty table       
        if($r == $this->row_lower){ $this->noDataFound(); }
        $this->scrollDivClose();        
        
        $this->drawMultiRowBar($r, $curr_url);  // draw multi-row row footer cell
        
        if($this->paging_allowed) $this->pagingSecondPart($this->lower_paging, true, true, "Lower");

        
        $curr_url_edit = $this->combineUrl("edit", "");
        $this->setUrlString($curr_url_edit, "filtering", "sorting", "paging");
        $curr_url_delete = $this->combineUrl("delete", "");
        $this->setUrlString($curr_url_delete, "filtering", "sorting", "paging");
        
        echo "
        <script>
            var selected_rid = 0;
            
            function radio_button_checker()
            {
                // set var radio_choice to false
                var radio_choice = false;
                var form_el = document.getElementById('".$this->unique_prefix."frmTabulerViewMode');
                
                // Loop from zero to the one minus the number of radio button selections
                if(form_el.rid_radio.length){                
                    for (counter = 0; counter < form_el.rid_radio.length; counter++){
                        if (form_el.rid_radio[counter].checked){
                            radio_choice = true;
                            selected_rid = form_el.rid_radio[counter].value;
                        }
                    }                
                }else{
                    if (form_el.rid_radio.checked){
                        radio_choice = true;
                        selected_rid = form_el.rid_radio.value;
                    }
                }
                
                if (!radio_choice){ return false; }
                return true;
            }
        
            //------------------------------------------------------------------        
            function editRow(param){
                if(!radio_button_checker()){
                    alert('Please select a row!');
                    return false;
                }
                document.location.href='".$this->HTTP_URL.$curr_url_edit."&".$this->unique_prefix."rid='+selected_rid;
                return true;
            }
            
            //------------------------------------------------------------------
            function verifyDelete(param){
                if(!radio_button_checker()){
                    alert('Please select a row.'); return false;
                }
                if(confirm('Are you sure you want to delete this record?')){
                    document.location.href='".$this->HTTP_URL.$curr_url_delete."&".$this->unique_prefix."rid='+selected_rid;
                } else {
                    window.event.cancelBubble = true; return false;
                }
            };
        
        </script>
        ";
        
        echo "<table class='".$this->css_class."_class_paging_table' dir='".$this->direction."' align='".$this->tblAlign[$this->mode]."' width='".$this->tblWidth[$this->mode]."' border='0'>";
        $this->rowClose();
        echo "<td align='left'>";
        if($this->rows_total > 0){        
            // eidt button 
            $this->drawModeButton("edit", $curr_url, $this->lang['edit'], $this->lang['edit_record'], "edit.gif", "\"javascript: editRow(); \"", false, $this->nbsp, "");
            echo "&nbsp;&nbsp;";            
            echo "<a href='#' onclick='verifyDelete();'><image src='".$this->directory."images/".$this->css_class."/extention_a/delete.gif'></a>&nbsp;&nbsp;";
        }  
        // draw add link-button cell
        if(isset($this->modes['add'][$this->mode]) && $this->modes['add'][$this->mode]){
            $curr_url = $this->combineUrl("add", "-1");
            $this->setUrlString($curr_url, "filtering", "sorting", "paging");
            $this->drawModeButton("add", $curr_url, $this->lang['add_new'], $this->lang['add_new_record'], "add.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$curr_url."';\"", false, "", "");                        
        }else{            
            if(isset($this->modes['edit'][$this->mode]) && $this->modes['edit'][$this->mode]){
                $this->mainColOpen("center",0,"nowrap", "1%", $this->css_class."_class_th_normal"); echo $this->nbsp; $this->mainColClose();                
            }
        }
  
        echo "</td>";
        $this->rowClose();
        $this->tblClose();

        
        // draw hide DG close div 
        $this->hideDivClose();
        echo "<script>document.getElementById('".$this->unique_random_prefix."loading_image').style.display='none';</script>";
        
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
        $this->tblOpen("style='border:0px;'");
        // draw header
        //$this->rowOpen($r);        
        //$this->mainColOpen("center",0,"nowrap","32%", (($req_print == true) ? $this->css_class."_class_td" : $this->css_class."_class_th")); echo "<b>".(($this->field_header != "") ? $this->field_header : $this->lang['field'])."</b>"; $this->mainColClose(); 
        //$this->mainColOpen("center",0,"nowrap","68%", (($req_print == true) ? $this->css_class."_class_td" : $this->css_class."_class_th")); echo "<b>".(($this->field_value_header != "") ? $this->field_value_header : $this->lang['field_value'])."</b>"; $this->mainColClose(); 
        //$this->rowClose();        

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
                else $this->rowOpen($r, $this->rowColor[1], "", "", "", "");
                if($key == "delimiter"){
                    $this->colOpen(($this->direction == "rtl")?"right":"left",2,"nowrap");
                        echo $this->getFieldProperty("delimiter", "inner_html");
                    $this->colClose();
                }else if($key == "validator"){
                    $field_property_for_field = $this->getFieldProperty("validator", "for_field");
                    $field_property_header    = $this->getFieldProperty("validator", "for_field");
                    // column's header
                    $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap");                
                        echo $this->nbsp;echo "".ucfirst($field_property_header)."";                        
                    $this->colClose();
                    // column's data                    
                    $col_align = ($this->direction == "rtl")?"right":"left";
                    $this->colOpen($col_align,0,"nowrap");
                        echo $this->getFieldValueByType('', 0, '', $field_property_for_field);
                    $this->colClose();
                }else if($this->getFieldProperty($key, "type") == "hidden"){
                    echo $this->getFieldValueByType('', 0, '', $key);
                }else{
                    // column's header
                    $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap", "", "", "", "style='border:0px;background-color:#ffffff;padding:2px;'");                
                        echo $this->nbsp;echo "".ucfirst($this->getHeaderName($key))."";                        
                    $this->colClose();
                    // column's data
                    $col_align = ($this->direction == "rtl")?"right":"left";
                    $this->colOpen($col_align,0,"nowrap", "", "", "", "style='border:0px;background-color:#ffffff;padding:2px;'");
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
                //if($r % 2 == 0) $this->rowOpen($r, $this->rowColor[0]);
                //else $this->rowOpen($r, $this->rowColor[1]);
                echo "<tr>";
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
                            $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap", "", "", "", "style='border:0px;padding:2px;'");                   
                            echo $this->nbsp;echo "".ucfirst($this->getHeaderName($this->getFieldName($c)))."";                        
                            $this->colClose();
                        }else if(($this->mode === "details") && ($this->canViewField($this->getFieldName($c)))){
                            $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap");                   
                            echo $this->nbsp;echo "".ucfirst($this->getHeaderName($this->getFieldName($c)))."";                        
                            $this->colClose();
                        }
                        
                        // column data 
                        $col_align = ($this->direction == "rtl") ? "right" : "left";
                        if(($this->mode === "view") && ($this->canViewField($this->getFieldName($c)))){
                            $this->colOpen($col_align,0,$this->columns_view_mode[$this->getFieldName($c)]['wrap']);
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
                                        $this->colOpen($col_align,0,"nowrap", "", "", "", "style='border:0px;padding:2px;'");
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
                                $field_property_header    = $this->getFieldProperty($key, "for_field");
                                $this->colOpen(($this->direction == "rtl")?"right":"left",0,"nowrap");                   
                                    echo $this->nbsp;echo "".ucfirst($field_property_header)."";                        
                                $this->colClose();
                                $this->colOpen($col_align,0,$this->columns_view_mode[$this->getFieldName($c)]['wrap']);
                                    echo $this->getFieldValueByType('', $this->getFieldOffset($field_property_for_field), $row);
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
            $this->tblOpen("style='border:0px;'");
            echo "<tr>";
            echo "<td>";
            //$this->rowOpen($r, $this->rowColor[1]);
            //$this->mainColOpen('left', 0, '', '', '', "style='BORDER:0px;'");
            if($this->mode === "details"){
                $cancel_url = $this->combineUrl("cancel", $row[$this->getFieldOffset($this->primary_key)]);
                $this->setUrlString($c_curr_url, "filtering", "sorting", "paging");                                                                                                        
                $cancel_url .= $c_curr_url;                
                //echo "<div style='float:";
                //echo ($this->direction == "rtl")?"left":"right";
                if($req_print != ""){
                    //echo ";'><span class='".$this->css_class."_class_a'><b>".$this->lang['back']."</b></span></div>";                                        
                }else{
                    //echo ";'>";
                    echo $this->drawModeButton("cancel", $cancel_url, $this->lang['back'], $this->lang['back'], "cancel.gif", "\"javascript:document.location.href='".$this->HTTP_URL.$cancel_url."'\"", false, "", "");
                    //echo "</div>";
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
                                    
                    //echo "<div style='float:"; echo ($this->direction == "rtl")?"left":"right"; echo ";'>";    
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
                    //echo "</div>";
                }else{
                    //echo "<div style='float:"; echo ($this->direction == "rtl")?"left":"right"; echo ";'>";    
                    $this->drawModeButton("cancel", $cancel_url, $this->lang['back'], $this->lang['back'], "cancel.gif", "\"\"", false, $this->nbsp, "");
                    //echo "</div>";
                }
            }
            $this->mainColClose();
            $this->rowClose();
            $this->tblClose();              
        }
        
        echo "</form>";
        echo "<script>document.getElementById('".$this->unique_random_prefix."loading_image').style.display='none';</script>";
        
        if($this->paging_allowed) $this->pagingSecondPart($this->lower_paging, true, true, "Lower");               
        if(($this->first_field_focus_allowed) && ($first_field_name != "")) echo "<script type='text/javascript'>\n<!--\n document.".$this->unique_prefix."frmEditRow.".$this->getFieldRequiredType($first_field_name).$first_field_name.".focus(); \n//-->\n</script>";
    } 

    
    
}

?>