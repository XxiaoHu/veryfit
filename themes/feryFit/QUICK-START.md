# 保修申请表单添加语言字段 - 线上部署指南

## 需要上传的文件

```
1. includes/class-warranty-manager.php
2. blocks/warranty-application/view.js
3. blocks/build/warranty-application/view.js
4. assets/js/warranty-admin.js
```

---

## 操作步骤

### 步骤 1：备份数据库
```bash
mysqldump -u 用户名 -p 数据库名 > backup.sql
```

### 步骤 2：更新数据库表结构

推荐通过 SSH 中的 WP-CLI 或数据库管理工具执行一次性 SQL，不要把数据库更新 PHP 脚本上传到主题目录。

```bash
wp db query "ALTER TABLE wp_feryfit_warranty_applications ADD COLUMN language VARCHAR(10) DEFAULT '' AFTER country;"
```

如果表前缀不是 `wp_`，请替换为实际表前缀。执行前可以先检查字段是否已存在：

```bash
wp db query "SHOW COLUMNS FROM wp_feryfit_warranty_applications LIKE 'language';"
```

### 步骤 3：上传代码文件
上传代码文件到服务器对应位置。

---

## ✅ 验证功能

1. 前端提交表单 → 成功
2. 后台看到"语言"列 → 成功

---

## 注意事项

1. 务必先备份数据库
2. 建议在低峰期执行
3. 不要把一次性数据库更新脚本放入 Web 可访问目录
4. 建议先在测试环境验证

---

## 🔄 如何回滚

```sql
ALTER TABLE wp_feryfit_warranty_applications DROP COLUMN language;
```
