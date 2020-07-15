#!/usr/bin/env bash
outfile=Ringlist.xml
echo -e "<CiscoIPPhoneRingList>" >$outfile
for filename in *.pcm *.raw; do
	if [ -f $filename ]; then 
		echo -e "\t<Ring>" >>$outfile
		echo -e "\t\t<DisplayName>${filename%.*}</DisplayName>" >>$outfile
 		echo -e "\t\t<FileName>${filename}</FileName>" >>$outfile
		echo -e "\t</Ring>" >>$outfile
	fi
done
echo -e "</CiscoIPPhoneRingList>" >>$outfile
[ -f DistinctiveRinglist.xml ] || ln -s Ringlist.xml DistinctiveRinglist.xml
[ -f distinctiveringlist.xml ] || ln -s Ringlist.xml distinctiveringlist.xml
[ -f ringlist.xml ] || ln -s Ringlist.xml ringlist.xml
for x in *.pcm *.xml *.raw; do
	[ -f $x ] && ../../etc/certs/signfile $x
done
