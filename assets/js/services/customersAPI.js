import axios from "axios";

function findAll() {
  return axios.get("SuperbeUrl de la mort").then((response) => {
    console.log("Afficher la Response" + response);
  });
}

function deleteCustomer(id) {
  return axios.delete(`https://localhost:8000/api/customers/${id}`);
}

export default {
  findAll,
  delete: deleteCustomer,
};
