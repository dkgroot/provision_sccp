var searchText ="";
var searchType ="";
var s = ""; // used for debugging
var TitleMarker = "TI:="; // static value denoting marker used in FI Array
var TextMarker = " TE:="; // static value denoting marker used in FI Array

function print_search_form()
{
right.document.writeln('<TABLE width="100%" BORDER="0" CELLPADDING="1" CELLSPACING="0">');
right.document.writeln('<TR BGCOLOR="#CCCCCC" VALIGN="top">');
right.document.writeln('<TD ALIGN="left" HEIGHT="20">');
right.document.writeln('<FORM method=GET ACTION="../search/PreSearch.html" NAME="everything">');
right.document.writeln('<INPUT TYPE="Text" NAME="USERTEXT" onKeyPress="checkEnter(event)"');
right.document.writeln('VALUE="'+searchText+'"');
right.document.writeln('SIZE=40>');
right.document.writeln('<INPUT TYPE="SUBMIT" onClick="doser();" NAME="submitbutton" VALUE="Zoeken">');
right.document.writeln('</TD>');
right.document.writeln('</TR>');

right.document.writeln('<TR BGCOLOR="#CCCCCC" ALIGN="left"><TD ALIGN="left">');

right.document.writeln('<SELECT NAME="BooleanType">');
if(searchType =="AnyWords")
{
    right.document.writeln('<OPTION VALUE="AllWords">Alle woorden');
    right.document.writeln('<OPTION VALUE="AnyWords" SELECTED>Willekeurige woorden');
}
else
{
    right.document.writeln('<OPTION VALUE="AllWords" SELECTED>Alle woorden');
    right.document.writeln('<OPTION VALUE="AnyWords" >Willekeurige woorden');
}
right.document.writeln('</SELECT></TD></TR>');
right.document.writeln('</FORM>');
right.document.writeln('</TABLE>');

right.document.writeln('<TABLE width="100%" BORDER="0" CELLPADDING="1" CELLSPACING="0">');
right.document.writeln('<TR BGCOLOR="white" ALIGN="left"><TD ALIGN="left">  ');
right.document.writeln('<FONT FACE="Arial, Helvetica, sans-serif" size="-1"> ');

var relPath = getRelativePath();
//right.document.writeln('<A HREF="'+relPath+'search/searchtips.html"><B>Zoekfunctie gebruiken</B></A>');
right.document.writeln('<A HREF="../search/searchtips.html"><B>Zoekfunctie gebruiken</B></A>');
right.document.writeln('</FONT>');
right.document.writeln('</TD></TR>');
right.document.writeln('</TABLE>');

right.document.writeln('<TABLE width="100%" BORDER="0" CELLPADDING="1" CELLSPACING="0">');
right.document.writeln('<TR><TD nowrap COLSPAN="3" BGCOLOR="#CCCCCC">');
right.document.writeln('<FONT color="black" FACE="Arial, Helvetica, sans-serif" size="-1">');

right.document.writeln('</FONT></TD>');
right.document.writeln('<TD nowrap COLSPAN="2"  align="right" BGCOLOR="CCCCCC">');
right.document.writeln('<FONT color="black" FACE="Arial, Helvetica, sans-serif" size="-1">');

right.document.writeln('</TD>');
right.document.writeln('</TR>');
right.document.writeln('</TABLE>');
}

function getRelativePath()
  {
  var locat = window.location.toString();
  var ind = locat.lastIndexOf("/help/");
  var count=0;
  if (ind != -1)
    {
      locat = locat.substr(ind + 6);
      for(var i=0; i<locat.length ;i++)
      {
        if(locat.charAt(i) == '/')
          count++;
      }
      var str = "";
      for(var i=0;i<count;i++)
      {
        str = str+"../";

      }
      return str;
    }

  return "";
  }

function print_search_results() {
    
    var hits = 0;
    var hitsIndex = new Array();

    print_search_form();
    hitsIndex = performSearch();    
    hits = hitsIndex.length;	    

right.document.writeln('<TABLE width="100%" BORDER="0" CELLPADDING="1" CELLSPACING="0">');
right.document.writeln('<TR><TD nowrap COLSPAN="3" BGCOLOR="CCCCCC">');
right.document.writeln('<FONT color="black" FACE="Arial, Helvetica, sans-serif" size="-1">');
right.document.writeln('Resultaten voor: '+searchText);
// right.document.writeln('<br>Debug line: '+s+'.');
right.document.writeln('</FONT></TD>');
right.document.writeln('<TD nowrap COLSPAN="2"  align="right" BGCOLOR="CCCCCC">');
right.document.writeln('<FONT color="black" FACE="Arial, Helvetica, sans-serif" size="-1">');
right.document.writeln(hits + ' Overeenkomsten');
right.document.writeln('</FONT></TD></TR></TABLE>');

//results
  if(hitsIndex.length == 0) {
    right.document.writeln('<br>');
    right.document.writeln('Geen overeenkomsten');
  }
  else {
	var pth = "";
	var ttl = "";
 	var txtStr = "";
      var fileLine = "";
	var textIndex = 0;
	var i;

	for(i=0;i<hitsIndex.length;++i) {
  	  fileLine = FI[hitsIndex[i]];
	  if(fileLine=="undefined") {
	    //array entry does not exist for this index
          }
	  else{
	
		if (fileLine.indexOf(TextMarker) > -1) textIndex = fileLine.indexOf(TextMarker);
	 	else textIndex = fileLine.length;
		pth = rootPath+fileLine.substring(0,fileLine.indexOf(TitleMarker));
		ttl = fileLine.substring(fileLine.indexOf(TitleMarker)+TitleMarker.length,textIndex);	
		if (textIndex < fileLine.length)
	    		txtStr = fileLine.substring(textIndex+TextMarker.length);
		else
	    		txtStr = "";
		right.document.writeln('<TABLE width="100%" BORDER="0" CELLPADDING="0" CELLSPACING="3">');
    		right.document.writeln('<TR VALIGN=TOP>');
		right.document.writeln('<TD><B>');
		right.document.writeln('<A TARGET="right" HREF="..' + pth + '">'+ ttl + '</a></B>');
		right.document.writeln('<FONT FACE="Arial, Helvetica, sans-serif" SIZE="-1">');
		right.document.writeln(txtStr);
		right.document.writeln('</FONT>');
		right.document.writeln('<BR>');
		right.document.writeln('</TD></TR></TABLE>');
    	  }	
	}//for
  }
right.document.close();
} //print_search_results
 
/*
 * performSearch function will retrieve all the pages where the
 * search parameters are a match.
 * Returns an array representation of the file indexes
 */
function performSearch() {
    var textArray = searchText.split(" ");
    var hitsIndexString= ""; // String representation of file indices, separated by " "
    currHitsString = " ";	

    if(textArray.length == 1 && textArray[0] == "") {
    //no search text.
    }
    else {
        for (var i=0; i<textArray.length; i++) {
           if (i == 0) {
              hitsIndexString = findHits(textArray[i].toLowerCase());
		    if (searchType == "AllWords") {
		        if (hitsIndexString == "") 
				break;
		        else if (hitsIndexString.split(" ").length > 1) 
				hitsIndexString = deleteRepeats(hitsIndexString.split(" "));
		    }
        	}
            else if (searchType == "AnyWords") {
		    currHitsString = findHits(textArray[i].toLowerCase());
		    if (hitsIndexString != "" && currHitsString != "") hitsIndexString += " ";
		    hitsIndexString += currHitsString;     
	      }
		else { //searchType == "AllWords"
		    hitsIndexString = mergeHits(hitsIndexString,findHits(textArray[i].toLowerCase()));
		    if (hitsIndexString == "") break;
		}
	  }//for
    }
    
    if (searchType == "AnyWords" && hitsIndexString.indexOf(" ") != -1) {
	return (deleteRepeats(hitsIndexString.split(" ")).split(" "));
    }	
    else if (hitsIndexString != "") { //searchType == "AllWords" OR 1 hit, no sort needed
        return (hitsIndexString.split(" "));
    }
    else 
	  return (new Array());
}

/*
 * Function will traverse through the array of file indices and
 * remove all the repeated entries.
 *
 * param: an array of file indices
 * return: a string of file indices separated by " "
 */
function deleteRepeats(currHitsArray) {
	var newString = " ";		
	for (var i=0; i<currHitsArray.length; i++) {
		if (newString.indexOf(" "+currHitsArray[i]+" ") == -1) {
			newString += currHitsArray[i];
			newString += " ";
		}
	}
	return newString.substring(1,newString.length-1);
}
/*
 * Function will traverse through the array of file indices and
 * return all the repeated entries.
 *
 * param: an array of file indices
 * return: a string of file indices separated by " "
 */
function getRepeats(currHitsArray) {
	var newString = " ";
	var tempString = " ";
	for (var i=0; i<currHitsArray.length; i++) {
		if (tempString.indexOf(" "+currHitsArray[i]+" ") == -1) {
			tempString += currHitsArray[i];
			tempString += " ";	
		}
		else if (newString.indexOf(" "+currHitsArray[i]+" ") == -1) {
			newString += currHitsArray[i];
			newString += " ";
		}
	}
	if (newString == " ") 
		return "";
	else	
		return newString.substring(1,newString.length-1);
}

/* 
 * returns a String that represents the file indices, each of which 
 * represent a search hit. 
 * @returns: String of file indices, separated by " " OR "" for no hits         
 */
function findHits(currWord) {
    var tempwordHits = "";
    var index = 0;

    while ((index = words.indexOf(currWord,index)) > -1) {
    	  // make sure this is a valid hit, not an array entry        
	  if (words.lastIndexOf("]",index) > words.lastIndexOf("[",index)) {
		tempwordHits += " ";
	  	tempwordHits += words.substring(words.indexOf("[",index)+1,
            	                      words.indexOf("]",index));        	    	
	  }
        index += currWord.length;
    }
    if (tempwordHits != "") 
	  tempwordHits = tempwordHits.substring(1); 
    return tempwordHits;
}




/* 
 * currHitsIndex = current array of file indices with a valid search hit. pre-sorted
 * currWordHits = array of file indices of search hits for this particular word. not sorted
 * this method should only be called when the searchType == "AllWords" 
 * and currHitsIndex is not empty.
 */   
function mergeHits(currHitsIndex,currWordHits) {
    if (currWordHits == "") {
	  currHitsIndex = "";
    }
    else {
        if (currWordHits.split(" ").length > 1) {
		currWordHits = deleteRepeats(currWordHits.split(" "));
	  }
	  currHitsIndex += " ";
	  currHitsIndex += currWordHits;
	 currHitsIndex = getRepeats(currHitsIndex.split(" "));
    }
    return currHitsIndex;
}






