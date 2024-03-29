# SuiteCRM Module Generator

It's like a studio... but for programmers.

If you want to customize some behavior, make a branch and edit code.


## Examples

```sh
cd /path/to/SuiteCRM

mkdir modules/VipAccounts
cd modules/VipAccounts
smog bean --module VipAccounts --object VipAccount --table vip_accounts
smog vardefs --fields code:varchar[12,required],num:int,vip_type:enum,status:enum,main_contact_id:id[Contacts],amount:currency,percent:double --implements basic,assignable,security_groups
smog menu
smog language
smog metadata
smog related
# Optional
smog view.detail
smog view.edit
smog controller
smog m2m --left Documents --right VipAccounts
smog touch -f 1.php

```

> modules/VipAccounts$ smog bean --object VipAccount --table vip_accounts
>
> New file created: modules/VipAccounts/VipAccount.php
>
> New file created: custom/Extension/application/Ext/Include/VipAccounts.php
>
> Autogenerated file deleted: custom/application/Ext/Include/modules.ext.php
>
> LBL_MI_REBUILDING Include...<br>
>
> New file created: custom/Extension/application/Ext/Language/en_us.VipAccounts.php
>
> modules/VipAccounts$ smog vardefs --fields code:varchar[12,required],num:int,vip_type:enum,status:enum,main_contact_id:id[Contacts],
>amount:currency,> percent:double --implements basic,assignable,security_groups
>
> New file created: modules/VipAccounts/vardefs.php
>
> modules/VipAccounts$ smog menu
>
> New file created: modules/VipAccounts/Menu.php
>
> modules/VipAccounts$ smog language
>
> New directory created: modules/VipAccounts/language
>
> New file created: modules/VipAccounts/language/en_us.lang.php
>
> modules/VipAccounts$ smog metadata
>
> New directory created: modules/VipAccounts/metadata
>
> New file created: modules/VipAccounts/metadata/detailviewdefs.php
>
> New file created: modules/VipAccounts/metadata/editviewdefs.php
>
> New file created: modules/VipAccounts/metadata/listviewdefs.php
>
> New file created: modules/VipAccounts/metadata/quickcreatedefs.php
>
> New file created: modules/VipAccounts/metadata/searchdefs.php
>
> New file created: modules/VipAccounts/metadata/SearchFields.php
>
> New file created: modules/VipAccounts/metadata/studio.php
>
> New file created: modules/VipAccounts/metadata/subpaneldefs.php
>
> New directory created: modules/VipAccounts/metadata/subpanels
>
> New file created: modules/VipAccounts/metadata/subpanels/default.php
>
> modules/VipAccounts$ smog related
>
> New file created: custom/Extension/modules/Contacts/Ext/Vardefs/VipAccounts.php
>
> New file created: custom/Extension/modules/Contacts/Ext/Layoutdefs/VipAccounts.php
>
> modules/VipAccounts$ # Optional
>
> modules/VipAccounts$ smog view.detail
>
> New directory created: modules/VipAccounts/views
>
> New file created: modules/VipAccounts/views/view.detail.php
>
> modules/VipAccounts$ smog view.edit
>
> New file created: modules/VipAccounts/views/view.edit.php
>
> modules/VipAccounts$ smog controller
>
> New file created: modules/VipAccounts/controller.php
>
> modules/VipAccounts$ smog m2m --left Documents --right VipAccounts
>
> New file created: custom/metadata/Documents_VipAccounts.php
>
> New file created: custom/Extension/application/Ext/TableDictionary/Documents_VipAccounts.php
>
> New file created: custom/Extension/modules/Documents/Ext/Vardefs/VipAccounts.php
>
> New file created: custom/Extension/modules/VipAccounts/Ext/Vardefs/Documents.php
>
> New file created: custom/Extension/modules/Documents/Ext/Layoutdefs/VipAccounts.php
>
> New file created: custom/Extension/modules/VipAccounts/Ext/Layoutdefs/Documents.php
>
> modules/VipAccounts$ touch -f 1.php
>
> New file created: 1.php


Clear this example
```sh
rm -rf modules/VipAccounts/*
rm -f custom/Extension/application/Ext/Include/VipAccounts.php
rm -f custom/Extension/application/Ext/Language/en_us.VipAccounts.php
rm -f custom/Extension/modules/Contacts/Ext/Layoutdefs/VipAccounts.php
rm -f custom/Extension/modules/Contacts/Ext/Vardefs/VipAccounts.php
rm -f custom/metadata/Documents_VipAccounts.php
rm -f custom/Extension/application/Ext/TableDictionary/Documents_VipAccounts.php
rm -f custom/Extension/modules/Documents/Ext/Vardefs/VipAccounts.php
rm -f custom/Extension/modules/VipAccounts/Ext/Vardefs/Documents.php
rm -f custom/Extension/modules/Documents/Ext/Layoutdefs/VipAccounts.php
rm -f custom/Extension/modules/VipAccounts/Ext/Layoutdefs/Documents.php

```
