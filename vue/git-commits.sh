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

git add -A
echo "--------------------"
# Проверяем, что передан хотя бы один аргумент
if [ "$#" -eq 0 ]; then
  echo "Usage: $0 <commit_message>"
  exit 1
fi

# Создаем массив для хранения аргументов
commit_messages=()

# Читаем аргументы и добавляем их в массив
while [ "$#" -gt 0 ]; do
  commit_messages+=("-m" "$1")
  shift
done

# Выполняем git commit с переданными аргументами
git commit "${commit_messages[@]}"
echo "--------------------"
git push origin dev
echo "--------------------"
git stash clear