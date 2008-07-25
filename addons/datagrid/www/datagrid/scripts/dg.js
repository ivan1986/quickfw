<!--

function isCookieAllowed(){
   setCookie('cookie_allowed',1,10); 
   if(readCookie('cookie_allowed') != 1) {alert('This operation requires that your browser accepts cookies! Please turn on cookies accepting.'); return false; }; 
   return true; 
}

function setCookie(name,value,days) {
   if (days) {
      var date = new Date();
      date.setTime(date.getTime()+(days*24*60*60*1000));
      var expires = '; expires='+date.toGMTString();
   }
   else var expires = '';
   document.cookie = name+'='+value+expires+'; path=/';
}

function readCookie(name) {
   var nameEQ = name + '=';
   var ca = document.cookie.split(';');
   for(var i=0;i < ca.length;i++) {
      var c = ca[i];
      while (c.charAt(0)==' ') c = c.substring(1,c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
   }
   return null;
}

// load new event 
function addDgLoadEvent(func) {
   var oldonload = window.onload;
   if (typeof window.onload != 'function') {
      window.onload = func;
   }else {
      window.onload = function() {
      oldonload();
      func();
      }
   }
}

// hide/unhide Filtering
function hideUnHideFiltering(type, unique_prefix){
   if(!isCookieAllowed()) return false;
   unique_prefix = (unique_prefix == null) ? '' : unique_prefix;
   if(type == 'hide'){
      document.getElementById(unique_prefix+'searchset').style.display = 'none'; 
      document.getElementById(unique_prefix+'a_hide').style.display = 'none'; 
      document.getElementById(unique_prefix+'a_unhide').style.display = ''; 
      setCookie(unique_prefix+'hide_search',1,10); 
   }else{
      document.getElementById(unique_prefix+'searchset').style.display = ''; 
      document.getElementById(unique_prefix+'a_hide').style.display = ''; 
      document.getElementById(unique_prefix+'a_unhide').style.display = 'none'; 
      setCookie(unique_prefix+'hide_search',0,10); 
   }
   return true;
}

// reload form with some action, saving entered data
function formAction(file_act, file_id, unique_prefix, http_url, query_string){
   unique_prefix = (unique_prefix==null) ? "" : unique_prefix;
   http_url = (http_url==null) ? "" : http_url;
   query_string = (query_string==null) ? "" : query_string;
   //alert(http_url+"?"+query_string+"&"+unique_prefix+"file_act="+file_act+"&"+unique_prefix+"file_id="+file_id);	   

   document.getElementById(unique_prefix+'frmEditRow').action=http_url+"?"+query_string+"&"+unique_prefix+"file_act="+file_act+"&"+unique_prefix+"file_id="+file_id;
   document.getElementById(unique_prefix+'frmEditRow').encoding='multipart/form-data';
   document.getElementById(unique_prefix+'frmEditRow').method='POST';
   document.getElementById(unique_prefix+'frmEditRow').submit();
}

// calendar script (popup)
function openCalendar(directory, params, form, req_type, field, type) {
   if (type != 'time') height = '240'; else height = '100';
   window.open(directory+'modules/calendar/calendar.php?' + params, 'calendar', 'width=220, height='+height+',status=yes');
   dateField = eval('document.' + form + '.' + req_type + field);
   dateType = type;
}


//-->