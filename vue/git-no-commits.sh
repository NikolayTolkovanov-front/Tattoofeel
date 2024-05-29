git fetch origin dev
echo "--------------------"
git stash -u
echo "--------------------"
git rebase origin/dev
echo "--------------------"
git push
echo "--------------------"
git stash apply
echo "--------------------"
git stash clear