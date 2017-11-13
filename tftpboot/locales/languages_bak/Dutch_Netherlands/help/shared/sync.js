var iss = parent.left.location.toString();
var isInd = iss.indexOf("indexframe");

if ( (self != top) && (parent.theMenu) && (isInd < 0) ) {	

	var eID = -1;
        
	eID = parent.theMenu.findEntry(top.right.location.pathname+top.right.location.hash, "url", "helpsys", 0);

	if (eID >= 0 && ( parent.theMenu.selectedEntry !=eID ))
	{
	   parent.theMenu.selectEntry(eID);
	   var e = parent.theMenu.entry[eID];
	   if(e.type != "Folder")
  	   {
	      if (parent.theMenu.setEntry(eID, true))
	      {
	       // parent.theMenu.refresh();
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
	   }	 
           parent.theMenu.refresh();
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

} else {
	var navPrinting = false;
	if ((navigator.appName + navigator.appVersion.substring(0, 1)) == "Netscape4") {
		navPrinting = (self.innerHeight == 0) && (self.innerWidth == 0);}
	if ((self.name != 'right') && (self.location.protocol != "file:") && !navPrinting) {
		var result = self.location.pathname.split("/");
		var filename = result[result.length-1];
		if(location.hash != ''){
			var newLoc = "index.html?" + escape(filename)+"#"+escape(self.location.hash.substring(1,self.location.hash.length));
		} else {
			var newLoc = "index.html?" + escape(filename);
		}
		if (parseInt(navigator.appVersion) >= 3) {self.location.replace(newLoc);} else {self.location.href = newLoc;}
	}
}