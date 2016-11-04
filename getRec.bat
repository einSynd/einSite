@echo off
echo Recording ID is hitbox.tv/video/[number]
set /p id=Enter recording ID: 
echo Delete individual recording files when done?
set /p todel=(Default no, enter anything for yes) || set todel=no
if %todel% neq no set todel=-d
if %todel% equ no set todel= 
HitboxDownloader.py %todel% %id%
if %errorlevel% neq 0 goto end
pause
exit

:end
echo A problem has occurred, try checking recording ID
pause