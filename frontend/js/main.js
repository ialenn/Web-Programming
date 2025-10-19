// SPAPP SETUP
$(document).ready(function () {
  var app = $.spapp({
    pageNotFound: 'error_404',
    templateDir: './tpl/'
  });

  var eventsData = [
    { id: 1, title: 'Sarajevo Jazz Night', date: '2025-11-01', venue: 'City Hall',         category: 'Music',   img: './assets/img/jazz.jpg' },
    { id: 2, title: 'Winter Festival',     date: '2025-12-15', venue: 'Skenderija',        category: 'Food',    img: './assets/img/winter.jpg' },
    { id: 3, title: 'Food Truck Parade',   date: '2026-01-20', venue: "Wilson's",          category: 'Food',    img: './assets/img/food.jpg' },
    { id: 4, title: 'City Marathon',       date: '2026-03-10', venue: 'Centar',            category: 'Sports',  img: './assets/img/marathon.jpg' },
    { id: 5, title: 'Theatre Night',       date: '2026-04-05', venue: 'National Theatre',  category: 'Culture', img: './assets/img/theatre.jpg' },
    { id: 6, title: 'Tech Expo',           date: '2026-05-20', venue: 'Skenderija',        category: 'Culture', img: './assets/img/expo.jpg' }
  ];

  function renderEventsCards($list, data) {
    var html = "";
    for (var i = 0; i < data.length; i++) {
      var ev = data[i];
      html += ''
        + '<div class="col-md-4 mb-4">'
        + '  <div class="card h-100">'
        + '    <img class="card-img-top" src="' + (ev.img || '') + '" alt="' + ev.title + '" onerror="this.style.display=\'none\';">'
        + '    <div class="card-body">'
        + '      <div class="d-flex justify-content-between align-items-start mb-1">'
        + '        <h5 class="card-title mb-0">' + ev.title + '</h5>'
        + '        <span class="badge bg-secondary">' + (ev.category || '') + '</span>'
        + '      </div>'
        + '      <p class="card-text text-muted mb-2">'
        + '        <strong>Date:</strong> ' + ev.date + '<br>'
        + '        <strong>Venue:</strong> ' + ev.venue
        + '      </p>'
        + '      <a class="btn btn-outline-light btn-sm" href="#event-details?id=' + ev.id + '">Details</a>'
        + '    </div>'
        + '  </div>'
        + '</div>';
    }
    $list.html(html);
  }

  function renderEventsPage() {
    var $list = $("#events-list");
    if (!$list.length) return;

    var $search = $("#events-search");
    var $cat    = $("#events-category");

    // Filter matching events based on search query and category selection.
    function matches(ev) {
      var q = $search.length ? ($search.val() || '').toLowerCase() : '';
      var cat = $cat.length ? ($cat.val() || 'All') : 'All';
      var titleMatch = ev.title.toLowerCase().indexOf(q) !== -1
                    || ev.venue.toLowerCase().indexOf(q) !== -1;
      var catMatch = (cat === 'All') ? true : (ev.category === cat);
      return titleMatch && catMatch;
    }

    // Show those that match current filters
    function render() {
      var filtered = [];
      for (var i = 0; i < eventsData.length; i++) {
        if (matches(eventsData[i])) filtered.push(eventsData[i]);
      }
      renderEventsCards($list, filtered);
    }

    // initial draw + bind filters if present
    render();
    if ($search.length && !$search.data("bound")) {
      $search.on('input', render).data("bound", true);
    }
    if ($cat.length && !$cat.data("bound")) {
      $cat.on('change', render).data("bound", true);
    }
  }

  // Routes
  app.route({ view: 'home',          load: 'home.html' });
  app.route({ view: 'events',        load: 'events.html',        onReady: renderEventsPage });
  app.route({ view: 'event-details', load: 'event-details.html' });
  app.route({ view: 'venues',        load: 'venues.html' });
  app.route({ view: 'tickets',       load: 'tickets.html' });
  app.route({ view: 'login',         load: 'login.html' });
  app.route({ view: 'register',      load: 'register.html' });
  app.route({ view: 'profile',       load: 'profile.html' });
  app.route({ view: 'error_404',     load: '404.html' });

  // Default route
  if (!window.location.hash) {
    window.location.hash = '#home';
  }

  app.run();

  // Show only the current section
  function showOnlyTarget() {
    var target = window.location.hash ? window.location.hash.substring(1) : 'home';
    $('#spapp > section').hide();
    $('#' + target).show();
  }
  showOnlyTarget();
  $(window).on('hashchange', showOnlyTarget);
  $(document).on("spapp:ready", showOnlyTarget);

  // Navbar highlight
  function setActiveLink() {
    var hash = window.location.hash || "#home";
    $(".nav-link").removeClass("active");
    $(".nav-link[href='" + hash + "']").addClass("active");
  }
  setActiveLink();
  $(window).on("hashchange", setActiveLink);
});