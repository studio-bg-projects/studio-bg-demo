/**
 Password toggle
 **/
import GLightbox from 'glightbox';

window.addEventListener('DOMContentLoaded', () => {
  const passwords = document.querySelectorAll('[data-app-password]');

  if (!passwords) {
    return;
  }

  passwords.forEach(password => {
    const passwordInput = password.querySelector('[data-app-password-input]');
    const passwordToggler = password.querySelector('[data-app-password-toggle]');

    passwordToggler.addEventListener('click', () => {
      if (passwordInput.type === 'password') {
        passwordInput.setAttribute('type', 'text');
        passwordToggler.classList.add('show-password');
      } else {
        passwordInput.setAttribute('type', 'password');
        passwordToggler.classList.remove('show-password');
      }
    });
  });
});

/**
 Tooltips
 **/
window.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    if (el.hasAttribute('data-datasource-tooltip')) {
      return;
    }

    new bootstrap.Tooltip(el);
  });
});

/**
 Toast
 **/
// Do not uncomment, it adds error "Bootstrap doesn't allow more than one instance per element. Bound instance: bs.collapse." when close accordion
// window.addEventListener('DOMContentLoaded', () => {
//   $(document).on('click', '[data-bs-target]', function () {
//     const $el = $($(this).data('bs-target'));
//     const toastBootstrap = bootstrap.Toast.getOrCreateInstance($el);
//     toastBootstrap.show();
//   });
// });

window.appToast = (settings) => {
  settings = {
    body: '',
    type: 'info',
    icon: 'fa-circle-info',
    ...settings
  };

  if (!window.appToast.wrapper) {
    window.appToast.wrapper = $(`<div class="toast-container position-fixed start-50 top-0 translate-middle-x p-3"></div>`);
    window.appToast.wrapper.appendTo('body');
  }

  const toastNode = $(`
    <div class="toast fade show mb-3 border-${settings.type}" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex align-items-center">
        <i class="fa-regular ${settings.icon} text-${settings.type} fs-base me-2"></i>

        <div class="toast-body me-2">
          ${settings.body}
        </div>

        <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  `);
  window.appToast.wrapper.append(toastNode);

  const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastNode);
  toastBootstrap.show();
};


/**
 Success & Error Toasts
 **/
window.addEventListener('DOMContentLoaded', () => {
  const successToastEl = document.querySelector('[data-app-success-toast]');
  const errorToastEl = document.querySelector('[data-app-error-toast]');

  let successToast;
  if (successToastEl) {
    successToast = new bootstrap.Toast(successToastEl);
    successToast.show();
  }

  let errorToast;
  if (errorToastEl) {
    errorToast = new bootstrap.Toast(errorToastEl);
    errorToast.show();
  }

  setTimeout(function () {
    if (successToast) {
      successToast.hide();
    }
    if (errorToast) {
      errorToast.hide();
    }
  }, 5000);
});

/**
 Sidebar toggle
 **/
window.addEventListener('DOMContentLoaded', () => {
  const htmlElement = document.documentElement;
  const className = 'navbar-vertical-collapsed';

  const setCollapsedState = (isCollapsed) => {
    localStorage.setItem('sidebarCollapsed', isCollapsed ? 'true' : 'false');
  };

  const initializeCollapsedState = () => {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
      htmlElement.classList.add(className);
    } else {
      htmlElement.classList.remove(className);
    }
  };

  const btn = document.querySelector('[data-app-sidebar-toggle]');

  if (btn) {
    btn.addEventListener('click', function () {
      const isCollapsed = htmlElement.classList.toggle(className);
      setCollapsedState(isCollapsed);
    });
  }

  initializeCollapsedState();
});

/**
 Simple search
 **/
window.addEventListener('DOMContentLoaded', () => {
  $('[data-simple-search]').on('input', function () {
    const searchText = $(this).val().toLowerCase();
    const $searchItems = $($(this).data('simple-search'));

    $searchItems.each(function () {
      const $li = $(this);
      const liText = $li.text().toLowerCase();

      if (liText.includes(searchText)) {
        $li.show();
      } else {
        $li.hide();
      }
    });
  });
});

/**
 Theme toggle
 **/
window.addEventListener('DOMContentLoaded', () => {
  const getStoredTheme = () => localStorage.getItem('theme');
  const setStoredTheme = theme => localStorage.setItem('theme', theme);

  const getPreferredTheme = () => {
    const storedTheme = getStoredTheme();
    if (storedTheme) {
      return storedTheme;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  };

  const setTheme = theme => {
    if (theme === 'auto') {
      document.documentElement.setAttribute('data-bs-theme', window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    } else {
      document.documentElement.setAttribute('data-bs-theme', theme);
    }
  };

  const updateIcon = theme => {
    const activeThemeIcon = document.querySelector('.theme-icon-active');
    if (activeThemeIcon) {
      activeThemeIcon.classList.remove('fa-lightbulb', 'fa-sunglasses', 'fa-adjust');

      if (theme === 'dark') {
        activeThemeIcon.classList.add('fa-sunglasses');
      } else if (theme === 'light') {
        activeThemeIcon.classList.add('fa-lightbulb');
      } else if (theme === 'auto') {
        activeThemeIcon.classList.add('fa-adjust');
      }
    }
  };

  const showActiveTheme = (theme) => {
    const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`);

    document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
      element.classList.remove('active');
      element.setAttribute('aria-pressed', 'false');
    });

    if (btnToActive) {
      btnToActive.classList.add('active');
      btnToActive.setAttribute('aria-pressed', 'true');
    }

    updateIcon(theme);
  };

  // Apply preferred theme on load
  setTheme(getPreferredTheme());
  showActiveTheme(getPreferredTheme());

  // Listen for changes in system theme (auto mode)
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const storedTheme = getStoredTheme();
    if (storedTheme === 'auto') {
      setTheme(getPreferredTheme());
      showActiveTheme(getPreferredTheme());
    }
  });

  // Handle theme change on button click
  document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
    toggle.addEventListener('click', () => {
      const theme = toggle.getAttribute('data-bs-theme-value');
      setStoredTheme(theme);
      setTheme(theme);
      showActiveTheme(theme, true);
    });
  });
});

/**
 Disable buttons in forms
 **/
window.addEventListener('DOMContentLoaded', () => {
  const forms = document.querySelectorAll('form[data-disable-on-submit]');

  forms.forEach((form) => {
    form.addEventListener('submit', (event) => {
      const buttons = form.querySelectorAll('button, input[type="submit"]');

      buttons.forEach((button) => {
        button.disabled = true;
      });

      if (!form.checkValidity()) {
        event.preventDefault();
      }

      setTimeout(() => {
        if (event.defaultPrevented) {
          buttons.forEach((button) => {
            button.disabled = false;
          });
        }
      });
    });
  });
});

/**
 Image preview - GLightbox
 **/
window.addEventListener('DOMContentLoaded', () => {
  GLightbox({
    selector: '[data-gallery]'
  });
});
