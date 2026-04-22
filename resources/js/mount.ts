import React from 'react';
import { createRoot } from 'react-dom/client';

export function mountIsland(id: string, Component: React.ComponentType<any>) {
    const el = document.getElementById(id);
    if (!el) return;
    const props = JSON.parse(el.dataset.props || '{}');
    createRoot(el).render(React.createElement(Component, props));
}
