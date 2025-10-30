lucide.createIcons();

let currentTheme = localStorage.getItem('theme') ?? 'light';


const options = document.querySelectorAll('.theme-option');
// options.forEach(o => {
//   o.addEventListener('click', e => {
//     currentTheme = e.currentTarget.dataset.name;
//     // console.log(currentTheme);
//     themeSwitcher(currentTheme);
//   });
// });

function updateThemeIcon(theme) {
  const themeIcon = document.getElementById('theme-icon');
  if (!themeIcon) return;

  let iconName = 'sun';
  if (theme === 'dark') {
    iconName = 'moon';
  } else if (theme === 'system') {
    iconName = 'laptop';
  }

  themeIcon.setAttribute('data-lucide', iconName);
  lucide.createIcons();
}

const systemThemeChangeHandler = e => {
  document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
};


options.forEach(o => o.addEventListener('click', e => themeSwitcher(e)))

function themeSwitcher(e) {
  // const theme = e.currentTarget.dataset.name;
  const theme = e?.currentTarget?.dataset?.name || e;

  window.matchMedia('(prefers-color-scheme: dark)').removeEventListener('change', systemThemeChangeHandler);

  if (theme === 'system') {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');

    window.matchMedia('prefers-color-scheme: dark').addEventListener('change', systemThemeChangeHandler);
  } else {
    document.documentElement.setAttribute('data-theme', theme);
  }
  localStorage.setItem('theme', theme);
  updateThemeIcon(theme);
}

themeSwitcher(currentTheme);

/* Sidebar functionality */
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const toggleSidebarButton = document.getElementById('toggle-sidebar');
  let overlay = null;

  // Function to create and show overlay
  function showOverlay() {
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.id = 'sidebar-overlay';
      overlay.className = 'fixed inset-0 bg-base-300/50 z-40 md:hidden'; // Tailwind classes for overlay
      document.body.appendChild(overlay);
      overlay.addEventListener('click', hideMobileSidebar); // Close sidebar when overlay is clicked
    }
    overlay.classList.remove('hidden');
  }

  // Function to hide and remove overlay
  function hideOverlay() {
    if (overlay) {
      overlay.classList.add('hidden');
    }
  }

  // Function to show sidebar (mobile)
  function openMobileSidebar() {
    sidebar.classList.remove('-translate-x-full');
    sidebar.classList.add('translate-x-0');
    showOverlay();
  }

  // Function to hide sidebar (mobile)
  function hideMobileSidebar() {
    sidebar.classList.remove('translate-x-0');
    sidebar.classList.add('-translate-x-full');
    hideOverlay();
  }

  // Main sidebar toggle logic
  if (sidebar && toggleSidebarButton) {
    // Initial state setup for desktop/tablet
    if (window.innerWidth >= 768) { // md breakpoint
      if (localStorage.getItem('sidebar') === 'collapsed') {
        sidebar.classList.add('w-16', 'collapsed');
        sidebar.classList.remove('w-64', '-translate-x-full', 'translate-x-0');
      } else {
        sidebar.classList.add('w-64');
        sidebar.classList.remove('w-16', 'collapsed', '-translate-x-full', 'translate-x-0');
      }
    } else { // Mobile view initial state
      sidebar.classList.add('-translate-x-full'); // Ensure it's hidden by default on mobile
      sidebar.classList.remove('w-64', 'w-16', 'collapsed', 'translate-x-0');
    }

    toggleSidebarButton.addEventListener('click', () => {
      if (window.innerWidth < 768) { // Mobile view
        if (sidebar.classList.contains('translate-x-0')) {
          hideMobileSidebar();
        } else {
          openMobileSidebar();
        }
      } else { // Desktop/Tablet view
        const isCollapsed = sidebar.classList.toggle('collapsed');
        sidebar.classList.toggle('w-64', !isCollapsed);
        sidebar.classList.toggle('w-16', isCollapsed);
        localStorage.setItem('sidebar', isCollapsed ? 'collapsed' : 'expanded');
      }
    });

    // Handle screen resize
    window.addEventListener('resize', () => {
      if (overlay && !overlay.classList.contains('hidden') && window.innerWidth >= 768) {
        hideMobileSidebar(); // Hide mobile sidebar and overlay if resizing to desktop
      }

      // Reapply desktop/tablet state if resizing to desktop
      if (window.innerWidth >= 768) {
        if (localStorage.getItem('sidebar') === 'collapsed') {
          sidebar.classList.add('w-16', 'collapsed');
          sidebar.classList.remove('w-64', '-translate-x-full', 'translate-x-0');
        } else {
          sidebar.classList.add('w-64');
          sidebar.classList.remove('w-16', 'collapsed', '-translate-x-full', 'translate-x-0');
        }
      } else { // Ensure mobile state when resizing to mobile
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('w-64', 'w-16', 'collapsed', 'translate-x-0');
        hideOverlay(); // Ensure overlay is hidden on mobile if not explicitly open
      }
    });
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

  // Service Worker Registration
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/service-worker.js')
        .then(registration => {
          console.log('Service Worker registered! Scope:', registration.scope);
        })
        .catch(err => {
          console.log('Service Worker registration failed:', err);
        });
    });
  }

  // PWA Install Banner Logic
  const pwaInstallBanner = document.getElementById('pwa-install-banner');
  const installButton = pwaInstallBanner ? pwaInstallBanner.querySelector('button') : null;
  let deferredPrompt;

  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    if (pwaInstallBanner) {
      pwaInstallBanner.style.display = 'flex'; // Show the banner
    }
  });

  if (installButton) {
    installButton.addEventListener('click', () => {
      if (pwaInstallBanner) {
        pwaInstallBanner.style.display = 'none'; // Hide the banner
      }
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
          if (choiceResult.outcome === 'accepted') {
            console.log('User accepted the A2HS prompt');
          } else {
            console.log('User dismissed the A2HS prompt');
          }
          deferredPrompt = null;
        });
      }
    });
  }

  window.addEventListener('appinstalled', () => {
    if (pwaInstallBanner) {
      pwaInstallBanner.style.display = 'none'; // Hide the banner once installed
    }
  });

  // Check if already installed (for subsequent visits)
  if (pwaInstallBanner && (navigator.standalone || window.matchMedia('(display-mode: standalone)').matches)) {
    pwaInstallBanner.style.display = 'none';
  }
});