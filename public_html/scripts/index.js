lucide.createIcons();

let currentTheme = localStorage.getItem('theme') ?? 'light';
themeSwitcher(currentTheme);

const options = document.querySelectorAll('.theme-option');
// options.forEach(o => {
//   o.addEventListener('click', e => {
//     currentTheme = e.currentTarget.dataset.name;
//     // console.log(currentTheme);
//     themeSwitcher(currentTheme);
//   });
// });

options.forEach(o => o.addEventListener('click', e => themeSwitcher(e)))

function themeSwitcher(e) {
  // const theme = e.currentTarget.dataset.name;
  const theme = e?.currentTarget?.dataset?.name || e;
  if (theme === 'system') {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');

    window.matchMedia('prefers-color-scheme: dark').addEventListener('change', e => {
      document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
    });
  } else {
    document.documentElement.setAttribute('data-theme', theme);
  }
  localStorage.setItem('theme', theme);
}

/* Sidebar minimize & maximize functionality */
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const toggleSidebarButton = document.getElementById('toggle-sidebar');

  if (sidebar) { // Add this check
    // Load state from localStorage
    if (localStorage.getItem('sidebar') === 'collapsed') {
      sidebar.classList.remove('w-64');
      sidebar.classList.add('w-16', 'collapsed');
    }

    if (toggleSidebarButton) {
      toggleSidebarButton.addEventListener('click', () => {
        const isCollapsed = sidebar.classList.toggle('collapsed');
        sidebar.classList.toggle('w-64', !isCollapsed);
        sidebar.classList.toggle('w-16', isCollapsed);

        // Save state
        localStorage.setItem('sidebar', isCollapsed ? 'collapsed' : 'expanded');
      });
    }
  }

  // User dropdown logic
  // console.log('hello');
  const btn = document.getElementById('userMenuBtn');
  const template = document.getElementById('userMenuTemplate');
  // console.log(btn, template);
  if (!btn || !template) return;

  // where dropdown will be appended (body)
  const portalRoot = document.body;
  let menu = null;
  let isOpen = false;

  // helper: show (append) the menu
  function openMenu() {
    if (!menu) {
      menu = template.content.querySelector('#userMenu').cloneNode(true);
      portalRoot.appendChild(menu);
      lucide.createIcons(); // <-- add this
      // make visible for measurement/positioning
      menu.style.display = 'block';
      menu.style.visibility = 'hidden';
      menu.style.position = 'fixed';
      menu.style.zIndex = '2000';
      // small padding + rounding already from classes
    }
    positionMenu();
    menu.style.visibility = 'visible';
    btn.setAttribute('aria-expanded', 'true');
    isOpen = true;
  }

  // helper: hide (keep in body but invisible)
  function closeMenu() {
    if (menu) {
      menu.style.visibility = 'hidden';
      // keep in DOM so we can measure next time without re-cloning
    }
    btn.setAttribute('aria-expanded', 'false');
    isOpen = false;
  }

  // compute & set menu position
  function positionMenu() {
    if (!menu) return;
    // temporarily ensure visible for accurate measurement
    const prevVis = menu.style.visibility;
    menu.style.visibility = 'hidden';
    menu.style.display = 'block';

    const btnRect = btn.getBoundingClientRect();
    const menuRect = menu.getBoundingClientRect();
    const sidebar = document.getElementById('sidebar');
    const sidebarRect = sidebar ? sidebar.getBoundingClientRect() : { right: 0, left: 0, top: 0, bottom: 0 };
    const viewportW = window.innerWidth;
    const viewportH = window.innerHeight;

    // is sidebar collapsed? (depends on your implementation: check .collapsed class)
    const collapsed = sidebar && sidebar.classList.contains('collapsed');

    let left, top;

    if (collapsed) {
      // place to the right of the sidebar (vertical align with button center)
      const gap = 12; // px spacing from sidebar edge
      left = Math.min(sidebarRect.right + gap, viewportW - menuRect.width - gap);
      // align top to button top (but ensure it doesn't overflow bottom)
      top = Math.min(Math.max(btnRect.top, 8), viewportH - menuRect.height - gap);
    } else {
      // expanded: position above the button (dropdown-top + dropdown-end behavior)
      const gap = 8;
      top = btnRect.top - menuRect.height - gap;
      // fallback to below if not enough space
      if (top < 8) top = btnRect.bottom + gap;
      // align right edges
      left = btnRect.right - menuRect.width;
      // keep within viewport
      if (left < 8) left = 8;
      if (left + menuRect.width > viewportW - 8) left = viewportW - menuRect.width - 8;
    }

    // apply
    menu.style.left = Math.round(left) + 'px';
    menu.style.top = Math.round(top) + 'px';

    // restore visibility
    menu.style.visibility = prevVis;
  }

  // toggle handler
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    if (isOpen) {
      closeMenu();
      return;
    }
    openMenu();
  });

  // close on outside click
  document.addEventListener('click', (e) => {
    if (!isOpen || !menu) return;
    if (btn.contains(e.target) || menu.contains(e.target)) return;
    closeMenu();
  });

  // reposition on scroll, resize, layout changes
  window.addEventListener('resize', () => { if (isOpen) positionMenu(); });
  window.addEventListener('scroll', () => { if (isOpen) positionMenu(); }, true);

  // If sidebar collapse toggles via some button, listen for mutation changes to reposition accordingly
  // const sidebar = document.getElementById('sidebar');
  if (sidebar) {
    const mo = new MutationObserver(() => { if (isOpen) positionMenu(); });
    mo.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
  }
});