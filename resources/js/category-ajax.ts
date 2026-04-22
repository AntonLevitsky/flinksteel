document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('product-grid-container');
    if (!container) return;

    // Helper to fetch partial and swap content
    async function loadProducts(url: string, pushState = true) {
        // Show loading overlay
        container!.style.opacity = '0.5';
        container!.style.pointerEvents = 'none';

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            container!.innerHTML = html;

            // Re-attach event listeners on new DOM
            attachGridListeners();

            // Update browser URL
            if (pushState) {
                // Build the display URL (the non-partial version) for the address bar
                const displayUrl = url.replace(/\/kategorie\/([^\/\?]+)\/products/, '/kategorie/$1');
                history.pushState({ url }, '', displayUrl);
            }
        } catch (error) {
            console.error('Filter error:', error);
            // Fallback to full page load
            window.location.href = url;
        } finally {
            container!.style.opacity = '1';
            container!.style.pointerEvents = 'auto';
        }
    }

    function attachGridListeners() {
        // Sort dropdown
        const sortSelect = container!.querySelector('[data-ajax-sort]') as HTMLSelectElement;
        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                const url = new URL(window.location.href);
                url.searchParams.set('sort', sortSelect.value);
                // Convert to partial endpoint
                const partialUrl = url.toString().replace(/\/kategorie\/([^\/\?]+)/, '/kategorie/$1/products');
                loadProducts(partialUrl);
            });
        }

        // Pagination links
        container!.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const href = (link as HTMLAnchorElement).href;
                // Convert to partial endpoint
                const partialUrl = href.replace(/\/kategorie\/([^\/\?]+)/, '/kategorie/$1/products');
                loadProducts(partialUrl);
                // Scroll to top of grid
                container!.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    // Listen for filter-apply from CategoryFilter React island
    window.addEventListener('filter-apply', ((e: CustomEvent) => {
        // Convert the full-page URL to the partial endpoint
        const url = e.detail.url.replace(/\/kategorie\/([^\/\?]+)/, '/kategorie/$1/products');
        loadProducts(url);
    }) as EventListener);

    // Handle browser back/forward
    window.addEventListener('popstate', (e) => {
        if (e.state?.url) {
            loadProducts(e.state.url, false);
        }
    });

    // Initial listener attachment
    attachGridListeners();

    // Store initial state — build the partial URL for potential back/forward navigation
    const initialPartialUrl = window.location.href.replace(/\/kategorie\/([^\/\?]+)/, '/kategorie/$1/products');
    history.replaceState({ url: initialPartialUrl }, '', window.location.href);
});
