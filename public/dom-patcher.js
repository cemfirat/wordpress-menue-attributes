(function(){
    var data = (typeof window.CF_MEF_DATA !== 'undefined' && window.CF_MEF_DATA) ? window.CF_MEF_DATA : {items:[]};
    var items = Array.isArray(data.items) ? data.items : [];

    function setAttr(el, key, val) {
        if (val === true || val === '') el.setAttribute(key, '');
        else el.setAttribute(key, String(val));
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
                if (item.custom_attr) {
                    var re = /([A-Za-z0-9\-\_]+)(?:="([^"]*)"|'([^']*)')?/g, m;
                    while ((m = re.exec(item.custom_attr)) !== null) {
                        var key = m[1], val = m[2] || m[3] || '';
                        setAttr(a, key, val);
                    }
                }
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