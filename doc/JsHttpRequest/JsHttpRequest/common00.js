// Multiple onload callback support.
(function() {
  var onloads = [];
  window.attachOnload = function(func) {
    onloads[onloads.length] = func;
  }
  window.executeOnloads = function() {
    for (var i=0; i<onloads.length; i++) onloads[i]();
  }
})();

// Copy text to clipboard.
// Originally got from decompiled `php_manual_en.chm`.
function copyText(from) { 
    if (!document.body.createTextRange) return false;
    var BodyRange = document.body.createTextRange(); 
    if (!BodyRange.moveToElementText) return false;
    BodyRange.moveToElementText(from); 
    if (!BodyRange.execCommand) return false;
    BodyRange.execCommand("Copy"); 
    return true;
} 

// Change display attribute of elements name+"_"+i (i=0,1,2,...).
function changeDisplay(name, display, dt) {
	var e, t=0;
  for (var i=0; e=document.getElementById(name+"_"+i); i++) {
  	if (!dt) {
	    e.style.display = display;
	  } else {
	  	(function (e, t) {
		  	setTimeout(function() { e.style.display = display; }, t);
		  })(e, t);
			t += dt;
	  }
  }
}

// Show or hide elements with IDs name+"_"+i, where i is counter
// from 0 till max non-existed element.
function showHide(name, onload) {
  var cookName = 'off_'+name;
  var off = Math.round(getCookie(cookName));
  if (!onload) off = !off;
  var e;
  for (var i=0; e=document.getElementById(name+"_"+i); i++) {
    e.style.display = off? 'none' : '';
  }
  setCookie(cookName, off? 1 : 0, '/', new Date(new Date().getTime()+3600*24*365*1000));
  return !off;
}

// Make element grayed.
function makeGray(e) {
  if (!e.style || e.style.position == 'absolute') return;
  //e.style.fontWeight = "normal";
  e.style.color = "#888888";
  e.style.textDecoration = "line-through";
  for (var n=0; n<e.childNodes.length; n++) makeGray(e.childNodes[n]);
}

// Функция установки значения cookie.
function setCookie(name, value, path, expires, domain, secure) {
  var curCookie = name + "=" + escape(value) +
    ((expires) ? "; expires=" + expires.toGMTString() : "") +
    ((path) ? "; path=" + path : "; path=/") +
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
  document.cookie = curCookie;
}

// Функция чтения значения cookie.
function getCookie(name) {
  var prefix = name + "=";
  var cookieStartIndex = document.cookie.indexOf(prefix);
  if(cookieStartIndex == -1) return null;
  var cookieEndIndex = document.cookie.indexOf(";", cookieStartIndex + prefix.length);
  if(cookieEndIndex == -1) cookieEndIndex = document.cookie.length;
  return unescape(document.cookie.substring(cookieStartIndex + prefix.length, cookieEndIndex));
}

// Cross-browser addEventListener()/attachEvent() replacement.
function addEvent(elt, name, handler, atEnd) {
  name = name.replace(/^(on)?/, 'on'); 
  var prev = elt[name];
  var tmp = '__tmp';
  elt[name] = function(e) {
    if (!e) e = window.event;
    var result;
    if (!atEnd) {
      elt[tmp] = handler; result = elt[tmp](e); elt[tmp] = null; // delete() does not work in IE 5.0 (???!!!)
      if (result === false) return result;
    }
    if (prev) {
      elt[tmp] = prev; result = elt[tmp](e); elt[tmp] = null;
    }
    if (atEnd && result !== false) {
      elt[tmp] = handler; result = elt[tmp](e); elt[tmp] = null;
    }
    return result;
  }
  return handler;
}

// Emulation of innerText for Mozilla.
if (window.HTMLElement && window.HTMLElement.prototype.__defineSetter__) {
  HTMLElement.prototype.__defineSetter__("innerText", function (sText) {
     this.innerHTML = sText.replace(/\&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  });
  HTMLElement.prototype.__defineGetter__("innerText", function () {
     var r = this.ownerDocument.createRange();
     r.selectNodeContents(this);
     return r.toString();
  });
}


// Support for strikes.
attachOnload(function() {
  var elts = document.getElementsByTagName("SPAN");
  var grayer = function() {
    makeGray(this.parentNode);
    return true;
  };
  for (var i=0; i<elts.length; i++) {
    var span = elts[i];
    if (span.className.indexOf("strikable") >= 0) {
      for (var n=0; n<span.childNodes.length; n++) {
        var child = span.childNodes[n];
        if (!child.style) continue;
        addEvent(child, 'onclick', addEvent(child, 'onmousedown', grayer));
      }
    }
  }
});

// DOM cleaner
// Remove all DOM nodes to free memory - stupid IE bug?
function documentCleaner (e) {
  domCleaner(document);
  for (var i in document) i = null;
}
function domCleaner(node) {
  while (node.firstChild) {
    domCleaner(node.firstChild);
    node.removeChild(node.firstChild);
  }
}
if (window.attachEvent && !window.opera) window.attachEvent('onunload',documentCleaner);
//else if(window.addEventListener) window.addEventListener('unload',documentCleaner,false);
// commented - FF & Opera do not need garbage collection

window.attachOnload (function(){
  var str = "GEN_BY_JS";
  var frs = document.getElementsByTagName("form");
  for (var i=0,fL=frs.length;i<fL;i++) {
    var inp = document.createElement("input");
    inp.type = "hidden";
    inp.name = "nospam";
    inp.value = str;
    frs[i].appendChild(inp);
  }
});