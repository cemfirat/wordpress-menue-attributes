(function(){
    var data = (typeof window.CF_MEF_DATA !== 'undefined' && window.CF_MEF_DATA) ? window.CF_MEF_DATA : {items:[]};
    var items = Array.isArray(data.items) ? data.items : [];

    function applyAttributes(el, attributes) {
        // PHP supplies the same normalized pairs as nav_menu_link_attributes.
        // Do not reinterpret saved text with a second attribute parser.
        if (!attributes || typeof attributes !== 'object' || Array.isArray(attributes)) return;
        Object.keys(attributes).forEach(function(key) {
            var val = attributes[key];
            if (!/^(?:role|rel|target|download|(?:aria-|data-|uk-)[a-z0-9_-]*)$/.test(key) || typeof val !== 'string') return;
            if (key === 'target' && ['_self', '_blank', '_parent', '_top'].indexOf(val) === -1) return;
            if (key === 'rel' && !/^(?:[a-z-]+(?: [a-z-]+)*)?$/.test(val)) return;
            el.setAttribute(key, val);
        });
    }
    function matchesAnchor(a, item) {
        var href = a.getAttribute('href') || '';
        if (!href) return false;
        var txt = (a.textContent || '').trim();
        try {
            var aUrl = new URL(href, window.location.origin);
            var iUrl = new URL(item.url || '', window.location.origin);
            if (iUrl.href && aUrl.href === iUrl.href) return true;
        } catch(e){}
        if (item.hash && ('#' + item.hash) === href) return true;
        if (item.title && txt === item.title) return true;
        return false;
    }
    function applyToAnchors() {
        if (!items.length) return;
        var anchors = document.querySelectorAll('a[href]');
        items.forEach(function(item){
            anchors.forEach(function(a){
                if (!matchesAnchor(a, item)) return;
                if (item.custom_id) a.id = item.custom_id;
                if (item.custom_class) item.custom_class.split(/\s+/).filter(Boolean).forEach(function(c){ a.classList.add(c); });
                applyAttributes(a, item.custom_attr);
            });
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', applyToAnchors);
    else applyToAnchors();
    try {
        var mo = new MutationObserver(function(muts){
            muts.forEach(function(m){
                if (m.addedNodes && m.addedNodes.length) applyToAnchors();
            });
        });
        mo.observe(document.documentElement, {subtree:true, childList:true});
    }catch(e){}
})();
