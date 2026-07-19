/**
 * Turns any <select class="select2"> into a search-and-select dropdown.
 * Just add the "select2" class to a <select> — no per-page JS needed.
 * Also watches the DOM so selects added/shown later (AJAX, toggled panels) get initialized too.
 */
(function ($) {
    if (!$ || !$.fn.select2) return;

    var dir = document.documentElement.getAttribute('dir') || 'ltr';

    function initSelect2(scope) {
        $(scope)
            .find('select.select2')
            .addBack('select.select2')
            .each(function () {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) return;

                $el.select2({
                    dir: dir,
                    width: '100%',
                    allowClear: $el.data('allow-clear') === true,
                    placeholder: $el.data('placeholder') || $el.find('option[value=""]').first().text() || null,
                });

                // Select2's own change trigger doesn't fire native events, so plain
                // addEventListener('change', ...) / onchange="" handlers never see it.
                // Re-dispatch a real native "change" event so existing page JS keeps working.
                $el.on('select2:select select2:unselect select2:clear', function () {
                    this.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });
    }

    $(function () {
        initSelect2(document);

        // Re-scan for selects that get added or shown after initial load
        var observer = new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                if (mutations[i].addedNodes.length) {
                    initSelect2(document);
                    break;
                }
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });

    // Exposed for pages that build/replace <select> options dynamically via JS
    window.initSelect2 = initSelect2;
})(window.jQuery);
