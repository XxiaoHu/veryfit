(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var menuToggle = document.querySelector('.menu-toggle');
        var langToggle = document.querySelector('.lang-toggle');
        var menuDrawer = document.getElementById('mobile-menu-drawer');
        var langDrawer = document.getElementById('lang-drawer');
        var overlay = document.getElementById('drawer-overlay');
        var closeButtons = document.querySelectorAll('.drawer-close');

        function openDrawer(drawer) {
            if (!drawer) return;
            drawer.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeAllDrawers() {
            if (menuDrawer) menuDrawer.classList.remove('active');
            if (langDrawer) langDrawer.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (menuToggle && menuDrawer) {
            menuToggle.addEventListener('click', function() {
                var isOpen = menuDrawer.classList.contains('active');
                if (isOpen) {
                    closeAllDrawers();
                    menuToggle.setAttribute('aria-expanded', 'false');
                } else {
                    if (langDrawer) langDrawer.classList.remove('active');
                    openDrawer(menuDrawer);
                    menuToggle.setAttribute('aria-expanded', 'true');
                }
            });
        }

        if (langToggle && langDrawer) {
            langToggle.addEventListener('click', function() {
                var isOpen = langDrawer.classList.contains('active');
                if (isOpen) {
                    closeAllDrawers();
                    langToggle.setAttribute('aria-expanded', 'false');
                } else {
                    if (menuDrawer) menuDrawer.classList.remove('active');
                    openDrawer(langDrawer);
                    langToggle.setAttribute('aria-expanded', 'true');
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                closeAllDrawers();
                if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
                if (langToggle) langToggle.setAttribute('aria-expanded', 'false');
            });
        }

        closeButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                closeAllDrawers();
                if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
                if (langToggle) langToggle.setAttribute('aria-expanded', 'false');
            });
        });

        var langDropdownToggle = document.querySelector('.lang-dropdown-toggle');
        if (langDropdownToggle) {
            langDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var langDropdown = this.closest('.lang-dropdown');
                if (!langDropdown) return;

                if (langDropdown.classList.contains('active')) {
                    langDropdown.classList.remove('active');
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    langDropdown.classList.add('active');
                    this.setAttribute('aria-expanded', 'true');
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.lang-dropdown')) {
                var allDropdowns = document.querySelectorAll('.lang-dropdown.active');
                allDropdowns.forEach(function(d) {
                    d.classList.remove('active');
                    var t = d.querySelector('.lang-dropdown-toggle');
                    if (t) t.setAttribute('aria-expanded', 'false');
                });
            }
        });

    });
})();
