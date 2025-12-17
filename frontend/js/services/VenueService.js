var VenueService = {
  getAll: function (success, error) {
    RestClient.get("/venues", success, error);
  }
};