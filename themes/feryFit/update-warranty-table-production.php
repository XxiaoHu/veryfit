<?php
/**
 * 生产环境数据库表更新脚本 - 添加 language 字段
 *
 * 安全说明：
 * 1. 此脚本从 wp-config.php 自动读取数据库配置，无需手动修改
 * 2. 使用后请立即删除此文件
 * 3. 建议仅通过 SSH 命令行执行
 *
 * 使用方法：
 * SSH 命令行：php update-warranty-table-production.php
 */

// 加载 WordPress 配置
if (!file_exists(__DIR__ . '/../../../wp-config.php')) {
    die("错误：找不到 wp-config.php 文件\n");
}

// 从 wp-config.php 读取数据库配置
$config_file = file_get_contents(__DIR__ . '/../../../wp-config.php');

// 提取数据库配置
preg_match("/define\(\s*'DB_NAME'\s*,\s*'([^']+)'\s*\)/", $config_file, $db_name);
preg_match("/define\(\s*'DB_USER'\s*,\s*'([^']+)'\s*\)/", $config_file, $db_user);
preg_match("/define\(\s*'DB_PASSWORD'\s*,\s*'([^']+)'\s*\)/", $config_file, $db_password);
preg_match("/define\(\s*'DB_HOST'\s*,\s*'([^']+)'\s*\)/", $config_file, $db_host);
preg_match("/\\\$table_prefix\s*=\s*'([^']+)'/", $config_file, $table_prefix);

if (empty($db_name[1]) || empty($db_user[1]) || empty($db_password[1]) || empty($db_host[1])) {
    die("错误：无法从 wp-config.php 读取数据库配置\n");
}

$DB_NAME = $db_name[1];
$DB_USER = $db_user[1];
$DB_PASSWORD = $db_password[1];
$DB_HOST = $db_host[1];
$TABLE_PREFIX = isset($table_prefix[1]) ? $table_prefix[1] : 'wp_';

echo "========================================\n";
echo "数据库表更新脚本\n";
echo "========================================\n";
echo "数据库名: $DB_NAME\n";
echo "数据库主机: $DB_HOST\n";
echo "表前缀: $TABLE_PREFIX\n";
echo "========================================\n\n";

// 连接数据库
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);

// 检查连接
if ($mysqli->connect_error) {
    die("✗ 连接失败: " . $mysqli->connect_error . "\n");
}

$mysqli->set_charset('utf8mb4');
echo "✓ 数据库连接成功\n\n";

$table_name = $TABLE_PREFIX . 'feryfit_warranty_applications';

// 检查表是否存在
$result = $mysqli->query("SHOW TABLES LIKE '$table_name'");
if ($result->num_rows === 0) {
    echo "✗ 表 $table_name 不存在，无需更新。\n";
    $mysqli->close();
    exit(1);
}

echo "✓ 表 $table_name 存在\n";

// 检查 language 字段是否已存在
$result = $mysqli->query("SHOW COLUMNS FROM $table_name LIKE 'language'");
if ($result->num_rows > 0) {
    echo "✓ language 字段已存在，无需更新。\n\n";
    echo "========================================\n";
    echo "更新完成！\n";
    echo "========================================\n";
    $mysqli->close();
    exit(0);
}

echo "→ 准备添加 language 字段...\n";

// 开始事务
$mysqli->begin_transaction();

try {
    // 添加 language 字段
    $sql = "ALTER TABLE $table_name ADD COLUMN language VARCHAR(10) DEFAULT '' AFTER country";

    if ($mysqli->query($sql) === TRUE) {
        // 提交事务
        $mysqli->commit();

        echo "✓ 成功添加 language 字段到表 $table_name\n\n";

        // 验证字段是否添加成功
        $result = $mysqli->query("SHOW COLUMNS FROM $table_name LIKE 'language'");
        if ($result->num_rows > 0) {
            echo "✓ 字段验证成功\n\n";

            // 显示表结构
            echo "当前表结构：\n";
            echo "----------------------------------------\n";
            $columns = $mysqli->query("DESCRIBE $table_name");
            while ($row = $columns->fetch_assoc()) {
                echo sprintf("%-20s %-20s\n", $row['Field'], $row['Type']);
            }
            echo "----------------------------------------\n\n";
        }

        echo "========================================\n";
        echo "更新成功完成！\n";
        echo "========================================\n";
        echo "\n⚠️  重要提示：\n";
        echo "1. 请立即删除此脚本文件以确保安全\n";
        echo "2. 请部署更新后的代码文件到生产环境\n";
        echo "3. 测试表单提交功能是否正常\n\n";

    } else {
        throw new Exception($mysqli->error);
    }

} catch (Exception $e) {
    // 回滚事务
    $mysqli->rollback();
    echo "✗ 添加 language 字段失败: " . $e->getMessage() . "\n";
    $mysqli->close();
    exit(1);
}

$mysqli->close();
