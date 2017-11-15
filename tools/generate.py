#!/usr/bin/env python
#from __future__ import absolute_import
import json
import os
import hachoir_regex

with open('config.json') as f:
    config = json.load(f)

def generate_regex(subdir):
    paths = os.listdir(subdir)
    as_regex = [hachoir_regex.parse(path) for path in paths]
    return reduce(lambda x, y: x | y, as_regex)
    
def generate_tftpd_rules(regex, dirname, outfile):
    if "tftpd-hpa" in config["generate_config"]:
        outfile.write("ri ^{}$ /firmware/{}/\\1\n".format(regex, dirname))

def generate_nginx_rules(regex, dirname, outfile):
    if "nginx" in config["generate_config"]:
        outfile.write("rewrite ^{}$ /firmware/{}/$1\n".format(regex, dirname))

if __name__ == '__main__':
    rootdir = "./tftpboot/firmware/"
#    tftpd_rules = open("etc/tftpd-hpa/tftpd.rules", "w")
#    nginx_rules = open("etc/nginx/sites-available/nginx.rules", "w")
#    
#    for subdir in next(os.walk(rootdir))[1]:
#        regex = generate_regex(os.path.join(rootdir, subdir))
#        generate_tftpd_rules(regex, os.path.basename(subdir), tftpd_rules)
#        generate_nginx_rules(regex, os.path.basename(subdir), nginx_rules)
#    
#    tftpd_rules.close();
#    nginx_rules.close();    
    #app.debug = os.environ.get('FLASK_DEBUG', True)
    #print config.get["scopes"]