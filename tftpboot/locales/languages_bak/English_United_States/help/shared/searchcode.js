var searchText ="";
var searchType ="AllWords";
var s = ""; // used for debugging
var TitleMarker = "TI:="; // static value denoting marker used in FI Array
var TextMarker = " TE:="; // static value denoting marker used in FI Array

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

function hide_summaries(d) {
print_search_results(d,false);
}

function show_summaries(d) {
print_search_results(d,true);
}

var hits = 0;
var hitsIndex = new Array();
var MAX_HITS = 100;


function do_search(d){
     hits = 0;
     hitsIndex = new Array();

    //print_search_form();
    hitsIndex = performSearch();    
    hits = hitsIndex.length;	    
    if(hits > MAX_HITS){
      hits = MAX_HITS;
      hitsIndex.length = MAX_HITS;
    }

    print_search_results(d,true);
}

function print_search_results(d,sum) {

if(top.lowerFrame.leftFrame.treeframe.indexEnter.location.href.indexOf('FavEnter')>-1)
  top.lowerFrame.leftFrame.treeframe.indexEnter.document.indexForm.indexLookup.value='';


var strA = new Array();
strA.push("<table width='90%' border='0' cellspacing='0' cellpadding='6'>");
strA.push("  <tr>");

strA.push("    <td><span class='bodycopy'><strong>SEARCH RESULTS</strong> <br>");
strA.push("      "+hits+" total results for <strong>"+searchText+"</strong></span><br>");
strA.push("      <hr size='1' color='#4A5AAA'>");
     
strA.push("     <span class='bodycopy'><a onClick='top.hide_summaries(document);' href='javascript:void(0)'>Hide Summaries</a>  |  <a onClick='top.show_summaries(document);' href='javascript:void(0)'>Show Summaries  ");
strA.push("     </a></span>");

//results
  if(hitsIndex.length == 0) {
    strA.push('<p class="bodycopy"><br>');
    strA.push('No Matches</p>');
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
		pth = rootPath+fileLine.substring(0,fileLine.indexOf(TitleMarker)-1)+'?'+searchText;
		ttl = fileLine.substring(fileLine.indexOf(TitleMarker)+TitleMarker.length,textIndex);	
		if (textIndex < fileLine.length)
	    		txtStr = fileLine.substring(textIndex+TextMarker.length);
		else
	    		txtStr = "";
                var tt = rootPath+'index.html?'+fileLine.substring(0,fileLine.indexOf(TitleMarker)-1)+'&ST='+searchText;

                strA.push('<p class="bodycopy"><strong>'+(i+1)+'.</strong> <a href="..'+pth+'"><strong>'+ttl+'</strong></a> <a href="..'+tt+'" target="_blank"><img src="images/open_new_win16.gif" alt="open '+ttl+' in new window" width="16" height="11"><a><br>');
                if(sum)
                  strA.push(txtStr);
                strA.push('</p>');
    	  }	
	}//for
  }
strA.push("</td></tr></table>");

d.getElementById('myDiv').innerHTML = strA.join('');
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

    //check if this is currWord= so that we can prioritize hits
    if(words.indexOf("]"+currWord+"=",index) > -1)
    {
      index = words.indexOf("]"+currWord+"=",index)+1;
      if (words.lastIndexOf("]",index) > words.lastIndexOf("[",index)) {
		tempwordHits += " ";
	  	tempwordHits += words.substring(words.indexOf("[",index)+1,
            	                      words.indexOf("]",index));        	    	
      }
    }

    index = 0;
    while ((index = words.indexOf(currWord,index)) > -1) {
          //make sure this is not currWord= since we have already added it above.
          if(words.indexOf("=",index) == currWord.length+index)
          {}
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






