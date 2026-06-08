#!/bin/bash
##############################################
# 联系表单语言字段数据库迁移脚本
# 用途: 在服务器上更新数据库表结构
# 使用方法: bash migrate-contact-language.sh
##############################################

echo "================================================"
echo "  联系表单数据库迁移 - 添加 language 字段"
echo "================================================"
echo ""

# 进入 WordPress 根目录
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
WP_ROOT="$SCRIPT_DIR/../../.."

cd "$WP_ROOT" || exit 1

echo "WordPress 根目录: $WP_ROOT"
echo ""

# 执行数据库迁移
echo "正在更新数据库表结构..."
echo ""

php -r "
require_once('wp-load.php');

global \$wpdb;
\$table_name = \$wpdb->prefix . 'feryfit_contact_messages';

echo '检查表: ' . \$table_name . \"\n\";

// 检查表是否存在
\$table_exists = \$wpdb->get_var(\"SHOW TABLES LIKE '\$table_name'\");
if (!\$table_exists) {
    echo \"❌ 错误: 表 \$table_name 不存在\n\";
    exit(1);
}

// 检查 language 字段是否已存在
\$column_exists = \$wpdb->get_results(\"SHOW COLUMNS FROM \$table_name LIKE 'language'\");

if (empty(\$column_exists)) {
    echo \"正在添加 language 字段...\n\";

    \$sql = \"ALTER TABLE \$table_name ADD COLUMN language VARCHAR(10) DEFAULT '' AFTER message\";
    \$result = \$wpdb->query(\$sql);

    if (\$result !== false) {
        echo \"✅ 成功添加 language 字段\n\";
    } else {
        echo \"❌ 失败: \" . \$wpdb->last_error . \"\n\";
        exit(1);
    }
} else {
    echo \"✅ language 字段已存在，跳过\n\";
}

echo \"\n当前表结构:\n\";
echo \"----------------------------------------\n\";
\$columns = \$wpdb->get_results(\"SHOW COLUMNS FROM \$table_name\");
foreach (\$columns as \$column) {
    echo \"  \" . str_pad(\$column->Field, 20) . \" \" . \$column->Type . \"\n\";
}
echo \"----------------------------------------\n\";
echo \"\n✅ 迁移完成!\n\";
"

echo ""
echo "================================================"
echo "  迁移完成"
echo "================================================"
