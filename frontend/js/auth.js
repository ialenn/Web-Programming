// user authentication and profile management
//setup login is called on login page load because user might be logged in or out
function setupLoginPage() {
  updateNavbar();

  var $form = $("#login-form");
  if (!$form.length || $form.data("bound")) return;

  $form.validate({
    submitHandler: function () {
      var email    = $("#login-email").val();
      var password = $("#login-password").val();

      $("#login-message").text("");

      AuthService.login(
        { email: email, password: password },
        function (res) {
          if (res && res.success) {
            saveAuth(res.token, res.user);
            updateNavbar();
            window.location.hash = "#home";
          } else {
            $("#login-message").text(res.error || "Login failed.");
          }
        },
        function () {
          $("#login-message").text("Login failed.");
        }
      );
    }
  }).data("bound", true);
}
// user authentication and profile management in here setup the registration page logic and profile page logic 
function setupRegisterPage() {
  updateNavbar();

  var $form = $("#register-form");
  if (!$form.length || $form.data("bound")) return;

  $form.validate({
    submitHandler: function () {
      var name     = $("#reg-name").val();
      var email    = $("#reg-email").val();
      var password = $("#reg-password").val();

      $("#register-message").text("");

      AuthService.register(
        { name: name, email: email, password: password },
        function (res) {
          if (res && res.success) {
            window.location.hash = "#login";
          } else {
            $("#register-message").text(res.error || "Registration failed.");
          }
        },
        function () {
          $("#register-message").text("Registration failed.");
        }
      );
    }
  }).data("bound", true);
}
// profile page setup and ticket management here load user profile data and tickets 
function setupProfilePage() {
  updateNavbar();

  var user      = getCurrentUser();
  var $warning  = $("#profile-login-warning");
  var $adminBox = $("#profile-admin-box");
// Show warning and redirect to login if not logged in
  if (!user) {
    if ($warning.length) {
      $warning.removeClass("d-none");
    }
    setTimeout(function () {
      window.location.hash = "#login";
    }, 1500);
    return;
  }

  $("#profile-name").text(user.name || "");
  $("#profile-email").text(user.email || "");

  var roleText = "";
  if (user.role_name) {
    roleText = user.role_name;
  } else {
    if (user.role_id === 1) {
      roleText = "admin";
    } else {
      roleText = "user";
    }
  }
  $("#profile-role").text(roleText);

  if (user.role_id === 1 || user.role_name === "admin") {
    if ($adminBox.length) {
      $adminBox.removeClass("d-none");
    }
  } else {
    if ($adminBox.length) {
      $adminBox.addClass("d-none");
    }
  }

  var $btnLogout = $("#btn-logout");
  if ($btnLogout.length && !$btnLogout.data("bound")) {
    $btnLogout.on("click", function () {
      clearAuth();
      updateNavbar();
      alert("You have been logged out.");
      window.location.hash = "#home";
    }).data("bound", true);
  }

  loadEventTitlesAndTickets(user);
}
// load event titles and user tickets for profile page
function loadEventTitlesAndTickets(user) {
  var $msg = $("#profile-tickets-message");
  if ($msg.length) {
    $msg.text("Loading tickets...");
  }

  var token = getToken();
  if (!token) {
    if ($msg.length) {
      $msg.text("Missing token. Please login again.");
    }
    return;
  }

  $.ajax({
    url: API_BASE + "/events",
    method: "GET",
    headers: { "Authorization": token },
    success: function (events) {
      var titlesById = {};
      var i;
      for (i = 0; i < events.length; i++) {
        var ev = events[i];
        titlesById[ev.id] = ev.title || ("Event #" + ev.id);
      }
      loadUserTickets(user, titlesById);
    },
    error: function () {
      loadUserTickets(user, {});
    }
  });
}
// load user tickets for profile page
function loadUserTickets(user, titlesById) {
  if (!titlesById) {
    titlesById = {};
  }

  var $list = $("#profile-tickets-list");
  var $msg  = $("#profile-tickets-message");

  if (!$list.length) {
    return;
  }

  if ($msg.length) {
    $msg.text("Loading tickets...");
  }

  var token = getToken();
  if (!token) {
    if ($msg.length) {
      $msg.text("Missing token. Please login again.");
    }
    return;
  }

  $.ajax({
    url: API_BASE + "/tickets",
    method: "GET",
    headers: { "Authorization": token },
    success: function (res) {
      var userTickets = [];
      var i;
      for (i = 0; i < res.length; i++) {
        if (res[i].user_id === user.id) {
          userTickets.push(res[i]);
        }
      }

      if (!userTickets.length) {
        $list.html("");
        if ($msg.length) {
          $msg.text("You have no tickets yet.");
        }
        return;
      }

      if ($msg.length) {
        $msg.text("");
      }

      var html = "";
      for (i = 0; i < userTickets.length; i++) {
        var t = userTickets[i];

        var title = "Event #" + t.event_id;
        if (titlesById[t.event_id]) {
          title = titlesById[t.event_id];
        }

        html += ''
          + '<div class="list-group-item d-flex justify-content-between align-items-center">'
          + '  <div>'
          + '    <div><strong>' + title + '</strong></div>'
          + '    <div class="small text-muted">Price: ' + t.price + '</div>'
          + '  </div>'
          + '  <div class="btn-group btn-group-sm">'
          + '    <button class="btn btn-outline-light btn-ticket-more"'
          + '            data-event-id="' + t.event_id + '"'
          + '            data-price="' + t.price + '">+1</button>'
          + '    <button class="btn btn-outline-light btn-ticket-remove"'
          + '            data-ticket-id="' + t.id + '">Remove</button>'
          + '  </div>'
          + '</div>';
      }

      $list.html(html);

      $list.off("click", ".btn-ticket-more");
      $list.off("click", ".btn-ticket-remove");

      $list.on("click", ".btn-ticket-more", function () {
        var eventId = $(this).data("event-id");
        var price   = $(this).data("price");

        buyTicketForEvent(
          eventId,
          user,
          price,
          function () {
            loadUserTickets(user, titlesById);
          },
          function () {
            alert("Could not add another ticket.");
          }
        );
      });

      $list.on("click", ".btn-ticket-remove", function () {
        var ticketId = $(this).data("ticket-id");

        deleteTicket(
          ticketId,
          function () {
            loadUserTickets(user, titlesById);
          },
          function () {
            alert("Could not remove ticket.");
          }
        );
      });
    },
    error: function () {
      if ($msg.length) {
        $msg.text("Could not load your tickets.");
      }
    }
  });
}