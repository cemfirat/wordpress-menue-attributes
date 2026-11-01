<?php
/**
 * Dependency-free contracts for the real plugin callbacks.
 * WordPress persistence/enqueue functions below are explicit test doubles.
 * This is not a live WordPress, role, browser or theme acceptance test.
 */

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

define('ABSPATH', __DIR__ . '/');
$GLOBALS['test_hooks'] = [];
$GLOBALS['test_meta'] = [];
$GLOBALS['test_menu_items'] = [];
$GLOBALS['test_scripts'] = [];
$GLOBALS['test_inline'] = [];
$GLOBALS['test_encoding_failure'] = false;
$GLOBALS['test_assertions'] = 0;

function add_action($name, $callback, $priority = 10, $accepted = 1)
{
    $GLOBALS['test_hooks'][$name][] = $callback;
}
function add_filter($name, $callback, $priority = 10, $accepted = 1)
{
    add_action($name, $callback, $priority, $accepted);
}
function get_post_meta($id, $key, $single = true)
{
    return isset($GLOBALS['test_meta'][$id][$key]) ? $GLOBALS['test_meta'][$id][$key] : '';
}
function update_post_meta($id, $key, $value)
{
    $GLOBALS['test_meta'][$id][$key] = $value;
}
function delete_post_meta($id, $key)
{
    unset($GLOBALS['test_meta'][$id][$key]);
}
function get_the_title($id)
{
    return get_post_meta($id, 'fixture_title');
}
function wp_get_nav_menus()
{
    return [(object) ['term_id' => 1]];
}
function wp_get_nav_menu_items($menu)
{
    return $GLOBALS['test_menu_items'];
}
function plugins_url($path, $file)
{
    return 'https://example.invalid/plugin/' . $path;
}
function wp_register_script($handle, $url, $dependencies, $version, $footer)
{
    $GLOBALS['test_scripts'][$handle] = compact('url', 'dependencies', 'version', 'footer');
}
function wp_enqueue_script($handle) {}
function wp_json_encode($value, $flags = 0)
{
    return $GLOBALS['test_encoding_failure'] ? false : json_encode($value, $flags);
}
function wp_add_inline_script($handle, $code, $position)
{
    $GLOBALS['test_inline'][] = compact('handle', 'code', 'position');
}
function esc_attr($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
}
function check_same($expected, $actual, $message)
{
    $GLOBALS['test_assertions']++;
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}
function invoke_action($name, array $arguments = [])
{
    foreach ($GLOBALS['test_hooks'][$name] as $callback) {
        call_user_func_array($callback, $arguments);
    }
}
function decode_inline($record)
{
    check_same('cf-mef-dom', $record['handle'], 'Inline data belongs to the registered script.');
    check_same('before', $record['position'], 'Inline data precedes the DOM patcher.');
    $prefix = 'window.CF_MEF_DATA = ';
    check_same($prefix, substr($record['code'], 0, strlen($prefix)), 'Inline assignment prefix.');
    check_same(';', substr($record['code'], -1), 'Inline assignment terminator.');
    $data = json_decode(substr($record['code'], strlen($prefix), -1));
    check_same(JSON_ERROR_NONE, json_last_error(), 'Inline payload is valid JSON.');
    return $data;
}

require dirname(__DIR__) . '/menu-item-extended-fields.php';

$cases = json_decode(file_get_contents(__DIR__ . '/attribute-cases.json'), true);
check_same(JSON_ERROR_NONE, json_last_error(), 'Fixture JSON is valid.');
$filter = $GLOBALS['test_hooks']['nav_menu_link_attributes'][0];
foreach ($cases as $index => $case) {
    $expected = $case['expected'];
    check_same($expected, \CF\MEF\parse_and_sanitize_attributes($case['input']), 'PHP parser: ' . $case['name']);
    $id = $index + 100;
    $url = 'https://example.invalid/item-' . $id;
    $GLOBALS['test_meta'][$id] = [
        '_menu_item_custom_attr' => $case['input'],
        '_menu_item_custom_id' => 'item-' . $id,
        '_menu_item_url' => $url,
        'fixture_title' => $case['name'],
    ];
    $GLOBALS['test_menu_items'][] = (object) ['ID' => $id, 'url' => $url, 'title' => $case['name']];
    $GLOBALS['cf_mef_used_menu_item_ids'][$id] = true;
    $initial = ['href' => $url, 'class' => 'theme-class'];
    $expected_filter = array_merge($initial, ['id' => 'item-' . $id], $expected);
    check_same($expected_filter, $filter($initial, (object) ['ID' => $id], null, 0), 'PHP link filter: ' . $case['name']);
}

// The existing storage contract keeps editable attribute text, including
// names that are filtered out at output. No migration or destructive rewrite.
$raw = 'onclick="fixture" data-note="Original"';
$_POST = [
    'menu-item-custom-id' => [99 => '9 test'],
    'menu-item-custom-class' => [99 => 'one one two!'],
    'menu-item-custom-attr' => [99 => $raw],
];
invoke_action('wp_update_nav_menu_item', [1, 99]);
check_same($raw, get_post_meta(99, '_menu_item_custom_attr'), 'Attribute text is passed to existing metadata storage unchanged.');
check_same('id_9test', get_post_meta(99, '_menu_item_custom_id'), 'ID storage keeps existing normalization.');
check_same('one two', get_post_meta(99, '_menu_item_custom_class'), 'Class storage keeps existing normalization.');
$_POST = [];
invoke_action('wp_update_nav_menu_item', [1, 99]);
check_same([], $GLOBALS['test_meta'][99], 'Cleared form fields remove the three metadata keys.');

invoke_action('wp_enqueue_scripts');
$used_record = $GLOBALS['test_inline'][0];
$used_data = decode_inline($used_record);
check_same(count($cases), count($used_data->items), 'Used-item export includes the fixture items.');
foreach ($used_data->items as $index => $item) {
    check_same(true, is_object($item->custom_attr), 'Attributes cross the boundary as a map, including empty maps.');
    check_same($cases[$index]['expected'], get_object_vars($item->custom_attr), 'JS payload uses PHP-normalized pairs: ' . $cases[$index]['name']);
}
check_same(false, strpos($used_record['code'], '<'), 'HTML/script delimiters are escaped before the inline-script API.');
check_same(false, strpos($used_record['code'], '>'), 'Closing HTML delimiters are escaped.');
check_same(false, strpos($used_record['code'], '&'), 'Ampersands are safely encoded while values round-trip.');
check_same(false, $GLOBALS['test_scripts']['cf-mef-dom']['version'] === '1.4', 'The changed DOM script does not retain the previous cache key.');
check_same(true, $GLOBALS['test_scripts']['cf-mef-dom']['footer'], 'Existing footer loading remains in place.');

$GLOBALS['cf_mef_used_menu_item_ids'] = [];
invoke_action('wp_enqueue_scripts');
$fallback_record = $GLOBALS['test_inline'][1];
$fallback_data = decode_inline($fallback_record);
check_same(json_encode($used_data), json_encode($fallback_data), 'Both menu collection paths export the same normalized data.');

$GLOBALS['test_encoding_failure'] = true;
invoke_action('wp_enqueue_scripts');
check_same(2, count($GLOBALS['test_inline']), 'JSON encoding failure does not emit invalid JavaScript.');

if (isset($argv[1]) && $argv[1] === '--fixtures') {
    echo json_encode(['inline' => $used_record['code']], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
} else {
    printf("PHP attribute contracts passed: %d cases, %d assertions.\n", count($cases), $GLOBALS['test_assertions']);
}
