## create a new repository on the command line

echo "# fcmhtmljs" >> README.md
git init
git add .
git commit -m "first commit"
git branch -M main
git remote add origin https://github.com/osolgithub/fcmhtmljs.git
git push -u origin main

## or push an existing repository from the command line

git remote add origin https://github.com/osolgithub/fcmhtmljs.git
git branch -M main
git push -u origin main

## Get To get current local branch and remote brank use

git show-ref

## push declined due to repository rule violations
https://github.com/orgs/community/discussions/158212#discussioncomment-13020288

1. git reset --soft HEAD~1
2. remove files with critical data.

or


Use git rm.

If you want to remove the file from the Git repository and the filesystem, use:
 
git rm file1.txt
But if you want to remove the file only from the Git repository and not remove it from the filesystem, use: 
git rm --cached file1.txt
git rm --cached easelex-1b2ef-firebase-adminsdk-fbsvc-dcb520fb54.json
3. git commit --amend --all -m "remove file1.txt"
4. git push origin main


git log main
git rebase -i <COMMIT-ID>~1
eg: git rebase -i 2ae0cdd86731f73cace7f78d6df5ca70e5fc3f17~1
git add .
git commit --amend
git rebase --continue
git push origin main

### Get list of files

git ls-tree -r branch_name --name-only
-r is recursive
--name-only strips other details

git ls-tree -r main --name-only


## Git Push and Git Fetch

https://www.theserverside.com/blog/Coffee-Talk-Java-News-Stories-and-Opinions/Git-pull-vs-fetch-Whats-the-difference


1. Remote Repository
2. Local Repostory
3. Working Directory

Pull = fetch and merge

My blog : https://www.outsource-online.net/blog/2022/06/13/git-command-line-tutorials/#gitFetchAndPull


## Please enter a commit message to explain why this merge is necessary,
~                                                                               
~                                                                               
~                                                                               
".git/MERGE_MSG" 7L, 302C

My question is - what do I need to do here? because I can't typing any message.

Ans: https://stackoverflow.com/a/14622763
It seems you are now in vi or vim.

press i, then input your merge message.

Then esc, and :wq


## [remote rejected] main -> main (push declined due to repository rule violations)
error: failed to push some refs to 'https://github.com/osolgithub/firebase.git'

Happens on : `git push -u origin main`


 https://docs.github.com/code-security/secret-scanning/working-with-secret-scanning-and-push-protection/working-with-push-protection-from-the-command-line#resolving-a-blocked-push
https://docs.github.com/en/code-security/secret-scanning/working-with-secret-scanning-and-push-protection/working-with-push-protection-from-the-command-line#removing-a-secret-introduced-by-an-earlier-commit-on-your-branch

git log
git rebase -i <COMMIT-ID>~1.
git rm --cached easelex-1b2ef-firebase-adminsdk-fbsvc-dcb520fb54.json or git rm --cached *.json
git add .
git commit --amend
git rebase --continue  //to finish the rebase. id any error, git rebase --edit-todo
git push -f -u origin main
-------------------------------------
You can amend the commit now, with

  git commit --amend

Once you are satisfied with your changes, run

  git rebase --continue
  ---------------------
  ## Git push rejected "non-fast-forward"
  
  git push
To https://github.com/githubnam/repository
 ! [rejected]        main -> main (non-fast-forward)
error: failed to push some refs to 'https://github.com/osolgithub/fcmhtmljs'
hint: Updates were rejected because the tip of your current branch is behind
hint: its remote counterpart. If you want to integrate the remote changes,
hint: use 'git pull' before pushing again.
hint: See the 'Note about fast-forwards' in 'git push --help' for details.

## Allow secret
 https://github.com/osolgithub/fcmhtmljs/security/secret-scanning/unblock-secret/2zrE4sF6sCOgggtbpYXSs0Qyb

## Add an exiting folder to github ( 3 methods)

### Method 1

1. create github repository
2. clone to local folder
3. copy the files to this cloned folder
4. commit and push

This works for small sized projects

### Method 2

**PS :** This repository should be blank. Not even .gitignore, README or License


```
echo "# firebase" >> README.md
git init
git add README.md
git commit -m "first commit"
git branch -M main
git remote add origin https://github.com/osolgithub/firebase.git
git push -u origin main
```

push an existing repository from the command line

```
git remote add origin https://github.com/osolgithub/firebase.git
git branch -M main
git push -u origin main
```

