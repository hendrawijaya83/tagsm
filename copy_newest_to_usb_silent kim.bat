@echo off
REM ==========================================
REM Script: copy_newest_to_usb.bat
REM Purpose: Auto-detect newest file in a folder and copy to USB
REM ==========================================

:: Prompt for source folder
set "SRC_FOLDER=d:\data"
set "FilePattern=*.gdb"

:: Check if folder exists
if not exist "%SRC_FOLDER%" (
::    echo [ERROR] Folder not found: %SRC_FOLDER%
    pause
    exit /b 1
)

:: Detect first removable drive (DriveType=2)
set "DEST_DRIVE="
for /f "skip=1 tokens=1" %%D in ('wmic logicaldisk where "DriveType=2" get DeviceID') do (
    if not "%%D"=="" (
        set "DEST_DRIVE=%%D"
        goto :found
    )
)

::echo [ERROR] No removable USB drive detected.
pause
exit /b 1

:found
::echo [INFO] Detected removable drive: %DEST_DRIVE%

:: Find the newest file in the folder (sorted by date/time descending)
set "NEWEST_FILE="
for /f "delims=" %%F in ('dir "%SRC_FOLDER%\%FilePattern%" /a:-d /o-d /b') do (
    set "NEWEST_FILE=%%F"
    goto :gotfile
)

:gotfile
if "%NEWEST_FILE%"=="" (
::    echo [ERROR] No files found in folder: %SRC_FOLDER%
::    pause
    exit /b 1
)

::echo [INFO] Newest file found: %NEWEST_FILE%

:: Copy file with overwrite confirmation
copy /y "%SRC_FOLDER%\%NEWEST_FILE%" "%DEST_DRIVE%\"

::if errorlevel 1 (
::    echo [ERROR] Failed to copy file.
::) else (
::    echo [SUCCESS] File copied successfully to %DEST_DRIVE%\
::)

::pause
exit /b 1
