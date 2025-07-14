@echo off
goto PROMPT
:USAGE
run `createGitAndAdd2BitBucket`
"Enter remote repository URL: or 'q' to quit :" https://github.com/osolgithub/firebase
make sure defualt branch is "main" if it is "master" replace the codes appropriately
if it asks for MERGE MESSAGE , use https://stackoverflow.com/a/14622763
setlocal
:PROMPT
rem Git URL eg:  https://userid@bitbucket.org/userid/repository-name.git
set /p remoteRepositoryURL="Enter remote repository URL: or 'q' to quit : "
echo "Entered remote repository URL was " %remoteRepositoryURL%
set quitbat=true
IF not "%remoteRepositoryURL%" == "q" IF not "%remoteRepositoryURL%" ==  "Q" set quitbat=false
if "%quitbat%" == "true" goto END
:NOTEND
rem echo "Inside :NOTEND Entered Value was " %ANYKEY%
git init
git add .
git commit -m "First commit"
git remote add origin %remoteRepositoryURL%
git remote -v
rem git pull --progress -v --no-rebase --allow-unrelated-histories "origin" main
git push origin main
set /p ANYKEY="Successfully added project to GIT, Press any key to continue: "
:END
endlocal