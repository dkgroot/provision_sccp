#!/usr/bin/env bash
outfile=ringlist.xml
echo -e "<CiscoIPPhoneRingList>" >$outfile
if [ ! -z "`ls *.pcm 2>/dev/null`" ]; then
	for filename in *.pcm;do
		basename=`basename ${filename} .pcm`
		echo -e "\t<Ring>" >>$outfile
		echo -e "\t\t<DisplayName>${basename}</DisplayName>" >>$outfile
		echo -e "\t\t<FileName>ringtones\${filename}</FileName>" >>$outfile
		echo -e "\t</Ring>" >>$outfile
	done
fi
if [ ! -z "`ls *.raw 2>/dev/null`" ]; then
	for filename in *.raw;do
		basename=`basename ${filename} .raw`
		echo -e "\t<Ring>" >>$outfile
		echo -e "\t\t<DisplayName>${basename}</DisplayName>" >>$outfile
		echo -e "\t\t<FileName>ringtones\${filename}</FileName>" >>$outfile
		echo -e "\t</Ring>" >>$outfile
	done
fi
echo -e "</CiscoIPPhoneRingList>" >>$outfile

