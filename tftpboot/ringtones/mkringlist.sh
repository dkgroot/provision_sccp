#!/usr/bin/env bash
outfile=ringlist.xml
echo -e "<CiscoIPPhoneRingList>" >$outfile
for filename in *.pcm;do
	basename=`basename ${filename} .pcm`
	echo -e "\t<Ring>" >>$outfile
	echo -e "\t\t<DisplayName>${basename}</DisplayName>" >>$outfile
	echo -e "\t\t<FileName>ringtones/${filename}</DisplayName>" >>$outfile
	echo -e "\t</Ring>" >>$outfile
done
for filename in *.raw;do
	basename=`basename ${filename} .raw`
	echo -e "\t<Ring>" >>$outfile
	echo -e "\t\t<DisplayName>${basename}</DisplayName>" >>$outfile
	echo -e "\t\t<FileName>ringtones/${filename}</DisplayName>" >>$outfile
	echo -e "\t</Ring>" >>$outfile
done
echo -e "</CiscoIPPhoneRingList>" >>$outfile

