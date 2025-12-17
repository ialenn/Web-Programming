function renderTicketsList(events) {
  var $list = $("#tickets-list");
  if (!$list.length) return;

  if (!events || events.length === 0) {
    $list.html('<p class="text-muted">No events available.</p>');
    return;
  }

  var html = "";
  var i;
  for (i = 0; i < events.length; i++) {
    var ev = events[i];

    html += ''
      + '<div class="col-md-6 mb-3">'
      + '  <div class="card h-100 p-3 d-flex flex-row justify-content-between align-items-center">'
      + '    <div>'
      + '      <h5 class="mb-1">' + ev.title + '</h5>'
      + '      <p class="mb-1 small text-muted">'
      + '        Date: ' + (ev.date || '') + '<br>'
      + '        ' + (ev.venue || '') +
             (ev.category ? ' | ' + ev.category : '') +
      '      </p>'
      + '    </div>'
      + '    <button class="btn btn-outline-light btn-sm btn-buy-ticket"'
      + '            data-event-id="' + ev.id + '">'
      + '      Buy'
      + '    </button>'
      + '  </div>'
      + '</div>';
  }

  $list.html(html);
}

function setupTicketsPage() {
  updateNavbar();

  var user = getCurrentUser();
  var $msg = $("#tickets-message");
  if ($msg.length) $msg.text("");

  if (!user) {
    if ($msg.length) $msg.text("Please login to buy tickets.");
    setTimeout(function () {
      window.location.hash = "#login";
    }, 1500);
    return;
  }

  loadEventsFromBackend(function (success) {
    if (!success) {
      if ($msg.length) $msg.text("Could not load events for tickets.");
      return;
    }

    renderTicketsList(eventsData);

    var $list = $("#tickets-list");

    $list.off("click", ".btn-buy-ticket");
    $list.on("click", ".btn-buy-ticket", function () {
      if ($msg.length) $msg.text("");

      var eventId = $(this).data("event-id");

      TicketService.buy(
        {
          event_id: eventId,
          user_id: user.id,
          price: 10
        },
        function () {
          alert("Ticket purchased!");
        },
        function () {
          if ($msg.length) {
            $msg.text("Could not buy ticket.");
          } else {
            alert("Could not buy ticket.");
          }
        }
      );
    });
  });
}