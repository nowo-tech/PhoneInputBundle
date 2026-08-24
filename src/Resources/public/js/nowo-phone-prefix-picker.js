/**
 * CSP-safe country prefix picker for nowo-tech/phone-input-bundle.
 *
 * Enhances [data-controller="phone-prefix-picker"] and [data-nowo-phone-prefix-picker]
 * without requiring Stimulus. Hosts that prefer Stimulus may omit this script and
 * register a controller named "phone-prefix-picker" that implements the same behaviour.
 *
 * Dropdown is portaled to document.body with position:fixed so it is not clipped by
 * overflow:auto ancestors (e.g. dialog bodies).
 */
(function (global) {
  'use strict';

  var ATTR_INIT = 'nowoPhonePickerInit';
  var SELECTOR =
    '[data-controller~="phone-prefix-picker"], [data-nowo-phone-prefix-picker]';

  /**
   * @param {HTMLElement} root
   */
  function createPicker(root) {
    if (root.dataset[ATTR_INIT] === '1') {
      return null;
    }
    root.dataset[ATTR_INIT] = '1';

    var select = root.querySelector('.nowo-phone-input__prefix-select');
    var toggle = root.querySelector('.nowo-phone-input__prefix-toggle');
    var dropdownInRoot = root.querySelector('.nowo-phone-input__prefix-dropdown');
    var menuInRoot = root.querySelector('.nowo-phone-input__prefix-menu');

    if (!select || !toggle || !dropdownInRoot || !menuInRoot) {
      return null;
    }

    select.classList.add('nowo-phone-input__prefix-select--enhanced');

    var highlightedOption = null;
    var placeholder = null;
    var portaledDropdown = null;
    var open = false;
    var flagDisplay = root.dataset.flagDisplay || 'CSS_ICON';
    var prefixDisplay = root.dataset.prefixDisplay || 'FLAG_AND_PREFIX';
    var prefixSearchEnabled = root.dataset.prefixSearch !== '0';

    function getDropdown() {
      return portaledDropdown || root.querySelector('.nowo-phone-input__prefix-dropdown');
    }

    function getSearchInput() {
      var dropdown = getDropdown();
      return dropdown ? dropdown.querySelector('.nowo-phone-input__prefix-search-input') : null;
    }

    function getMenu() {
      var dropdown = getDropdown();
      return (dropdown && dropdown.querySelector('.nowo-phone-input__prefix-menu'))
        || root.querySelector('.nowo-phone-input__prefix-menu');
    }

    function getEmptyState() {
      var dropdown = getDropdown();
      return (dropdown && dropdown.querySelector('.nowo-phone-input__prefix-empty'))
        || root.querySelector('.nowo-phone-input__prefix-empty');
    }

    function getOptions() {
      var scope = getDropdown() || root;
      return scope.querySelectorAll('.nowo-phone-input__prefix-option');
    }

    function renderFlagHtml(iso, emoji) {
      if (flagDisplay === 'NONE') {
        return '';
      }
      if (flagDisplay === 'CSS_ICON' || flagDisplay === 'UX_ICON') {
        return '<span class="nowo-phone-input__flag fi fi-' + iso.toLowerCase() + '" aria-hidden="true"></span>';
      }
      return '<span class="nowo-phone-input__flag nowo-phone-input__flag--emoji" aria-hidden="true">' + emoji + '</span>';
    }

    function normalizeQuery(query) {
      return query.trim().toLowerCase().replace(/\+/g, '');
    }

    function optionMatches(option, query) {
      if (!query) {
        return true;
      }
      var haystack = (option.dataset.search || '').toLowerCase().replace(/\+/g, '');
      var tokens = normalizeQuery(query).split(/\s+/).filter(Boolean);
      return tokens.every(function (token) {
        return haystack.indexOf(token) !== -1;
      });
    }

    function visibleOptions() {
      return Array.prototype.filter.call(getOptions(), function (option) {
        return !option.hidden;
      });
    }

    function setHighlighted(option) {
      if (highlightedOption) {
        highlightedOption.classList.remove('is-highlighted');
      }
      highlightedOption = option;
      if (highlightedOption) {
        highlightedOption.classList.add('is-highlighted');
        highlightedOption.scrollIntoView({ block: 'nearest' });
      }
    }

    function filterOptions(query) {
      var visibleCount = 0;
      var menu = getMenu();
      var emptyState = getEmptyState();

      Array.prototype.forEach.call(getOptions(), function (option) {
        var matches = optionMatches(option, query);
        option.hidden = !matches;
        if (matches) {
          visibleCount += 1;
        } else {
          option.classList.remove('is-highlighted');
        }
      });

      if (emptyState) {
        emptyState.hidden = visibleCount > 0;
      }
      if (menu) {
        menu.hidden = visibleCount === 0;
      }
      setHighlighted(visibleOptions()[0] || null);
    }

    function resetFilter() {
      var searchInput = getSearchInput();
      if (searchInput) {
        searchInput.value = '';
      }
      filterOptions('');
    }

    function positionDropdown() {
      var dropdown = portaledDropdown;
      if (!dropdown || !toggle) {
        return;
      }

      var rect = toggle.getBoundingClientRect();
      var viewportPadding = 8;
      var gap = 4;
      var minWidth = Math.max(rect.width, 16 * 16);
      var maxWidth = Math.min(20 * 16, window.innerWidth - viewportPadding * 2);
      var width = Math.min(Math.max(minWidth, rect.width), maxWidth);

      var left = rect.left;
      if (left + width > window.innerWidth - viewportPadding) {
        left = Math.max(viewportPadding, window.innerWidth - viewportPadding - width);
      }
      if (left < viewportPadding) {
        left = viewportPadding;
      }

      var spaceBelow = window.innerHeight - rect.bottom - viewportPadding;
      var spaceAbove = rect.top - viewportPadding;
      var preferBelow = spaceBelow >= 12 * 16 || spaceBelow >= spaceAbove;
      var maxHeight = Math.max(8 * 16, Math.min(18 * 16, preferBelow ? spaceBelow - gap : spaceAbove - gap));

      dropdown.style.position = 'fixed';
      dropdown.style.zIndex = '200';
      dropdown.style.width = width + 'px';
      dropdown.style.minWidth = width + 'px';
      dropdown.style.left = Math.round(left) + 'px';
      dropdown.style.maxHeight = Math.round(maxHeight) + 'px';
      dropdown.style.overflow = 'auto';

      if (preferBelow) {
        dropdown.style.top = Math.round(rect.bottom + gap) + 'px';
      } else {
        var height = Math.min(dropdown.offsetHeight || maxHeight, maxHeight);
        dropdown.style.top = Math.round(rect.top - gap - height) + 'px';
      }
    }

    function onDocumentPointerDown(event) {
      var target = event.target;
      if (!(target instanceof Node)) {
        return;
      }
      var dropdown = getDropdown();
      if (root.contains(target) || (dropdown && dropdown.contains(target))) {
        return;
      }
      closeMenu();
    }

    function onReposition() {
      if (open) {
        positionDropdown();
      }
    }

    function openMenu() {
      var dropdown = root.querySelector('.nowo-phone-input__prefix-dropdown');
      if (!(dropdown instanceof HTMLElement)) {
        return;
      }

      if (!placeholder) {
        placeholder = document.createComment('phone-prefix-picker-dropdown');
        dropdown.parentNode.insertBefore(placeholder, dropdown);
      }

      portaledDropdown = dropdown;
      dropdown.classList.add('nowo-phone-input__prefix-dropdown--portaled');
      document.body.appendChild(dropdown);
      dropdown.hidden = false;
      toggle.setAttribute('aria-expanded', 'true');
      open = true;
      resetFilter();
      positionDropdown();

      document.addEventListener('pointerdown', onDocumentPointerDown, true);
      window.addEventListener('resize', onReposition);
      document.addEventListener('scroll', onReposition, true);

      requestAnimationFrame(function () {
        var searchInput = getSearchInput();
        if (searchInput) {
          searchInput.focus();
        }
      });
    }

    function closeMenu() {
      var dropdown = portaledDropdown;

      document.removeEventListener('pointerdown', onDocumentPointerDown, true);
      window.removeEventListener('resize', onReposition);
      document.removeEventListener('scroll', onReposition, true);

      if (dropdown instanceof HTMLElement) {
        dropdown.hidden = true;
        dropdown.classList.remove('nowo-phone-input__prefix-dropdown--portaled');
        dropdown.style.removeProperty('position');
        dropdown.style.removeProperty('top');
        dropdown.style.removeProperty('left');
        dropdown.style.removeProperty('width');
        dropdown.style.removeProperty('min-width');
        dropdown.style.removeProperty('max-height');
        dropdown.style.removeProperty('overflow');
        dropdown.style.removeProperty('z-index');
        if (placeholder && placeholder.parentNode) {
          placeholder.parentNode.insertBefore(dropdown, placeholder);
          placeholder.parentNode.removeChild(placeholder);
        }
        placeholder = null;
      }

      portaledDropdown = null;
      open = false;
      toggle.setAttribute('aria-expanded', 'false');
      resetFilter();
      setHighlighted(null);
    }

    function applySelection(option) {
      var iso = option.dataset.iso || '';
      var prefix = option.dataset.prefix || '';
      var emoji = option.dataset.flag || '';

      select.value = iso;
      select.dispatchEvent(new Event('change', { bubbles: true }));

      var toggleFlag = root.querySelector('.nowo-phone-input__prefix-toggle-flag');
      var toggleCode = root.querySelector('.nowo-phone-input__prefix-toggle-code');
      if (toggleFlag && flagDisplay !== 'NONE') {
        toggleFlag.innerHTML = renderFlagHtml(iso, emoji);
      }
      if (toggleCode && prefixDisplay !== 'FLAG_ONLY') {
        toggleCode.textContent = prefix;
      }

      Array.prototype.forEach.call(getOptions(), function (item) {
        var selected = item.dataset.iso === iso;
        item.classList.toggle('is-selected', selected);
        item.setAttribute('aria-selected', selected ? 'true' : 'false');
      });
    }

    function selectHighlighted() {
      if (highlightedOption && !highlightedOption.hidden) {
        applySelection(highlightedOption);
        closeMenu();
      }
    }

    function moveHighlight(direction) {
      var visible = visibleOptions();
      if (visible.length === 0) {
        setHighlighted(null);
        return;
      }
      var index = highlightedOption ? visible.indexOf(highlightedOption) : -1;
      index = (index + direction + visible.length) % visible.length;
      setHighlighted(visible[index] || null);
    }

    function toggleMenu(event) {
      if (event) {
        event.preventDefault();
        event.stopPropagation();
      }
      if (open) {
        closeMenu();
      } else {
        openMenu();
      }
    }

    function onSearchInput() {
      var searchInput = getSearchInput();
      filterOptions(searchInput ? searchInput.value : '');
    }

    function onSearchKeydown(event) {
      if (event.key === 'ArrowDown') {
        event.preventDefault();
        moveHighlight(1);
      } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        moveHighlight(-1);
      } else if (event.key === 'Enter') {
        event.preventDefault();
        selectHighlighted();
      } else if (event.key === 'Escape') {
        event.preventDefault();
        closeMenu();
        toggle.focus();
      }
    }

    function onToggleKeydown(event) {
      if (event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        if (!open) {
          openMenu();
        } else if (!prefixSearchEnabled && event.key === 'ArrowDown') {
          moveHighlight(1);
        }
      } else if (event.key === 'ArrowUp' && open && !prefixSearchEnabled) {
        event.preventDefault();
        moveHighlight(-1);
      } else if (event.key === 'Escape') {
        closeMenu();
      }
    }

    function onOptionClick(event) {
      event.preventDefault();
      event.stopPropagation();
      var option = event.currentTarget;
      if (!(option instanceof HTMLElement)) {
        return;
      }
      applySelection(option);
      closeMenu();
    }

    function onOptionMouseEnter(event) {
      var option = event.currentTarget;
      if (option instanceof HTMLElement && !option.hidden) {
        setHighlighted(option);
      }
    }

    toggle.addEventListener('click', toggleMenu);
    toggle.addEventListener('keydown', onToggleKeydown);

    var searchInput = getSearchInput();
    if (searchInput) {
      searchInput.addEventListener('input', onSearchInput);
      searchInput.addEventListener('keydown', onSearchKeydown);
    }

    Array.prototype.forEach.call(getOptions(), function (option) {
      option.addEventListener('click', onOptionClick);
      option.addEventListener('mouseenter', onOptionMouseEnter);
    });

    return {
      destroy: function () {
        closeMenu();
      },
    };
  }

  /**
   * @param {ParentNode} [scope]
   */
  function enhanceAll(scope) {
    var root = scope || document;
    var nodes = root.querySelectorAll(SELECTOR);
    Array.prototype.forEach.call(nodes, function (node) {
      if (node instanceof HTMLElement) {
        createPicker(node);
      }
    });
  }

  global.NowoPhonePrefixPicker = {
    enhance: createPicker,
    enhanceAll: enhanceAll,
  };

  var TAG = 'nowo-phone-input';

  class NowoPhoneInputElement extends HTMLElement {
    connectedCallback() {
      var picker = this.querySelector('[data-nowo-phone-prefix-picker], [data-controller~="phone-prefix-picker"]');
      if (picker instanceof HTMLElement) {
        createPicker(picker);
      }
    }
  }

  if (typeof customElements !== 'undefined' && customElements.get(TAG) === undefined) {
    customElements.define(TAG, NowoPhoneInputElement);
  }

  function boot() {
    enhanceAll(document);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})(typeof window !== 'undefined' ? window : this);
