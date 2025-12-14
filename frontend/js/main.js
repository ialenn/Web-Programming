$(document).ready(function () {

  updateNavbar();

  var app = $.spapp({
    defaultView: "home",
    templateDir: "./tpl/",
    pageNotFound: "error_404"
  });

  $(document).on("click", ".home-learn-more", function (e) {
  e.preventDefault();


  var id = $(this).data("id"); // get event id from data id data id from home page
  if (!id) return;
  localStorage.setItem("ems_selected_event", id); // store event id in local storage for event details page
  window.location.hash = "#event-details";
});

  app.route({ view: "events",   onReady: renderEventsPage });
  app.route({ view: "event-details", onReady: renderEventDetailsPage });
  app.route({ view: "venues",   onReady: renderVenuesPage });
  app.route({ view: "tickets",  onReady: setupTicketsPage });
  app.route({ view: "login",    onReady: setupLoginPage });
  app.route({ view: "register", onReady: setupRegisterPage });
  app.route({ view: "profile",  onReady: setupProfilePage });
  
  app.run();

function setActiveLink() {
  var hash = window.location.hash || "#home";

  var baseHash = hash.split("?")[0]; // Remove query parameters if any meaning only the part before ? is considered which is the actual view name. 

  $(".nav-link").removeClass("active");
  $('.nav-link[href="' + baseHash + '"]').addClass("active");
}

  setActiveLink();
  $(window).on("hashchange", setActiveLink);
});