var TicketService = {
  getAll: function (success, error) {
    RestClient.get("/tickets", success, error);
  },
  buy: function (data, success, error) {
    RestClient.post("/tickets", data, success, error);
  },
  delete: function (id, success, error) {
    RestClient.delete("/tickets/" + id, success, error);
  }
};