function LiveSearch(field, div) { this.construct(field, div) }
LiveSearch.prototype = {
  url:     (window.BASE_SITE || '') + '/dk_improve/live/LiveSearch/load_search.php',
  field:   null,
  div:     null,
  prevQ:   '',
  prevT:   null,
  timeout: null,
  
  construct: function(field, div) {
    this.field = field;
    this.div = div;
    this.prevT = new Date();
    
    var th = this;
    addEvent(field, 'onkeydown', function(e) {
      th.prevT = new Date();
      if (e.keyCode == 13) th.onchangeControl(this.value, 100);
      return true;
    })
    addEvent(field, 'onkeyup', function(e) { 
      if (e.keyCode == 13) return true;
      th.onchangeControl(this.value, e.keyCode==32? 1000 : null);
      return true;
    })
    addEvent(field, 'onfocus', function() {
      // stupid Mozilla sometimes loses focus on DIV repainting :-(
      th.focused = true;
      return true;
    })
    addEvent(field, 'onblur', function() {
      th.onchangeControl(field.value, 0);
      th.focused = false;
      return true;
    })
  },
  
  onchangeControl: function(text, dt) {
    var t = new Date();
    var wait = 0;
    if (dt == null) dt = 2000;
    
    if (t.getTime() - this.prevT.getTime() < dt) {
      this.prevT = t;
      wait = dt;
    }
    
    var th = this;
    if (this.timeout) { clearTimeout(this.timeout); this.timeout=null; }
    this.timeout = setTimeout(function() { th.prevT = t; th.timeout=null; th.onchange(text) }, wait);
  },
  
  onchange: function(text, force) {
    var q = this.clean(text);
    if (q != this.prevQ && q != "") {
      this.prevQ = q;
      var th = this;
      var req = new JsHttpRequest();
      req.onreadystatechange = function() {
        if (window.hackerConsole) window.hackerConsole.out(req.responseText, '', 'Shell');
        if (req.readyState != 4) return;
        if (!req.responseJS || !Math.round(req.responseJS[0])) {
          th.div.style.display = "none";
          return;
        }
        th.div.innerHTML = req.responseJS[1];
        if (window.livePreview) {
          var links = th.div.getElementsByTagName('A');
          for (var i=0; i<links.length; i++) {
            window.livePreview.attachLink(links[i]);
          }
        }
        th.div.style.display = "block";
        if (th.focused) th.field.focus();
      }
      req.caching = true;
      req.open('GET', th.url, true);
      req.send({ 'q': q });
    }
  },
  
  clean: function(text) {
    var spl = text.split(/[\s~!@#&$%^*()\[\]{}:\"<>?`=;\',\.\/\\|\-]+/i);
    var words = [];
    for (var i=0; i<spl.length; i++) if (!spl[i].match(/^[a-zà-ÿ_0-9]{0,2}$/)) 
      words[words.length] = spl[i].toLowerCase();
    return words.join(" ");
  }
};

