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