import sys

'''Check for "requests" library since it seems Python 3 didn't have it but Python 2 did?'''
if sys.version_info.major == 3:
    import importlib
    check_requests = importlib.find_loader('requests')
    found = check_requests is not None

elif sys.version_info.major == 2:
    import imp
    try:
        imp.find_module('requests')
        found = True
    except ImportError:
        found = False
    
if not found:
    print("Required library 'Requests' not found, run 'pip install requests' to install it")
    quit()

import requests
import json
import re
import os
import string
import argparse
import subprocess
import shutil
 
BASE_URL = 'http://www.hitbox.tv'
PROG_INFO = "Hitbox Recording Downloader v1.2"
 
def download_file(url, local_filename):
    fileInfo = "Downloading {0}...".format(local_filename)
    CS = 1024
    done = 0
    r = requests.get(url, stream=True)
    with open(local_filename, 'wb') as f:
        for chunk in r.iter_content(chunk_size=CS):
            if not chunk: # filter out keep-alive new chunks
                continue
            f.write(chunk)
            f.flush()
            done += CS
            sys.stdout.write("\r" + fileInfo + " {0:>7.2f} MB".format(done/float(pow(1024,2))))
 
    print(", done")
 
def download_broadcast(id_, delete):
    if not id_.isdigit():
        print("Invalid recording ID, Hitbox only uses numbers.")
        quit()
    print(sys.version)
    """ download all video parts for broadcast 'id_' """
    pattern = '{base}/api/player/hlsvod/{id}'
    plurl = pattern.format(base=BASE_URL, id=id_)
    r = requests.get(plurl)
    
    if r.status_code != 200:
        if r.status_code == 404:
            print("Error: Recording ID not found.")
        else:
            print("Error: API returned {0}".format(r.status_code))
        quit()
    try:
        k = r.text
    except ValueError as e:
        print("API did not return valid JSON: {}".format(e))
        print("{}".format(r.text))
        quit()
    
    for line in k.splitlines():
        if not line.startswith("#"):
            playlist = line
    
    print(playlist)
    r = requests.get(playlist)
    if r.status_code != 200:
        print("Error: API returned {0}".format(r.status_code))
        quit()
    try:
        fullList = r.text
    except ValueError as e:
        print("API did not return valid JSON: {}".format(e))
        print("{}".format(r.text))
        quit()
    
    baseVODs = playlist[0:-10]
    folder = id_ + '_files\\'
    joined = id_ + '_joined.ts'
    if not os.path.exists(folder):
        os.mkdir(folder)
    os.chdir(folder)
    
    for line in fullList.splitlines():
        if not line.startswith("#"):
            video_url = baseVODs + line
            ext = os.path.splitext(video_url)[1]
            nr = os.path.splitext(line)[0]
            nr = nr.replace('index', '')
            filename = "{0}_{1:0>2}{2}".format(id_, nr, ext)
            filename = filename
            download_file(video_url, filename)
    
    noOutput = open(os.devnull, 'w')
    print("Combining TS files ...")
    copy = subprocess.Popen("copy /b *.ts " + joined, shell=True, stdout=noOutput)
    copy.wait()
    print("Moving out of _files folder ...")
    move = subprocess.Popen("move " + joined + " ..", shell=True, stdout=noOutput)
    move.wait()
    if delete:
        print("Deleting folder '" + folder + "'")
        os.chdir("..")
        shutil.rmtree(folder)
        
    print("Download complete.")
    
if __name__=="__main__":
    print(PROG_INFO)
    parser = argparse.ArgumentParser(description="Downloads Hitbox recording TS files")
    parser.add_argument('videoID', help='Hitbox video ID')
    parser.add_argument('-d', '--delete', action='store_true', help="Delete the individual TS files and _files folder when done")
    args = parser.parse_args()
    download_broadcast(args.videoID, args.delete)