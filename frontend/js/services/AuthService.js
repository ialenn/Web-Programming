var AuthService = {
  login: function (data, success, error) {
    RestClient.post("/auth/login", data, success, error);
  },
  register: function (data, success, error) {
    RestClient.post("/auth/register", data, success, error);
  },
  logout: function () {
    clearAuth();
    updateNavbar();
    window.location.hash = "#login";
  }
};