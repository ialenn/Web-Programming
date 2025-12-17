var RestClient = {

  request: function (method, url, data, success, error) {
    $.blockUI();

    $.ajax({
      url: API_BASE + url,
      method: method,
      data: data,
      headers: { Authorization: getToken() },

      success: function (response) {
        $.unblockUI();
        if (success) success(response);
      },

      error: function (xhr) {
        $.unblockUI();

        if (xhr.status === 401) {
          clearAuth();
          updateNavbar();
          window.location.hash = "#login";
        }

        if (error) error(xhr);
      }
    });
  },

  get: function (url, success, error) {
    this.request("GET", url, null, success, error);
  },
  post: function (url, data, success, error) {
    this.request("POST", url, data, success, error);
  },
  put: function (url, data, success, error) {
    this.request("PUT", url, data, success, error);
  },
  delete: function (url, success, error) {
    this.request("DELETE", url, null, success, error);
  }
};