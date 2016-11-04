import requests
import sys
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
    
    ''' 
        Playlist file for recording v6257972: 
        http://usher.justin.tv/vod/6257972?nauth={"user_id":null,"vod_id":6257972,"expires":1435765485,"chansub":{"restricted_bitrates":[]},"privileged":false}&nauthsig=6559b3a61fba97d637d3e30c6887b7dda701e871
        Playlist gives a list of different m3u8 files, one for each quality.
        Seems to need some sort of authorization, don't know if there's a way for me to get it myself.
    '''
    '''
        Individual TS playlist from above's info listed as NAME="Source":
        http://vod.ak.hls.ttvnw.net/v1/AUTH_system/vods_a0cb/iateyourpie_14886031920_257917932/chunked/highlight-6257972.m3u8
        
        First recording from said playlist:
        URL, not given, is: http://vod.ak.hls.ttvnw.net/v1/AUTH_system/vods_a0cb/iateyourpie_14886031920_257917932/chunked/
        Actual TS file: index-0000001694-exdq.ts?start_offset=1514716&end_offset=1751595
        The file seems to be in there several times, with start offset 1 higher than end offset
    '''
    ''' Below is the Hitbox Downloader function, for ease of adaptation
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
    '''
    
if __name__=="__main__":
    print(PROG_INFO)
    parser = argparse.ArgumentParser(description="Downloads Hitbox recording TS files")
    parser.add_argument('videoID', help='Hitbox video ID')
    parser.add_argument('-d', '--delete', action='store_true', help="Delete the individual TS files and _files folder when done")
    args = parser.parse_args()
    download_broadcast(args.videoID, args.delete)
