var activetab = 1;

var useTree = 1; //indicates use of tree view

var spFrameSz = "8";
var banFrameSz = "68";//88";
var navFrameSz = "50";//28";
var indFrameSz = "30";

var tocfile = parent.getRelativePath()+"shared/menu.html";
var navtocfile = "menu.html";
var browser= navigator.appName;
var getver = navigator.appVersion.substring(0, 1);
if ((navigator.appName == "Netscape") && (getver == 4)) {
   if(navigator.userAgent.indexOf('NT 5') != -1) {
      useTree = 0;
      tocfile = "toc.html";
      navtocfile = parent.window.location.toString();
      navtocfile = navtocfile.substring(0,navtocfile.lastIndexOf("/")) + "/" + tocfile;
   }
   //set frameset vars
   spFrameSz = "10";
   banFrameSz = "70";//90";
   navFrameSz = "52";//30";//"25";
   indFrameSz = "32";
}


function getContentFile()
{
  if(location.search != "")
  {
    if(top.location.search.indexOf("PATH") >= 0)
    {    
       var i = top.location.search.indexOf("/");
       return "/help"+top.location.search.substring(i);
    }
    else
    {
       return top.location.search.substring(1);
    }

  }
  return "";
}

function writeFrames()
{
self.document.write("<frameset rows='*,3' frameborder='NO' border='0' framespacing='0'>");
  self.document.write("<frameset cols='250,*' frameborder='NO' border='0' framespacing='0' rows='*'> ");
    self.document.write("<frameset rows='"+parent.banFrameSz+","+parent.navFrameSz+",*' frameborder='NO' border='0' framespacing='0' cols='*'> ");
      self.document.write("<frame name='banFrame' scrolling='NO' noresize marginwidth='0' marginheight='0' src='"+relPath+"shared/ban.html'>");
      self.document.write("<frame name='navFrame' scrolling='NO' noresize marginwidth='0' marginheight='0' src='"+relPath+"shared/nav.html'>");
      self.document.write("<frame name='left' scrolling='AUTO' noresize marginwidth='0' marginheight='0' src='"+parent.tocfile+"'>");
    self.document.write("</frameset>");
    self.document.write("<frameset cols='"+parent.spFrameSz+",*' frameborder='NO' border='0' framespacing='0' rows='*'> ");
      self.document.write("<frame name='spFrame' scrolling='NO' noresize marginwidth='0' marginheight='0' src='"+relPath+"shared/sp.html'>");
      self.document.write("<frameset rows='27,*' frameborder='NO' border='0' framespacing='0' cols='*'> ");
        self.document.write("<frame name='toolBarFrame' noresize scrolling='NO' marginwidth='0' marginheight='0' src='"+relPath+"shared/toolbar.html'>");
        if(location.search != '')
        {
           self.document.write("<frame src='" + getContentFile() + location.hash + "' name='right' marginwidth='5' marginheight='5  scrolling='AUTO'>");
        }
        else
        {
           self.document.write("<frame src='"+startPage+"' name='right' scrolling='AUTO' marginwidth='5' marginheight='5' >");
        }
      self.document.write("</frameset>");
    self.document.write("</frameset>");
  self.document.write("</frameset>");
  self.document.write("<frame name='footerFrame' scrolling='NO' noresize src='"+relPath+"shared/footer.html' marginwidth='0' marginheight='0'>");
self.document.write("</frameset>");
}

function nsync()
{
var iss2 = parent.left.location.toString();
var isToc = iss2.indexOf("menu.htm");
  if ( parent.theMenu && (isToc >= 0) ) {	

	var eID = -1;
        
	eID = parent.theMenu.findEntry(top.right.location.pathname+top.right.location.hash, "url", "helpsys", 0);		

	if (eID >= 0)
	{
	   parent.theMenu.selectEntry(eID);
	   var e = parent.theMenu.entry[eID];
	   if(e.type != "Folder")
  	   {
	      if (parent.theMenu.setEntry(eID, true))
	      {
	        parent.theMenu.refresh();
	      }
	   }
	   else
	   {
 	      var cl = ',' + eID + ',';
	      var p = e.parent;
	      var mc;
	      while (p >= 0) {
		 cl += p + ',';
		 e = parent.theMenu.entry[p];
		 mc |= (e.setIsOpen(true));
		 p = e.parent;
	      }
              parent.theMenu.refresh();
	   }	
	}
        if(eID == -1)
        {            
           if(parent.theMenu.SelectedEntry != -1)
           {
              var e2 = parent.theMenu.entry[parent.theMenu.selectedEntry];             
              if(e2!=null)
              {
                 parent.theMenu.selectedEntry = -1;
                 parent.theMenu.lastPMClicked = -1;
                 e2.setSelected(false);
                 parent.theMenu.refresh();
              }           
           }
       }
     }
return true;
}
function test2()
{
 parent.window.close(); return false;
}
function test()
{
var w = window.open("tagerror.html","test");
w.document.write("with hash: " +top.right.location.pathname+top.right.location.hash);
w.document.write("<br>");
w.document.write("without hash: " + top.right.location.pathname);
w.document.write("<br>");
w.document.write("title: " + top.right.document.title);

for (var i = 0; i <= parent.theMenu.count; i++) {
		if (parent.theMenu.entry[i]) {
			e = parent.theMenu.entry[i];
                        w.document.write("<br>");
                        
                        var c = e.url;
                        var s = (top.right.location.pathname+top.right.location.hash);
                        w.document.write("check: c="+c+" s="+s);
                        if(cmp_helpsys(c,s,w))
                        {
                           w.document.write(" yes");
                        }
                        else{  w.document.write(" no");}
		}		
	}


w.document.close();
}
function setTab(num)
{
   activetab=num;
   window.open(parent.getRelativePath()+"/shared/nav.html","navFrame");
   return false;
}

function setActiveTab(num,file)
{
   activetab=num;
   openLeft(file);
   window.open(parent.getRelativePath()+"/shared/nav.html","navFrame");
   return false;
}

function openLeft(file)
{
   window.open(file,"left");
}

function getActiveTab()
{
   return activetab;
}

function getMyColor(num)
{
   if(num==activetab)
   {
      return '#D6D3CE';
   }
   return '#B5B6B5';
}

function getPath()
{
  var locat = parent.window.location.toString();
  var ind = locat.lastIndexOf("/");
  if (ind != -1)
    {
      locat = locat.substr(0,ind+1);
      return unescape(locat);
    } 
  return "";
}

function getFullPath()
{
  var locat = parent.window.location.toString();
  var ind = locat.lastIndexOf("/help/");
  if (ind != -1)
    {
      ind = locat.lastIndexOf("/");
      locat = locat.substr(0,ind+1);
      return unescape(locat);
    } 
  return parent.getSearchPath();
}
