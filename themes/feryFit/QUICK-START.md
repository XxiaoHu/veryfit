# 保修申请表单添加语言字段 - 线上部署指南

## 📦 需要上传的文件（4个）

```
1. update-warranty-table-production.php  ← 数据库更新脚本（用完删除）
2. includes/class-warranty-manager.php   ← 后端代码
3. blocks/warranty-application/view.js   ← 前端代码
4. assets/js/warranty-admin.js           ← 后台管理代码
```

---

## 🚀 操作步骤（3步完成）

### 步骤 1：备份数据库
```bash
mysqldump -u 用户名 -p 数据库名 > backup.sql
```

### 步骤 2：更新数据库表结构
```bash
# 上传 update-warranty-table-production.php 到主题目录后执行：
cd /path/to/wp-content/themes/feryFit
php update-warranty-table-production.php

# 看到成功提示后立即删除
rm update-warranty-table-production.php
```

**成功的输出：**
```
✓ 数据库连接成功
✓ 成功添加 language 字段
========================================
更新成功完成！
========================================
```

### 步骤 3：上传代码文件
上传文件 2、3、4 到服务器对应位置

---

## ✅ 验证功能

1. 前端提交表单 → 成功
2. 后台看到"语言"列 → 成功

---

## 📝 脚本说明

**update-warranty-table-production.php 做什么？**

- 自动从 wp-config.php 读取数据库配置（无需手动填密码）
- 添加 language 字段到数据库表
- 自动检查字段是否已存在（可重复执行）
- 包含事务支持（失败自动回滚）

**安全吗？**
- ✅ 不修改现有数据
- ✅ 只添加一个新字段
- ✅ 可重复执行
- ✅ 用完立即删除

---

## ⚠️ 注意事项

1. 务必先备份数据库
2. 建议在低峰期执行
3. 脚本执行完立即删除
4. 建议先在测试环境验证

---

## 🔄 如何回滚

```sql
ALTER TABLE wp_feryfit_warranty_applications DROP COLUMN language;
```
