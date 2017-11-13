//Joust Outliner Version 2.5.3
//(c) Copyright 1996-2001, MITEM (Europe) Ltd. All rights reserved.
//This code may be freely copied and distributed provided that it is accompanied by this 
//header.  For full details of the Joust license, as well as documentation and help, go 
//to http://www.ivanpeters.com/.
function simpleArray() {this.item = 0;}
function imgStoreItem(n, s, w, h) {
	this.name = n;
	this.src = s;
	this.obj = null;
	this.w = w;
	this.h = h;
	if ((theBrowser.canCache) && (s)) {
		this.obj = new Image(w, h);
		this.obj.src = s;
	}
}
function imgStoreObject() {
	this.count = -1;
	this.img = new imgStoreItem;
	this.find = imgStoreFind;
	this.add = imgStoreAdd;
	this.getSrc = imgStoreGetSrc;
	this.getTag = imgStoreGetTag;
}
function imgStoreFind(theName) {
	var foundItem = -1;
	for (var i = 0; i <= this.count; i++) {if (this.img[i].name == theName) {foundItem = i;break;}}
	return foundItem;
}
function imgStoreAdd(n, s, w, h) {
	var i = this.find(n);
	if (i == -1) {i = ++this.count;}
	this.img[i] = new imgStoreItem(n, s, parseInt(w, 10), parseInt(h, 10));
}
function imgStoreGetSrc(theName) {
	var i = this.find(theName);
	var img = this.img[i];
	return (i == -1) ? '' : ((img.obj) ? img.obj.src : img.src);
}
function imgStoreGetTag(theName, iconID, altText) {
	var i = this.find(theName);
	if (i < 0) {return ''}
	with (this.img[i]) {
		if (src == '') {return ''}
		var tag = '<img src="' + src + '" width="' + w + '" height="' + h + '" border="0" align="left" hspace="0" vspace="0"';
		tag += (iconID != '') ? ' name="' + iconID + '"' : '';
		tag += ' alt="' + ((altText)?altText:'') + '">';
	}
	return tag;
}
// The MenuItem object.  This contains the data and functions for drawing each item.
function MenuItem (owner, id, type, text, url, status, nItem, pItem, parent, target) {
	var t = this;
	this.multiLine = false;
//this.noOutlineImg=true;
	this.owner = owner;
	this.id = id;
	this.type = type;
	this.text = text;
	this.url = url;
	this.status = status;
	this.target = target;
	this.nextItem = nItem;
	this.prevItem = pItem;
	this.FirstChild = -1;
	this.parent = parent;
	this.isopen = false;
	this.isSelected = false;
	this.draw = MIDraw;
	this.PMIconName = MIGetPMIconName;
	this.docIconName = MIGetDocIconName;
	this.setImg = MISetImage;
	this.setIsOpen = MISetIsOpen;
	this.setSelected = MISetSelected;
	this.setIcon = MISetIcon;
	this.mouseOver = MIMouseOver;
	this.mouseOut = MIMouseOut;
	var i = (this.owner.imgStore) ? this.owner.imgStore.find(type) : -2;
	if (i == -1) {i = this.owner.imgStore.find('iconPlus');}
	this.height = (i > -1) ? this.owner.imgStore.img[i].h : 0;
}

function MIDraw (indentStr) {
	var o = this.owner;
	var mRef = '="return ' + o.reverseRef + "." + o.name;
	var tmp = mRef + '.entry[' + this.id + '].';
	var MOver = ' onMouseOver' + tmp + 'mouseOver(\''
	var MOut = ' onMouseOut' + tmp + 'mouseOut(\''
	var iconTag = o.imgStore.getTag(this.PMIconName(), 'plusMinusIcon' + this.id, '');
	var aLine = '<nobr>' + indentStr;
	if (!this.noOutlineImg) {
		if (this.FirstChild != -1) {
			aLine += '<A HREF="#" onClick' + mRef + '.toggle('+theMenu.yOffsetMethod+',' + this.id + ');"' + MOver + 'plusMinusIcon\',this);"' + MOut + 'plusMinusIcon\');">' + iconTag + '</A>';				
		} else {
			aLine += iconTag;
		}
	}
	var tip = (o.tipText == 'text') ? this.text : ((o.tipText == 'status') ? this.status : '');
	var theEntry = o.imgStore.getTag(this.docIconName(), 'docIcon' + this.id, tip) + this.text;
	var theImg = o.imgStore.getTag(this.docIconName(), 'docIcon' + this.id, tip);
	var sTxt = '<SPAN CLASS="' + ((this.CSSClass) ? this.CSSClass : ((this.FirstChild != -1) ? 'node' : 'leaf')) + '">';
	var lTxt = '<A NAME="joustEntry' + this.id + '"';
	var theUrl = (((this.url == '') && theBrowser.canJSVoid && o.showAllAsLinks) || o.wizardInstalled) ? 'javascript:void(0);' : this.url;
	if (theUrl != '') {
		if (this.target.charAt(0) == "_") {}else{theUrl = parent.getFullPath() + theUrl;}
         //theUrl = "javascript:" + o.reverseRef + ".loadURLInTarget('" + theUrl + "', '" + this.target + "');";}//
			lTxt += ' HREF="' + theUrl + '" TARGET="' + this.target + '" onClick' + mRef + '.itemClicked('+theMenu.yOffsetMethod+',' + this.id + ');"'
			+ MOver + 'docIcon\',this);"' + MOut + 'docIcon\');"';
	}
	lTxt += (tip) ? ' TITLE="' + tip + '">' : '>';
	aLine += sTxt + lTxt + theImg;
	if (this.multiLine) {
		aLine += '</A></SPAN><TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0"><TR><TD>' + sTxt + lTxt + this.text + '</A></SPAN></TD></TR></TABLE>';
	} else {
		aLine += this.text + '</A></SPAN>';
	}
	aLine += '</nobr>';
	if ((theBrowser.hasW3CDOM) && (theBrowser.hasDHTML) && (!this.multiLine) )  { aLine += '<br>'; }
	return aLine
}
function MIGetPMIconName() {
	var n = 'icon' + ((this.FirstChild != -1) ? ((this.isopen == true) ? 'Minus' : 'Plus') : 'Join');
	n += (this.id == this.owner.firstEntry) ? ((this.nextItem == -1) ? 'Only' : 'Top') : ((this.nextItem == -1) ? 'Bottom' : '');
	return n;
}
function MIGetDocIconName() {
	var is = this.owner.imgStore; var n = this.type;

	n += ((this.isopen) && (this.isSelected) && (is.getSrc(n + 'OSelected') != '')) ? 'OSelected' : '';
	n += ((this.isopen) && (!this.isSelected) && (is.getSrc(n + 'Expanded') != '')) ? 'Expanded' : '';

	n += ((this.isSelected) && (is.getSrc(n + 'CSelected') != '')) ? 'CSelected' : '';
	return n;
}
function MISetImage(imgID, imgName) {
	var o = this.owner; var s = o.imgStore.getSrc(imgName);
	if ((s != '') && (theBrowser.canCache) && (!o.amBusy)) {
		var img = eval(o.container).document.images[imgID];
		if (img && img.src != s) {img.src = s;} 
	}
}
function MISetIsOpen (isOpen) {
	if ((this.isopen != isOpen) && (this.FirstChild != -1)) {
		this.isopen = isOpen;
		this.setImg('plusMinusIcon' + this.id, this.PMIconName());
		this.setImg('docIcon' + this.id, this.docIconName());
		return true;
	} else {
		return false;
	}
}
function MISetSelected (isSelected) {
	this.isSelected = isSelected;
	this.setImg('docIcon' + this.id, this.docIconName());
	if ((this.parent >= 0) && this.owner.selectParents) {this.owner.entry[this.parent].setSelected(isSelected);}
}
function MISetIcon (newType) {
	this.type = newType;
	this.setImg('docIcon' + this.id, this.docIconName());
}
function MIMouseOver(imgName, theURL) {
	eval(this.owner.container).status = '';  //Needed for setStatus to work on MSIE 3 - Go figure!?
	var newImg = '';
	var s = '';
	if (imgName == 'plusMinusIcon') {
		newImg = this.PMIconName();
		s = 'Click to ' + ((this.isopen == true) ? 'collapse.' : 'expand.');
	} else {
		if (imgName == 'docIcon') {
			newImg = this.docIconName();
			s = (this.status != null) ? this.status : theURL;
		}
	}
	setStatus(s);
	if (theBrowser.canOnMouseOut) {this.setImg(imgName + this.id, newImg + 'MouseOver');}
	if(this.onMouseOver) {var me=this;eval(me.onMouseOver);}
	return true;
}
function MIMouseOut(imgName) {
	clearStatus();
	var newImg = '';
	if (imgName == 'plusMinusIcon') {
		newImg = this.PMIconName();
	} else {
		if (imgName == 'docIcon') {newImg = this.docIconName();}
	}
	this.setImg(imgName + this.id, newImg);
	if(this.onMouseOut) {var me=this;eval(me.onMouseOut);}
	return true;
}
// The Menu object.  This is basically an array object although the data in it is a tree.
function Menu () {
        this.pgyoffset = 0;
        this.yOffsetMethod = 'window.pageYOffset';
	this.count = -1;
	this.version = '2.5.3';
	this.firstEntry = -1;
	this.autoScrolling = false;
	this.modalFolders = false;
	this.linkOnExpand = false;
	this.toggleOnLink = true;
	this.showAllAsLinks = true;
	this.savePage = true;
	this.name = 'theMenu';
	this.container = 'left';
	this.reverseRef = 'parent';
	this.contentFrame = 'right';
	this.defaultTarget = 'right';
	this.tipText = 'none';
	this.selectParents = false;
	this.lastPMClicked = -1;
	this.selectedEntry = -1;
	this.wizardInstalled = false;
	this.amBusy = true;
	this.maxHeight = 0;
	this.imgStore = new imgStoreObject;
	this.entry = new MenuItem(this, 0, '', '', '', '', -1, -1, -1,'right');
	this.contentWin = MenuGetContentWin;
	this.getEmptyEntry = MenuGetEmptyEntry;
	this.addEntry = MenuAddEntry;
	this.addMenu = MenuAddEntry;
	this.addChild = MenuAddChild;
	this.draw = MenuDraw;
	this.drawALevel = MenuDrawALevel;
	this.refresh = MenuRefresh;
	this.reload = MenuReload;
	this.scrollTo = MenuScrollTo;
	this.itemClicked = MenuItemClicked;
	this.selectEntry = MenuSelectEntry;
	this.setEntry = MenuSetEntry;
	this.setEntryByURL = MenuSetEntryByURL;
	this.findEntry = MenuFindEntry;
	this.toggle = MenuToggle;
}
function MenuGetContentWin() {
	return eval(((myOpener != null) ? 'myOpener.' : 'self.') + this.contentFrame);
}
function MenuGetEmptyEntry() {
	for (var i = 0; i <= this.count; i++) {if (this.entry[i] == null) {break;}}
	if (i > this.count) {this.count = i};
	return i
}
function MenuAddEntry (addTo, type, text, url, status, target, insert) {
	if (!target) {target=this.defaultTarget;}
	if (!insert) {insert=false;}
	var theNI = -1;var theP = -1;var thePI = -1;
	if (addTo < 0) {
		var i = addTo = this.firstEntry;
		if (!insert) {while (i > -1) {addTo = i;i = this.entry[i].nextItem;}}
	}
	if (addTo >= 0) {
		var e = this.entry[addTo];
		if (!e) {return -1;}
		thePI = (insert)?e.prevItem:addTo;
		theNI = (insert)?addTo:e.nextItem;
		theP = e.parent;
	}
	var eNum = this.getEmptyEntry();
	if (thePI >= 0) {
		this.entry[thePI].nextItem = eNum;
	} else {
		if (theP >= 0) {
			this.entry[theP].FirstChild = eNum;
		} else {
			this.firstEntry = eNum;
		}
	}
	if (theNI >= 0) {this.entry[theNI].prevItem = eNum;}
	this.entry[eNum] = new MenuItem(this, eNum, type, text, url, status, theNI, thePI, theP, target);
	return eNum;
}
function MenuAddChild (addTo, type, text, url, status, target, insert) {
	if (!target) {target=this.defaultTarget;}
	if (!insert) {insert=false;}
	var eNum = -1;
	if ((this.count == -1) || (addTo < 0)) {
		eNum = this.addEntry(-1, type, text, url, status, target, false);
	} else {
		var e = this.entry[addTo];
		if (!e) {return -1;}
		var cID = e.FirstChild;
		if (cID < 0) {
			e.FirstChild = eNum = this.getEmptyEntry();
			this.entry[eNum] = new MenuItem(this, eNum, type, text, url, status, -1, -1, addTo, target);	
		} else {
			while (!insert && (this.entry[cID].nextItem >= 0)) {cID = this.entry[cID].nextItem;}
			eNum = this.addEntry(cID, type, text, url, status, target, insert);
		}
	}
	return eNum;
}
function MenuDraw() {
	this.maxHeight = 0;
	var theDoc = eval(this.container + ".document");
	eval(this.container).document.writeln(this.drawALevel(this.firstEntry, '', true, theDoc));
	if ((this.lastPMClicked > 0) && theBrowser.mustMoveAfterLoad && this.autoScrolling) {
		this.scrollTo(this.lastPMClicked);
		}
	
}
function MenuDrawALevel(firstItem, indentStr, isVisible, theDoc) {
	var currEntry = firstItem;
	var padImg = "";
	var aLine = "";
	var theLevel = "";
	var e = null;
	while (currEntry > -1) {
		e = this.entry[currEntry];
		aLine = e.draw(indentStr);	
aLine += '<BR CLEAR="ALL">';	
		theBrowser.lineByLine = true;
		if (theBrowser.lineByLine) {theDoc.writeln(aLine);} else {theLevel += aLine;}
		if ((e.FirstChild > -1) && (e.isopen && isVisible)) {
			padImg =  this.imgStore.getTag((e.nextItem == -1) ? 'iconBlank' : 'iconLine', '', '');
			theLevel += this.drawALevel(e.FirstChild, indentStr + padImg, (e.isopen && isVisible), theDoc);
		}
		currEntry = e.nextItem;
	}
	return theLevel;
}
function MenuRefresh() {
	this.reload();
}
function MenuReload() {
	if (!this.amBusy) {
		this.amBusy = true;
		var l = eval(this.container).location;
		var rm = theBrowser.reloadMethod;
		var newLoc = fixPath(l.pathname);
		if (l.search) {newLoc += l.search;}
		if (theBrowser.code == 'OP') {var d = new Date(); newLoc += '?' + d.getTime();}
		if (this.autoScrolling && (this.lastPMClicked > 0) && !theBrowser.mustMoveAfterLoad) {
			newLoc += "#joustEntry" + this.lastPMClicked;
		}
		if (rm == 'replace') {
			l.replace(newLoc);
		} else {
			if (rm == 'reload') {
				l.reload();
			} else {
				if (rm == 'timeout') {
					setTimeout(this.container + ".location.href ='" + newLoc + "';", 100);
				} else {
					l.href = newLoc;
				}
			}
		}
	}
}
function MenuScrollTo(entryNo) {
	var l; 
        if(this.pgyoffset != -1)
	{ 
   	   l = fixPath(eval(this.container).location.pathname) + '#joustEntry';// + entryNo;
           setTimeout(this.container + '.location.href = "' + l + '";' + this.container + '.scrollTo(0,'+this.pgyoffset+');', 100);
        }
        else
        {
           l = fixPath(eval(this.container).location.pathname) + '#joustEntry' + entryNo;
           setTimeout(this.container + '.location.href = "' + l + '";' , 100);        
        }
this.pgyoffset=-1;
}
function MenuItemClicked(yoffset,entryNo, fromToggle) {
	var r = true;
	var e = this.entry[entryNo];
	var w = this.contentWin();
	var b = theBrowser;
this.pgyoffset = yoffset;
	this.selectEntry(entryNo);
	if (this.wizardInstalled) {w.menuItemClicked(entryNo);}
	if(e.onClickFunc) {e.onClick = e.onClickFunc;}
	if(e.onClick) {var me=e;if(eval(e.onClick) == false) {r = false;}}
	if (r) {
		if (((this.toggleOnLink)  && (e.FirstChild != -1) && !(fromToggle)) || e.noOutlineImg) {
			setTimeout(this.name + '.toggle('+yoffset+',' + entryNo + ', true);', 100);
		}
	}
	return (e.url != '') ? r : false;
}
function isEntrySelected(entryNo) {
	if(this.selectedEntry == entryNo){ return true;}
        return false;
}
function MenuSelectEntry(entryNo) {
	var ee = this.entry[entryNo];
	if(ee.url != ''){
		var oe = this.entry[this.selectedEntry];
		if (oe) {oe.setSelected(false);}
		var e = this.entry[entryNo];
		if (e) {e.setSelected(true);}
		this.selectedEntry = entryNo;
	}
}
function MenuSetEntry(entryNo, state) {
	var cl = ',' + entryNo + ',';
	var e = this.entry[entryNo];
	this.lastPMClicked = entryNo;
	var mc = e.setIsOpen(state);
	var p = e.parent;
	while (p >= 0) {
		cl += p + ',';
		e = this.entry[p];
		mc |= (e.setIsOpen(true));
		p = e.parent;
	}
	if (this.modalFolders) {
		for (var i = 0; i <= this.count; i++) {
			e = this.entry[i];
			if ((cl.indexOf(',' + i + ',') < 0) && e) {mc |= e.setIsOpen(false);}
		}
	}
	return mc;
}
function MenuSetEntryByURL(theURL, state) {
	var i = this.findEntry(theURL, 'url', 'right', 0);
	return (i != -1) ? this.setEntry(i, state) : false;
}
function MenuFindEntry(srchVal, srchProp, matchType, start) {
	var e;
	if (srchVal == "") {return -1;}
	if (!srchProp) {srchProp = "url";}
	if (!matchType) {matchType = "exact";}
	if (!start) {start = 0;}
	if (srchProp == "URL") {srchProp = "url";}
	if (srchProp == "title") {srchProp = "text";}
	eval("this.sf = cmp_" + matchType);
	for (var i = start; i <= this.count; i++) {
		if (this.entry[i]) {
			e = this.entry[i];
			if (this.sf(eval("e." + srchProp), srchVal)) {return i;}
		}		
	}
	return -1;
}
function cmp_helpsys(c, s) {   
        if(c=="")
          return false;     
        var cb = c;
        var str = c;
	if(c.length < s.length)
        {
           cb = s;
           s = str;
        }        
	if(cb.indexOf(s) >= 0)
        {
          return true;
        }
        else
        {
           if(cb.lastIndexOf("#") >= 0) { cb = cb.substring(0,cb.lastIndexOf("#")); }
           if(s.lastIndexOf("#") >= 0) { s = s.substring(0,s.lastIndexOf("#")); }
 	   if(cb.indexOf(s) >= 0)
           {
             return true;
           }
        }
   return false;
}
function cmp_exact(c, s) {return (c == s);}
function cmp_left(c, s) {
	var l = Math.min(c.length, s.length);
	return ((c.substring(1, l) == s.substring(1, l)) && (c != ""));
}
function cmp_right(c, s) {
	var l = Math.min(c.length, s.length);
	return ((c.substring(c.length-l) == s.substring(s.length-l)) && (c != ""));
}
function cmp_contains(c, s) {return (c.indexOf(s) >= 0);}
function MenuToggle(yoffset,entryNo, fromClicked) {
	var r = true;
this.pgyoffset = yoffset;
	var e = this.entry[entryNo];
	if (e.onToggle) {var me=e;if(eval(e.onToggle) == false) {r = false;}}
	if (r) {
		var chg = this.setEntry(entryNo, e.isopen ^ 1);
		if (this.linkOnExpand && e.isopen) {
			if (e.url != '') {loadURLInTarget(e.url, e.target);}
			if (!fromClicked) {this.itemClicked(yoffset,entryNo, true);}
		}
		if (chg) {this.refresh();}
	}
	return false;
}
// Other functions
function DrawMenu(m) {
	m.draw();
}
function browserInfo() {
	this.code = 'unknown';
	this.version = 0;
	this.platform = 'Win';
	var ua = navigator.userAgent;
	var i = ua.indexOf('WebTV');
	if (i >= 0) {
		this.code = 'WebTV';
		i += 6;
	} else {
		i = ua.indexOf('Opera');
		if (i >= 0) {
			this.code = 'OP';
			i = ua.indexOf(') ') + 2;
		} else {
			i = ua.indexOf('MSIE');
			if (i >= 0) {
				this.code = 'MSIE';
				i += 5;
			} else {
				i = ua.indexOf('Mozilla/');
				if (i >= 0) {
					this.code = 'NS';
					i += 8;
				}
			}
		}
	}
	this.version = parseFloat(ua.substring(i, i+4));
	if (ua.indexOf('Mac') >= 0) {this.platform = 'Mac';}
	if (ua.indexOf('OS/2') >= 0) {this.platform = 'OS/2';}
	if (ua.indexOf('X11') >= 0) {this.platform = 'UNIX';}
	var v = this.version;
	var p = this.platform;
	var NS = (this.code == 'NS');
	var IE = (this.code == 'MSIE');
	var WTV = (this.code == 'WebTV');
	var OP = (this.code == 'OP');
	var OP32up = (OP && (v >= 3.2));
	var IE4up = (IE && (v >= 4));
	var NS3up = (NS && (v >= 3));
	var NS6up = (NS && (v >= 5));
        this.isIE = IE;
	this.canCache = NS3up || IE4up || OP32up || WTV;
	this.canOnMouseOut = this.canCache;
	this.canOnError = NS3up || IE4up || OP32up;
	this.canJSVoid = !((NS && !NS3up) || (IE && !IE4up) || (OP && (v < 3.5)));
	this.lineByLine = (v < 4);
	this.mustMoveAfterLoad = NS3up || (IE4up && (p != 'Mac')) || WTV;
	if (NS6up == true) {
		this.reloadMethod = 'reload';
	} else {
		if (NS3up || IE4up || WTV) {
			this.reloadMethod = 'replace';
		} else {
			this.reloadMethod = (NS && (v == 2.01) && (p != 'Win')) ? 'timeout' : 'href';
		}
	}
	this.canFloat = NS || (IE && !((p == 'Mac') && (v >= 4) && (v < 5)));
	this.hasDHTML = false;
	this.slowDHTML = IE4up || NS6up;
	this.hasW3CDOM = (document.getElementById) ? true : false;
	this.needLM = (!this.hasW3CDOM && NS) || (IE && (p == 'Mac') && (v >= 4.5));
	this.DHTMLRange = IE ? '.all' : '';
	this.DHTMLStyleObj = IE ? '.style' : '';
	this.DHTMLDivHeight = IE ? '.offsetHeight' : '.clip.height';
}
function getWindow() {return (floatingMode ) ? myOpener : self;}
function setStatus(theText) {
	var theWindow = getWindow();
	if (theWindow) {
		theWindow.status = theText;
		if (!theBrowser.canOnMouseOut) {
			clearTimeout(statusTimeout);
			statusTimeout = setTimeout('clearStatus()', 5000);
		}
	}
	return true;
}
function clearStatus() {
	var theWindow = getWindow();
	if (theWindow) {theWindow.status = '';}
}
function fixPath(p) {
	if (p.substring(0,2) == '/:') {p = p.substring(p.indexOf('/', 2), p.length);}
	var i = p.indexOf('\\', 0);
	while (i >= 0) {
		p = p.substring(0,i) + '/' + p.substring(i+1,p.length);
		i = p.indexOf('\\', i);
	}
	return p;
}
function loadURLInTarget(u, t) {
	var w = eval("self." + t);
	if (!w && myOpener) {w = eval("myOpener." + t);}
	if (!w && ("_top,_parent,_self".indexOf(t) >= 0)) {
		w = eval("getWindow()." + t.substring(1));}
	if (w) {w.location.href = u;} else {window.open(u, t);}
}
function defOnError(msg, url, lno) {
	if (jsErrorMsg == '') {
		return false;
	} else {
		alert(jsErrorMsg + '.\n\nError: ' + msg + '\nPage: ' + url + '\nLine: ' + lno + '\nBrowser: ' + navigator.userAgent);
		return true;
	}
}
function setRelPath(rpath)
{
this.relPath = rpath;
}
function getRelPath()
{
return this.relPath;
}

// Declare global variables
var theBrowser = new browserInfo;

var jsErrorMsg = 'A JavaScript error has occurred on this page!  Please note down the ';
jsErrorMsg += 'following information and pass it on to the Webmaster.';
if (theBrowser.canOnError) {self.onerror = defOnError;}

var relPath = '/';
var theMenu = new Menu;
if(theBrowser.isIE){theMenu.yOffsetMethod = 'document.body.scrollTop';}
var floatingMode = false;

var myOpener = null;


//	############################   End of Joust   ############################

function initOutlineIcons(imgStore) {
	var ip = relPath + 'graphics/';
	
	imgStore.add('iconPlusTop', ip + 'plustop.gif', 18, 16);
	imgStore.add('iconPlus', ip + 'plus.gif', 18, 16);
	imgStore.add('iconPlusBottom', ip + 'plusbottom.gif', 18, 16);
	imgStore.add('iconPlusOnly', ip + 'plusonly.gif', 18, 16);
	imgStore.add('iconMinusTop', ip + 'minustop.gif', 18, 16);
	imgStore.add('iconMinus', ip + 'minus.gif', 18, 16);
	imgStore.add('iconMinusBottom', ip + 'minusbottom.gif', 18, 16);
	imgStore.add('iconMinusOnly', ip + 'minusonly.gif', 18, 16);
	imgStore.add('iconLine', ip + 'line.gif', 18, 16);
	imgStore.add('iconBlank', ip + 'blank.gif', 18, 16);
	imgStore.add('iconJoinTop', ip + 'jointop.gif', 18, 16);
	imgStore.add('iconJoin', ip + 'join.gif', 18, 16);
	imgStore.add('iconJoinBottom', ip + 'joinbottom.gif', 18, 16);

	//Add folder and document images to the imgStore.
	imgStore.add('Folder', ip + 'book_closed.gif', 18, 16);
	imgStore.add('FolderExpanded', ip + 'book_open.gif', 18, 16);
	imgStore.add('FolderOSelected', ip + 'book_o_sel.gif', 18, 16);
	imgStore.add('FolderCSelected', ip + 'book_c_sel.gif', 18, 16);
	
	imgStore.add('Document', ip + 'help_doc.gif', 18, 16);
	imgStore.add('DocumentMouseOver', ip + 'help_doc_mo.gif', 18, 16);
	imgStore.add('DocumentCSelected', ip + 'help_doc_sel.gif', 18, 16);
}

function initialise() {
	
	// Set up parameters to control menu behaviour
	theMenu.autoScrolling = true;
	theMenu.modalFolders = false;
	theMenu.linkOnExpand = false;
	theMenu.toggleOnLink = true;
	theMenu.showAllAsLinks = true;
	theMenu.savePage = true;
	theMenu.tipText = "text";
	theMenu.selectParents = false;
	theMenu.name = "theMenu";
	theMenu.container = "self.left";
	theMenu.reverseRef = "parent";
	theMenu.contentFrame = "right";
	//theMenu.defaultTarget = "right";


	// Initialise all the icons
	initOutlineIcons(theMenu.imgStore);
	
	
}

self.defaultStatus = "";	

