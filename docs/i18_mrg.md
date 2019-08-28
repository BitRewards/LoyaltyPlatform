1. Установите merge-tool:
```
composer global require giftd/translation-merge-tool
```
2. Откройте раздел [profile](https://gitlab.com/profile/personal_access_tokens) на gitlab и добавьте 
Personal Access Token с выбраным scope "api".
3. Добавьте переменную окружения I18N_MRG_VCS_AUTH_TOKEN="{ВАШ_ТОКЕН}". 
Для bash:
```
echo "export I18N_MRG_VCS_AUTH_TOKEN="{ВАШ_ТОКЕН}" >> ~/.bash_profile
```
Для zsh:
```
echo "export I18N_MRG_VCS_AUTH_TOKEN="{ВАШ_ТОКЕН}" >> ~/.zshrc
```
4. Перейдите в ветку, в которой требуется импортировать переводы и выполните i18n_mrg 