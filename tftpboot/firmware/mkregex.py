#!/usr/bin/env python
from hachoir_regex import parse
from os import listdir,walk,path
from os.path import basename,join,isdir

rootdir = ".";
for subdir in next(walk(rootdir))[1]:
        paths = listdir(subdir)
        as_regex = [parse(path) for path in paths]
        print "^{}$ /firmware/{}/\\1".format(reduce(lambda x, y: x | y, as_regex), basename(subdir))
