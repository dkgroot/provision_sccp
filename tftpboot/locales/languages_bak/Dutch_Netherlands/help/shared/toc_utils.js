// Functions called by the menu under the tree
// If you are not going to have an extra frame with action buttons, if your links
// are embedded in the documents that load in the right frame, for example,
// you should move these functions to the frameset file (in this case demoFuncs.html)
// If you are using a frameless layout, you will also have to move these functions
// to the appropriate page.
// In both cases: you will have to update the DOM paths used to access the functions
// and used by the functions themselves.

// Open all folders
// May not work with very large trees (browser may time out)
// You may call this on a node other than the root, but it must be a folder
function expandTree(folderObj)
{
    var childObj;
    var i;

    //Open folder
    if (!folderObj.isOpen)
      clickOnNodeObj(folderObj)

    //Call this function for all folder children
    for (i=0 ; i < folderObj.nChildren; i++)  {
      childObj = folderObj.children[i]
      if (typeof childObj.setState != "undefined") {//is folder
        expandTree(childObj)
      }
    }
}

// Close all folders
function collapseTree()
{
	//hide all folders
	clickOnNodeObj(foldersTree)
	//restore first level
	clickOnNodeObj(foldersTree)
}

// In order to show a folder, open all the folders that are higher in the hierarchy 
// all the way to the root must also be opened.
// (Does not affect selection highlight.)
function openFolderInTree(linkID) 
{
	var folderObj;
	folderObj = findObj(linkID);
	folderObj.forceOpeningOfAncestorFolders();
	if (!folderObj.isOpen)
		clickOnNodeObj(folderObj);
} 

// Load a page as if a node on the tree was clicked (synchronize frames)
// (Highlights selection if highlight is available.)
function loadSynchPage(linkID) 
{
    //remove highlight
    if(linkID<=0){clearLastHighlightetObjLink(); return;}
    var folderObj;
    docObj = findObj(linkID);
    //remove highlight
    if(docObj==null){clearLastHighlightetObjLink(); return;}
    docObj.forceOpeningOfAncestorFolders();
//	clickOnLink(linkID,docObj.link,'basefrm'); 
    highlightObjLink(docObj);

    //Scroll the tree window to show the selected node
    //Other code in these functions needs to be changed to work with
    //frameless pages, but this code should, I think, simply be removed
    if (typeof document.body != "undefined") //scroll doesn work with NS4, for example
        document.body.scrollTop=docObj.navObj.offsetTop
} 

function syncTOC()
{
if(top.lowerFrame.mainFrame.basefrm.myid!=null)
loadSynchPage(top.lowerFrame.mainFrame.basefrm.myid);
}