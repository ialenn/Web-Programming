function renderVenuesPage() {
  updateNavbar();

  var $list = $("#venues-list");
  var $msg  = $("#venues-message");
  if ($msg.length) {
    $msg.text("");
  }
  if (!$list.length) {
    return;
  }

  var headers = {};
  var token = getToken();
  if (token) {
    headers["Authorization"] = token;
  }

  $.ajax({
    url: API_BASE + "/venues",
    method: "GET",
    headers: headers,
    success: function (res) {
      var html = "";
      var i; 
      for (i = 0; i < res.length; i++) {
        var v = res[i];
        var name     = v.name || "";
        var address  = v.address || v.location || "";
        var capacity = v.capacity || v.capacity_seats || "";

        html += ''
          + '<div class="col-md-6 mb-3">'
          + '  <div class="card p-3 h-100">'
          + '    <h5 class="mb-1">' + name + '</h5>'
          + '    <p class="mb-1">' + address + '</p>'
          + '    <p class="text-muted mb-0">Capacity: ' + capacity + '</p>'
          + '  </div>'
          + '</div>';
      }

      $list.html(html);
    },
    error: function () {
      if ($msg.length) {
        $msg.text("Could not load venues.");
      }
    }
  });
}