# 联系表单语言字段功能更新

## ✅ 已完成的修改

### 1. 数据库更新
- ✅ 在 `wp_feryfit_contact_messages` 表中添加 `language` 字段 (VARCHAR(10))
- ✅ 更新 `create_table()` 方法
- ✅ 更新 `feryfit_create_contact_table()` 函数

### 2. 后端 PHP 更新 (class-contact-manager.php)

#### 新增功能：
- ✅ `handle_form_submission()` - 接收并保存语言参数
- ✅ `get_languages()` - 获取所有已存在的语言列表
- ✅ `export_messages()` - 导出消息为 CSV（包含语言字段）
- ✅ `get_messages()` - 支持语言、日期范围筛选

#### 新增 REST API 端点：
- ✅ `GET /feryfit/v1/contact-languages` - 获取语言列表
- ✅ `GET /feryfit/v1/contact-messages/export` - 导出数据

### 3. 前端表单更新 (blocks/contact-form/save.js)
- ✅ 添加隐藏的 `language` 输入字段
- ✅ 字段 ID: `contact-form-language`

### 4. 前端 JavaScript 更新 (blocks/contact-form/view.js)
- ✅ 页面加载时自动检测当前网站语言
- ✅ 从 `document.documentElement.lang` 获取语言代码
- ✅ 自动填充到隐藏的 `language` 字段
- ✅ 表单重置后重新设置语言值
- ✅ 添加控制台日志便于调试

### 5. 管理界面更新 (class-contact-manager.php + contact-admin.js)

#### 筛选功能：
- ✅ 关键词搜索（邮箱、姓名、消息）
- ✅ 语言筛选下拉框（自动加载已存在的语言）
- ✅ 日期范围筛选（jQuery UI Datepicker）
  - 开始日期
  - 结束日期
  - 日期联动（结束日期不能早于开始日期）
  - 不能选择未来日期

#### 数据展示：
- ✅ 显示找到的记录总数
- ✅ 表格显示语言列显示语言列
- ✅ 改进的分页（显示省略号）
- ✅ 加载动画

#### 导出功能：
- ✅ 导出按钮
- ✅ 应用当前筛选条件
- ✅ CSV 格式，包含 UTF-8 BOM
- ✅ 文件名带日期：`contact-messages-YYYY-MM-DD.csv`

## 📋 CSV 导出格式

```csv
ID, 邮箱, 姓名, 消息, 语言, 创建时间
1, user@example.com, 张三, 这是测试消息, zh-CN, 2024-06-08 10:30:00
```

## 🔄 工作流程

### 前端提交流程：
1. 用户访问包含联系表单的页面
2. JavaScript 自动检测页面语言（`<html lang="...">`）
3. 将语言代码填充到隐藏字段 `contact-form-language`
4. 用户填写表单并提交
5. FormData 包含所有字段（email, name, message, language, website）
6. 发送到 `/wp-json/feryfit/v1/submit-contact`
7. 后端保存到数据库

### 后端管理流程：
1. 管理员访问"联系消息"管理页面
2. 页面加载时自动获取语言列表
3. 管理员可以：
   - 按关键词搜索
   - 按语言筛选
   - 按日期范围筛选
   - 导出筛选后的数据

## 🎯 语言代码示例

前端会自动捕获的语言代码格式：
- `en-US` - 美国英语
- `zh-CN` - 简体中文
- `zh-TW` - 繁体中文
- `ja` - 日语
- `ko` - 韩语
- `de-DE` - 德语
- `fr-FR` - 法语
- `es-ES` - 西班牙语

## 🧪 测试步骤

### 1. 测试前端语言捕获
```
1. 访问包含联系表单的页面
2. 打开浏览器控制台（F12）
3. 查看日志：Contact form language set to: xxx
4. 填写表单并提交
5. 检查数据库，language 字段是否正确保存
```

### 2. 测试管理界面筛选
```
1. 登录 WordPress 后台
2. 访问"联系消息"菜单
3. 测试语言筛选：选择一个语言，点击"筛选"
4. 测试日期筛选：选择日期范围，点击"筛选"
5. 测试组合筛选：同时使用多个筛选条件
6. 测试重置：点击"重置"按钮
```

### 3. 测试导出功能
```
1. 设置筛选条件（可选）
2. 点击"导出数据"按钮
3. 检查下载的 CSV 文件
4. 用 Excel 打开，验证中文显示正常
5. 验证数据与筛选条件一致
```

## 📂 修改的文件

```
/wp-content/themes/feryFit/
├── includes/
│   └── class-contact-manager.php
│       - 添加 language 字段到数据库表
│       - 修改 handle_form_submission() 接收语言参数
│       - 修改 get_messages() 支持语言和日期筛选
│       - 新增 get_languages() 方法
│       - 新增 export_messages() 方法
│       - 更新管理界面 HTML（添加筛选框）
│       - 加载 jQuery UI Datepicker
├── blocks/contact-form/
│   ├── save.js
│   │   - 添加隐藏的 language 输入字段
│   └── view.js
│       - 自动检测和填充当前页面语言
│       - 表单重置后重新设置语言
└── assets/js/
    └── contact-admin.js
        - 完全重写，添加所有筛选和导出功能
        - 支持语言筛选
        - 支持日期范围筛选（jQuery UI Datepicker）
        - 支持导出数据
```

## 🚀 部署检查清单

- [x] 数据库表已添加 language 字段
- [x] 后端 REST API 已更新
- [x] 前端表单已添加隐藏字段
- [x] 前端 JavaScript 自动捕获语言
- [x] 管理界面已添加筛选功能
- [x] 管理界面已添加导出功能
- [x] jQuery UI Datepicker 已加载
- [x] CSS 样式已优化

## 🔍 调试方法

### 前端调试
打开浏览器控制台，查看：
```javascript
Contact form language set to: zh-CN  // 页面加载时
请求URL: http://...                  // 表单提交时
```

### 后端调试
管理界面控制台会显示：
```javascript
请求URL: http://.../wp-json/feryfit/v1/contact-messages?page=1&language=zh-CN&date_from=2024-01-01
筛选条件: {search: "", language: "zh-CN", dateFrom: "2024-01-01", dateTo: ""}
返回数据: {total: 10, pages: 1, current_page: 1, data: Array(10)}
```

## 💡 关键特性

1. **自动语言检测**：无需用户手动选择，自动从页面检测
2. **隐藏字段**：用户不可见，不影响表单体验
3. **后端筛选**：支持按语言筛选消息
4. **导出包含语言**：CSV 文件包含语言列
5. **管理界面完善**：与保修申请管理界面功能对齐

## 📞 常见问题

**Q: 为什么有些提交的语言为空？**
A: 可能是旧数据（在添加语言字段之前提交的），或者页面的 `<html>` 标签没有 `lang` 属性。

**Q: 如何修改捕获的语言代码？**
A: 修改 `view.js` 中的这一行：
```javascript
const currentLanguage = document.documentElement.lang || 'en-US';
```

**Q: 能否让用户手动选择语言？**
A: 可以，修改 `save.js`，将隐藏字段改为下拉框，但这会破坏表单简洁性。

---

**更新时间**: 2024-06-08
**版本**: 1.0.0
