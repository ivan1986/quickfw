<!--// 
////////////////////////////////////////////////////////////////////////////////
//
// JS Auto Form Validator version 1.0.4 (07.02.2008)
// Author: Leumas Naypoka <leumas.a@gmail.com>
// Lisence: GNU GPL
// Site: http://phpbuilder.blogspot.com
//
////////////////////////////////////////////////////////////////////////////////
//
// Usage:
// -----
// //*** copy & paste this line between <head> and </head> tags
// <script type="text/JavaScript" src="form.scripts.js"></script> 
//
// //*** copy & paste these lines between before your </form> tag
// <!--
//  first parameter  - (required) form name
//  second parameter - (optional, default - false) handle all fields or handle each field separately
//  third parameter  - (optional, default - false) handle hidden fields or not 
// -->
// <input type="submit" name="button" value="Submit"
//        onClick="return onSubmitCheck(document.forms['form_name'], false,false);"> 
//
////////////////////////////////////////////////////////////////////////////////

// - new type checked for checkboxes |radiobuttons (> 0 or ????)
// - mistake renter instead of re-enter
// - u url
// wiki: - if element in non-displayed area -. error (must be non-dosplaed too)
//       - w - web site address (or d - domain?) shared simbol

// =============================================================================
// TODO
// - end third letter for all types - strings
// - new type domain
// - template type - x - fields (xxx-xx-xx) with js template
// - getting started full + wiki
// - isSet - Parse (' "" ...)  - pass dig + lett -> current not works */
// - level of difficulty for passwords - letters, l+digits etc.
// =============================================================================


var digits="0123456789";
var digits1="0123456789.";
var digits2="0123456789,";
var digits3="0123456789.,";
var textchars="/'\"[]{}()*&^%$#@!~?<>-_+=|\\ \r\t\n.,:;`";
var lwr="abcdefghijklmnopqrstuvwxyz";
var upr="ABCDEFGHIJKLMNOPQRSTUVWXYZ";

var diac_lwr='абвгдеёжзийклмнопрстуфхцчшщьыъэюя';
var diac_upr='АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯ';

// r - required, s - simple
var rtypes="rs";
// n - numeric,     i - integer,    f - float,
// a - alphabetic,  t - text,       e - email,
// p - password,    y - any,        l - login
// z - zipcode,     v - verified    c- checked (for chekboxes)
// u - url
var vtypes="nifatepylzvcu";
// for numbers: s - signed, u - unsigned,   p - positive,   n - negative
// for strings: u - upper,  l - lower,      n - normal,     y - any
var svtypes="supnly";       

function makeArray(n){for(var i=1; i<=n;i++){this[i]=0;}return this;};
var dInM=makeArray(12);dInM[1]=31;dInM[2]=29;dInM[3]=31;dInM[4]=30;dInM[5]=31;dInM[6]=30;dInM[7]=31;dInM[8]=31;dInM[9]=30;dInM[10]=31;dInM[11]=30;dInM[12]=31;
var PassLength=6;
var LoginLength=6;

var bgcolor_error = "#ff8822";
var bgcolor_normal_1 = "#ffffff";
var bgcolor_normal_2 = "#fcfaf6";
var MaxInt=13
var MaxString=30;
var MaxAdress=200;
var MaxCP=15;
var whitespace=" \t\n\r";                     
var decimalPointDelimiter=".";                  
var phoneNumberDelimiters="()- ";  
var validPhoneChars=digits + phoneNumberDelimiters;
var validWorldPhoneChars=digits + phoneNumberDelimiters + "+"; 
var SSNDelimiters="- ";
var validSSNChars=digits + SSNDelimiters;  // intr-un nr. SSN
var digitsInSocialSecurityNumber=9;
var digitsInPhoneNumber=9;
var digitsInMinPhoneNumber=5;
var ZIPCodeDelimiters="-";
var validZIPCodeChars=digits + ZIPCodeDelimiters;
var digitsInZIPCode1=5;
var digitsInZIPCode2=9;
var creditCardDelimiters=" "
var USStateCodeDelimiter="|";
var DEOK=false;

function isEmpty(s){return((s==null)||(s.length==0))}
function isShorter(str_text, str_length){s_length=(str_length==null) ? "1" : str_length;if(str_text.length < s_length) return true;else return false;}
function isValid(parm,val){if(parm=="")return true;for(i=0;i<parm.length;i++){if(val.indexOf(parm.charAt(i),0)==-1)return false;}return true;}
function isSubmitReqType(parm){return isLower(parm) && isValid(parm,rtypes);}
function isSubmitVarType(parm){return isLower(parm) && isValid(parm,vtypes);}
function isSubmitSubVarType(parm){return isLower(parm) && isValid(parm,svtypes);}
function isNumeric(parm,type){ptype=(type==null)?"0":type; pdigits=-1;switch(ptype){case 0:pdigits=digits;break;case1:pdigits=digits1;break;case 2:pdigits=digits2;break;case 3:pdigits=digits3;break;default:pdigits=digits;break;}return isValid(parm,pdigits);}
function isLower(parm){return isValid(parm,lwr);}
function isUpper(parm){return isValid(parm,upr);}
function isAlpha(parm){return isValid(parm,lwr + upr);}
function isAlphaNumeric(parm){return isValid(parm,lwr + upr + digits);}
function isText(parm){return isValid(parm,lwr + upr + digits3 + textchars + diac_lwr + diac_upr);}
function isAny(parm){return true;}
function isWhitespace(s){i=0;if(isEmpty(s)) return true; for(i=0;i< s.length;i++){c=s.charAt(i);if(whitespace.indexOf(c)==-1) return false;} return true;}
function isLetter(c){return (((c>="a")&&(c<="z"))||((c>="A")&&(c<="Z"))||((c>="а")&&(c<="я"))||((c>="А")&&(c<="Я")))}
function isDigit(c){return ((c>="0")&&(c<="9"))}
function isLetterOrDigit(c){return (isLetter(c)||isDigit(c))}

// integer checking
function isInteger(s){ i; if(isEmpty(s)) if(isInteger.arguments.length==1) return DEOK; else return (isInteger.arguments[1]==true); for(i=0;i< s.length;i++){ c=s.charAt(i); if(!isDigit(c)) return false; } return true;}
function isSignedInteger(s){ if(isEmpty(s)){ if(isSignedInteger.arguments.length==1) return DEOK; else return (isSignedInteger.arguments[1]==true); }else{ startPos=0; secondArg=DEOK; if(isSignedInteger.arguments.length>1) secondArg=isSignedInteger.arguments[1]; if((s.charAt(0)=="-") || (s.charAt(0)=="+")) startPos=1; return (isInteger(s.substring(startPos,s.length),secondArg));}}
function isPositiveInteger(s){secondArg=DEOK;if(isPositiveInteger.arguments.length > 1) secondArg=isPositiveInteger.arguments[1];return (isSignedInteger(s,secondArg) && ((isEmpty(s) && secondArg) || (parseInt(s) > 0)));}
function isNegativeInteger(s){secondArg=DEOK;if(isNegativeInteger.arguments.length > 1) secondArg=isNegativeInteger.arguments[1]; return (isSignedInteger(s,secondArg) && ((isEmpty(s) && secondArg) || (parseInt(s) < 0)));}
function isIntegerInRange(s,a,b){if(isEmpty(s))if(isIntegerInRange.arguments.length==1) return DEOK;else return (isIntegerInRange.arguments[1]==true);if(!isInteger(s, false)) return false;num=parseInt(s);return ((num >=a) && (num <=b));}
// float checking
function isFloat(s){i=0; seenDecimalPoint=false; if(isEmpty(s)){ if (isFloat.arguments.length==1) return DEOK; else return (isFloat.arguments[1]==true); } if(s==decimalPointDelimiter) return false; for(i=0; i < s.length; i++){ c=s.charAt(i); if((c==decimalPointDelimiter) && !seenDecimalPoint) seenDecimalPoint=true; else if(!isDigit(c)) return false; } return true;}
function isSignedFloat(s){if(isEmpty(s)) if(isSignedFloat.arguments.length==1) return DEOK; else return (isSignedFloat.arguments[1]==true); else{ startPos=0;secondArg=!DEOK; if(isSignedFloat.arguments.length > 1) secondArg=isSignedFloat.arguments[1]; if((s.charAt(0)=="-") || (s.charAt(0)=="+")) startPos=1; return (isFloat(s.substring(startPos, s.length), secondArg))}}
function isPositiveFloat(s){secondArg=DEOK;if(isPositiveFloat.arguments.length > 1) secondArg=isPositiveFloat.arguments[1];return (isSignedFloat(s,secondArg) && ((isEmpty(s) && secondArg) || (parseInt(s) > 0)));}
function isNegativeFloat(s){secondArg=DEOK;if(isNegativeFloat.arguments.length > 1) secondArg=isNegativeFloat.arguments[1];return (isSignedFloat(s,secondArg) && ((isEmpty(s) && secondArg) || (parseInt(s) < 0)));}

function isAlphabetic(s){i=0;if(isEmpty(s))if(isAlphabetic.arguments.length==1) return DEOK;else return (isAlphabetic.arguments[1]==true);for(i=0;i<s.length;i++){c=s.charAt(i);if(!isLetter(c)) return false;}return true;}
function isAlphanumeric(s){i=0;if(isEmpty(s))if(isAlphanumeric.arguments.length==1) return DEOK;else return (isAlphanumeric.arguments[1]==true);for(i=0;i<s.length;i++){c=s.charAt(i);if(!(isLetter(c) || isDigit(c))) return false;}return true;}
function isZipCode(s){return isValid(s,validZIPCodeChars);}

function Trim(fld){result="";c=0; for(i=0;i<fld.length;i++){if (fld.charAt(i) !=" " || c > 0){result +=fld.charAt(i);if (fld.charAt(i) !=" ") c=result.length;}}return result.substr(0,c);} 
function isEmail(s){if(isEmpty(s))if(isEmail.arguments.length==1) return DEOK;else return(isEmail.arguments[1]==true);if(isWhitespace(s)) return false;i=1;sLength=s.length;while((i<sLength) && (s.charAt(i) !="@")){i++};if((i >=sLength) || (s.charAt(i) !="@")) return false;else i +=2;while((i < sLength) && (s.charAt(i) !=".")){i++};if((i >=sLength - 1) || (s.charAt(i) !=".")) return false;else return true;}
function isPassword(s){return !isShorter(s,PassLength) && isValid(s,lwr+upr + digits);};
function isLogin(s){return (!isShorter(s,LoginLength) && isValid(s.charAt(0),lwr + upr) && isValid(s,lwr + upr + digits));};
function validField(fld){fld=stripBlanks(fld);if(fld=='') return false;return true;}

function isMobPhoneNumber(s){if(isEmpty(s))if(isMobPhoneNumber.arguments.length==1) return DEOK; else return (isMobPhoneNumber.arguments[1]==true); return (isInteger(s)  && s.length==digitsInPhoneNumber);}
function isFixPhoneNumber(s){if(isEmpty(s))if(isFixPhoneNumber.arguments.length==1) return DEOK; else return (isFixPhoneNumber.arguments[1]==true); return (isInteger(s) && s.length==digitsInPhoneNumber);}
function isInternationalPhoneNumber(s){if(isEmpty(s))if(isInternationalPhoneNumber.arguments.length==1) return DEOK; else return (isInternationalPhoneNumber.arguments[1]==true);  return (isPositiveInteger(s)); }

function isYear(s){if(isEmpty(s))if(isYear.arguments.length==1)return DEOK; else return (isYear.arguments[1]==true); if (!isNonnegativeInteger(s)) return false; return (s.length==4);}
function isMonth(s){if(isEmpty(s))if(isMonth.arguments.length==1)return DEOK;else return (isMonth.arguments[1]==true);return isIntegerInRange(s,1,12);}
function isDay(s){if(isEmpty(s))if(isDay.arguments.length==1)return DEOK;else return (isDay.arguments[1]==true);return isIntegerInRange(s, 1, 31);}
function daysInFebruary(year){return(((year % 4==0) && ((!(year % 100==0)) || (year % 400==0) ) ) ? 29 : 28 );}
function isDate(year,month,day){if(!(isYear(year,false) && isMonth(month, false) && isDay(day, false))) return false; intYear=parseInt(year); intMonth=parseInt(month); intDay=parseInt(day); if (intDay > dInM[intMonth]) return false; if ((intMonth==2) && (intDay > daysInFebruary(intYear))) return false; return true; }

function isChecked(frm,ind){ return frm.elements[ind].checked; };
function isURL(url){ 
    var RegExp = /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/; 
    if(RegExp.test(url)){ 
        return true; 
    }else{ 
        return false; 
    } 
} 

function getProValidateFieldValue(frm,p_ind){cur_field_name=frm.elements[p_ind].name.substring(2,frm.elements[p_ind].name.length);cur_field_prefics = frm.elements[p_ind].name.substring(0,2);found_field_ind=-1;for(gvind=0;((gvind<frm.elements.length) && (found_field_ind==-1));gvind++){if((cur_field_name==frm.elements[gvind].name.substring(2, frm.elements[gvind].name.length)) && (cur_field_prefics != frm.elements[gvind].name.substring(0,2))){found_field_ind=gvind; break;}}if(found_field_ind !=-1) return frm.elements[found_field_ind].value;else return -1;}
function getValidateField(frm,p_ind,ret_type){cur_field_name=frm.elements[p_ind].name.substring(2,frm.elements[p_ind].name.length);found_field_ind=-1;for(gvind=0;((gvind<frm.elements.length) && (found_field_ind==-1));gvind++){if(cur_field_name==frm.elements[gvind].name.substring(2, frm.elements[gvind].name.length))found_field_ind=gvind;}if(found_field_ind !=-1){if(ret_type=="type") return frm.elements[found_field_ind].name.charAt(1);else return frm.elements[found_field_ind].title;}else{return 0;}}
function isValidateField(frm,p_ind){validation_result=false;cur_field_name=frm.elements[p_ind].name.substring(2,frm.elements[p_ind].name.length);cur_field_type=frm.elements[p_ind].name.charAt(1);found_field_ind=-1;for(vind=0;((vind<frm.elements.length)&&(found_field_ind==-1));vind++){if((cur_field_type !=frm.elements[vind].name.charAt(1)) && (cur_field_name==frm.elements[vind].name.substring(2, frm.elements[vind].name.length)))found_field_ind=vind;}if(found_field_ind !=-1){if(frm.elements[found_field_ind].name.charAt(1)=="e"){validation_result=isEmail(frm.elements[p_ind].value);}else if(frm.elements[found_field_ind].name.charAt(1)=="p"){validation_result=isPassword(frm.elements[p_ind].value);}else{validation_result=false;}}else{validation_result=false;}return validation_result;}
function equalValidateField(frm,p_ind){validation_result=false;cur_field_name=frm.elements[p_ind].name.substring(2,frm.elements[p_ind].name.length);cur_field_type=frm.elements[p_ind].name.charAt(0);found_field_ind=-1;for(evind=0;((evind<frm.elements.length) && (found_field_ind==-1)); evind++){ if((cur_field_type !=frm.elements[evind].name.charAt(1)) && (cur_field_name==frm.elements[evind].name.substring(2, frm.elements[evind].name.length))) found_field_ind=evind; }if(found_field_ind !=-1){validation_result=(frm.elements[p_ind].value==frm.elements[found_field_ind].value);}else{validation_result=false;}return validation_result;}

function setNormalBackground(frm, ind){
    if(frm.elements[ind].type.substring(0,6) !="select"){
        frm.elements[ind].style.background = bgcolor_normal_1;
    }else{
        frm.elements[ind].style.background = bgcolor_normal_2;                            
    }    
}
function setErrorBackground(frm, ind){
    frm.elements[ind].style.background = bgcolor_error;                                
}
function getFieldTitle(frm,ind){title_field=frm.elements[ind].title;if(title_field=="")title_field=frm.elements[ind].name.substring(3,frm.elements[ind].name.length);return title_field;}
function onSubmit(frm){return true;}

function onReqAlert(frm,ind,all_fields){
    check_all_fields = (all_fields==null) ? false : true;
    is_first_found = (is_found==null) ? false : is_found;
    title_of_field=getFieldTitle(frm,ind);
    setErrorBackground(frm, ind);
    if((!is_first_found) && (frm.elements[ind].style.display != "none")) {
        frm.elements[ind].focus();
    }
    if(check_all_fields){
        /// return "The <" + title_of_field + "> is a required field!\n";
        return FormValudator._MSG['TITLE_OF_FIELD'].replace("_TITLE_OF_FIELD_", title_of_field);
    }else{
        alert("The <" + title_of_field + "> is a required field!\nPlease, enter a valid " + title_of_field + ".");
        if(frm.elements[ind].type.substring(0,6) !="select"){ frm.elements[ind].select(); }
        return false;        
    }
}

function onInvalidAlert(frm,ind,ftype,fstype,all_fields){
    check_all_fields = (all_fields==null) ? false : true;
    is_first_found = (is_found==null) ? false : is_found;
    type_of_field="value";
    title_of_field=getFieldTitle(frm,ind);
    switch (fstype){ //supnly
        case 's': syb_type_of_field="a signed "; break;
        case 'u': syb_type_of_field="an unsigned "; syb_type_of_field2="an upper case"; break;
        case 'p': syb_type_of_field="a positive "; break;
        case 'n': syb_type_of_field="a negative "; syb_type_of_field2="a normal case "; break;
        case 'l': syb_type_of_field="a lower case "; break;
        default: syb_type_of_field="a "; syb_type_of_field2="a "; break; 
    }
    switch (ftype){
        case 'n': type_of_field="be "+syb_type_of_field+"numeric value"; break;
        case 'i': type_of_field="be "+syb_type_of_field+"integer value"; break;
        case 'f': type_of_field="be "+syb_type_of_field+"float(real) value"; break;
        case 'a': type_of_field="be "+syb_type_of_field2+"alphabetic value"; break;
        case 't': type_of_field="be "+syb_type_of_field2+"text"; break;
        case 'p': type_of_field="be " + PassLength + " characters at least\nand consist of letters and digits"; break;
        case 'l': type_of_field="be " + LoginLength + " characters at least,\nstart from letter and consist of letters or digits"; break;
        case 'z': type_of_field="be a zip(post) code value"; break;
        case 'e': type_of_field="be in email format"; break;
        case 'v': if(getValidateField(frm, ind, "type")=="e")
                    type_of_field="be in email format"; 
                  else if(getValidateField(frm, ind, "type")=="p")
                    type_of_field="be " + PassLength + " characters at least"; 
                  else
                    type_of_field="be a required type";
                  break;
        case 'c': type_of_field=""; break;
        case 'u': type_of_field="be a valid URL"; break;
        default: break; 
    }
    setErrorBackground(frm, ind);
    if(!is_first_found) frm.elements[ind].focus();
    if(check_all_fields){
        if(ftype == "c") return "You have to sign <" + title_of_field + "> box as checked!\n";
        else return "The <" + title_of_field + "> field must " + type_of_field + "!\n";        
    }else{
        if(ftype == "c") alert("You have to sign <" + title_of_field + "> box as checked!\n");
        else alert("The <" + title_of_field + "> field must " + type_of_field + "!\nPlease, re-enter.");
        if(frm.elements[ind].type.substring(0,6) !="select") frm.elements[ind].select();
        return false;            
    }
}

function onNotEqualAlert(frm,ind,all_fields,is_found){
    check_all_fields = (all_fields==null) ? false : true;
    is_first_found = (is_found==null) ? false : is_found;
    type_of_field=getValidateField(frm, ind, "name");
    title_of_field=getFieldTitle(frm,ind);
    if(type_of_field==0) type_of_field="required field";
    setErrorBackground(frm, ind);
    if(!is_first_found) frm.elements[ind].focus();
    if(check_all_fields){
        return "The <" + title_of_field + "> field must be match with " + type_of_field + "!\n";        
    }else{
        alert("The <" + title_of_field + "> field must be match with " + type_of_field + "!\nPlease, re-enter.");        
        if(frm.elements[ind].type.substring(0,6) !="select") frm.elements[ind].select();
        return false;
    }
}


// parametr - check hidden fields+check display.none fileds 
function onSubmitCheck(frm, handle_all_fields, handle_hidden_fields){
    check_all_fields = (handle_all_fields == null) ? false : handle_all_fields;
    check_hidden_fields = (handle_hidden_fields == null) ? false : handle_hidden_fields;
    is_required="";
    a_type="";
    b_type="";
    msg = "";
    is_found = false;
    for(ind=0;ind<frm.elements.length;ind++){
        if((frm.elements[ind].type.substring(0,6) != "submit") && (frm.elements[ind].type.substring(0,6) != "button"))
            setNormalBackground(frm,ind);
    }        
    for(ind=0;ind<frm.elements.length;ind++){
        if(!check_hidden_fields){            
           if(frm.elements[ind].type.substring(0,6) == "hidden") continue;
        }
        is_required=frm.elements[ind].name.charAt(0);
        a_type=frm.elements[ind].name.charAt(1);
        b_type=frm.elements[ind].name.charAt(2);
        if(!isSubmitSubVarType(b_type)) b_type = "";        
        true_value=true;
        if(isSubmitReqType(is_required)
           && isSubmitVarType(a_type)
           && (((frm.elements[ind].style.display !="none") && (frm.elements[ind].type != 'textarea')) || (frm.elements[ind].type == 'textarea'))
          )
        {
            field_value=frm.elements[ind].value; //trim
            if(is_required=='r'){
                if(isEmpty(field_value)){
                    if(check_all_fields){
                        msg += onReqAlert(frm,ind,check_all_fields,is_found);
                        is_found = true;                                                
                        continue;
                    }else{
                        return onReqAlert(frm,ind);
                    }
                }else{
                    setNormalBackground(frm,ind);
                }
            };
            if(((is_required=='r') || ((is_required=='s') && (!isEmpty(field_value)))) ||
                ((a_type=='v') && (!isEmpty(getProValidateFieldValue(frm,ind)))) 
              ){
                switch (a_type){
                    case 'n': if(!isNumeric(field_value, 3))    { true_value=false; } break;
                    case 'i':
                        switch (b_type){                   
                            case 's': if(!isSignedInteger(field_value))   { true_value=false; } break;
                            case 'u': if(!isInteger(field_value))         { true_value=false; } break;
                            case 'p': if(!isPositiveInteger(field_value)) { true_value=false; } break;
                            case 'n': if(!isNegativeInteger(field_value)) { true_value=false; } break;
                            default:  if(!isSignedInteger(field_value))   { true_value=false; } break;
                        }
                        break;
                    case 'f':
                        switch (b_type){                   
                            case 's': if(!isSignedFloat(field_value))     { true_value=false; } break;
                            case 'u': if(!isFloat(field_value))           { true_value=false; } break;
                            case 'p': if(!isPositiveFloat(field_value))   { true_value=false; } break;
                            case 'n': if(!isNegativeFloat(field_value))   { true_value=false; } break;
                            default: if(!isSignedFloat(field_value))      { true_value=false; } break;
                        }
                        break;                        
                    case 'a': if(!isAlphabetic(field_value))    { true_value=false; } break;
                    case 't': if(!isText(field_value))          { true_value=false; } break;
                    case 'e': if(!isEmail(field_value))         { true_value=false; } break;
                    case 'p': if(!isPassword(field_value))      { true_value=false; } break;
                    case 'y': if(!isAny(field_value))           { true_value=false; } break;
                    case 'l': if(!isLogin(field_value))         { true_value=false; } break;
                    case 'z': if(!isZipCode(field_value))       { true_value=false; } break;
                    case 'v': if(!isValidateField(frm, ind))    { true_value=false; }
                              else if(!equalValidateField(frm, ind)){
                                    if(check_all_fields){
                                        msg += onNotEqualAlert(frm, ind, check_all_fields,is_found);
                                    }else{
                                        return onNotEqualAlert(frm, ind);
                                    }
                                    is_found = true;
                                }                              
                              break;
                    case 'c': if(!isChecked(frm,ind))           { true_value=false; } break;
                    case 'u': if(!isURL(field_value))           { true_value=false; } break;                            
                    default: break; 
                }
                if(!true_value){
                    if(check_all_fields){
                        msg += onInvalidAlert(frm, ind, a_type, b_type, check_all_fields,is_found);    
                    }else{
                        return onInvalidAlert(frm, ind, a_type, b_type);    
                    }
                    is_found = true;
                }
            }                            
        }
    }
    if(check_all_fields){
        if(msg != ""){
            alert(msg);
            return false;
        }            
    }    
    return true;    
}
//-->