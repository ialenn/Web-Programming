var API_BASE = "https://events-app-z8s7n.ondigitalocean.app";

function saveAuth(token, user) {
  localStorage.setItem("ems_token", token);
  localStorage.setItem("ems_user", JSON.stringify(user));
}

function clearAuth() {
  localStorage.removeItem("ems_token");
  localStorage.removeItem("ems_user");
}

function getToken() {
  return localStorage.getItem("ems_token");
}

function getCurrentUser() {
  var raw = localStorage.getItem("ems_user");
  if (!raw) {
    return null;
  }
  try {
    return JSON.parse(raw);
  } catch (e) {
    return null;
  }
}

function updateNavbar() {
  var user = getCurrentUser();

  var $profile = $("#nav-profile");
  var $login   = $('a[href="#login"]').parent();
  var $reg     = $('a[href="#register"]').parent();

  if (user) {
    $profile.removeClass("d-none");
    if (user.name) {
      $profile.find("a").text(user.name);
    } else {
      $profile.find("a").text("Profile");
    }
    $login.addClass("d-none");
    $reg.addClass("d-none");
  } else {
    $profile.addClass("d-none");
    $login.removeClass("d-none");
    $reg.removeClass("d-none");
  }
}