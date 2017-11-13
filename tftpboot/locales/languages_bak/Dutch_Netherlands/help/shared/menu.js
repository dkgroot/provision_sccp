var theMenuRef = "parent.theMenu";
var theMenu = eval(theMenuRef);
var theBrowser = parent.theBrowser;
theBrowser.hasDHTML = false;
var belowMenu = null;
var menuStart = 0;

if (parent.theBrowser) {
	if (parent.theBrowser.canOnError) {window.onerror = parent.defOnError;}
}
function myVoid() { ; }
function setBottom() {
	if (theMenu) {
		theMenu.amBusy = false;
	}
}
function frameResized() {if (theBrowser.hasDHTML) {theMenu.refreshDHTML();}}
function syncFromMenu()
{
	var eID = -1;
	eID = theMenu.findEntry(parent.right.location.pathname+parent.right.location.hash, "url", "right", 0);		
        if(eID < 0)
        {
          eID = parent.theMenu.findEntry(location.pathname, "url", "right", 0);		
        }
	if (eID >= 0)
	{
	   theMenu.selectEntry(eID);
	   var e = theMenu.entry[eID];
	   if(e.type != "Folder")
  	   {
	      if (theMenu.setEntry(eID, true))
	      {
	        theMenu.refresh();
	      }
	   }
	   else
	   {
 	      var cl = ',' + eID + ',';
	      var p = e.parent;
	      var mc;
	      while (p >= 0) {
		 cl += p + ',';
		 e = theMenu.entry[p];
		 mc |= (e.setIsOpen(true));
		 p = e.parent;
	      }
              theMenu.refresh();
	   }	
 
	}
}

//	############################   End of Joust   ############################

if (self.name != 'left') { self.location.href = 'index.html'; }
