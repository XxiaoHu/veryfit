# 🚀 联系表单语言字段 - 服务器部署指南

## 📋 部署清单

### 需要上传的文件

```
/wp-content/themes/feryFit/
├── includes/
│   └── class-contact-manager.php          # 已修改
├── blocks/
│   ├── contact-form/
│   │   ├── save.js                        # 已修改
│   │   └── view.js                        # 已修改
│   └── build/
│       └── contact-form/                  # 已重新编译
│           ├── index.js
│           └── view.js
├── assets/js/
│   └── contact-admin.js                   # 已修改
└── migrate-contact-language.sh            # 新增（迁移脚本）
```

---

## 🔧 部署步骤

### 第一步：上传代码

```bash
# 1. 上传修改后的文件到服务器
# 使用 FTP、SFTP 或 Git 将以上文件上传到对应目录
```

### 第二步：执行数据库迁移

**方法 1：使用 Shell 脚本（推荐）**

```bash
# SSH 连接到服务器后
cd /path/to/wp-content/themes/feryFit/

# 执行迁移脚本
bash migrate-contact-language.sh
```

**方法 2：使用 WP-CLI**

```bash
cd /path/to/wordpress/

wp eval-file wp-content/themes/feryFit/migrate-contact-language.sh
```

**方法 3：通过 WordPress 后台**

将以下代码临时添加到 `functions.php` 的**最底部**：

```php
// 临时代码：执行一次后立即删除
add_action('admin_init', 'feryfit_migrate_contact_language', 999);
function feryfit_migrate_contact_language() {
    if (get_option('feryfit_contact_language_migrated')) {
        return; // 已执行过
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'feryfit_contact_messages';

    // 检查 language 字段是否已存在
    $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'language'");

    if (empty($column_exists)) {
        $sql = "ALTER TABLE $table_name ADD COLUMN language VARCHAR(10) DEFAULT '' AFTER message";
        $wpdb->query($sql);
    }

    update_option('feryfit_contact_language_migrated', true);
}
```

**执行步骤：**
1. 添加上述代码到 `functions.php`
2. 登录 WordPress 后台（访问任意管理页面即可触发）
3. 检查数据库表是否已添加 `language` 字段
4. **立即删除**添加的代码

**方法 4：直接 SQL（如果有数据库访问权限）**

```sql
-- 检查表结构
SHOW COLUMNS FROM wp_feryfit_contact_messages;

-- 如果没有 language 字段，执行以下命令
ALTER TABLE wp_feryfit_contact_messages 
ADD COLUMN language VARCHAR(10) DEFAULT '' AFTER message;

-- 验证
SHOW COLUMNS FROM wp_feryfit_contact_messages;
```

---

### 第三步：清除缓存

```bash
# 1. 清除 WordPress 缓存（如果使用了缓存插件）
# 在 WordPress 后台 -> 缓存插件 -> 清除所有缓存

# 2. 清除 CDN 缓存（如果使用了 CDN）
# 在 CDN 控制面板中清除缓存

# 3. 清除 Opcache（如果使用）
# 方法1: 重启 PHP-FPM
sudo systemctl restart php-fpm

# 方法2: 或访问 opcache reset 页面
```

### 第四步：重新保存表单区块

1. 登录 WordPress 后台
2. 找到包含**联系表单区块**的页面
3. 点击"编辑"
4. 不做任何修改，直接点击"更新"按钮
5. 这会重新生成页面的 HTML，确保包含新的 `language` 字段

---

## ✅ 验证部署

### 1. 检查数据库表结构

```bash
# SSH 连接服务器后
cd /path/to/wordpress/

php -r "
require_once('wp-load.php');
global \$wpdb;
\$table = \$wpdb->prefix . 'feryfit_contact_messages';
\$columns = \$wpdb->get_results(\"SHOW COLUMNS FROM \$table\");
foreach (\$columns as \$col) {
    echo \$col->Field . ' (' . \$col->Type . ')' . PHP_EOL;
}
"
```

**预期输出应包含：**
```
id (bigint unsigned)
email (varchar(255))
name (varchar(255))
message (text)
language (varchar(10))          ← 确认此字段存在
created_at (datetime)
```

### 2. 检查前端表单 HTML

在前端页面：
1. 右键点击表单 -> "检查元素"
2. 查找以下 HTML：

```html
<input type="hidden" name="language" id="contact-form-language" value="">
```

### 3. 测试表单提交

1. 打开包含联系表单的页面
2. 按 `F12` 打开开发者工具
3. 切换到 **Console（控制台）**标签
4. 应该看到：`Contact form language set to: zh-CN`（或其他语言代码）
5. 填写表单并提交
6. 检查数据库，新记录应包含 `language` 值

### 4. 测试管理后台筛选

1. 登录 WordPress 后台
2. 进入"联系消息"菜单
3. 确认有以下筛选功能：
   - 关键词搜索
   - **语言筛选下拉框**（应自动加载已存在的语言）
   - **日期范围筛选**
   - **导出数据按钮**

---

## 🐛 故障排查

### 问题1：表单提交 500 错误

**可能原因：**
- 数据库表未更新
- PHP 缓存未清除

**解决方法：**
```bash
# 1. 验证表结构
SHOW COLUMNS FROM wp_feryfit_contact_messages;

# 2. 清除 PHP Opcache
sudo systemctl restart php-fpm

# 3. 查看 PHP 错误日志
tail -f /var/log/php-fpm/error.log
```

### 问题2：language 字段始终为空

**可能原因：**
- 页面使用了旧的缓存 HTML
- 区块未重新保存

**解决方法：**
1. 清除所有缓存
2. 在 WordPress 后台重新保存包含表单的页面
3. 清除浏览器缓存或使用无痕模式测试

### 问题3：管理后台语言筛选为空

**可能原因：**
- 数据库中没有任何记录
- 已有记录的 language 字段都为空

**解决方法：**
- 提交几条测试表单
- 或手动更新现有记录：
```sql
UPDATE wp_feryfit_contact_messages 
SET language = 'zh-CN' 
WHERE language = '' OR language IS NULL 
LIMIT 10;
```

---

## 📝 回滚方案

如果需要回滚到之前的版本：

### 1. 删除 language 字段（可选）

```sql
ALTER TABLE wp_feryfit_contact_messages DROP COLUMN language;
```

### 2. 恢复旧代码

使用 Git 恢复旧版本：
```bash
git checkout HEAD~1 -- includes/class-contact-manager.php
git checkout HEAD~1 -- blocks/contact-form/save.js
git checkout HEAD~1 -- blocks/contact-form/view.js
git checkout HEAD~1 -- assets/js/contact-admin.js
```

---

## 📊 修改的数据库表

### 表名
`wp_feryfit_contact_messages`

### 新增字段
| 字段名 | 类型 | 默认值 | 说明 |
|--------|------|--------|------|
| language | VARCHAR(10) | '' | 表单提交时的语言代码（如 zh-CN, en-US） |

### 完整表结构
```sql
CREATE TABLE IF NOT EXISTS wp_feryfit_contact_messages (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  language VARCHAR(10) DEFAULT '',           -- 新增
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
```

---

## 🎯 部署后确认清单

- [ ] 数据库表已添加 `language` 字段
- [ ] 前端表单包含隐藏的 language 输入框
- [ ] 控制台显示 "Contact form language set to: xxx"
- [ ] 提交表单成功，数据库记录包含 language 值
- [ ] 管理后台可以按语言筛选
- [ ] 管理后台可以选择日期范围筛选
- [ ] 导出的 CSV 文件包含 language 列
- [ ] 删除了临时迁移代码（如果使用方法3）
- [ ] 删除了 `migrate-contact-language.sh`（可选）

---

## 📞 技术支持

如遇到问题，请检查：

1. **PHP 错误日志**
   ```bash
   tail -f /var/log/php-fpm/error.log
   ```

2. **WordPress 调试日志**
   ```bash
   tail -f /path/to/wp-content/debug.log
   ```

3. **浏览器控制台**
   - 按 F12
   - 查看 Console 和 Network 标签

---

**部署日期**: 2024-06-08  
**版本**: 1.0.0  
**文档更新**: 2024-06-08
