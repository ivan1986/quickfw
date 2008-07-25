<!--// 

function onMouseClickRow(unique_prefix, row_id, row_color, row_color_light, row_color_dark){
   if((row_id % 2) == 0) row_color_back = row_color_dark;
   else row_color_back = row_color_light;
   if(document.getElementById(unique_prefix+'checkbox_'+row_id)){
      if(document.getElementById(unique_prefix+'checkbox_'+row_id).checked == true){
         document.getElementById(unique_prefix+'checkbox_'+row_id).checked = false;
         document.getElementById(unique_prefix+'row_'+row_id).style.background = row_color_back;                        
      }else{
         document.getElementById(unique_prefix+'checkbox_'+row_id).checked = true;
         document.getElementById(unique_prefix+'row_'+row_id).style.background = row_color;
      }
   }else{
      document.getElementById(unique_prefix+'row_'+row_id).style.background = row_color;
   }
}

function onMouseOverRow(unique_prefix, row_id, row_color, row_selected_color_dark){
   if(document.getElementById(unique_prefix+'checkbox_'+row_id)){
      if(document.getElementById(unique_prefix+'checkbox_'+row_id).checked != true){
         document.getElementById(unique_prefix+'row_'+row_id).style.background = row_color;
      }else{
         document.getElementById(unique_prefix+'row_'+row_id).style.background = row_selected_color_dark;                    
      }
   }else{
      document.getElementById(unique_prefix+'row_'+row_id).style.background = row_color;                
   }
}            

function onMouseOutRow(unique_prefix, row_id, row_color, row_color_selected){
   if(document.getElementById(unique_prefix+'checkbox_'+row_id)){
      if(document.getElementById(unique_prefix+'checkbox_'+row_id).checked != true){
         document.getElementById(unique_prefix+'row_'+row_id).style.background = row_color;
      }else{
         document.getElementById(unique_prefix+'row_'+row_id).style.background = row_color_selected;
      }
   }else{
      document.getElementById(unique_prefix+'row_'+row_id).style.background = row_color;                
   }
}            

//-->