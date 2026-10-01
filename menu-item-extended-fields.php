<?php
/*
Plugin Name: Menüfeld Erweiterung (ID, Class & Attribute) – 1.5.5
Description: Fügt ID-, Class- und Attribute-Felder zu Menüeinträgen hinzu. Gehärtete Validierung, JS-Fallback und sichere Bereinigung via uninstall.php. Behebt fehlende Datenübergabe an das JS (CF_MEF_DATA).
Version: 1.5.5
Author: Cem Firat
*/

namespace CF\MEF;

if (!defined('ABSPATH')) exit;

/** ================= Sanitizer ================= */
function sanitize_html_id($id)
{
    $id = trim((string)$id);
    if ($id === '') return '';
    if (!preg_match('/^[A-Za-z][A-Za-z0-9\-\_\:\.]*$/', $id)) {
        $id = preg_replace('/[^A-Za-z0-9\-\_\:\.]/', '', $id);
        if ($id === '' || !preg_match('/^[A-Za-z]/', $id)) $id = 'id_' . $id;
    }
    return $id;
}
function sanitize_class_list($classes)
{
    $classes = trim((string)$classes);
    if ($classes === '') return '';
    $parts = preg_split('/\s+/', $classes);
    $out = [];
    foreach ($parts as $c) {
        $c = preg_replace('/[^A-Za-z0-9\-\_]/', '', $c);
        if ($c !== '') $out[] = $c;
    }
    return implode(' ', array_unique($out));
}
function parse_and_sanitize_attributes($attr_string)
{
    $attr_string = trim((string)$attr_string);
    if ($attr_string === '') return [];
    $allowed = ['role' => true, 'rel' => true, 'target' => true, 'download' => true];
    $allowed_prefixes = ['aria-', 'data-', 'uk-'];
    $result = [];
    $re = '/([A-Za-z0-9\-\_]+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'))?/';

    if (preg_match_all($re, $attr_string, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $key = strtolower($m[1]);
            $val = isset($m[2]) && $m[2] !== '' ? $m[2] : (isset($m[3]) && $m[3] !== '' ? $m[3] : '');
            $allowed_key = isset($allowed[$key]);
            if (!$allowed_key) {
                foreach ($allowed_prefixes as $p) {
                    if (strpos($key, $p) === 0) {
                        $allowed_key = true;
                        break;
                    }
                }
            }
            if (!$allowed_key) continue;
            if ($key === 'target') {
                $valid_targets = ['_self', '_blank', '_parent', '_top'];
                if (!in_array($val, $valid_targets, true)) $val = '_self';
            }
            if ($key === 'rel') {
                $tokens = preg_split('/\s+/', $val);
                $safe = [];
                foreach ($tokens as $t) {
                    $t = preg_replace('/[^a-z\-]/', '', strtolower($t));
                    if ($t !== '') $safe[] = $t;
                }
                $val = implode(' ', array_unique($safe));
            }
            $result[$key] = $val;
        }
    }
    return $result;
}

/** ================= Backend-Felder ================= */
add_action('wp_nav_menu_item_custom_fields', function ($item_id, $item) {
    $menu_item_id    = get_post_meta($item_id, '_menu_item_custom_id', true);
    $menu_item_class = get_post_meta($item_id, '_menu_item_custom_class', true);
    $menu_item_attr  = get_post_meta($item_id, '_menu_item_custom_attr', true);
?>
    <hr style="margin:12px 0;border-top:1px solid #ddd;">
    <h4 style="margin-top:0;">Erweiterte Attribute</h4>
    <p class="description description-wide">
        <label for="edit-menu-item-custom-id-<?php echo esc_attr($item_id); ?>">ID<br>
            <input type="text" id="edit-menu-item-custom-id-<?php echo esc_attr($item_id); ?>"
                class="widefat code edit-menu-item-custom-id"
                name="menu-item-custom-id[<?php echo esc_attr($item_id); ?>]"
                value="<?php echo esc_attr($menu_item_id); ?>" maxlength="128">
        </label>
    </p>
    <p class="description description-wide">
        <label for="edit-menu-item-custom-class-<?php echo esc_attr($item_id); ?>">Class<br>
            <input type="text" id="edit-menu-item-custom-class-<?php echo esc_attr($item_id); ?>"
                class="widefat code edit-menu-item-custom-class"
                name="menu-item-custom-class[<?php echo esc_attr($item_id); ?>]"
                value="<?php echo esc_attr($menu_item_class); ?>" maxlength="512">
            <span class="description">Beispiel: <code>el-link uk-transition-slow neu</code></span>
        </label>
    </p>
    <p class="description description-wide">
        <label for="edit-menu-item-custom-attr-<?php echo esc_attr($item_id); ?>">Zusätzliche Attribute<br>
            <input type="text" id="edit-menu-item-custom-attr-<?php echo esc_attr($item_id); ?>"
                class="widefat code edit-menu-item-custom-attr"
                name="menu-item-custom-attr[<?php echo esc_attr($item_id); ?>]"
                value="<?php echo esc_attr($menu_item_attr); ?>" maxlength="1024">
            <span class="description">Erlaubt: <code>aria-*</code>, <code>data-*</code>, <code>uk-*</code>, <code>role</code>, <code>rel</code>, <code>target</code>, <code>download</code></span>
        </label>
    </p>
<?php
}, 10, 2);

/** ================= Speichern ================= */
add_action('wp_update_nav_menu_item', function ($menu_id, $menu_item_db_id) {
    $id    = isset($_POST['menu-item-custom-id'][$menu_item_db_id])    ? sanitize_html_id($_POST['menu-item-custom-id'][$menu_item_db_id]) : '';
    $class = isset($_POST['menu-item-custom-class'][$menu_item_db_id]) ? sanitize_class_list($_POST['menu-item-custom-class'][$menu_item_db_id]) : '';
    $attr  = isset($_POST['menu-item-custom-attr'][$menu_item_db_id])  ? $_POST['menu-item-custom-attr'][$menu_item_db_id] : '';

    if ($id !== '')    update_post_meta($menu_item_db_id, '_menu_item_custom_id', $id);
    else delete_post_meta($menu_item_db_id, '_menu_item_custom_id');
    if ($class !== '') update_post_meta($menu_item_db_id, '_menu_item_custom_class', $class);
    else delete_post_meta($menu_item_db_id, '_menu_item_custom_class');
    if ($attr !== '')  update_post_meta($menu_item_db_id, '_menu_item_custom_attr', $attr);
    else delete_post_meta($menu_item_db_id, '_menu_item_custom_attr');
}, 10, 2);

/** ================= Frontend-Filter (PHP) ================= */
add_filter('nav_menu_link_attributes', function ($atts, $item, $args, $depth) {
    $id    = get_post_meta($item->ID, '_menu_item_custom_id', true);
    $class = get_post_meta($item->ID, '_menu_item_custom_class', true);
    $attr  = get_post_meta($item->ID, '_menu_item_custom_attr', true);
    if ($id) {
        $atts['id'] = esc_attr($id);
    }
    if ($class) {
        $atts['class'] = trim(($atts['class'] ?? '') . ' ' . $class);
    }
    if ($attr) {
        $pairs = parse_and_sanitize_attributes($attr);
        foreach ($pairs as $k => $v) {
            $atts[$k] = $v;
        }
    }
    return $atts;
}, 10, 4);

/** ================= JS-Fallback: Daten sammeln + Script laden ================= */
$GLOBALS['cf_mef_used_menu_item_ids'] = [];

// Sammle möglichst präzise die genutzten Items
add_filter('wp_nav_menu_objects', function ($sorted_menu_items, $args) {
    foreach ($sorted_menu_items as $it) {
        $GLOBALS['cf_mef_used_menu_item_ids'][$it->ID] = true;
    }
    return $sorted_menu_items;
}, 10, 2);
add_filter('wp_get_nav_menu_items', function ($items, $menu, $args) {
    foreach ((array)$items as $it) {
        if (isset($it->ID)) $GLOBALS['cf_mef_used_menu_item_ids'][$it->ID] = true;
    }
    return $items;
}, 10, 3);

add_action('wp_enqueue_scripts', function () {
    // JS registrieren + enqueuen
    wp_register_script('cf-mef-dom', plugins_url('public/dom-patcher.js', __FILE__), [], '1.4', true);
    wp_enqueue_script('cf-mef-dom');

    // Map zusammenstellen
    $map = [];
    $ids = array_keys($GLOBALS['cf_mef_used_menu_item_ids'] ?? []);

    if (!empty($ids)) {
        foreach ($ids as $id) {
            $title = get_the_title($id);
            $url   = get_post_meta($id, '_menu_item_url', true);
            $cid   = get_post_meta($id, '_menu_item_custom_id', true);
            $cls   = get_post_meta($id, '_menu_item_custom_class', true);
            $att   = get_post_meta($id, '_menu_item_custom_attr', true);
            if ($cid || $cls || $att) {
                $map[] = [
                    'menu_item_id' => intval($id),
                    'title'        => $title,
                    'url'          => $url,
                    'hash'         => parse_url($url, PHP_URL_FRAGMENT),
                    'custom_id'    => $cid,
                    'custom_class' => $cls,
                    'custom_attr'  => $att,
                ];
            }
        }
    } else {
        // Fallback: alle Menüs
        $menus = wp_get_nav_menus();
        foreach ($menus as $menu_obj) {
            $items = wp_get_nav_menu_items($menu_obj->term_id);
            if (!$items) continue;
            foreach ($items as $it) {
                $cid = get_post_meta($it->ID, '_menu_item_custom_id', true);
                $cls = get_post_meta($it->ID, '_menu_item_custom_class', true);
                $att = get_post_meta($it->ID, '_menu_item_custom_attr', true);
                if ($cid || $cls || $att) {
                    $map[] = [
                        'menu_item_id' => intval($it->ID),
                        'title'        => $it->title,
                        'url'          => $it->url,
                        'hash'         => parse_url($it->url, PHP_URL_FRAGMENT),
                        'custom_id'    => $cid,
                        'custom_class' => $cls,
                        'custom_attr'  => $att,
                    ];
                }
            }
        }
    }

    // JSON inline bereitstellen (CSP-freundlich, keine lokale globale Var nötig)
    $json = wp_json_encode(['items' => $map], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    wp_add_inline_script('cf-mef-dom', 'window.CF_MEF_DATA = ' . $json . ';', 'before');
}, 20);
