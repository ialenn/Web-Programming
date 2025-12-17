var EventService = {
  getAll: function (success, error) {
    RestClient.get("/events", success, error);
  },
  getById: function (id, success, error) {
    RestClient.get("/events/" + id, success, error);
  },
  create: function (data, success, error) {
    RestClient.post("/events", data, success, error);
  },
  update: function (id, data, success, error) {
    RestClient.put("/events/" + id, data, success, error);
  },
  delete: function (id, success, error) {
    RestClient.delete("/events/" + id, success, error);
  }
};