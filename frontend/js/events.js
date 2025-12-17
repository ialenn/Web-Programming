var eventsData = [];

function loadEventsFromBackend(callback) {

  EventService.getAll(
    function (res) {
      var newEvents = [];
      var i;
      for (i = 0; i < res.length; i++) {
        var ev = res[i];

        var dateValue = "";
        if (ev.starts_at) {
          dateValue = ev.starts_at.substring(0, 10);
        }

        var venueText = "Venue ID: ";
        if (ev.venue_id) {
          venueText = "Venue ID: " + ev.venue_id;
        }

        var catText = "";
        if (ev.category_id) {
          catText = "Category " + ev.category_id;
        }

        newEvents.push({
          id: ev.id,
          title: ev.title,
          date: dateValue,
          venue: venueText,
          category: catText,
          category_id: ev.category_id,
          img: "./assets/img/default.jpg"
        });
      }

      eventsData = newEvents;
      if (callback) callback(true);
    },
    function () {
      if (callback) callback(false);
    }
  );
}

function renderEventsCards($list, data, user) {
  var isAdmin = !!(user && (user.role_id === 1 || user.role_name === "admin"));

  var html = "";
  var i;
  for (i = 0; i < data.length; i++) {
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
      + '      <a class="btn btn-outline-light btn-sm me-2 btn-event-details"'
      + '         data-id="' + ev.id + '"'
      + '         href="#event-details">Details</a>';

    if (isAdmin) {
      html += ''
        + '      <button class="btn btn-outline-warning btn-sm me-2 btn-event-edit"'
        + '              data-id="' + ev.id + '">Edit</button>'
        + '      <button class="btn btn-outline-danger btn-sm btn-event-delete"'
        + '              data-id="' + ev.id + '">Delete</button>';
    }

    html += ''
      + '    </div>'
      + '  </div>'
      + '</div>';
  }
  $list.html(html);
}

function renderEventDetailsPage() {
  updateNavbar();

  var $title       = $("#event-details-title");
  var $description = $("#event-details-description");
  var $date        = $("#event-details-date");
  var $venue       = $("#event-details-venue");
  var $category    = $("#event-details-category");
  var $message     = $("#event-details-message");

  if ($message.length) $message.text("");

  var eventId = localStorage.getItem("ems_selected_event");
  if (!eventId) {
    if ($message.length) $message.text("No event selected.");
    return;
  }

  EventService.getById(
    eventId,
    function (ev) {
      if (!ev || !ev.id) {
        if ($message.length) $message.text("Event not found.");
        return;
      }

      $title.text(ev.title || "");
      $description.text(ev.description || "");
      $date.text(ev.starts_at ? ev.starts_at.substring(0, 10) : "");
      $venue.text(ev.venue_id ? "Venue ID: " + ev.venue_id : "");
      $category.text(ev.category_id ? "Category " + ev.category_id : "");
    },
    function () {
      if ($message.length) $message.text("Could not load event details.");
    }
  );
}

function renderEventsPage() {
  updateNavbar();

  var user = getCurrentUser();
  if (!user) {
    window.location.hash = "#login";
    return;
  }

  var $list = $("#events-list");
  if (!$list.length) return;

  var $btnAdd  = $("#btn-add-event");
  var $addForm = $("#event-add-form");

  if (user.role_id === 1 || user.role_name === "admin") {
    $btnAdd.removeClass("d-none");
  } else {
    $btnAdd.addClass("d-none");
    $addForm.addClass("d-none");
  }

  if ($btnAdd.length && !$btnAdd.data("bound")) {
    $btnAdd.on("click", function () {
      $addForm.toggleClass("d-none");
    }).data("bound", true);
  }

  function renderFiltered() {
    renderEventsCards($list, eventsData, user);
  }

  loadEventsFromBackend(function () {
    renderFiltered();
  });

  if ($addForm.length && !$addForm.data("bound")) {
    $addForm.on("submit", function (e) {
      e.preventDefault();

      var data = {
        title: $("#add-title").val(),
        starts_at: $("#add-starts-at").val(),
        venue_id: $("#add-venue-id").val(),
        description: $("#add-description").val()
      };

      EventService.create(data, function () {
        loadEventsFromBackend(renderFiltered);
      });
    }).data("bound", true);
  }

  var deleteEventId = null;

  $list.off("click", ".btn-event-delete");
  $list.on("click", ".btn-event-delete", function () {
    deleteEventId = $(this).data("id");
    $("#deleteModal").modal("show");
  });

  $("#btn-confirm-delete").off("click");
  $("#btn-confirm-delete").on("click", function () {
    if (!deleteEventId) return;

    EventService.delete(
      deleteEventId,
      function () {
        $("#deleteModal").modal("hide");
        deleteEventId = null;
        loadEventsFromBackend(renderFiltered);
      },
      function () {
        $("#deleteModal").modal("hide");
        deleteEventId = null;
        alert("Failed to delete event.");
      }
    );
  });

  $list.off("click", ".btn-event-edit");
  $list.on("click", ".btn-event-edit", function () {
    var id = $(this).data("id");
    var newTitle = window.prompt("New title:");

    if (!newTitle) return;

    EventService.update(id, { title: newTitle }, function () {
      loadEventsFromBackend(renderFiltered);
    });
  });
}