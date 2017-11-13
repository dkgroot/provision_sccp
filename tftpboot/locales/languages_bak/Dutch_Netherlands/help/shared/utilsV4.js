var indexList = new Array();

function getContentFile()
{
  if(top.location.search != "")
  {
    if(top.location.search.indexOf("&ST=") > -1){
      return top.location.search.substring(1,top.location.search.indexOf("&ST="))+"?"+top.location.search.substring(top.location.search.indexOf("&ST=")+4);
    }
    
    if(top.location.hash != "")
     return top.location.search.substring(1)+top.location.hash;
    else
     return top.location.search.substring(1);
  }
  return top.startPage;
}

function pophelp(start)
{
 if(top.location.protocol.indexOf("file:")== 0 && moz){
    if(top.location.toString().indexOf("indexta.html") == -1){
      pt = top.location.pathname.substring(top.location.pathname.lastIndexOf("/help/")+6,top.location.pathname.lastIndexOf("/"));
      st = top.location.pathname.substring(0,top.location.pathname.lastIndexOf("/help/")+6);
      top.location=st+"index.html?"+"st="+start+"pt="+pt; //start+pt;
    }
 }
}

function writeFrames()
{
self.document.write("<frameset onload='pophelp(top.startPage);' rows='41,*' id='mainFrame' frameborder='no' border='0' framespacing='0'>");
self.document.write(" <frame src='"+relPath+"shared/ToolbarFrame.htm' name='topFrame' scrolling='NO' noresize >");
self.document.write("   <frame src='"+relPath+"shared/LowerFrameSet.htm' name='lowerFrame' scrolling='NO' >");
self.document.write("</frameset>");
}


function getPath()
{
  var locat = top.window.location.toString();
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
  var locat = top.window.location.toString();
  var ind = locat.lastIndexOf("/help/");
  if (ind != -1)
    {
      ind = locat.lastIndexOf("/");
      locat = locat.substr(0,ind+1);
      return unescape(locat);
    } 
  return parent.getSearchPath();
}
