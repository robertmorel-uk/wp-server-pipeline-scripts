#!/bin/bash
#Merge local branch and git commit to remote master
#----------DO NOT MODIFY THIS FILE--------------------------------------------------------

CUR_USER=$(whoami)
BRANCH=$(git symbolic-ref HEAD | sed -e 's,.*/\(.*\),\1,')

printf "Running script to commit changes to the branch and master repositories.\n"
printf "\nUser: $CUR_USER\n"
printf "Branch: $BRANCH\n"
printf "Commit message: $1\n\n"

git add -A
if [ -z "$1" ]; then
    git commit -a -m "automated commit from: $CUR_USER" 
else
    git commit -a -m "$1"
fi

if [ "$BRANCH" = "master" ]; then
git push
  printf "Master branch commited. \n\n***Now pull repo from 34sp.***\n\n"
else
    git checkout master
    git pull
    git merge "feature/$BRANCH"
    git push
    printf "$BRANCH branch commited. \n\n***Now pull repo from 34sp.***\n\n"
fi