/* SmartVision CRM - Mobile UX Enhancements (V6.3)
   - Converts tables to card layout on small screens
   - Adds safe padding for sticky action bar
*/

(function () {
  function isMobile() {
    return window.matchMedia && window.matchMedia('(max-width: 767.98px)').matches;
  }

  function enhanceTables() {
    var tables = document.querySelectorAll('.table-responsive table.table');
    tables.forEach(function (table) {
      if (table.classList.contains('no-mobile-cards')) return;

      // Ensure we have headers
      var headers = Array.from(table.querySelectorAll('thead th')).map(function (th) {
        return (th.textContent || '').trim();
      });
      if (!headers.length) return;

      // Add class (CSS will activate only on mobile)
      table.classList.add('sv-mobile-cards');

      // Add data-label to each cell
      table.querySelectorAll('tbody tr').forEach(function (tr) {
        Array.from(tr.children).forEach(function (td, idx) {
          if (!(td instanceof HTMLElement)) return;
          var label = headers[idx] || '';
          if (label) td.setAttribute('data-label', label);
        });

        // Mark first cell as primary for readability
        var firstTd = tr.querySelector('td');
        if (firstTd) firstTd.classList.add('sv-cell-primary');
      });
    });
  }

  function handleActionBar() {
    var bar = document.querySelector('.sv-actionbar');
    if (!bar) {
      document.body.classList.remove('sv-has-actionbar');
      return;
    }
    if (isMobile()) {
      document.body.classList.add('sv-has-actionbar');
    } else {
      document.body.classList.remove('sv-has-actionbar');
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    enhanceTables();
    handleActionBar();
  });

  window.addEventListener('resize', function () {
    handleActionBar();
  });
})();
