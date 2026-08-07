@echo off
REM Print HTML to PDF using Microsoft Edge
REM This script converts USER_MANUAL.html to PDF

set HTMLFILE=%~dp0downloads\USER_MANUAL.html
set PDFFILE=%~dp0downloads\USER_MANUAL.pdf

REM Check if HTML file exists
if not exist "%HTMLFILE%" (
    echo Error: USER_MANUAL.html not found at %HTMLFILE%
    pause
    exit /b 1
)

echo Converting %HTMLFILE% to PDF...
echo This will open in Microsoft Edge. Please use Print (Ctrl+P) and select "Save as PDF"

REM Open in Edge with print dialog
start msedge.exe "file:///%HTMLFILE:~0,1%|%HTMLFILE:~2%"

echo.
echo After printing to PDF, save it to: %PDFFILE%
echo.
pause
