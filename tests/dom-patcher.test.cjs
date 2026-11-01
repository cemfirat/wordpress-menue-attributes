'use strict';

const assert = require('node:assert/strict');
const { spawnSync } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

// Run the real PHP callbacks once. WP boundaries are declared test doubles
// in attribute-contract.php; the DOM objects here are also explicit doubles.
const exported = spawnSync(process.env.PHP_BIN || 'php', [path.join(__dirname, 'attribute-contract.php'), '--fixtures'], { encoding: 'utf8' });
assert.equal(exported.status, 0, (exported.error && exported.error.message) || exported.stderr || exported.stdout);
const fixture = JSON.parse(exported.stdout);
fixture.cases = JSON.parse(fs.readFileSync(path.join(__dirname, 'attribute-cases.json'), 'utf8'));
const script = fs.readFileSync(path.join(__dirname, '../public/dom-patcher.js'), 'utf8');

function anchor(url, title) {
    const attributes = { href: url };
    const classes = new Set();
    return {
        attributes,
        textContent: title,
        classList: { add(value) { classes.add(value); } },
        classes,
        get id() { return attributes.id || ''; },
        set id(value) { attributes.id = value; },
        getAttribute(name) { return attributes[name] || ''; },
        setAttribute(name, value) {
            assert.equal(typeof value, 'string');
            attributes[name] = value;
        },
    };
}

function run(items, anchors, loading = false, inline = null) {
    const listeners = {};
    let observer;
    const document = {
        readyState: loading ? 'loading' : 'complete',
        documentElement: {},
        querySelectorAll(selector) {
            assert.equal(selector, 'a[href]');
            return anchors;
        },
        addEventListener(name, callback) { listeners[name] = callback; },
    };
    function MutationObserver(callback) {
        observer = callback;
        this.observe = function () {};
    }
    const context = { window: { CF_MEF_DATA: { items }, location: { origin: 'https://example.invalid' } }, document, URL, MutationObserver };
    if (inline !== null) vm.runInNewContext(inline, context, { timeout: 1000 });
    vm.runInNewContext(script, context, { timeout: 1000 });
    return { listeners, mutate(records) { observer(records); }, data: context.window.CF_MEF_DATA };
}

test('PHP-normalized pairs reach the DOM unchanged for every attribute fixture', () => {
    const anchors = fixture.cases.map((item, index) => anchor('https://example.invalid/item-' + (index + 100), item.name));
    const unrelated = anchor('https://example.invalid/unrelated', 'unrelated');
    const result = run([], [...anchors, unrelated], false, fixture.inline);
    fixture.cases.forEach((item, index) => {
        const expected = { href: anchors[index].attributes.href, id: 'item-' + (index + 100), ...item.expected };
        assert.deepEqual(anchors[index].attributes, expected, item.name);
        assert.deepEqual(JSON.parse(JSON.stringify(result.data.items[index].custom_attr)), item.expected, item.name);
    });
    assert.deepEqual(unrelated.attributes, { href: 'https://example.invalid/unrelated' });
});

test('raw legacy text and malformed attribute containers are not interpreted by JS', () => {
    for (const value of ['onclick="fixture" role="button"', ['onclick', 'fixture'], null, 12, true]) {
        const a = anchor('/target', 'Target');
        run([{ url: '/target', custom_attr: value }], [a]);
        assert.deepEqual(a.attributes, { href: '/target' });
    }
});

test('DOM writes enforce allowed names, string values and prepared target/rel values', () => {
    const a = anchor('/target', 'Target');
    const attributes = Object.assign(Object.create({ 'data-inherited': 'no' }), {
        role: 'button',
        'data-ok': 'text',
        'data-bad key': 'no',
        'data-object': { toString() { throw new Error('Do not stringify arbitrary values.'); } },
        onclick: 'fixture',
        style: 'color:red',
        href: '/other',
        target: 'named-frame',
        rel: 'NOFOLLOW invalid_1',
    });
    run([{ url: '/target', custom_attr: attributes }], [a]);
    assert.deepEqual(a.attributes, { href: '/target', role: 'button', 'data-ok': 'text' });
});

test('allowed empty values, IDs and class additions retain their existing behavior', () => {
    const a = anchor('/target', 'Target');
    run([{ url: '/target', custom_id: 'menu-id', custom_class: 'first second', custom_attr: { download: '', 'uk-toggle': '', rel: '', target: '_blank' } }], [a]);
    assert.deepEqual(a.attributes, { href: '/target', id: 'menu-id', download: '', 'uk-toggle': '', rel: '', target: '_blank' });
    assert.deepEqual([...a.classes], ['first', 'second']);
});

test('DOMContentLoaded and added-node fallback apply the same prepared pairs', () => {
    const anchors = [];
    const result = run([{ url: '/target', custom_attr: { role: 'button', target: '_self', rel: 'nofollow noopener' } }], anchors, true);
    const a = anchor('/target', 'Target');
    anchors.push(a);
    assert.deepEqual(a.attributes, { href: '/target' });
    result.listeners.DOMContentLoaded();
    assert.equal(a.attributes.target, '_self');
    const b = anchor('/target', 'Target');
    anchors.push(b);
    result.mutate([{ addedNodes: [b] }]);
    assert.deepEqual(b.attributes, a.attributes);
});

test('quoted values and script-end text survive safe JSON transport as data', () => {
    assert.equal(fixture.inline.includes('<'), false);
    assert.equal(fixture.inline.includes('>'), false);
    assert.equal(fixture.inline.includes('&'), false);
    const result = run([], [], false, fixture.inline);
    const index = fixture.cases.findIndex(item => item.name === 'quotes ampersands and script delimiters stay data');
    assert.equal(result.data.items[index].custom_attr['data-note'], 'A "quote" & </script><script>plain text</script>');
});
