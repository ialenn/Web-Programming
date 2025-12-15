var eventsData = [];

function loadEventsFromBackend(callback) {
  var token = getToken(); 
  if (!token) { 
    if (callback) { 
      callback(false);
    }
    return;
  }

  $.ajax({
    url: API_BASE + "/events",
    method: "GET",
    headers: { "Authorization": token },
    success: function (res) {
      var newEvents = [];
      var i;
      for (i = 0; i < res.length; i++) {
        var ev = res[i];

        var dateValue = "";
        if (ev.starts_at) {
          dateValue = ev.starts_at.substring(0, 10); // this gets YYYY-MM-DD
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

      if (callback) {
        callback(true);
      }
    },
    error: function () {
      if (callback) {
        callback(false);
      }
    }
  });
}
// Renders event cards into the given $list element. list is a jQuery object renderEventsCards($("#events-list"), data, user);
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

  if ($message.length) {
    $message.text("");
  }

  if ($title.length)       $title.text("");
  if ($description.length) $description.text("");
  if ($date.length)        $date.text("");
  if ($venue.length)       $venue.text("");
  if ($category.length)    $category.text("");

  // Get selected event ID from localStorage because frontend routing is used no URL params
  var storedId = localStorage.getItem("ems_selected_event");
  var eventId  = storedId;

  if (!eventId) {
    if ($message.length) {
      $message.text("No event selected.");
    }
    return;
  }

  var token = getToken();
  if (!token) {
    if ($message.length) {
      $message.text("Please login to see event details.");
    }
    setTimeout(function () {
      window.location.hash = "#login";
    }, 1500);
    return;
  }

  $.ajax({
    url: API_BASE + "/events/" + eventId,
    method: "GET",
    headers: { "Authorization": token },
    success: function (ev) {
      if (!ev || !ev.id) {
        if ($message.length) {
          $message.text("Event not found.");
        }
        return;
      }

      if ($title.length) {
        $title.text(ev.title || "Event #" + ev.id);
      }
      if ($description.length) {
        $description.text(
          ev.description || "No description provided for this event."
        );
      }

      if ($date.length) {
        var d = "";
        if (ev.starts_at) {
          d = ev.starts_at.substring(0, 10);
          if (ev.ends_at) {
            d += " – " + ev.ends_at.substring(0, 10); // d + = " – " + is the syntax for appending to string 
          }
        }
        $date.text(d);
      }

      if ($venue.length) {
        var venueText = ev.venue_id ? "Venue ID: " + ev.venue_id : "";
        $venue.text(venueText);
      }

      if ($category.length) {
        var catText = ev.category_id ? "Category " + ev.category_id : "";
        $category.text(catText);
      }
    },
    error: function () {
      if ($message.length) {
        $message.text("Could not load event details.");
      }
    }
  });
}

function renderEventsPage() {
  updateNavbar();

  var user = getCurrentUser();
  if (!user) {
    alert("Please login to see events.");
    window.location.hash = "#login";
    return;
  }

  var $list = $("#events-list");
  if (!$list.length) {
    return;
  }

  var $search     = $("#events-search");
  var $cat        = $("#events-category");
  var $btnAdd     = $("#btn-add-event");
  var $addForm    = $("#event-add-form");
  var $addMessage = $("#event-add-message");

  if (user.role_id === 1 || user.role_name === "admin") {
    $btnAdd.removeClass("d-none");
  } else {
    $btnAdd.addClass("d-none");
  }
// Checks if event matches current search and category filters 
  function matches(ev) {
    var q = "";
    if ($search.length) {
      var searchValue = $search.val();
      if (searchValue) {
        q = searchValue.toLowerCase();
      }
    }

    var cat = "All";
    if ($cat.length) {
      var catValue = $cat.val();
      if (catValue) {
        cat = catValue;
      }
    }

    var titleLower = ev.title.toLowerCase();
    var venueLower = ev.venue.toLowerCase();

    var titleMatch = false;
    if (titleLower.indexOf(q) !== -1 || venueLower.indexOf(q) !== -1) {  //indexOf checks if q is a substring of titleLower or venueLower
      titleMatch = true;
    }

    var catMatch = true;
if (cat !== "All") {
  catMatch = false;
  if (ev.category_id) {
    if (String(ev.category_id) === String(cat)) {
      catMatch = true;
    }
  }
}

    if (titleMatch && catMatch) {
      return true;
    } else {
      return false;
    }
  }

  function renderFiltered() {
    var filtered = [];
    var i;
    for (i = 0; i < eventsData.length; i++) {
      if (matches(eventsData[i])) {
        filtered.push(eventsData[i]);
      }
    }
    renderEventsCards($list, filtered, user);
  }
// Loads events from backend and renders them with current filters
  loadEventsFromBackend(function () {
    renderFiltered();
  });

  renderFiltered();

  if ($search.length && !$search.data("bound")) {
    $search.on("input", renderFiltered).data("bound", true);
  }
  if ($cat.length && !$cat.data("bound")) {
    $cat.on("change", renderFiltered).data("bound", true);
  }
// toggles event addition form visibility
  if ($btnAdd.length && !$btnAdd.data("bound")) {
    $btnAdd.on("click", function () {
      if ($addForm.hasClass("d-none")) {
        $addForm.removeClass("d-none");
      } else {
        $addForm.addClass("d-none");
      }
    }).data("bound", true);
  }
// creates new event on form submission
  if ($addForm.length && !$addForm.data("bound")) {
    $addForm.on("submit", function (e) {
      e.preventDefault();

      if ($addMessage.length) {
        $addMessage.text("");
      }

      var titleValue    = $("#add-title").val();
      var startsAtValue = $("#add-starts-at").val();
      var venueIdValue  = $("#add-venue-id").val();
      var descValue     = $("#add-description").val();

      if (!titleValue || !startsAtValue || !venueIdValue) {
        if ($addMessage.length) {
          $addMessage.text("Please fill in title, date and venue.");
        }
        return;
      }

      startsAtValue = startsAtValue.replace("T", ":");
      var parts = startsAtValue.split(":");
      if (parts.length === 3) {
        startsAtValue = parts[0] + ":" + parts[1] + ":00";
      }

      var token = getToken();
      if (!token) {
        alert("Missing token. Please login again.");
        window.location.hash = "#login";
        return;
      }

      $.ajax({
        url: API_BASE + "/events",
        method: "POST",
        headers: { "Authorization": token },
        data: {
          title:       titleValue,
          description: descValue,
          starts_at:   startsAtValue,
          venue_id:    venueIdValue
        },
        success: function () {
          alert("Event created.");
          $("#add-title").val("");
          $("#add-starts-at").val("");
          $("#add-venue-id").val("");
          $("#add-description").val("");

          loadEventsFromBackend(function () {
            renderFiltered();
          });
        },
        error: function () {
          alert("Failed to create event.");
        }
      });

    }).data("bound", true);
  }

  $list.off("click", ".btn-event-delete");
    $list.off("click", ".btn-event-edit");

    $list.on("click", ".btn-event-delete", function () {
      var id    = $(this).data("id");
      var token = getToken();

      if (!token) {
        alert("Missing token. Please login again.");
        window.location.hash = "#login";
        return;
      }

      if (!confirm("Delete this event?")) {
        return;
      }

      $.ajax({
        url: API_BASE + "/events/" + id,
        method: "DELETE",
        headers: { "Authorization": token },
        success: function () {
          alert("Event deleted.");
          loadEventsFromBackend(function () {
            renderFiltered();
          });
        },
        error: function () {
          alert("Failed to delete event.");
        }
      });
    });

     $list.off("click", ".btn-event-details");
  $list.on("click", ".btn-event-details", function (e) {
    e.preventDefault();

    var id = $(this).data("id");
    if (!id) {
      return;
    }
    localStorage.setItem("ems_selected_event", id);
    window.location.hash = "#event-details";
  });

    $list.on("click", ".btn-event-edit", function () {
      var id = $(this).data("id");
      var token = getToken();

      if (!token) {
        alert("Missing token. Please login again.");
        window.location.hash = "#login";
        return;
      }

      var current = null;
      var i;
      for (i = 0; i < eventsData.length; i++) {
        if (eventsData[i].id === id) {
          current = eventsData[i];
          break;
        }
      }
      if (!current) {
        alert("Event not found in current list.");
        return;
      }

      var newTitle = window.prompt("New title:", current.title);
      if (newTitle === null) {
        return; 
      }
      newTitle = newTitle.trim();
      if (!newTitle) {
        alert("Title cannot be empty.");
        return;
      }

      $.ajax({
        url: API_BASE + "/events/" + id,
        method: "PUT",
        headers: { "Authorization": token },
        data: {
          title: newTitle
        },
        success: function () {
          alert("Event updated.");
          loadEventsFromBackend(function () {
            renderFiltered();
          });
        },
        error: function () {
          alert("Failed to update event.");
        }
      });
    });
}